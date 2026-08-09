<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Support\SpreadsheetResponse;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProductsImport
{
    protected bool $skipHeader = true;

    protected array $results = [
        'imported' => 0,
        'failed' => 0,
        'errors' => [],
    ];

    public function skipHeader(bool $skip = true): static
    {
        $this->skipHeader = $skip;

        return $this;
    }

    public function import(string $path): array
    {
        $spreadsheet = SpreadsheetResponse::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, false, false);

        if (empty($rows)) {
            return $this->results;
        }

        // H 0 is the header row when we skip (assume first row = column names)
        $headerRow = array_map('trim', array_map('strval', $rows[0]));
        $headerMap = array_flip($headerRow);

        $startAt = $this->skipHeader ? 1 : 0;

        foreach (array_slice($rows, $startAt) as $index => $cells) {
            $row = [];
            foreach ($headerMap as $heading => $offset) {
                $key = $heading !== '' ? strtolower($heading) : 'col_' . $offset;
                $row[$key] = $cells[$offset] ?? null;
            }

            try {
                $this->importRow($row);
                $this->results['imported']++;
            } catch (\Throwable $e) {
                $this->results['failed']++;
                $this->results['errors'][] = 'Row ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        return $this->results;
    }

    protected function importRow(array $row)
    {
        $categoryId = null;
        if (!empty($row['category'])) {
            $category = Category::firstOrCreate(
                ['name' => $row['category']],
                ['slug' => Str::slug($row['category']), 'is_active' => true]
            );
            $categoryId = $category->id;
        }

        $existing = Product::where('ean', $row['ean'] ?? null)
            ->orWhere('sku', $row['sku'] ?? null)
            ->first();

        $payload = [
            'ean' => $row['ean'] ?? null,
            'sku' => $row['sku'] ?? null,
            'name' => $row['name'] ?? null,
            'brand' => $row['brand'] ?? null,
            'manufacturer' => $row['manufacturer'] ?? null,
            'category_id' => $categoryId,
            'short_description' => $row['short description'] ?? $row['short_description'] ?? null,
            'description' => $row['description'] ?? null,
            'moq' => (int) ($row['moq'] ?? 1),
            'moq_enforced' => $this->toBool($row['moq enforced'] ?? $row['moq_enforced'] ?? true),
            'moq_increment' => (int) ($row['moq increment'] ?? $row['moq_increment'] ?? 1),
            'is_active' => $this->toBool($row['is active'] ?? $row['is_active'] ?? true),
            'is_featured' => $this->toBool($row['is featured'] ?? $row['is_featured'] ?? false),
            'is_on_sale' => $this->toBool($row['is on sale'] ?? $row['is_on_sale'] ?? false),
        ];

        if ($existing) {
            $existing->update($payload);
            $product = $existing;
        } else {
            $payload['slug'] = Str::slug($row['name'] ?? 'product') . '-' . Str::random(5);
            $product = Product::create($payload);
        }

        $variantTypes = ['unit', 'case', 'layer', 'pallet'];
        foreach ($variantTypes as $type) {
            $priceKey = $type . ' price';
            $stockKey = $type . ' stock';
            $moqKey = $type . ' moq';

            if (!array_key_exists($priceKey, $row) || $row[$priceKey] === null || $row[$priceKey] === '') {
                continue;
            }

            ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'variant_type' => $type],
                [
                    'variant_name' => $row[$type . '_name'] ?? $row[$type . ' name'] ?? null,
                    'quantity_per_unit' => (int) ($row[$type . '_quantity'] ?? $row[$type . ' quantity'] ?? 1),
                    'base_price' => (float) $row[$priceKey],
                    'cost_price' => isset($row[$type . '_cost']) && $row[$type . '_cost'] !== '' ? (float) $row[$type . '_cost'] : null,
                    'sale_price' => isset($row[$type . '_sale']) && $row[$type . '_sale'] !== '' ? (float) $row[$type . '_sale'] : null,
                    'stock_quantity' => (int) ($row[$stockKey] ?? 0),
                    'reorder_level' => (int) ($row[$type . '_reorder'] ?? $row[$type . ' reorder'] ?? 0),
                    'weight_kg' => isset($row[$type . '_weight']) && $row[$type . '_weight'] !== '' ? (float) $row[$type . '_weight'] : null,
                    'moq' => isset($row[$moqKey]) && $row[$moqKey] !== '' ? (int) $row[$moqKey] : null,
                    'in_stock' => (int) ($row[$stockKey] ?? 0) > 0,
                    'is_active' => true,
                ]
            );
        }

        return $product;
    }

    protected function toBool($value): bool
    {
        if (is_bool($value)) return $value;
        if (in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y', 'active'], true)) return true;

        return false;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}