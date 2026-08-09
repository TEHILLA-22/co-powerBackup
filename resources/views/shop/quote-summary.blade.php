{{-- resources/views/shop/quote-summary.blade.php --}}
@extends('layouts.app')

@section('title', 'Your Quote - Copower Wholesale')

@section('content')
<div class="bg-copower-gray py-8">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6">
            <ol class="list-none p-0 inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="text-copower-dark hover:text-copower-banner">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('customer.products') }}" class="text-copower-dark hover:text-copower-banner">Products</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-copower-dark font-medium">Your Quote</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-copower-dark">Your Quote</h1>
                <p class="text-gray-600 mt-1">{{ count($items) }} item(s) in your quote</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('customer.products') }}" class="border border-copower-dark text-copower-dark px-6 py-2 rounded-lg hover:bg-copower-gray transition font-medium text-sm">
                    <i class="fas fa-plus mr-2"></i> Add More Items
                </a>
                <a href="{{ route('quote.bulk') }}" class="border border-copower-dark text-copower-dark px-6 py-2 rounded-lg hover:bg-copower-gray transition font-medium text-sm">
                    <i class="fas fa-layer-group mr-2"></i> Bulk Order
                </a>
                <button onclick="clearQuote()" class="border border-red-300 text-red-600 px-6 py-2 rounded-lg hover:bg-red-50 transition font-medium text-sm">
                    <i class="fas fa-trash mr-2"></i> Clear All
                </button>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-4">
                {{ session('info') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-4">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Errors -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- MOQ Errors -->
        @if(count($moqErrors) > 0)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-4">
                <strong>MOQ Requirements:</strong>
                <ul class="list-disc list-inside mt-1">
                    @foreach($moqErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stock Errors -->
        @if(count($stockErrors) > 0)
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <strong>Stock Issues:</strong>
                <ul class="list-disc list-inside mt-1">
                    @foreach($stockErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quote Items -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-copower-gray">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase tracking-wider">Variant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-copower-dark uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($items as $item)
                            <tr class="hover:bg-copower-gray/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 rounded-lg bg-copower-gray overflow-hidden flex-shrink-0">
                                            @if($item['image'])
                                                <img src="{{ asset('storage/' . $item['image']) }}" 
                                                     alt="{{ $item['product_name'] }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-copower-dark text-sm">{{ $item['product_name'] }}</p>
                                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                <span class="font-mono">SKU: {{ $item['sku'] }}</span>
                                                <span>|</span>
                                                <span class="font-mono">EAN: {{ $item['ean'] }}</span>
                                            </div>
                                            @if($item['moq'] > 1)
                                                <span class="text-xs text-gray-500">MOQ: {{ $item['moq'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-copower-gray rounded-full text-xs font-medium text-copower-dark capitalize">
                                        {{ $item['variant_type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="updateQuantity('{{ $item['key'] }}', {{ $item['quantity'] - 1 }})" 
                                                class="w-8 h-8 rounded-lg border border-gray-300 text-gray-600 hover:bg-copower-gray transition flex items-center justify-center"
                                                @if($item['quantity'] <= 1) disabled @endif>
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-12 text-center font-medium text-copower-dark" id="qty-{{ $item['key'] }}">
                                            {{ $item['quantity'] }}
                                        </span>
                                        <button onclick="updateQuantity('{{ $item['key'] }}', {{ $item['quantity'] + 1 }})" 
                                                class="w-8 h-8 rounded-lg border border-gray-300 text-gray-600 hover:bg-copower-gray transition flex items-center justify-center">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-copower-dark">
                                    £{{ number_format($item['unit_price'], 2) }}
                                </td>
                                <td class="px-6 py-4 font-bold text-copower-dark">
                                    £{{ number_format($item['total'], 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="removeItem('{{ $item['key'] }}')" 
                                            class="text-red-500 hover:text-red-700 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-copower-gray">
                        <tr>
                            <td colspan="3" class="px-6 py-4"></td>
                            <td colspan="2" class="px-6 py-4">
                                <div class="space-y-1 text-right">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Subtotal:</span>
                                        <span class="font-semibold text-copower-dark">£{{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Minimum Order Value:</span>
                                        <span class="font-semibold {{ $meetsMinimum ? 'text-green-600' : 'text-red-600' }}">
                                            £{{ number_format($minimumOrderValue, 2) }}
                                        </span>
                                    </div>
                                    @if(!$meetsMinimum)
                                        <div class="text-sm text-red-600 mt-1">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Need £{{ number_format($minimumOrderValue - $subtotal, 2) }} more to meet minimum order
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('checkout.index') }}" method="GET">
                                    <button type="submit" 
                                            class="w-full bg-copower-banner text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition font-semibold text-sm"
                                            @if(!$meetsMinimum) disabled @endif>
                                        <i class="fas fa-arrow-right mr-2"></i>
                                        Proceed to Checkout
                                    </button>
                                    @if(!$meetsMinimum)
                                        <p class="text-xs text-red-500 mt-1 text-center">Minimum order not met</p>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Continue Shopping -->
        <div class="mt-6 text-center">
            <a href="{{ route('customer.products') }}" class="text-copower-dark hover:text-copower-banner transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Continue Shopping
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update quantity via AJAX
function updateQuantity(key, quantity) {
    if (quantity < 1) return;

    fetch('{{ route('quote.update', ['key' => ':key']) }}'.replace(':key', key), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the quantity display
            document.getElementById('qty-' + key).textContent = data.quantity;
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update quantity.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update quantity. Please try again.');
    });
}

// Remove item via AJAX
function removeItem(key) {
    if (!confirm('Are you sure you want to remove this item?')) return;

    fetch('{{ route('quote.remove', ['key' => ':key']) }}'.replace(':key', key), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to remove item.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove item. Please try again.');
    });
}

// Clear quote
function clearQuote() {
    if (!confirm('Are you sure you want to clear your entire quote?')) return;

    fetch('{{ route('quote.clear') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route('customer.products') }}';
        } else {
            alert(data.message || 'Failed to clear quote.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to clear quote. Please try again.');
    });
}
</script>
@endpush
@endsection