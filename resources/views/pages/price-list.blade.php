{{-- resources/views/pages/price-list.blade.php --}}
@extends('layouts.app')

@section('title', 'Price List - Copower Wholesale')

@section('meta_description', 'View our wholesale price list for baby care, hair products, cosmetics, oral hygiene and personal care brands.')

@section('content')
    {{-- Trust Banner (consistent across all pages) --}}
    @include('partials.trust-banner')

    {{-- ===== Page Hero ===== --}}
    <section class="relative bg-copower-dark text-white overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background: radial-gradient(ellipse at 20% 0%, #00A3E0 0%, transparent 60%);"></div>
        <div class="relative max-w-7xl mx-auto px-6 py-16 md:py-20">
            <nav class="text-xs uppercase tracking-widest text-copower-banner font-semibold space-x-2">
                <span>Home</span><span>/</span><span>Price List</span>
            </nav>
            <h1 class="mt-4 text-4xl md:text-5xl font-black leading-tight">Wholesale Price List</h1>
            <p class="mt-4 max-w-2xl text-lg text-copower-gray/90">Transparent, tiered pricing for our B2B partners. Registered customers see live per-unit prices with their exclusive trade discounts applied.</p>
        </div>
    </section>

    {{-- ===== Intro ===== --}}
    <section class="py-10 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl font-bold text-copower-dark">How pricing works</h2>
            <p class="mt-2 max-w-3xl text-gray-600">
                Prices are shown after you sign in, based on your customer tier. Minimum order value is £2,000 ex VAT. Every product carries a minimum order quantity (MOQ) shown on the product page.
            </p>
        </div>
    </section>

    {{-- ===== Tier Pricing ===== --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl font-bold text-copower-dark mb-8">Account tiers &amp; discounts</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-2xl p-6 hover:border-copower-banner/40 hover:shadow-lg transition">
                    <h3 class="font-bold text-copower-dark">Standard</h3>
                    <p class="mt-2 text-sm text-gray-600">Entry-level wholesale pricing for new trade accounts. Sign in to see live prices for all products.</p>
                    <p class="mt-4 text-sm font-bold text-copower-banner">List price</p>
                </div>
                <div class="border border-gray-200 rounded-2xl p-6 hover:border-copower-banner/40 hover:shadow-lg transition">
                    <h3 class="font-bold text-copower-dark">Premium</h3>
                    <p class="mt-2 text-sm text-gray-600">For established accounts with consistent order volumes. Enhanced discount applied automatically at checkout.</p>
                    <p class="mt-4 text-sm font-bold text-copower-banner">5% discount</p>
                </div>
                <div class="border border-gray-200 rounded-2xl p-6 hover:border-copower-banner/40 hover:shadow-lg transition">
                    <h3 class="font-bold text-copower-dark">VIP</h3>
                    <p class="mt-2 text-sm text-gray-600">Our top wholesale partners with the strongest trade pricing and priority processing.</p>
                    <p class="mt-4 text-sm font-bold text-copower-banner">10% discount</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Download Area ===== --}}
    <section class="py-16 bg-copower-gray">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                <h2 class="text-2xl font-bold text-copower-dark">Browse live products</h2>
                <p class="mt-2 max-w-2xl mx-auto text-gray-600">
                    Every product, brand, SKU, EAN and MOQ is available in the catalog. Sign in to see your tier's live pricing before you build your quote.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('price-list.download') }}" class="inline-flex items-center px-6 py-3 bg-copower-banner text-white rounded-xl font-semibold text-sm hover:bg-opacity-90 transition">
                        Download Price List (xlsx)
                    </a>
                    <a href="{{ route('customer.products') }}" class="inline-flex items-center px-6 py-3 bg-copower-dark text-white rounded-xl font-semibold text-sm hover:bg-opacity-90 transition">
                        All Products
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-copower-banner text-copower-banner rounded-xl font-semibold text-sm hover:bg-copower-banner hover:text-white transition">
                        Open a Trade Account
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection