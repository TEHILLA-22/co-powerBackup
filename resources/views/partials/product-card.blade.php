{{-- resources/views/partials/product-card.blade.php --}}
<div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
    <div class="relative overflow-hidden">
        <img src="{{ $product->image_url }}" 
             alt="{{ $product->name }}" 
             class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
        <div class="absolute top-2 right-2 bg-yellow-400 text-blue-900 text-xs font-bold px-2 py-1 rounded">
            B2B
        </div>
        @if($product->is_featured)
            <div class="absolute top-2 left-2 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded">
                Featured
            </div>
        @endif
    </div>
    
    <div class="p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-mono">{{ $product->sku }}</span>
            <span class="text-xs text-gray-500 font-mono">EAN: {{ $product->ean }}</span>
        </div>
        
        <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2 min-h-[3rem] text-sm">
            {{ $product->name }}
        </h3>
        
        <p class="text-sm text-gray-600 mb-3">{{ $product->brand ?? 'Unbranded' }}</p>
        
        <div class="border-t pt-3">
            <div class="grid grid-cols-3 gap-2 text-xs">
                @php
                    $variants = $product->variants->keyBy('variant_type');
                @endphp
                <div>
                    <span class="text-gray-500">Unit</span>
                    <p class="font-semibold text-gray-800">£{{ number_format($variants->get('unit')->base_price ?? 0, 2) }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Case</span>
                    <p class="font-semibold text-gray-800">£{{ number_format($variants->get('case')->base_price ?? 0, 2) }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Pallet</span>
                    <p class="font-semibold text-gray-800">£{{ number_format($variants->get('pallet')->base_price ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            @auth
                <a href="{{ route('customer.product.show', $product->slug) }}" 
                   class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    View Product
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="block text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition font-medium text-sm">
                    Login to View Prices
                </a>
            @endauth
        </div>
    </div>
</div>