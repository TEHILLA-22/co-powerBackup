{{-- resources/views/shop/catalog/index.blade.php --}}
@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name . ' - Copower Wholesale' : 'All Products - Copower Wholesale')

@section('content')
<div class="bg-copower-gray py-8">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6">
            <ol class="list-none p-0 inline-flex items-center space-x-2 flex-wrap">
                <li><a href="{{ route('home') }}" class="text-copower-dark hover:text-copower-banner">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-copower-dark font-medium">
                    @if($currentCategory)
                        {{ $currentCategory->name }}
                    @else
                        All Products
                    @endif
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-copower-dark">
                    @if($currentCategory)
                        {{ $currentCategory->name }}
                    @else
                        All Products
                    @endif
                </h1>
                @if($filters['search'])
                    <p class="text-gray-600 mt-1">Results for: "{{ $filters['search'] }}"</p>
                @endif
                <p class="text-sm text-gray-500 mt-1">{{ $products->total() }} products found</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select id="sortSelect" class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                        <option value="name_asc" {{ ($filters['sort'] ?? '') == 'name' && ($filters['direction'] ?? '') == 'asc' ? 'selected' : '' }}>Name: A-Z</option>
                        <option value="name_desc" {{ ($filters['sort'] ?? '') == 'name' && ($filters['direction'] ?? '') == 'desc' ? 'selected' : '' }}>Name: Z-A</option>
                        <option value="price_asc" {{ ($filters['sort'] ?? '') == 'price' && ($filters['direction'] ?? '') == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ ($filters['sort'] ?? '') == 'price' && ($filters['direction'] ?? '') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="created_at_desc" {{ ($filters['sort'] ?? '') == 'created_at' && ($filters['direction'] ?? '') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="brand_asc" {{ ($filters['sort'] ?? '') == 'brand' && ($filters['direction'] ?? '') == 'asc' ? 'selected' : '' }}>Brand: A-Z</option>
                    </select>
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-copower-dark mb-4">Filter Products</h3>

                    <!-- Clear Filters -->
                    <a href="{{ route('customer.products') }}" class="text-sm text-copower-banner hover:underline mb-4 inline-block">
                        Clear all filters
                    </a>

                    <!-- Search Filter -->
                    <div class="mb-6">
                        <form action="{{ route('customer.products') }}" method="GET" class="relative">
                            @foreach($filters as $key => $value)
                                @if($key != 'search' && $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="text"
                                   name="search"
                                   value="{{ $filters['search'] }}"
                                   placeholder="Search products..."
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-copower-dark hover:text-copower-banner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Categories</h4>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('customer.products', array_filter(array_merge($filters, ['category' => null]))) }}"
                                   class="text-sm {{ !$filters['category'] ? 'text-copower-banner font-semibold' : 'text-gray-600 hover:text-copower-banner' }} transition">
                                    All Categories
                                </a>
                            </li>
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('customer.products', array_filter(array_merge($filters, ['category' => $category->slug]))) }}"
                                       class="text-sm {{ $filters['category'] == $category->slug ? 'text-copower-banner font-semibold' : 'text-gray-600 hover:text-copower-banner' }} transition">
                                        {{ $category->name }}
                                        <span class="text-xs text-gray-400">({{ $category->products_count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Brand Filter -->
                    @if($brands->isNotEmpty())
                    <div class="mb-6">
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Brands</h4>
                        <select name="brand"
                                onchange="window.location.href='{{ route('customer.products') }}?' + new URLSearchParams({...{{ json_encode(array_filter($filters)) }}, brand: this.value})"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ $filters['brand'] == $brand ? 'selected' : '' }}>
                                    {{ $brand }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Price Range Filter -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Price Range</h4>
                        <div class="flex space-x-2">
                            <input type="number"
                                   name="min_price"
                                   value="{{ $filters['min_price'] }}"
                                   placeholder="Min"
                                   class="w-1/2 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                            <input type="number"
                                   name="max_price"
                                   value="{{ $filters['max_price'] }}"
                                   placeholder="Max"
                                   class="w-1/2 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                        </div>
                    </div>

                    <!-- Stock Filter -->
                    <div class="mb-6">
                        <label class="flex items-center space-x-2 text-sm text-gray-600">
                            <input type="checkbox"
                                   name="in_stock"
                                   value="1"
                                   {{ $filters['in_stock'] ? 'checked' : '' }}
                                   onchange="window.location.href='{{ route('customer.products') }}?' + new URLSearchParams({...{{ json_encode(array_filter($filters)) }}, in_stock: this.checked ? 1 : ''})">
                            <span>In Stock Only</span>
                        </label>
                    </div>

                    <!-- Apply Filters Button -->
                    <button type="button"
                            onclick="applyFilters()"
                            class="w-full bg-copower-dark text-white py-2 rounded-lg hover:bg-opacity-90 transition font-medium text-sm">
                        Apply Filters
                    </button>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="flex-1">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        @foreach($products as $product)
                            @include('partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-copower-dark">No products found</h3>
                        <p class="text-gray-600 mt-2">Try adjusting your filters or search terms.</p>
                        <a href="{{ route('customer.products') }}" class="inline-block mt-4 text-copower-banner hover:underline">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Sort dropdown handler
document.getElementById('sortSelect')?.addEventListener('change', function() {
    const [sort, direction] = this.value.split('_');
    const params = new URLSearchParams(window.location.search);
    params.set('sort', sort);
    params.set('direction', direction);
    window.location.href = window.location.pathname + '?' + params.toString();
});

// Apply filters
function applyFilters() {
    const params = new URLSearchParams(window.location.search);

    // Get filter values
    const minPrice = document.querySelector('input[name="min_price"]');
    const maxPrice = document.querySelector('input[name="max_price"]');
    const inStock = document.querySelector('input[name="in_stock"]');

    if (minPrice.value) params.set('min_price', minPrice.value);
    else params.delete('min_price');

    if (maxPrice.value) params.set('max_price', maxPrice.value);
    else params.delete('max_price');

    if (inStock.checked) params.set('in_stock', '1');
    else params.delete('in_stock');

    window.location.href = window.location.pathname + '?' + params.toString();
}
</script>
@endpush
@endsection
