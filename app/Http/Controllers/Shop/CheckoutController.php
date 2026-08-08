<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\AuditLog;
use App\Mail\OrderConfirmationMail;
use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderProcessingMail;
use App\Services\Pricing\Contracts\PricingEngineInterface;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected PricingEngineInterface $pricingEngine;

    public function __construct(PricingEngineInterface $pricingEngine)
    {
        $this->middleware(['auth', 'b2b.access']);
        $this->pricingEngine = $pricingEngine;
    }

    /**
     * Show checkout page
     */
    public function index()
    {
        $quoteItems = session('quote_items', []);
        
        if (empty($quoteItems)) {
            return redirect()->route('customer.products')
                ->with('info', 'Your quote is empty. Start adding products.');
        }

        $user = auth()->user();
        $items = [];
        $subtotal = 0;
        $errors = [];

        foreach ($quoteItems as $key => $item) {
            $variant = ProductVariant::with('product')->find($item['variant_id']);
            
            if (!$variant) {
                $errors[] = "Product not found.";
                continue;
            }

            $quantity = (int) $item['quantity'];

            // Validate MOQ
            $moqValidation = $variant->validateMoq($quantity, $user->customer_tier_id);
            if (!$moqValidation['valid']) {
                $errors[] = $moqValidation['message'];
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
            ];

            $subtotal += $priceResult->finalPrice * $quantity;
        }

        // Check for errors
        if (!empty($errors)) {
            return redirect()
                ->route('quote.index')
                ->withErrors(['quote' => implode(' ', $errors)]);
        }

        // Get minimum order value from settings (default 2000)
        $minimumOrderValue = SettingsService::get('minimum_order_value', 2000);
        $meetsMinimum = $subtotal >= $minimumOrderValue;

        // Get customer addresses
        $addresses = $user->addresses()->where('is_active', true)->get();
        $defaultAddress = $addresses->firstWhere('is_default', true);

        // Get user email
        $userEmail = $user->email;

        return view('shop.checkout', compact(
            'items',
            'subtotal',
            'addresses',
            'defaultAddress',
            'minimumOrderValue',
            'meetsMinimum',
            'userEmail'
        ));
    }

    /**
     * Submit order
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'shipping_address_id' => ['required', 'exists:addresses,id'],
            'billing_address_id' => ['nullable', 'exists:addresses,id'],
            'shipping_method' => ['required', 'string'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,credit_account'],
            'customer_notes' => ['nullable', 'string', 'max:500'],
            'terms' => ['required', 'accepted'],
        ]);

        $quoteItems = session('quote_items', []);
        
        if (empty($quoteItems)) {
            return redirect()
                ->route('customer.products')
                ->withErrors(['error' => 'Your quote is empty.']);
        }

        $user = auth()->user();

        // Check minimum order value
        $subtotal = 0;
        foreach ($quoteItems as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if ($variant) {
                $priceResult = $this->pricingEngine->calculatePrice(
                    $variant->id,
                    $item['quantity'],
                    $user->id
                );
                $subtotal += $priceResult->finalPrice * $item['quantity'];
            }
        }

        $minimumOrderValue = SettingsService::get('minimum_order_value', 2000);
        if ($subtotal < $minimumOrderValue) {
            return back()
                ->withInput()
                ->withErrors(['minimum_order' => "Minimum order value is £" . number_format($minimumOrderValue, 2) . ". Your current total is £" . number_format($subtotal, 2) . "."]);
        }

        try {
            DB::beginTransaction();

            $items = [];

            foreach ($quoteItems as $key => $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                
                if (!$variant) {
                    continue;
                }

                $quantity = (int) $item['quantity'];

                // Validate MOQ one more time
                $moqValidation = $variant->validateMoq($quantity, $user->customer_tier_id);
                if (!$moqValidation['valid']) {
                    throw new \Exception($moqValidation['message']);
                }

                $priceResult = $this->pricingEngine->calculatePrice(
                    $variant->id,
                    $quantity,
                    $user->id
                );

                $items[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $priceResult->finalPrice,
                    'total' => $priceResult->finalPrice * $quantity,
                    'discount' => $priceResult->totalDiscount,
                    'applied_rules' => $priceResult->appliedRules,
                ];
            }

            // Get addresses
            $shippingAddress = $user->addresses()->find($validated['shipping_address_id']);
            if (!$shippingAddress) {
                throw new \Exception('Shipping address not found.');
            }

            $billingAddress = null;
            if ($validated['billing_address_id']) {
                $billingAddress = $user->addresses()->find($validated['billing_address_id']);
            }

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'customer_company' => $user->company_name,
                'customer_email' => $validated['email'],
                'customer_tier' => $user->customerTier?->name ?? 'Standard',
                'status' => 'submitted',
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_cost' => 0,
                'tax_total' => 0,
                'grand_total' => $subtotal,
                'shipping_address' => $shippingAddress->full_address,
                'billing_address' => $billingAddress?->full_address ?? $shippingAddress->full_address,
                'shipping_method' => $validated['shipping_method'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'submitted_at' => now(),
                'customer_notes' => $validated['customer_notes'] ?? null,
            ]);

            // Create order items
            foreach ($items as $item) {
                $variant = $item['variant'];
                $product = $variant->product;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_ean' => $product->ean,
                    'variant_type' => $variant->variant_type,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount_price' => $item['discount'] ?? 0,
                    'line_total' => $item['total'],
                    'tax_amount' => 0,
                    'applied_discounts' => $item['applied_rules'] ?? [],
                    'status' => 'pending',
                ]);
            }

            // Clear quote session
            session()->forget('quote_items');
            session()->forget('quote_count');

            // Log
            AuditLog::log(
                'create',
                'order',
                $order->id,
                null,
                $order->toArray(),
                "Order {$order->order_number} created by {$user->full_name}"
            );

            DB::commit();

            // Send emails (queued)
            // 1. Customer confirmation
            Mail::to($validated['email'])->queue(new OrderConfirmationMail($order, $user));

            // 2. Order processing notification
            Mail::to($validated['email'])->queue(new OrderProcessingMail($order, $user));

            // 3. Admin notification
            $adminEmails = SettingsService::get('admin_notification_emails', 'admin@copower.com');
            $adminEmails = array_map('trim', explode(',', $adminEmails));
            Mail::to($adminEmails)->queue(new NewOrderNotificationMail($order, $user));

            return redirect()
                ->route('order.confirmation', $order)
                ->with('success', 'Your order has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Order submission failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['submit' => $e->getMessage()]);
        }
    }

    /**
     * Order confirmation page
     */
    public function confirmation(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items', 'items.product']);

        return view('shop.order-confirmation', compact('order'));
    }

    /**
     * Generate order number
     */
    protected function generateOrderNumber(): string
    {
        $prefix = 'CP';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}