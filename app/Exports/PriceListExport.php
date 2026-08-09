<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\CustomerTier;
use App\Support\SpreadsheetResponse;
use App\Services\Pricing\Contracts\PricingEngineInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PriceListExport
{
    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public static function headings(): array
    {
        return [
            'EAN',
            'SKU',
            'Name',
            'Brand',
            'Category',
            'Your Price (Unit)',
            'Unit MOQ',
            'Unit Stock',
            'Case Price',
            'Case MOQ',
            'Case Stock',
            'Layer Price',
            'Layer MOQ',
            'Layer Stock',
            'Pallet Price',
            'Pallet MOQ',
            'Pallet Stock',
            'Tier Discount',
            'Allow Backorder',
            'In Stock',
        ];
    }

    public function spreadsheet(): Spreadsheet
    {
        $pricingEngine = app(PricingEngineInterface::class);

        $tier = CustomerTier::whereHas('users', function ($q) {
            $q->where('id', $this->userId);
        })->first();

        $tierDiscount = $tier ? (float) $tier->discount_percentage : 0.0;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Price List');

        $headings = static::headings();
        $sheet->fromArray([$headings], null, 'A1');

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0F3D5E');
        $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF');
        $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 2;
        $products = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        foreach ($products as $product) {
            $variants = $product->variants->keyBy('variant_type');

            $priced = [];
            foreach (['unit', 'case', 'layer', 'pallet'] as $type) {
                $variant = $variants->get($type);
                if (!$variant) {
                    $priced[$type] = ['price' => null, 'moq' => null, 'stock' => null];
                    continue;
                }

                $priceResult = $pricingEngine->calculatePrice($variant->id, 1, $this->userId);

                $priced[$type] = [
                    'price' => $priceResult->finalPrice,
                    'moq' => $variant->getEffectiveMoq(),
                    'stock' => $variant->stock_quantity,
                ];
            }

            $values = [
                $product->ean,
                $product->sku,
                $product->name,
                $product->brand,
                $product->category?->name,
                $priced['unit']['price'],
                $priced['unit']['moq'],
                $priced['unit']['stock'],
                $priced['case']['price'],
                $priced['case']['moq'],
                $priced['case']['stock'],
                $priced['layer']['price'],
                $priced['layer']['moq'],
                $priced['layer']['stock'],
                $priced['pallet']['price'],
                $priced['pallet']['moq'],
                $priced['pallet']['stock'],
                $tierDiscount > 0 ? $tierDiscount . '%' : '—',
                $product->variants->contains('allow_backorder', true) ? 'Yes' : 'No',
                $product->variants->contains(fn ($v) => (bool) $v->in_stock) ? 'Yes' : 'No',
            ];

            $sheet->fromArray([$values], null, 'A' . $rowNumber);
            $rowNumber++;
        }

        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(4)->setAutoSize(true);
        $sheet->getStyle('A3:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return $spreadsheet;
    }

    public static function download(int $userId, string $format = 'xlsx')
    {
        return SpreadsheetResponse::download((new static($userId))->spreadsheet(), "copower-price-list.{$format}");
    }
}
