{{-- resources/views/shop/quote-confirmation.blade.php --}}
@extends('layouts.app')

@section('title', 'Quote Confirmation #' . $quote->quote_number . ' - Copower Wholesale')

@section('content')
<div class="bg-copower-gray py-8">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Success Message -->
        <div class="bg-white rounded-xl shadow-sm p-8 text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-copower-dark">Quote Submitted!</h1>
            <p class="text-gray-600 mt-2">Thank you for your quote request. Our sales team will be in touch with pricing and availability.</p>
            <p class="text-sm text-gray-500 mt-1">Quote #: <span class="font-mono font-medium">{{ $quote->quote_number }}</span></p>
            <p class="text-xs text-gray-400 mt-1">Valid until {{ $quote->valid_until?->format('d M Y') ?? '7 days' }}</p>
        </div>

        <!-- Quote Details -->
        @if(is_array($quote->items) && count($quote->items) > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="font-bold text-copower-dark">Quote Details</h2>
                <p class="text-sm text-gray-500">Submitted on {{ $quote->submitted_at?->format('d M Y, H:i') ?? $quote->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-copower-gray">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase">Variant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($quote->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-copower-dark text-sm">{{ $item['product_name'] ?? 'Product' }}</p>
                                    <p class="text-xs text-gray-500 font-mono">SKU: {{ $item['sku'] ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-copower-gray rounded-full text-xs font-medium text-copower-dark capitalize">
                                        {{ $item['variant_type'] ?? 'unit' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-copower-dark">{{ $item['quantity'] ?? 0 }}</td>
                                <td class="px-6 py-4 font-medium text-copower-dark">£{{ number_format((float) ($item['unit_price'] ?? 0), 2) }}</td>
                                <td class="px-6 py-4 font-bold text-copower-dark">£{{ number_format((float) ($item['total'] ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-copower-gray">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-bold text-copower-dark">Subtotal</td>
                            <td class="px-6 py-4 font-bold text-copower-dark">£{{ number_format((float) $quote->subtotal, 2) }}</td>
                        </tr>
                        @if((float) $quote->discount_total > 0)
                        <tr class="bg-green-50">
                            <td colspan="4" class="px-6 py-4 text-right font-bold text-green-700">Trade Discount</td>
                            <td class="px-6 py-4 font-bold text-green-700">-£{{ number_format((float) $quote->discount_total, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-bold text-copower-dark">Total</td>
                            <td class="px-6 py-4 font-bold text-copower-dark">£{{ number_format((float) $quote->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        <!-- Next Steps -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h3 class="font-bold text-copower-dark mb-4">What Happens Next?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-copower-dark/10 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-copower-dark font-bold">1</span>
                    </div>
                    <p class="font-medium text-copower-dark text-sm">Quote Review</p>
                    <p class="text-xs text-gray-500">Our sales team reviews your quote</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-copower-dark/10 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-copower-dark font-bold">2</span>
                    </div>
                    <p class="font-medium text-copower-dark text-sm">Pricing Confirmation</p>
                    <p class="text-xs text-gray-500">We confirm pricing and availability by email</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-copower-dark/10 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-copower-dark font-bold">3</span>
                    </div>
                    <p class="font-medium text-copower-dark text-sm">Convert to Order</p>
                    <p class="text-xs text-gray-500">Convert to a confirmed order with us</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('customer.products') }}" class="bg-copower-dark text-white px-6 py-2.5 rounded-lg hover:bg-opacity-90 transition font-medium text-sm">
                <i class="fas fa-store mr-2"></i>
                Continue Shopping
            </a>
            <a href="{{ route('quote.bulk') }}" class="border border-copower-dark text-copower-dark px-6 py-2.5 rounded-lg hover:bg-copower-gray transition font-medium text-sm">
                <i class="fas fa-layer-group mr-2"></i>
                New Bulk Order
            </a>
        </div>
    </div>
</div>
@endsection