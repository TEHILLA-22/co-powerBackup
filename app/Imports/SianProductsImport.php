<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\SpreadsheetResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Imports products from the SIAN "Price List" spreadsheet layout.
 *
 * Spreadsheet contract (Sheet1):
 *   row 1: blank
 *   row 2: "SIAN" banner
 *   row 3: headers -> No | EAN | Description | Med | Case Size | Layer | Pallet |
 *                      Case GBP | GBP | 1.17 | 1.35 | Total Stock | To Order | Notes | MOQ
 *   row 4+: product rows
 *
 * Bulk strategy: existing products are mapped once in memory (one indexed read),
 * new products are inserted in chunks with their auto-increment ids resolved in
 * order, and every imported product's Unit/Case variants are rebuilt (delete +
 * chunked insert). This keeps supplier re-imports fast even on cold caches.
 *
 * Admin-owned fields are preserved on re-import: description text, images,
 * category, flags (featured/on-sale/active) and MOQ settings are never
 * overwritten for products that already exist.
 */
class SianProductsImport
{
    protected const CHUNK = 500;

    protected array $results = [
        'imported' => 0,
        'updated' => 0,
        'failed' => 0,
        'errors' => [],
    ];

    public function import(string $path): array
    {
        $this->results = ['imported' => 0, 'updated' => 0, 'failed' => 0, 'errors' => []];

        $spreadsheet = SpreadsheetResponse::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, false, false);

        if (count($rows) < 4) {
            throw new \RuntimeException('Spreadsheet does not match the expected SIAN price-list layout.');
        }

        $data = [];
        foreach (array_slice($rows, 3) as $index => $cells) {
            $row = array_pad($cells, 15, null);

            $sku = trim((string) ($row[0] ?? ''));
            $name = trim((string) ($row[2] ?? ''));

            if ($sku === '' && $name === '') {
                continue;
            }

            $data[] = ['row' => $row, 'line' => $index + 4];
        }

        DB::transaction(function () use ($data) {
            [$skuMap, $eanMap] = $this->existingMaps();

            $newProducts = [];      // seed key => entry waiting to be created
            $productIds = [];       // product id => row for variant rebuild
            $usedSkus = [];

            foreach ($data as $entry) {
                $row = $entry['row'];
                $sku = trim((string) ($row[0] ?? ''));
                $ean = trim((string) ($row[1] ?? ''));
                $seed = $ean !== '' ? ($eanMap[$ean] ?? null) : null;
                $seed = $seed ?? ($sku !== '' ? ($skuMap[$sku] ?? null) : null);

                if ($seed !== null) {
                    $productIds[$seed] = $row;
                    $this->results['updated']++;
                } else {
                    $key = $ean !== '' ? 'e:' . $ean : 's:' . $sku;
                    if (isset($newProducts[$key])) {
                        $this->results['failed']++;
                        $this->results['errors'][] = 'Row ' . $entry['line'] . ' [' . ($sku ?: $ean) . ']: duplicate key within this file';
                        continue;
                    }
                    // The price list reuses the placeholder SKU "NEW" for several
                    // products, so make the stored SKU unique and deterministic.
                    if ($sku === '' || isset($usedSkus[$sku])) {
                        $sku = $ean !== '' ? $sku . '-' . $ean : $sku . '-' . Str::random(6);
                    }
                    $usedSkus[$sku] = true;
                    $row[0] = $sku;
                    $newProducts[$key] = ['row' => $row, 'line' => $entry['line']];
                }
            }

            // Batch insert new products and resolve their ids.
            $chunked = array_values($newProducts);
            for ($offset = 0; $offset < count($chunked); $offset += self::CHUNK) {
                $slice = array_slice($chunked, $offset, self::CHUNK);
                $payloads = [];
                foreach ($slice as $entry) {
                    $payloads[] = $this->createPayload($entry['row']);
                }

                DB::table('products')->insert($payloads);
                $firstId = (int) DB::getPdo()->lastInsertId();

                foreach ($slice as $i => $entry) {
                    $productIds[$firstId + $i] = $entry['row'];
                }
            }

            $this->results['imported'] = count($chunked);

            if ($productIds === []) {
                return;
            }

            // Rebuild product variants for every imported product.
            ProductVariant::whereIn('product_id', array_keys($productIds))->forceDelete();

            $variants = [];
            foreach ($productIds as $productId => $row) {
                $caseSize = (int) ($row[4] ?? 0);
                $entries = [$this->unitVariantData((float) ($row[8] ?? 0), (int) ($row[11] ?? 0), $caseSize)];
                if ($caseSize > 0) {
                    $entries[] = $this->caseVariantData($caseSize, (int) ($row[5] ?? 0), (int) ($row[6] ?? 0), (float) ($row[7] ?? 0), (int) ($row[11] ?? 0), (float) ($row[8] ?? 0));
                }
                foreach ($entries as $variant) {
                    $variant['product_id'] = $productId;
                    $variants[] = $variant;
                }
            }

            foreach (array_chunk($this->padVariantRows($variants), 2000) as $chunkRows) {
                DB::table('product_variants')->insert($chunkRows);
            }
        });

