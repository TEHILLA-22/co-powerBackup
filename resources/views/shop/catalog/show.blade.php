{{-- resources/views/shop/catalog/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name . ' - Copower Wholesale')

@section('content')
<div class="bg-copower-gray py-8">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6">
            <ol class="list-none p-0 inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="text-copower-dark hover:text-copower-banner">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('customer.products') }}" class="text-copower-dark hover:text-copower-banner">Products</a></li>
                @if($product->category)
                    <li><span class="text-gray-400">/</span></li>
                    <li><a href="{{ route('customer.products', ['category' => $product->category->slug]) }}" class="text-copower-dark hover:text-copower-banner">{{ $product->category->name }}</a></li>
                @endif
                <li><span class="text-gray-400">/</span></li>
                <li class="text-copower-dark font-medium">{{ $product->name }}</li>
            </ol>
        </nav>

        <!-- Product Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8">
                <!-- Product Image -->
                <div>
                    <div class="bg-copower-gray rounded-xl overflow-hidden">
                        <img src="{{ $product->main_image ? asset('storage/' . $product->main_image) : asset('images/placeholder-product.jpg') }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-96 object-cover">
                    </div>
                    @if($product->gallery_images)
                        <div class="flex space-x-2 mt-4 overflow-x-auto">
                            @foreach($product->gallery_images as $image)
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-20 h-20 object-cover rounded-lg border-2 border-transparent hover:border-copower-banner cursor-pointer transition">
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div>
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-copower-dark">{{ $product->name }}</h1>
                            @if($product->brand)
                                <p class="text-gray-600 text-sm mt-1">Brand: <span class="font-medium">{{ $product->brand }}</span></p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-500 font-mono">SKU: {{ $product->sku }}</span>
                            <span class="text-xs text-gray-500 font-mono">EAN: {{ $product->ean }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-gray-600 text-sm">{{ $product->short_description }}</p>
                        @if($product->description)
                            <div class="mt-4 text-sm text-gray-600">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="font-semibold text-copower-dark mb-3">Pricing & Variants</h3>
                        <div class="space-y-3">
                            @foreach($product->enhanced_variants as $variant)
                                <div class="flex items-center justify-between p-3 bg-copower-gray rounded-lg">
                                    <div>
                                        <span class="font-medium text-copower-dark capitalize">{{ $variant['type'] }}</span>
                                        @if($variant['name'])
                                            <span class="text-sm text-gray-500 ml-2">({{ $variant['name'] }})</span>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $variant['quantity_per_unit'] }} units per {{ $variant['type'] }}
                                            @if($variant['moq'] > 1)
                                                · MOQ: {{ $variant['moq'] }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-copower-dark">£{{ number_format($variant['price'], 2) }}</span>
                                        @if($variant['original_price'] > $variant['price'])
                                            <span class="text-sm text-gray-400 line-through ml-2">£{{ number_format($variant['original_price'], 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add to Quote -->
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <form action="#" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            @csrf
                            <div class="flex items-center space-x-3">
                                <label for="quantity" class="text-sm font-medium text-copower-dark">Qty:</label>
                                <input type="number" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="{{ $product->moq }}" 
                                       min="{{ $product->moq }}"
                                       step="{{ $product->moq_increment ?? 1 }}"
                                       class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                            </div>
                            <div class="flex space-x-3 w-full sm:w-auto">
                                <button type="submit" class="flex-1 sm:flex-none bg-copower-banner text-white px-8 py-2.5 rounded-lg hover:bg-opacity-90 transition font-semibold text-sm">
                                    <i class="fas fa-cart-plus mr-2"></i> Add to Quote
                                </button>
                                <a href="{{ route('quote.bulk') }}" class="flex-1 sm:flex-none border border-copower-dark text-copower-dark px-8 py-2.5 rounded-lg hover:bg-copower-gray transition font-semibold text-sm">
                                    <i class="fas fa-layer-group mr-2"></i> Bulk Order
                                </a>
                            </div>
                        </form>
                        <p class="text-xs text-gray-500 mt-3">
                            <i class="fas fa-info-circle mr-1"></i> 
                            Minimum order: {{ $product->moq }} units. 
                            @if($product->moq_increment > 1)
                                Quantity must be in multiples of {{ $product->moq_increment }}.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-copower-dark mb-6">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($relatedProducts as $related)
                        @include('partials.product-card', ['product' => $related])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection