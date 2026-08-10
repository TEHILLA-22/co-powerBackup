<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\SettingsService;
use App\Services\Pricing\Contracts\PricingEngineInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller implements HasMiddleware
{
    protected PricingEngineInterface $pricingEngine;
    protected $productsPerPage;

    public static function middleware(): array
    {
        return ['auth', 'b2b.access'];
    }

    public function __construct(PricingEngineInterface $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
        $this->productsPerPage = SettingsService::get('products_per_page', 24);
    }

    /**
     * Display product catalog
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tierId = $user->customer_tier_id;

        $query = Product::with(['category', 'variants'])
            ->where('is_active', true);

        // Category filter
        if ($request->has('category') && $request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = $this->getCategoryIds($category);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Featured filter
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('ean', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Brand filter
        if ($request->has('brand') && $request->brand) {
            $query->where('brand', $request->brand);
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->where('base_price', '>=', $request->min_price);
            });
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->where('base_price', '<=', $request->max_price);
            });
        }

        // In stock filter
        if ($request->has('in_stock') && $request->in_stock) {
            $query->whereHas('variants', function($q) {
                $q->where('stock_quantity', '>', 0);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['name', 'brand', 'created_at', 'price'];
        
        if (in_array($sortField, $allowedSorts)) {
            if ($sortField === 'price') {
                $query->orderBy(
                    ProductVariant::select('base_price')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('base_price', $sortDirection)
                        ->limit(1),
                    $sortDirection
                );
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        $products = $query->paginate($this->productsPerPage)->withQueryString();

        // Enhance products with pricing and MOQ
        foreach ($products as $product) {
            $this->enhanceProduct($product, $user);
        }

        // Get categories for sidebar (parent counts roll up children)
        $categories = Category::where('is_active', true)
            ->parents()
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->withCount('products');
            }])
            ->withCount('products')
            ->orderBy('display_order')
            ->get()
            ->map(function ($parent) {
                $parent->products_count = $parent->products_count + $parent->children->sum('products_count');
                return $parent;
            });

        // Get brands for filter
        $brands = Product::where('is_active', true)
            ->distinct()
            ->pluck('brand')
            ->filter()
            ->values();

        // Get current category
        $currentCategory = null;
        if ($request->has('category')) {
            $currentCategory = Category::where('slug', $request->category)->first();
        }

        // Get current filters for view
        $filters = [
            'search' => $request->search,
            'category' => $request->category,
            'brand' => $request->brand,
            'sort' => $request->sort,
            'direction' => $request->direction,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'in_stock' => $request->in_stock,
            'featured' => $request->featured,
        ];

        // Get quote count
        $quoteCount = session('quote_count', 0);

        return view('shop.catalog.index', compact(
            'products',
            'categories',
            'brands',
            'currentCategory',
            'filters',
            'quoteCount'
        ));
    }

    /**
     * Show product detail
     */
    public function show($slug)
    {
        $user = auth()->user();
        $product = Product::with(['category', 'variants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->enhanceProduct($product, $user);

        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(8)
            ->get();

        foreach ($relatedProducts as $related) {
            $this->enhanceProduct($related, $user);
        }

        // Get quote count
        $quoteCount = session('quote_count', 0);

        return view('shop.catalog.show', compact(
            'product',
            'relatedProducts',
            'quoteCount'
        ));
    }

    /**
     * Quick search (AJAX)
     */
    public function quickSearch(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhere('ean', 'LIKE', "%{$query}%")
                  ->orWhere('brand', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'brand' => $product->brand,
                    'image' => $product->main_image,
                    'url' => route('customer.product.show', $product->slug),
                    'price' => $product->variants->min('base_price'),
                ];
            });

        return response()->json($products);
    }

    /**
     * Barcode scan lookup (AJAX)
     */
    public function barcodeLookup(Request $request)
    {
        $barcode = $request->get('barcode');
        
        if (empty($barcode)) {
            return response()->json(['error' => 'Barcode required'], 400);
        }

        $product = Product::where('ean', $barcode)
            ->orWhere('sku', $barcode)
            ->orWhere('upc', $barcode)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $user = auth()->user();
        $this->enhanceProduct($product, $user);

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'ean' => $product->ean,
                'brand' => $product->brand,
                'image' => $product->main_image,
                'price' => $product->variants->min('base_price'),
                'moq' => $product->moq,
                'url' => route('customer.product.show', $product->slug),
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'type' => $variant->variant_type,
                        'price' => $variant->base_price,
                        'stock' => $variant->stock_quantity,
                        'moq' => $variant->getEffectiveMoq(auth()->user()->customer_tier_id),
                    ];
                }),
            ]
        ]);
    }

    /**
     * Enhance product with pricing and MOQ
     */
    protected function enhanceProduct($product, $user)
    {
        $tierId = $user->customer_tier_id;
        $variants = [];

        foreach ($product->variants as $variant) {
            $priceResult = $this->pricingEngine->calculatePrice(
                $variant->id,
                $variant->getEffectiveMoq($tierId),
                $user->id
            );

            $variants[] = [
                'id' => $variant->id,
                'type' => $variant->variant_type,
                'name' => $variant->variant_name,
                'quantity_per_unit' => $variant->quantity_per_unit,
                'price' => $priceResult->finalPrice,
                'original_price' => $priceResult->originalPrice,
                'discount' => $priceResult->totalDiscount,
                'discount_type' => $priceResult->discountType,
                'stock' => $variant->stock_quantity,
                'available' => $variant->available_quantity,
                'in_stock' => $variant->in_stock,
                'moq' => $variant->getEffectiveMoq($tierId),
                'moq_increment' => $variant->getEffectiveIncrement($tierId),
                'weight' => $variant->weight_kg,
                'sku' => $variant->sku,
            ];
        }

        $product->enhanced_variants = $variants;
        $product->lowest_price = collect($variants)->min('price');
        $product->moq = $product->getEffectiveMoq($tierId);
        $product->moq_enforced = $product->moq_enforced;

        return $product;
    }

    /**
     * Get category IDs including children
     */
    protected function getCategoryIds($category)
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getCategoryIds($child));
        }
        return $ids;
    }
}