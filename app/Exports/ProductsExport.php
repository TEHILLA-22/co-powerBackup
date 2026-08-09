<?php

namespace App\Exports;

use App\Models\Product;
use App\Support\SpreadsheetResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public static function headings(): array
    {
        return [
            'EAN',
            'SKU',
            'Name',
            'Brand',
            'Manufacturer',
            'Category',
            'Short Description',
            'Description',
            'MOQ',
            'MOQ Enforced',
            'MOQ Increment',
            'Is Active',
            'Is Featured',
            'Is On Sale',
            'Unit Price',
            'Unit Stock',
            'Unit MOQ',
            'Case Price',
            'Case Stock',
            'Case MOQ',
            'Layer Price',
            'Layer Stock',
            'Layer MOQ',
            'Pallet Price',
            'Pallet Stock',
            'Pallet MOQ',
        ];
    }

    public function spreadsheet(): Spreadsheet
    {
        $query = Product::with(['category', 'variants']);

        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhere('ean', 'LIKE', "%{$search}%");
            });
        }

        if (isset($this->filters['category_id']) && $this->filters['category_id']) {
            $query->where('category_id', $this->filters['category_id']);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headings = static::headings();
        $sheet->fromArray([$headings], null, 'A1');

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0F3D5E');
        $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF');
        $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 2;
        foreach ($query->get() as $product) {
            $variants = $product->variants->keyBy('variant_type');

            $values = [
                $product->ean,
                $product->sku,
                $product->name,
                $product->brand,
                $product->manufacturer,
                $product->category?->name,
                $product->short_description,
                $product->description,
                $product->moq,
                $product->moq_enforced ? 'Yes' : 'No',
                $product->moq_increment,
                $product->is_active ? 'Yes' : 'No',
                $product->is_featured ? 'Yes' : 'No',
                $product->is_on_sale ? 'Yes' : 'No',
                $variants->get('unit')?->base_price,
                $variants->get('unit')?->stock_quantity,
                $variants->get('unit')?->moq,
                $variants->get('case')?->base_price,
                $variants->get('case')?->stock_quantity,
                $variants->get('case')?->moq,
                $variants->get('layer')?->base_price,
                $variants->get('layer')?->stock_quantity,
                $variants->get('layer')?->moq,
                $variants->get('pallet')?->base_price,
                $variants->get('pallet')?->stock_quantity,
                $variants->get('pallet')?->moq,
            ];

            $sheet->fromArray([$values], null, 'A' . $rowNumber);
            $rowNumber++;
        }

        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $sheet->getStyle('A3:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return $spreadsheet;
    }

    public static function download(array $filters = [], string $format = 'xlsx')
    {
        return SpreadsheetResponse::download((new static($filters))->spreadsheet(), "products.{$format}");
    }
}