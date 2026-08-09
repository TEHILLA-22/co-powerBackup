{{-- resources/views/admin/reports/sales.blade.php --}}
@extends('layouts.admin')

@section('title', 'Sales Report - Copower Wholesale Admin')
@section('page_title', 'Sales Report')

@section('content')
<div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
        <p class="text-2xl font-bold text-green-600 mt-1">£{{ number_format($revenue, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Revenue ({{ now()->format('M Y') }})</p>
        <p class="text-2xl font-bold text-copower-dark mt-1">£{{ number_format($monthlyRevenue, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Submitted</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($statusCounts['submitted']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Approved</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($statusCounts['approved']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Rejected</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($statusCounts['rejected']) }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-200">
        <h3 class="font-bold text-copower-dark">All Orders</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Order #</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-copower-dark">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->customer_company }}</td>
                        <td class="px-6 py-4 text-sm font-medium">£{{ number_format($order->grand_total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->status == 'approved' ? 'bg-green-100 text-green-700' : ($order->status == 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection