<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::with(['category', 'variants']);

        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('ean', 'LIKE', "%{$search}%");
            });
        }

        if (isset($this->filters['category_id']) && $this->filters['category_id']) {
            $query->where('category_id', $this->filters['category_id']);
        }

        return $query->get();
    }

    public function headings(): array
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

    public function map($product): array
    {
        $variants = $product->variants->keyBy('variant_type');

        return [
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
    }
}