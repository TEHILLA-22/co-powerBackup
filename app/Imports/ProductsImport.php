<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $skipHeader = true;
    protected $results = [
        'imported' => 0,
        'failed' => 0,
        'errors' => [],
    ];

    public function skipHeader($skip = true)
    {
        $this->skipHeader = $skip;
        return $this;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $this->importRow($row);
                $this->results['imported']++;
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->results['errors'][] = "Row " . ($row->get('row_index') ?? 'unknown') . ": " . $e->getMessage();
            }
        }
    }

    protected function importRow($row)
    {
        // Find or create category
        $categoryId = null;
        if ($row->get('category')) {
            $category = Category::firstOrCreate(
                ['name' => $row->get('category')],
                ['slug' => Str::slug($row->get('category')), 'is_active' => true]
            );
            $categoryId = $category->id;
        }

        // Find or create product
        $productData = [
            'ean' => $row->get('ean'),
            'sku' => $row->get('sku'),
            'name' => $row->get('name'),
            'slug' => Str::slug($row->get('name')) . '-' . Str::random(5),
            'brand' => $row->get('brand'),
            'manufacturer' => $row->get('manufacturer'),
            'category_id' => $categoryId,
            'short_description' => $row->get('short_description'),
            'description' => $row->get('description'),
            'moq' => $row->get('moq', 1),
            'moq_enforced' => $row->get('moq_enforced', true),
            'moq_increment' => $row->get('moq_increment', 1),
            'is_active' => $row->get('is_active', true),
            'is_featured' => $row->get('is_featured', false),
            'is_on_sale' => $row->get('is_on_sale', false),
            'created_by' => auth()->id(),
        ];

        // Check if product exists by EAN or SKU
        $product = Product::where('ean', $row->get('ean'))
            ->orWhere('sku', $row->get('sku'))
            ->first();

        if ($product) {
            // Update existing product
            $product->update($productData);
        } else {
            // Create new product
            $product = Product::create($productData);
        }

        // Create variants
        $variantTypes = ['unit', 'case', 'layer', 'pallet'];
        foreach ($variantTypes as $type) {
            $priceKey = $type . '_price';
            $stockKey = $type . '_stock';
            $moqKey = $type . '_moq';

            if ($row->get($priceKey) !== null) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variant_type' => $type,
                    ],
                    [
                        'variant_name' => $row->get($type . '_name'),
                        'quantity_per_unit' => $row->get($type . '_quantity', 1),
                        'base_price' => $row->get($priceKey),
                        'cost_price' => $row->get($type . '_cost'),
                        'sale_price' => $row->get($type . '_sale'),
                        'stock_quantity' => $row->get($stockKey, 0),
                        'reorder_level' => $row->get($type . '_reorder', 0),
                        'weight_kg' => $row->get($type . '_weight'),
                        'moq' => $row->get($moqKey),
                        'in_stock' => ($row->get($stockKey, 0) > 0),
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]
                );
            }
        }

        return $product;
    }

    public function rules(): array
    {
        return [
            'ean' => ['required', 'string', 'max:20'],
            'sku' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'case_price' => ['nullable', 'numeric', 'min:0'],
            'layer_price' => ['nullable', 'numeric', 'min:0'],
            'pallet_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function getResults()
    {
        return $this->results;
    }
}