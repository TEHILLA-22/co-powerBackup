{{-- resources/views/admin/quotes/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Quote ' . $quote->quote_number . ' - Copower Wholesale Admin')
@section('page_title', 'Quote ' . $quote->quote_number)

@section('content')
@if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 text-sm font-medium rounded-lg">{{ session('success') }}</div>
@endif
@if(session('warning'))
    <div class="mb-6 px-4 py-3 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg">{{ session('warning') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg">
        @foreach($errors->all() as $error) {{ $error }} @endforeach
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Quote Items -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-copower-dark">Quote Items</h3>
                <p class="text-sm text-gray-500">Status: {{ $quote->status_label }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Variant</th>
                        <th class="px-6 py-3">Qty</th>
                        <th class="px-6 py-3">Unit Price</th>
                        <th class="px-6 py-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($quote->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-copower-dark">{{ $item['product_name'] ?? 'Product' }}</p>
                                <p class="text-xs text-gray-500 font-mono">SKU: {{ $item['sku'] ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item['variant_type'] ?? 'unit' }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-copower-dark">{{ $item['quantity'] ?? 0 }}</td>
                            <td class="px-6 py-4 text-sm text-copower-dark">£{{ number_format((float) ($item['unit_price'] ?? 0), 2) }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-copower-dark">£{{ number_format((float) ($item['total'] ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium">Subtotal</td>
                        <td class="px-6 py-4 text-sm font-bold">£{{ number_format($quote->subtotal, 2) }}</td>
                    </tr>
                    @if((float) $quote->discount_total > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-green-700">Discount</td>
                        <td class="px-6 py-4 text-sm font-bold text-green-700">-£{{ number_format($quote->discount_total, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium">Shipping</td>
                        <td class="px-6 py-4 text-sm font-bold">£{{ number_format($quote->shipping_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium">Tax</td>
                        <td class="px-6 py-4 text-sm font-bold">£{{ number_format($quote->tax_total, 2) }}</td>
                    </tr>
                    <tr class="border-t border-gray-200">
                        <td colspan="4" class="px-6 py-4 text-right font-bold text-copower-dark">Grand Total</td>
                        <td class="px-6 py-4 font-bold text-copower-dark">£{{ number_format($quote->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Review Panel -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h4 class="font-bold text-copower-dark mb-3">Customer</h4>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Company</dt><dd class="font-medium">{{ $quote->customer_company }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Contact</dt><dd class="font-medium">{{ $quote->customer_email }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Tier</dt><dd class="font-medium">{{ $quote->customer_tier }}</dd></div>
                @if($quote->user)
                    <div class="flex justify-between"><dt class="text-gray-500">Verified</dt><dd class="font-medium">{{ $quote->user->is_admin_verified ? 'Yes' : 'No' }}</dd></div>
                @endif
            </dl>
            @if($quote->customer_notes)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                    <p class="font-medium mb-1">Customer Notes</p>{{ $quote->customer_notes }}
                </div>
            @endif
            @if($quote->valid_until)
                <p class="mt-3 text-xs text-gray-500">Valid until {{ $quote->valid_until->format('d M Y') }}</p>
            @endif
        </div>

        @if($quote->status == 'submitted')
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h4 class="font-bold text-copower-dark mb-4">Review Actions</h4>

                <form method="POST" action="{{ route('admin.quotes.approve', $quote) }}" class="mb-4">
                    @csrf
                    <label class="block text-xs font-medium text-gray-500 mb-1" for="notes">Review notes (optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-3"></textarea>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>Approve Quote
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.quotes.reject', $quote) }}">
                    @csrf
                    <label class="block text-xs font-medium text-gray-500 mb-1" for="rejection_reason">Rejection reason (required, min 10 chars)</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-3"></textarea>
                    <button type="submit" onclick="return confirm('Reject this quote?')" class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                        <i class="fas fa-times mr-2"></i>Reject Quote
                    </button>
                </form>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h4 class="font-bold text-copower-dark mb-3">Quote Status</h4>
                <p class="text-sm text-gray-600 mb-2">This quote has been <strong>{{ $quote->status }}</strong>.</p>
                @if($quote->rejection_reason)
                    <div class="p-3 bg-red-50 rounded-lg text-sm text-red-700 mt-3">Rejection reason: {{ $quote->rejection_reason }}</div>
                @endif
                @if($quote->review_notes)
                    <div class="p-3 bg-gray-50 rounded-lg text-sm text-gray-600 mt-3">Review notes: {{ $quote->review_notes }}</div>
                @endif
            </div>
        @endif

        <a href="{{ route('admin.quotes.index') }}" class="block text-center border border-copower-dark text-copower-dark px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Quotes
        </a>
    </div>
</div>
@endsection