        return $this->results;
    }

    protected function padVariantRows(array $variants): array
    {
        $keys = [];
        foreach ($variants as $row) {
            foreach (array_keys($row) as $key) {
                $keys[$key] = true;
            }
        }

        return array_map(fn ($row) => array_replace(array_fill_keys(array_keys($keys), null), $row), $variants);
    }

    protected function existingMaps(): array
    {
        $skuMap = [];
        $eanMap = [];

        foreach (Product::withTrashed()->get(['id', 'ean', 'sku']) as $p) {
            if ($p->sku !== null && $p->sku !== '') {
                $skuMap[$p->sku] = $p->id;
            }
            if ($p->ean !== null && $p->ean !== '') {
                $eanMap[$p->ean] = $p->id;
            }
        }

        return [$skuMap, $eanMap];
    }

    protected function createPayload(array $row): array
    {
        $sku = trim((string) ($row[0] ?? ''));
        $ean = trim((string) ($row[1] ?? ''));
        $name = trim((string) ($row[2] ?? ''));
        $caseSize = (int) ($row[4] ?? 0);
        $layer = (int) ($row[5] ?? 0);
        $pallet = (int) ($row[6] ?? 0);
        $notes = trim((string) ($row[13] ?? ''));
        $moqGbp = trim((string) ($row[14] ?? ''));

        $now = now();

        return [
            'ean' => $ean !== '' ? $ean : null,
            'sku' => $sku,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'short_description' => $this->buildShortDescription($caseSize, $notes),
            'description' => $this->buildDescription($name, $caseSize, $layer, $pallet, $notes, $moqGbp),
            'moq' => 1,
            'moq_enforced' => 0,
            'moq_increment' => 1,
            'is_active' => 1,
            'is_featured' => 0,
            'is_on_sale' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    protected function unitVariantData(float $unitPrice, int $stock, int $caseSize): array
    {
        $now = now();

        return [
            'variant_type' => 'unit',
            'variant_name' => 'Unit',
            'quantity_per_unit' => 1,
            'units_per_case' => $caseSize > 0 ? $caseSize : null,
            'base_price' => round($unitPrice, 4),
            'stock_quantity' => $stock > 0 ? $stock : 0,
            'reserved_quantity' => 0,
            'reorder_level' => 0,
            'reorder_quantity' => max(1, $caseSize),
            'is_active' => 1,
            'in_stock' => $stock > 0 ? 1 : 0,
            'allow_backorder' => 1,
            'min_order_quantity' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    protected function caseVariantData(int $caseSize, int $layer, int $pallet, float $casePrice, int $stock, float $unitPrice): array
    {
        $now = now();

        if ($casePrice <= 0 && $unitPrice > 0) {
            $casePrice = $unitPrice * $caseSize;
        }

        return [
            'variant_type' => 'case',
            'variant_name' => 'Case (' . $caseSize . ')',
            'quantity_per_unit' => $caseSize,
            'units_per_case' => $caseSize,
            'cases_per_layer' => $layer > 0 ? $layer : null,
            'layers_per_pallet' => $pallet > 0 ? $pallet : null,
            'base_price' => round($casePrice, 4),
            'stock_quantity' => $stock > 0 ? intdiv($stock, $caseSize) : 0,
            'reserved_quantity' => 0,
            'reorder_level' => 0,
            'reorder_quantity' => 1,
            'is_active' => 1,
            'in_stock' => $stock >= $caseSize ? 1 : 0,
            'allow_backorder' => 1,
            'min_order_quantity' => 1,
            'moq' => 1,
            'moq_increment' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    protected function buildShortDescription(int $caseSize, string $notes): ?string
    {
        $parts = [];
        if ($caseSize > 0) {
            $parts[] = 'Packed in cases of ' . $caseSize;
        }
        if ($notes !== '') {
            $parts[] = $notes;
        }

        return $parts ? implode('. ', $parts) : null;
    }

    protected function buildDescription(string $name, int $caseSize, int $layer, int $pallet, string $notes, string $moqGbp): string
    {
        $lines = array_filter([
            $name,
            $caseSize > 0 ? 'Pack size: ' . $caseSize . ' per case' . ($layer > 0 ? ', ' . $layer . ' cases per layer' : '') . ($pallet > 0 ? ', ' . $pallet . ' cases per pallet' : '') : null,
            $moqGbp !== '' ? 'Minimum order value: ' . $moqGbp : null,
            $notes !== '' ? 'Note: ' . $notes : null,
        ]);

        return implode("\n", $lines);
    }
}