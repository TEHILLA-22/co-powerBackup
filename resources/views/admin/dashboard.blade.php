{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard - Copower Wholesale Admin')
@section('page_title', 'Dashboard Overview')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Orders -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Orders</p>
                <p class="text-2xl font-bold text-copower-dark mt-1">{{ number_format($stats['total_orders']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
        <div class="mt-2 flex items-center space-x-2 text-xs">
            <span class="text-gray-500">Pending: <span class="font-medium text-yellow-600">{{ number_format($stats['pending_orders']) }}</span></span>
            <span class="text-gray-300">|</span>
            <span class="text-gray-500">Processing: <span class="font-medium text-blue-600">{{ number_format($stats['processing_orders']) }}</span></span>
        </div>
    </div>

    <!-- Revenue -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
                <p class="text-2xl font-bold text-green-600 mt-1">£{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-2 text-xs text-gray-500">
            This month: <span class="font-medium text-green-600">£{{ number_format($stats['monthly_revenue'], 2) }}</span>
        </div>
    </div>

    <!-- Customers -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Customers</p>
                <p class="text-2xl font-bold text-copower-dark mt-1">{{ number_format($stats['total_customers']) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-2 flex items-center space-x-2 text-xs">
            <span class="text-gray-500">Pending: <span class="font-medium text-yellow-600">{{ number_format($stats['pending_approvals']) }}</span></span>
            <span class="text-gray-300">|</span>
            <span class="text-gray-500">New this month: <span class="font-medium text-green-600">{{ number_format($stats['new_customers_this_month']) }}</span></span>
        </div>
    </div>

    <!-- Products -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Products</p>
                <p class="text-2xl font-bold text-copower-dark mt-1">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        <div class="mt-2 flex items-center space-x-2 text-xs">
            <span class="text-gray-500">Low Stock: <span class="font-medium text-red-600">{{ number_format($stats['low_stock_products']) }}</span></span>
            <span class="text-gray-300">|</span>
            <span class="text-gray-500">Out of Stock: <span class="font-medium text-red-600">{{ number_format($stats['out_of_stock_products']) }}</span></span>
        </div>
    </div>
</div>

<!-- Charts and Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Sales Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-bold text-copower-dark mb-4">Monthly Sales</h3>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-bold text-copower-dark mb-4 flex items-center justify-between">
            <span>Pending Approvals</span>
            <a href="{{ route('admin.customers.pending') }}" class="text-xs text-copower-banner hover:underline">View all</a>
        </h3>
        @if($pendingCustomers->count() > 0)
            <div class="space-y-3">
                @foreach($pendingCustomers as $customer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-copower-dark text-sm">{{ $customer->company_name }}</p>
                            <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                        </div>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-copower-banner hover:underline text-sm">
                            Review
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-8">No pending approvals</p>
        @endif
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-copower-dark">Recent Orders</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-copower-banner hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                    <th class="pb-3 pr-4">Order #</th>
                    <th class="pb-3 pr-4">Customer</th>
                    <th class="pb-3 pr-4">Total</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3 pr-4">Date</th>
                    <th class="pb-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 pr-4 text-sm font-medium text-copower-dark">{{ $order->order_number }}</td>
                        <td class="py-3 pr-4 text-sm text-gray-600">{{ $order->customer_company }}</td>
                        <td class="py-3 pr-4 text-sm font-medium text-copower-dark">£{{ number_format($order->grand_total, 2) }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($order->status == 'submitted') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status == 'approved') bg-green-100 text-green-800
                                @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-copower-banner hover:underline text-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['months']),
            datasets: [{
                label: 'Monthly Sales (£)',
                data: @json($monthlyData['sales']),
                borderColor: '#00A3E0',
                backgroundColor: 'rgba(0, 163, 224, 0.1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '£' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush