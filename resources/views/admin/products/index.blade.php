{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Products - Copower Wholesale Admin')
@section('page_title', 'Product Management')

@section('content')
<!-- Stats -->
<div class="mb-6 grid grid-cols-2 sm:grid-cols-5 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Total</p>
        <p class="text-2xl font-bold text-copower-dark mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Active</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Inactive</p>
        <p class="text-2xl font-bold text-gray-600 mt-1">{{ number_format($stats['inactive']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Out of Stock</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['out_of_stock']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Low Stock</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['low_stock']) }}</p>
    </div>
</div>

<!-- Toolbar -->
<div class="mb-6 bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex flex-wrap items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, SKU, EAN..."
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56">
        <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="stock_status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Stock</option>
            <option value="in_stock" @selected(request('stock_status') === 'in_stock')>In Stock</option>
            <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>Out of Stock</option>
            <option value="low_stock" @selected(request('stock_status') === 'low_stock')>Low Stock</option>
        </select>
        <button type="submit" class="bg-copower-dark text-white px-4 py-2 rounded-lg text-sm font-medium"><i class="fas fa-search mr-1"></i>Filter</button>
    </form>
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-1">
            <a href="{{ route('admin.products.export', ['format' => 'xlsx'] + request()->all()) }}" class="border border-copower-dark text-copower-dark px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50"><i class="fas fa-file-excel mr-1"></i>Export</a>
            <a href="{{ route('admin.products.import-form') }}" class="border border-copower-dark text-copower-dark px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50"><i class="fas fa-file-import mr-1"></i>Import</a>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-copower-banner text-white px-4 py-2 rounded-lg text-sm font-medium"><i class="fas fa-plus mr-1"></i>Add Product</a>
    </div>
</div>

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

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">MOQ</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    @php
                        $totalStock = $product->variants->sum('stock_quantity');
                        $activeCount = $product->variants->where('in_stock', true)->count();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-copower-dark">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500 font-mono">SKU: {{ $product->sku }} | EAN: {{ $product->ean }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->moq }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium {{ $totalStock === 0 ? 'text-red-600' : ($totalStock <= ($product->variants->min('reorder_level') ?? 0) ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($totalStock) }}
                            </span>
                            <span class="text-xs text-gray-400">({{ $activeCount }}/{{ $product->variants->count() }} variants)</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($product->is_on_sale)
                                <span class="ml-1 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Sale</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-copower-banner hover:underline" title="Toggle active">{{ $product->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-copower-banner hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $products->links() }}
    </div>
</div>
@endsection