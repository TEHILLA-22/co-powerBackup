{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('title', 'Copower Wholesale - B2B Distributor for Health & Beauty')

@section('content')
    <!-- Trust Banner -->
    @include('partials.trust-banner')

    <!-- Hero Section -->
    @include('partials.hero')

    <!-- Categories Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-copower-dark">Shop by Category</h2>
                <p class="text-gray-600 mt-2">Find the products you need in our extensive catalog</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                @foreach($categories ?? [] as $category)
                <a href="{{ route('customer.products') }}?category={{ $category->slug }}" class="group">
                    <div class="bg-copower-gray rounded-xl p-6 text-center hover:shadow-lg transition transform hover:-translate-y-1 duration-300 border border-transparent hover:border-copower-banner/20">
                        <div class="w-16 h-16 mx-auto mb-4 bg-copower-dark/10 rounded-full flex items-center justify-center group-hover:bg-copower-dark group-hover:text-white transition">
                            <i class="{{ $category->icon ?? 'fas fa-box' }} text-2xl text-copower-dark group-hover:text-white transition"></i>
                        </div>
                        <h3 class="font-semibold text-copower-dark text-sm">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $category->products_count ?? 0 }} products</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-copower-gray">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-copower-dark">Featured Products</h2>
                    <p class="text-gray-600 mt-1">Popular items from our wholesale catalog</p>
                </div>
                @auth
                    <a href="{{ route('customer.products') }}" class="text-copower-banner hover:text-copower-dark font-semibold transition">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                @endauth
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($featuredProducts ?? [] as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    <!-- Wholesale Benefits -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-copower-dark">Why Choose Copower?</h2>
                <p class="text-gray-600 mt-2">The smart choice for B2B wholesale distribution</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-copower-dark/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-2xl text-copower-dark"></i>
                    </div>
                    <h3 class="font-semibold text-xl mb-2 text-copower-dark">Fast Delivery</h3>
                    <p class="text-gray-600">Next-day delivery across the UK with real-time tracking</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-copower-dark/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tag text-2xl text-copower-dark"></i>
                    </div>
                    <h3 class="font-semibold text-xl mb-2 text-copower-dark">Competitive Pricing</h3>
                    <p class="text-gray-600">Volume discounts and tiered pricing for wholesale customers</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-copower-dark/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-2xl text-copower-dark"></i>
                    </div>
                    <h3 class="font-semibold text-xl mb-2 text-copower-dark">Dedicated Support</h3>
                    <p class="text-gray-600">Expert account managers and 24/7 customer support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="bg-copower-dark py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white">Subscribe to Our Newsletter</h3>
                    <p class="text-gray-300 mt-2">Get the latest deals, new products, and industry insights</p>
                </div>
                <div>
                    <form action="#" method="POST" class="flex">
                        @csrf
                        <input type="email" 
                               name="email" 
                               placeholder="Enter your email address" 
                               class="flex-1 px-4 py-3 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-copower-banner text-sm"
                               required>
                        <button type="submit" class="bg-copower-banner text-white px-6 py-3 rounded-r-lg font-semibold hover:bg-opacity-90 transition whitespace-nowrap text-sm">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection