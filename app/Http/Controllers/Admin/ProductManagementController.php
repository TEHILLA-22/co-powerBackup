<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CustomerTier;
use App\Models\ProductVariant;
use App\Models\AuditLog;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use App\Exports\ProductsExportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('rate.limit:100,1')->only(['store', 'update', 'destroy', 'import']);
    }

    /**
     * Display product list with filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'creator']);

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

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->whereHas('variants', function($q) {
                    $q->where('stock_quantity', '>', 0);
                });
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->whereDoesntHave('variants', function($q) {
                    $q->where('stock_quantity', '>', 0);
                });
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereHas('variants', function($q) {
                    $q->whereColumn('stock_quantity', '<=', 'reorder_level');
                });
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['name', 'sku', 'brand', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query->paginate(20)->withQueryString();

        // Get categories for filter
        $categories = Category::where('is_active', true)->parents()->get();

        // Stats
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'out_of_stock' => Product::whereDoesntHave('variants', function($q) {
                $q->where('stock_quantity', '>', 0);
            })->count(),
            'low_stock' => Product::whereHas('variants', function($q) {
                $q->whereColumn('stock_quantity', '<=', 'reorder_level');
            })->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->parents()->with('children')->get();
        $tiers = CustomerTier::where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'tiers'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Basic info
            'ean' => ['required', 'string', 'max:20', 'unique:products'],
            'sku' => ['required', 'string', 'max:50', 'unique:products'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:100'],
            'manufacturer' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            
            // MOQ
            'moq' => ['required', 'integer', 'min:1'],
            'moq_enforced' => ['boolean'],
            'moq_increment' => ['required', 'integer', 'min:1'],
            'tier_moq' => ['nullable', 'array'],
            'tier_moq.*' => ['integer', 'min:1'],
            
            // Media
            'main_image' => ['nullable', 'image', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            
            // Status
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_on_sale' => ['boolean'],
            'sale_start_date' => ['nullable', 'date'],
            'sale_end_date' => ['nullable', 'date', 'after:sale_start_date'],
            
            // Variants
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.variant_type' => ['required', 'string', 'in:unit,case,layer,pallet'],
            'variants.*.variant_name' => ['nullable', 'string'],
            'variants.*.quantity_per_unit' => ['required', 'integer', 'min:1'],
            'variants.*.base_price' => ['required', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],
            'variants.*.reorder_level' => ['nullable', 'integer', 'min:0'],
            'variants.*.weight_kg' => ['nullable', 'numeric', 'min:0'],
            'variants.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'variants.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'variants.*.height_cm' => ['nullable', 'numeric', 'min:0'],
            'variants.*.moq' => ['nullable', 'integer', 'min:1'],
            'variants.*.moq_increment' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            // Create product
            $product = Product::create([
                'ean' => $data['ean'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . Str::random(5),
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'brand' => $data['brand'] ?? null,
                'manufacturer' => $data['manufacturer'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                
                // MOQ
                'moq' => $data['moq'],
                'moq_enforced' => $data['moq_enforced'] ?? true,
                'moq_increment' => $data['moq_increment'] ?? 1,
                'tier_moq' => $data['tier_moq'] ?? null,
                
                // Status
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'is_on_sale' => $data['is_on_sale'] ?? false,
                'sale_start_date' => $data['sale_start_date'] ?? null,
                'sale_end_date' => $data['sale_end_date'] ?? null,
                'created_by' => auth()->guard('admin')->id(),
            ]);

            // Handle images
            $this->handleImages($product, $request);

            // Create variants
            foreach ($data['variants'] as $variantData) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_type' => $variantData['variant_type'],
                    'variant_name' => $variantData['variant_name'] ?? null,
                    'quantity_per_unit' => $variantData['quantity_per_unit'],
                    'base_price' => $variantData['base_price'],
                    'cost_price' => $variantData['cost_price'] ?? null,
                    'sale_price' => $variantData['sale_price'] ?? null,
                    'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                    'reorder_level' => $variantData['reorder_level'] ?? 0,
                    'weight_kg' => $variantData['weight_kg'] ?? null,
                    'length_cm' => $variantData['length_cm'] ?? null,
                    'width_cm' => $variantData['width_cm'] ?? null,
                    'height_cm' => $variantData['height_cm'] ?? null,
                    'moq' => $variantData['moq'] ?? null,
                    'moq_increment' => $variantData['moq_increment'] ?? null,
                    'in_stock' => ($variantData['stock_quantity'] ?? 0) > 0,
                    'is_active' => true,
                ]);
            }

            // Log
            AuditLog::log(
                'create',
                'product',
                $product->id,
                null,
                $product->toArray(),
                "Product {$product->name} created by " . auth()->guard('admin')->user()?->full_name ?? 'Admin'
            );

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product {$product->name} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show edit product form
     */
    public function edit(Product $product)
    {
        $product->load(['variants', 'category']);
        $categories = Category::where('is_active', true)->parents()->with('children')->get();
        $tiers = CustomerTier::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'tiers'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'ean' => ['required', 'string', 'max:20', 'unique:products,ean,' . $product->id],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:100'],
            'manufacturer' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            
            'moq' => ['required', 'integer', 'min:1'],
            'moq_enforced' => ['boolean'],
            'moq_increment' => ['required', 'integer', 'min:1'],
            'tier_moq' => ['nullable', 'array'],
            'tier_moq.*' => ['integer', 'min:1'],
            
            'main_image' => ['nullable', 'image', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_on_sale' => ['boolean'],
            'sale_start_date' => ['nullable', 'date'],
            'sale_end_date' => ['nullable', 'date', 'after:sale_start_date'],
            
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.variant_type' => ['required', 'string', 'in:unit,case,layer,pallet'],
            'variants.*.variant_name' => ['nullable', 'string'],
            'variants.*.quantity_per_unit' => ['required', 'integer', 'min:1'],
            'variants.*.base_price' => ['required', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],
            'variants.*.reorder_level' => ['nullable', 'integer', 'min:0'],
            'variants.*.weight_kg' => ['nullable', 'numeric', 'min:0'],
            'variants.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'variants.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'variants.*.height_cm' => ['nullable', 'numeric', 'min:0'],
            'variants.*.moq' => ['nullable', 'integer', 'min:1'],
            'variants.*.moq_increment' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $oldData = $product->toArray();

            // Update product
            $product->update([
                'ean' => $data['ean'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'brand' => $data['brand'] ?? null,
                'manufacturer' => $data['manufacturer'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                
                'moq' => $data['moq'],
                'moq_enforced' => $data['moq_enforced'] ?? true,
                'moq_increment' => $data['moq_increment'] ?? 1,
                'tier_moq' => $data['tier_moq'] ?? null,
                
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'is_on_sale' => $data['is_on_sale'] ?? false,
                'sale_start_date' => $data['sale_start_date'] ?? null,
                'sale_end_date' => $data['sale_end_date'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            // Handle images
            $this->handleImages($product, $request);

            // Update variants
            $this->syncVariants($product, $data['variants']);

            // Log
            AuditLog::log(
                'update',
                'product',
                $product->id,
                $oldData,
                $product->toArray(),
                "Product {$product->name} updated by " . auth()->guard('admin')->user()?->full_name ?? 'Admin'
            );

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product {$product->name} updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete product (soft delete)
     */
    public function destroy(Product $product)
    {
        try {
            // Log before delete
            AuditLog::log(
                'delete',
                'product',
                $product->id,
                $product->toArray(),
                null,
                "Product {$product->name} deleted by " . auth()->guard('admin')->user()?->full_name ?? 'Admin'
            );

            // Delete images
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product {$product->name} deleted successfully.");

        } catch (\Exception $e) {
            \Log::error('Product deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        AuditLog::log(
            'toggle-status',
            'product',
            $product->id,
            ['is_active' => !$product->is_active],
            ['is_active' => $product->is_active],
            "Product {$product->name} status toggled to " . ($product->is_active ? 'active' : 'inactive') . " by " . auth()->guard('admin')->user()?->full_name ?? 'Admin'
        );

        return back()->with('success', "Product status updated.");
    }

    /**
     * Export products to Excel/CSV
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        return ProductsExport::download($request->all(), $format);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.products.import');
    }

    /**
     * Import products from Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,tsv', 'max:5120'],
            'skip_header' => ['nullable', 'boolean'],
        ]);

        try {
            $import = new ProductsImport();
            $import->skipHeader($request->has('skip_header'));
            $results = $import->import($request->file('file')->getRealPath());

            AuditLog::log(
                'import',
                'product',
                null,
                null,
                ['imported' => $results['imported'], 'failed' => $results['failed']],
                "Products imported by " . (auth()->guard('admin')->user()?->full_name ?? 'Admin') . ". {$results['imported']} imported, {$results['failed']} failed."
            );

            $message = "Import completed. {$results['imported']} products imported.";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} rows failed. Check the error log.";
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Product import failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Download template for import
     */
    public function template()
    {
        return ProductsExportTemplate::download();
    }

    // ==================== Helper Methods ====================

    protected function handleImages($product, $request)
    {
        // Main image
        if ($request->hasFile('main_image')) {
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $path = $request->file('main_image')->store('products', 'public');
            $product->main_image = $path;
            $product->save();
        }

        // Gallery images
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $product->gallery_images = $galleryPaths;
            $product->save();
        }
    }

    protected function syncVariants($product, $variantData)
    {
        $existingIds = $product->variants->pluck('id')->toArray();
        $updatedIds = [];

        foreach ($variantData as $data) {
            if (isset($data['id']) && in_array($data['id'], $existingIds)) {
                // Update existing variant
                $variant = ProductVariant::find($data['id']);
                if ($variant) {
                    $variant->update([
                        'variant_type' => $data['variant_type'],
                        'variant_name' => $data['variant_name'] ?? null,
                        'quantity_per_unit' => $data['quantity_per_unit'],
                        'base_price' => $data['base_price'],
                        'cost_price' => $data['cost_price'] ?? null,
                        'sale_price' => $data['sale_price'] ?? null,
                        'stock_quantity' => $data['stock_quantity'] ?? 0,
                        'reorder_level' => $data['reorder_level'] ?? 0,
                        'weight_kg' => $data['weight_kg'] ?? null,
                        'length_cm' => $data['length_cm'] ?? null,
                        'width_cm' => $data['width_cm'] ?? null,
                        'height_cm' => $data['height_cm'] ?? null,
                        'moq' => $data['moq'] ?? null,
                        'moq_increment' => $data['moq_increment'] ?? null,
                        'in_stock' => ($data['stock_quantity'] ?? 0) > 0,
                    ]);
                    $updatedIds[] = $variant->id;
                }
            } else {
                // Create new variant
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_type' => $data['variant_type'],
                    'variant_name' => $data['variant_name'] ?? null,
                    'quantity_per_unit' => $data['quantity_per_unit'],
                    'base_price' => $data['base_price'],
                    'cost_price' => $data['cost_price'] ?? null,
                    'sale_price' => $data['sale_price'] ?? null,
                    'stock_quantity' => $data['stock_quantity'] ?? 0,
                    'reorder_level' => $data['reorder_level'] ?? 0,
                    'weight_kg' => $data['weight_kg'] ?? null,
                    'length_cm' => $data['length_cm'] ?? null,
                    'width_cm' => $data['width_cm'] ?? null,
                    'height_cm' => $data['height_cm'] ?? null,
                    'moq' => $data['moq'] ?? null,
                    'moq_increment' => $data['moq_increment'] ?? null,
                    'in_stock' => ($data['stock_quantity'] ?? 0) > 0,
                    'is_active' => true,
                ]);
                $updatedIds[] = $variant->id;
            }
        }

        // Delete variants that were removed
        $toDelete = array_diff($existingIds, $updatedIds);
        if ($toDelete) {
            ProductVariant::whereIn('id', $toDelete)->delete();
        }
    }
}