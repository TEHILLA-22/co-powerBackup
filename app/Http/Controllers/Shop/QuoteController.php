<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quote;
use App\Models\AuditLog;
use App\Mail\QuoteConfirmationMail;
use App\Mail\NewQuoteNotificationMail;
use App\Services\Pricing\Contracts\PricingEngineInterface;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller implements HasMiddleware
{
    protected PricingEngineInterface $pricingEngine;

    public static function middleware(): array
    {
        return ['auth', 'b2b.access'];
    }

    public function __construct(PricingEngineInterface $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
    }

    /**
     * Show quote summary
     */
    public function index()
    {
        $sessionQuote = session('quote_items', []);
        
        if (empty($sessionQuote)) {
            return redirect()->route('customer.products')
                ->with('info', 'Your quote is empty. Start adding products.');
        }

        $items = [];
        $subtotal = 0;
        $user = auth()->user();
        $moqErrors = [];
        $stockErrors = [];

        foreach ($sessionQuote as $key => $item) {
            $variant = ProductVariant::with('product')->find($item['variant_id']);
            
            if (!$variant) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            
            // Validate MOQ
            $moqValidation = $variant->validateMoq($quantity, $user->customer_tier_id);
            if (!$moqValidation['valid']) {
                $moqErrors[] = $moqValidation['message'];
            }

            // Check stock
            if ($variant->stock_quantity < $quantity && !$variant->allow_backorder) {
                $stockErrors[] = "{$variant->product->name}: Only {$variant->stock_quantity} units available.";
            }

            $priceResult = $this->pricingEngine->calculatePrice(
                $variant->id,
                $quantity,
                $user->id
            );

            $items[] = [
                'key' => $key,
                'product_id' => $variant->product_id,
                'product_name' => $variant->product->name,
                'sku' => $variant->sku,
                'ean' => $variant->product->ean,
                'variant_type' => $variant->variant_type,
                'quantity' => $quantity,
                'unit_price' => $priceResult->finalPrice,
                'total' => $priceResult->finalPrice * $quantity,
                'moq' => $variant->getEffectiveMoq($user->customer_tier_id),
                'stock' => $variant->stock_quantity,
                'image' => $variant->product->main_image,
                'in_stock' => $variant->stock_quantity >= $quantity,
                'allow_backorder' => $variant->allow_backorder,
            ];

            $subtotal += $priceResult->finalPrice * $quantity;
        }

        // Get minimum order value from settings
        $minimumOrderValue = SettingsService::get('minimum_order_value', 2000);
        $meetsMinimum = $subtotal >= $minimumOrderValue;

        // Update session quote count
        session(['quote_count' => count($items)]);

        return view('shop.quote-summary', compact(
            'items',
            'subtotal',
            'minimumOrderValue',
            'meetsMinimum',
            'moqErrors',
            'stockErrors'
        ));
    }

    /**
     * Add a single product (from its detail page) to the session quote.
     * Adding the same product again increases its quantity.
     */
    public function add(Request $request, string $slug)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'variant_type' => ['nullable', 'string', 'in:unit,case,layer,pallet'],
        ]);

        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $variant = $product->variants()
            ->where('is_active', true)
            ->when($validated['variant_type'] ?? null, function ($q, $type) {
                return $q->where('variant_type', $type);
            })
            ->orderByRaw("FIELD(variant_type, 'unit', 'case', 'layer', 'pallet')")
            ->first();

        if (! $variant) {
            return back()->withErrors(['quantity' => 'No purchasable variant is available for this product.']);
        }

        $quantity = (int) $validated['quantity'];
        $user = auth()->user();

        if ($variant->stock_quantity < $quantity && ! $variant->allow_backorder) {
            return back()->withErrors(['quantity' => "Only {$variant->stock_quantity} units available for this product."]);
        }

        $quoteItems = session('quote_items', []);
        $key = (string) $variant->id;

        if (isset($quoteItems[$key])) {
            $quoteItems[$key]['quantity'] += $quantity;
            $message = "{$product->name} quantity increased in your quote.";
        } else {
            $quoteItems[$key] = [
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'variant_type' => $variant->variant_type,
            ];
            $message = "{$product->name} added to your quote.";
        }

        session(['quote_items' => $quoteItems]);
        session(['quote_count' => count($quoteItems)]);

        return back()->with('success', $message);
    }

    // ... other methods (updateItem, removeItem, clear, bulkIndex, bulkValidate, bulkStore, parsePaste, submit, confirmation)


    /**
     * Update quote item quantity
     */
    public function updateItem(Request $request, $key)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quoteItems = session('quote_items', []);
        
        if (!isset($quoteItems[$key])) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $variant = ProductVariant::find($quoteItems[$key]['variant_id']);
        $user = auth()->user();

        // Validate MOQ
        $moqValidation = $variant->validateMoq($validated['quantity'], $user->customer_tier_id);
        if (!$moqValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $moqValidation['message'],
            ], 422);
        }

        $quoteItems[$key]['quantity'] = $validated['quantity'];
        session(['quote_items' => $quoteItems]);

        // Recalculate price
        $priceResult = $this->pricingEngine->calculatePrice(
            $variant->id,
            $validated['quantity'],
            $user->id
        );

        return response()->json([
            'success' => true,
            'quantity' => $validated['quantity'],
            'total' => $priceResult->finalPrice * $validated['quantity'],
            'unit_price' => $priceResult->finalPrice,
        ]);
    }

    /**
     * Remove item from quote
     */
    public function removeItem($key)
    {
        $quoteItems = session('quote_items', []);
        
        if (isset($quoteItems[$key])) {
            unset($quoteItems[$key]);
            session(['quote_items' => $quoteItems]);
        }

        return redirect()
            ->route('quote.index')
            ->with('success', 'Item removed from quote.');
    }

    /**
     * Clear quote
     */
    public function clear()
    {
        session()->forget('quote_items');
        return redirect()
            ->route('quote.index')
            ->with('info', 'Quote cleared.');
    }


    

    /**
     * Show bulk order builder
     */
    public function bulkIndex()
    {
        // Check if bulk order is enabled via settings
        if (!SettingsService::get('enable_bulk_order', true)) {
            abort(404, 'Bulk order is currently disabled.');
        }

        $sessionQuote = session('quote_items', []);
        $items = [];
        $subtotal = 0;
        $user = auth()->user();

        // Load existing quote items (if any) to show in a summary
        foreach ($sessionQuote as $key => $item) {
            $variant = ProductVariant::with('product')->find($item['variant_id']);
            if ($variant) {
                $priceResult = $this->pricingEngine->calculatePrice(
                    $variant->id,
                    $item['quantity'],
                    $user->id
                );
                $items[] = [
                    'key' => $key,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'variant_type' => $variant->variant_type,
                    'quantity' => $item['quantity'],
                    'price' => $priceResult->finalPrice,
                    'total' => $priceResult->finalPrice * $item['quantity'],
                    'moq' => $variant->getEffectiveMoq($user->customer_tier_id),
                    'stock' => $variant->stock_quantity,
                ];
                $subtotal += $priceResult->finalPrice * $item['quantity'];
            }
        }

        // Recent products for quick add
        $recentProducts = Product::with(['variants'])
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->limit(12)
            ->get();

        return view('shop.bulk-order', compact('items', 'subtotal', 'recentProducts'));
    }

    /**
     * Validate a single bulk order item (AJAX)
     */
    public function bulkValidate(Request $request)
    {
        $request->validate([
            'sku' => ['nullable', 'string', 'max:50'],
            'ean' => ['nullable', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:1'],
            'variant_type' => ['nullable', 'string', 'in:unit,case,layer,pallet'],
        ]);

        $identifier = $request->sku ?? $request->ean;
        if (!$identifier) {
            return response()->json(['valid' => false, 'error' => 'SKU or EAN is required.'], 422);
        }

        // Find product
        $product = Product::where('sku', $identifier)
            ->orWhere('ean', $identifier)
            ->orWhere('upc', $identifier)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json(['valid' => false, 'error' => 'Product not found.'], 422);
        }

        // Find variant
        $variant = null;
        if ($request->variant_type) {
            $variant = $product->variants()
                ->where('variant_type', $request->variant_type)
                ->where('is_active', true)
                ->first();
        } else {
            $variant = $product->variants()->where('is_active', true)->first();
        }

        if (!$variant) {
            return response()->json(['valid' => false, 'error' => 'No active variant available.'], 422);
        }

        $quantity = (int) $request->quantity;
        $user = auth()->user();

        // Validate MOQ
        $moqValidation = $variant->validateMoq($quantity, $user->customer_tier_id);
        if (!$moqValidation['valid']) {
            return response()->json([
                'valid' => false,
                'error' => $moqValidation['message'],
                'required' => $moqValidation['required'] ?? null,
                'current' => $moqValidation['current'] ?? null,
            ], 422);
        }

        // Check stock
        if ($variant->stock_quantity < $quantity && !$variant->allow_backorder) {
            return response()->json([
                'valid' => false,
                'error' => "Only {$variant->stock_quantity} units available.",
                'available' => $variant->stock_quantity,
            ], 422);
        }

        // Calculate price for this quantity
        $priceResult = $this->pricingEngine->calculatePrice(
            $variant->id,
            $quantity,
            $user->id
        );

        return response()->json([
            'valid' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $variant->sku,
                'ean' => $product->ean,
                'brand' => $product->brand,
                'variant_type' => $variant->variant_type,
                'variant_name' => $variant->variant_name,
            ],
            'pricing' => [
                'unit_price' => $priceResult->finalPrice,
                'total' => $priceResult->finalPrice * $quantity,
                'original_price' => $priceResult->originalPrice,
                'discount' => $priceResult->totalDiscount,
                'discount_type' => $priceResult->discountType,
            ],
            'moq' => $variant->getEffectiveMoq($user->customer_tier_id),
            'stock' => $variant->stock_quantity,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Process bulk order (add multiple items to quote)
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.sku' => ['required_without:items.*.ean', 'string', 'max:50'],
            'items.*.ean' => ['required_without:items.*.sku', 'string', 'max:20'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.variant_type' => ['nullable', 'string', 'in:unit,case,layer,pallet'],
        ]);

        $items = $request->items;
        $errors = [];
        $successItems = [];
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $quoteItems = session('quote_items', []);

            foreach ($items as $index => $item) {
                $identifier = $item['sku'] ?? $item['ean'];
                $quantity = (int) $item['quantity'];
                $variantType = $item['variant_type'] ?? null;

                // Find product
                $product = Product::where('sku', $identifier)
                    ->orWhere('ean', $identifier)
                    ->orWhere('upc', $identifier)
                    ->where('is_active', true)
                    ->first();

                if (!$product) {
                    $errors[] = [
                        'index' => $index,
                        'identifier' => $identifier,
                        'error' => 'Product not found.',
                    ];
                    continue;
                }

                // Find variant
                $variant = null;
                if ($variantType) {
                    $variant = $product->variants()
                        ->where('variant_type', $variantType)
                        ->where('is_active', true)
                        ->first();
                } else {
                    $variant = $product->variants()->where('is_active', true)->first();
                }

                if (!$variant) {
                    $errors[] = [
                        'index' => $index,
                        'identifier' => $identifier,
                        'error' => 'No active variant available.',
                    ];
                    continue;
                }

                // Validate MOQ
                $moqValidation = $variant->validateMoq($quantity, $user->customer_tier_id);
                if (!$moqValidation['valid']) {
                    $errors[] = [
                        'index' => $index,
                        'identifier' => $identifier,
                        'error' => $moqValidation['message'],
                    ];
                    continue;
                }

                // Check stock
                if ($variant->stock_quantity < $quantity && !$variant->allow_backorder) {
                    $errors[] = [
                        'index' => $index,
                        'identifier' => $identifier,
                        'error' => "Only {$variant->stock_quantity} units available.",
                    ];
                    continue;
                }

                // Add to quote
                $key = $variant->id . '_' . uniqid();
                $quoteItems[$key] = [
                    'variant_id' => $variant->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'variant_type' => $variant->variant_type,
                ];

                $successItems[] = [
                    'index' => $index,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                ];
            }

            session(['quote_items' => $quoteItems]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($successItems) . ' item(s) added to quote.',
                'data' => [
                    'success_items' => $successItems,
                    'errors' => $errors,
                    'quote_count' => count($quoteItems),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk order failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'items' => $items,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse pasted text from spreadsheet (AJAX)
     */
    public function parsePaste(Request $request)
    {
        $request->validate([
            'text' => ['required', 'string'],
        ]);

        $lines = explode("\n", $request->text);
        $items = [];
        $errors = [];

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Try to parse: SKU/EAN, Quantity, Variant (optional)
            $parts = preg_split('/[\t,;|]+/', $line);
            $parts = array_map('trim', $parts);

            if (count($parts) < 2) {
                $errors[] = "Line " . ($lineNum + 1) . ": Invalid format. Need at least SKU/EAN and Quantity.";
                continue;
            }

            $identifier = $parts[0];
            $quantity = (int) $parts[1];
            $variantType = $parts[2] ?? null;

            if ($quantity < 1) {
                $errors[] = "Line " . ($lineNum + 1) . ": Quantity must be at least 1.";
                continue;
            }

            // Determine if identifier is SKU or EAN
            if (preg_match('/^[0-9]{8,14}$/', $identifier)) {
                $items[] = [
                    'ean' => $identifier,
                    'quantity' => $quantity,
                    'variant_type' => $variantType && in_array($variantType, ['unit','case','layer','pallet']) ? $variantType : null,
                ];
            } else {
                $items[] = [
                    'sku' => $identifier,
                    'quantity' => $quantity,
                    'variant_type' => $variantType && in_array($variantType, ['unit','case','layer','pallet']) ? $variantType : null,
                ];
            }
        }

        return response()->json([
            'items' => $items,
            'errors' => $errors,
            'count' => count($items),
        ]);
    }

    /**
     * Submit quote for processing
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'customer_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $quoteItems = session('quote_items', []);
        
        if (empty($quoteItems)) {
            return redirect()
                ->route('quote.index')
                ->withErrors(['quote' => 'Your quote is empty.']);
        }

        $user = auth()->user();

        try {
            DB::beginTransaction();

            // Build quote items with pricing
            $items = [];
            $subtotal = 0;
            $totalDiscount = 0;
            $moqErrors = [];

            foreach ($quoteItems as $key => $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                if (!$variant) continue;

                // Re-validate MOQ
                $moqValidation = $variant->validateMoq($item['quantity'], $user->customer_tier_id);
                if (!$moqValidation['valid']) {
                    $moqErrors[] = "{$variant->product->name}: {$moqValidation['message']}";
                    continue;
                }

                $priceResult = $this->pricingEngine->calculatePrice(
                    $variant->id,
                    $item['quantity'],
                    $user->id
                );

                $items[] = [
                    'product_id' => $variant->product_id,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'ean' => $variant->product->ean,
                    'variant_type' => $variant->variant_type,
                    'quantity' => $item['quantity'],
                    'unit_price' => $priceResult->finalPrice,
                    'total' => $priceResult->finalPrice * $item['quantity'],
                    'discount' => $priceResult->totalDiscount,
                    'moq' => $variant->getEffectiveMoq($user->customer_tier_id),
                ];

                $subtotal += $priceResult->finalPrice * $item['quantity'];
                $totalDiscount += $priceResult->totalDiscount;
            }

            if (!empty($moqErrors)) {
                return redirect()
                    ->route('quote.index')
                    ->withErrors(['moq' => implode(' ', $moqErrors)]);
            }

            // Create quote
            $quote = Quote::create([
                'quote_number' => $this->generateQuoteNumber(),
                'user_id' => $user->id,
                'customer_company' => $user->company_name,
                'customer_email' => $user->email,
                'customer_tier' => $user->customerTier?->name ?? 'Standard',
                'status' => 'submitted',
                'items' => $items,
                'subtotal' => $subtotal,
                'discount_total' => $totalDiscount,
                'shipping_cost' => 0,
                'tax_total' => 0,
                'grand_total' => $subtotal - $totalDiscount,
                'submitted_at' => now(),
                'valid_until' => now()->addDays(7),
                'customer_notes' => $validated['customer_notes'] ?? null,
            ]);

            // Clear session
            session()->forget('quote_items');

            // Log
            AuditLog::log(
                'create',
                'quote',
                $quote->id,
                null,
                $quote->toArray(),
                "Quote {$quote->quote_number} submitted by {$user->full_name}"
            );

            DB::commit();

            // Send emails
            Mail::to($user->email)->queue(new QuoteConfirmationMail($quote));
            
            $adminEmails = SettingsService::get('admin_notification_emails', 'admin@copower.com');
            Mail::to(explode(',', $adminEmails))->queue(new NewQuoteNotificationMail($quote));

            return redirect()
                ->route('quote.confirmation', $quote)
                ->with('success', 'Your quote has been submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Quote submission failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('quote.index')
                ->withErrors(['submit' => 'Failed to submit quote. Please try again.']);
        }
    }

    /**
     * Show quote confirmation
     */
    public function confirmation(Quote $quote)
    {
        if ($quote->user_id !== auth()->id()) {
            abort(403);
        }

        return view('shop.quote-confirmation', compact('quote'));
    }

    /**
     * Generate quote number
     */
    protected function generateQuoteNumber(): string
    {
        $prefix = 'QT';
        $year = date('Y');
        $month = date('m');
        
        $lastQuote = Quote::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $sequence = $lastQuote ? intval(substr($lastQuote->quote_number, -4)) + 1 : 1;

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}