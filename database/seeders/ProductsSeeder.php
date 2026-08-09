<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $cats = Category::whereIn('slug', ['baby-care', 'hair-products', 'cosmetics', 'oral-hygiene', 'skin-care'])
            ->pluck('id', 'slug');

        $products = [
            [
                'category' => 'baby-care',
                'ean' => '5012345678901',
                'sku' => 'BC-SHMPU',
                'name' => 'Baby Shampoo & Wash 500ml',
                'brand' => 'Copower',
                'brand2' => 'GentleCare',
                'moq' => 24,
                'base_price' => 3.40,
                'unit_cost' => 1.95,
            ],
            [
                'category' => 'baby-care',
                'ean' => '5012345678902',
                'sku' => 'BC-BBYWT',
                'name' => 'Baby Wipes 80 Pack',
                'brand' => 'Copower',
                'brand2' => 'GentleCare',
                'moq' => 48,
                'base_price' => 2.10,
                'unit_cost' => 1.20,
            ],
            [
                'category' => 'hair-products',
                'ean' => '5012345678903',
                'sku' => 'HR-CRLCREM',
                'name' => 'Curl Defining Cream 250ml',
                'brand' => 'Copower',
                'brand2' => 'GlowRite',
                'moq' => 12,
                'base_price' => 4.80,
                'unit_cost' => 2.60,
            ],
            [
                'category' => 'cosmetics',
                'ean' => '5012345678904',
                'sku' => 'CS-MTCHSTK',
                'name' => 'Matte Lipstick 3.5g',
                'brand' => 'Copower',
                'brand2' => 'Belle',
                'moq' => 36,
                'base_price' => 3.95,
                'unit_cost' => 2.05,
            ],
            [
                'category' => 'oral-hygiene',
                'ean' => '5012345678905',
                'sku' => 'OH-TBPASTE',
                'name' => 'Whitening Toothpaste 100ml',
                'brand' => 'Copower',
                'brand2' => 'DentaGlo',
                'moq' => 48,
                'base_price' => 1.85,
                'unit_cost' => 0.95,
            ],
            [
                'category' => 'skin-care',
                'ean' => '5012345678906',
                'sku' => 'SK-SERUM',
                'name' => 'Vitamin C Serum 30ml',
                'brand' => 'Copower',
                'brand2' => 'DermaGlw',
                'moq' => 12,
                'base_price' => 9.50,
                'unit_cost' => 4.80,
            ],
        ];

        $count = 0;
        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'ean' => $data['ean'],
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']) . '-' . $data['sku'],
                    'short_description' => $data['brand2'] . ': wholesale ' . Str::lower($data['name']) . ' from Copower Wholesale.',
                    'description' => 'High-quality ' . Str::lower($data['name']) . ' from trusted brand ' . $data['brand2'] . ', available exclusively through Copower Wholesale B2B for registered trade accounts.',
                    'brand' => $data['brand'],
                    'manufacturer' => $data['brand2'],
                    'category_id' => $cats[$data['category']] ?? null,
                    'moq' => $data['moq'],
                    'moq_enforced' => true,
                    'moq_increment' => 1,
                    'is_active' => true,
                    'is_featured' => true,
                    'is_on_sale' => false,
                ]
            );

            $variants = [
                ['type' => 'unit', 'name' => 'Unit', 'qty' => 1, 'mult' => 1.0, 'pack' => 1],
                ['type' => 'case', 'name' => 'Case (12)', 'qty' => 12, 'mult' => 11.0, 'pack' => 12],
                ['type' => 'layer', 'name' => 'Layer (48)', 'qty' => 48, 'mult' => 42.0, 'pack' => 48],
                ['type' => 'pallet', 'name' => 'Pallet (240)', 'qty' => 240, 'mult' => 205.0, 'pack' => 240],
            ];

            foreach ($variants as $v) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variant_type' => $v['type'],
                    ],
                    [
                        'variant_name' => $v['name'],
                        'quantity_per_unit' => $v['qty'],
                        'base_price' => round($data['base_price'] * $v['mult'], 2),
                        'cost_price' => round($data['unit_cost'] * $v['qty'], 2),
                        'stock_quantity' => 200 * $v['pack'],
                        'reserved_quantity' => 0,
                        'reorder_level' => 24,
                        'reorder_quantity' => 96,
                        'is_active' => true,
                        'in_stock' => true,
                        'allow_backorder' => false,
                        'moq' => $data['moq'] * $v['qty'],
                        'moq_increment' => $v['qty'],
                        'weight_kg' => round(0.2 * $v['qty'], 2),
                    ]
                );
            }

            $count++;
        }

        $this->command?->info("Seeded {$count} demo products with unit/case/layer/pallet variants.");
    }
}