{{-- resources/views/admin/reports/inventory.blade.php --}}
@extends('layouts.admin')

@section('title', 'Inventory Report - Copower Wholesale Admin')
@section('page_title', 'Inventory Report')

@section('content')
<div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Products</p>
        <p class="text-2xl font-bold text-copower-dark mt-1">{{ number_format($products->total()) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Low Stock</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($lowStock) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Out of Stock</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($outOfStock) }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-200">
        <h3 class="font-bold text-copower-dark">Product Stock Levels</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3">Variants</th>
                    <th class="px-6 py-3">Total Stock</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    @php
                        $totalStock = $product->variants->sum('stock_quantity');
                        $status = $totalStock === 0 ? 'Out of Stock' : ($product->variants->where('stock_quantity', '<=', 0)->count() ? 'Low Stock' : 'In Stock');
                        $color = $totalStock === 0 ? 'bg-red-100 text-red-700' : ($status === 'Low Stock' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-copower-dark">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500 font-mono">SKU: {{ $product->sku }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @foreach($product->variants as $variant)
                                <span class="inline-block px-2 py-0.5 mb-1 mr-1 bg-gray-100 rounded-full text-xs">{{ ucfirst($variant->variant_type) }}: {{ $variant->stock_quantity }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-copower-dark">{{ number_format($totalStock) }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">{{ $status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">No products.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $products->links() }}
    </div>
</div>
@endsection