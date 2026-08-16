{{-- resources/views/customer/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'My Account - Copower Wholesale')

@section('content')
<div class="bg-copower-gray min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-8">

        <!-- Page Header -->
        <div class="mb-8">
            <nav class="text-sm mb-4">
                <ol class="list-none p-0 inline-flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-copower-dark">Home</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li class="text-copower-dark font-medium">My Account</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-copower-dark">My Account</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- ==================== SIDEBAR NAVIGATION ==================== -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Account Identity -->
                    <div class="bg-copower-dark text-white p-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-copower-banner flex items-center justify-center shrink-0">
                                <span class="text-lg font-bold">{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold truncate">{{ $user->full_name }}</p>
                                <p class="text-xs text-copower-banner truncate">{{ $user->company_name }}</p>
                            </div>
                        </div>
                    </div>

                    <nav class="p-3 space-y-1 text-sm">
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg bg-copower-gray text-copower-dark font-medium">
                            <span class="flex items-center"><i class="fas fa-grip-vertical mr-3 w-4"></i> My Account</span>
                        </a>
                        <a href="{{ route('customer.products') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-600 hover:bg-copower-gray hover:text-copower-dark transition">
                            <i class="fas fa-store mr-3 w-4"></i> All Products
                        </a>
                        <a href="{{ route('quote.bulk') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-600 hover:bg-copower-gray hover:text-copower-dark transition">
                            <i class="fas fa-truck-fast mr-3 w-4"></i> Order Builder
                        </a>
                        <a href="{{ route('quote.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-600 hover:bg-copower-gray hover:text-copower-dark transition">
                            <span class="flex items-center"><i class="fas fa-file-invoice mr-3 w-4"></i> My Quote</span>
                            @if($stats['quote_count'] > 0)
                                <span class="bg-copower-banner text-white text-xs px-2 py-0.5 rounded-full">{{ $stats['quote_count'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('price-list') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-600 hover:bg-copower-gray hover:text-copower-dark transition">
                            <i class="fas fa-file-invoice-dollar mr-3 w-4"></i> Price List
                        </a>
                        <a href="{{ route('about') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-600 hover:bg-copower-gray hover:text-copower-dark transition">
                            <i class="fas fa-headset mr-3 w-4"></i> Support
                        </a>
                        <div class="border-t border-gray-100 mt-2 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt mr-3 w-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="lg:col-span-3 space-y-6">

                <!-- Welcome Banner -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-copower-dark">Welcome back, {{ $user->first_name }}!</h2>
                        <p class="text-gray-500 text-sm mt-1">Manage your account, orders and quote requests.</p>
                    </div>
                    <a href="{{ route('customer.products') }}" class="inline-flex items-center justify-center bg-copower-banner text-white px-5 py-2.5 rounded-lg font-medium hover:bg-opacity-90 transition shrink-0">
                        <i class="fas fa-store mr-2"></i> Shop Products
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Orders</p>
                            <i class="fas fa-box text-copower-banner"></i>
                        </div>
                        <p class="text-2xl font-bold text-copower-dark mt-2">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">In Review</p>
                            <i class="fas fa-clock text-yellow-500"></i>
                        </div>
                        <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['pending_orders'] }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Spent</p>
                            <i class="fas fa-pound-sign text-green-500"></i>
                        </div>
                        <p class="text-2xl font-bold text-copower-dark mt-2">£{{ number_format($stats['total_spent'], 2) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Saved Quotes</p>
                            <i class="fas fa-file-invoice text-purple-500"></i>
                        </div>
                        <p class="text-2xl font-bold text-copower-dark mt-2">{{ $stats['saved_quotes'] }}</p>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-copower-dark"><i class="fas fa-user-circle mr-2 text-copower-banner"></i> Account Information</h3>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Verified Account
                        </span>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Info -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Contact Information</h4>
                            <div class="space-y-2 text-sm">
                                <p class="text-copower-dark font-medium">{{ $user->full_name }}</p>
                                <p class="text-gray-600 flex items-center"><i class="fas fa-envelope mr-2 text-gray-400 w-4"></i> {{ $user->email }}</p>
                                <p class="text-gray-600 flex items-center"><i class="fas fa-phone mr-2 text-gray-400 w-4"></i> {{ $user->mobile ?: ($user->phone ?: '—') }}</p>
                            </div>
                        </div>
                        <!-- Company Info -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Company Information</h4>
                            <div class="space-y-2 text-sm">
                                <p class="text-copower-dark font-medium">{{ $user->company_name }}</p>
                                <p class="text-gray-600 flex items-center"><i class="fas fa-tag mr-2 text-gray-400 w-4"></i> {{ $user->customerTier?->name ?? 'Standard' }} Tier</p>
                                @if($user->company_registration_number)
                                    <p class="text-gray-600 flex items-center"><i class="fas fa-hashtag mr-2 text-gray-400 w-4"></i> Reg: {{ $user->company_registration_number }}</p>
                                @endif
                                @if($user->vat_number)
                                    <p class="text-gray-600 flex items-center"><i class="fas fa-file-invoice mr-2 text-gray-400 w-4"></i> VAT: {{ $user->vat_number }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Book -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-copower-dark"><i class="fas fa-map-marker-alt mr-2 text-copower-banner"></i> Address Book</h3>
                    </div>
                    <div class="p-6">
                        @if($defaultAddress)
                            <div class="text-sm text-gray-700">
                                <p class="font-medium text-copower-dark">{{ $defaultAddress->recipient_name ?: $defaultAddress->company_name }}</p>
                                <p class="mt-1">{{ $defaultAddress->full_address }}</p>
                                @if($defaultAddress->phone)<p class="text-gray-500 mt-1">{{ $defaultAddress->phone }}</p>@endif
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No saved address on file.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-copower-dark"><i class="fas fa-box mr-2 text-copower-banner"></i> Recent Orders</h3>
                    </div>
                    <div class="overflow-x-auto">
                        @if($recentOrders->count() > 0)
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                        <th class="px-6 py-3">Order #</th>
                                        <th class="px-6 py-3">Date</th>
                                        <th class="px-6 py-3">Total</th>
                                        <th class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($recentOrders as $order)
                                        <tr class="hover:bg-copower-gray/50 transition">
                                            <td class="px-6 py-3 font-medium text-copower-dark">{{ $order->order_number }}</td>
                                            <td class="px-6 py-3 text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                            <td class="px-6 py-3 text-gray-700 font-medium">£{{ number_format($order->grand_total, 2) }}</td>
                                            <td class="px-6 py-3">
                                                @php
                                                    $colors = [
                                                        'gray' => 'bg-gray-100 text-gray-700',
                                                        'yellow' => 'bg-yellow-100 text-yellow-700',
                                                        'orange' => 'bg-orange-100 text-orange-700',
                                                        'blue' => 'bg-blue-100 text-blue-700',
                                                        'red' => 'bg-red-100 text-red-700',
                                                        'indigo' => 'bg-indigo-100 text-indigo-700',
                                                        'purple' => 'bg-purple-100 text-purple-700',
                                                        'green' => 'bg-green-100 text-green-700',
                                                    ];
                                                    $color = $colors[$order->status_color] ?? 'bg-gray-100 text-gray-700';
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-6 text-center text-gray-500 text-sm">
                                <i class="fas fa-inbox text-2xl text-gray-300 mb-2 block"></i>
                                No orders yet. Once you place an order, it will appear here.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Quotes -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-copower-dark"><i class="fas fa-file-invoice mr-2 text-copower-banner"></i> Recent Quote Requests</h3>
                        <a href="{{ route('quote.bulk') }}" class="text-copower-banner hover:underline font-medium text-sm">New Quote</a>
                    </div>
                    <div class="overflow-x-auto">
                        @if($recentQuotes->count() > 0)
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                        <th class="px-6 py-3">Reference</th>
                                        <th class="px-6 py-3">Date</th>
                                        <th class="px-6 py-3">Total</th>
                                        <th class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($recentQuotes as $quote)
                                        <tr class="hover:bg-copower-gray/50 transition">
                                            <td class="px-6 py-3 font-medium text-copower-dark">{{ $quote->quote_number }}</td>
                                            <td class="px-6 py-3 text-gray-600">{{ $quote->created_at->format('d M Y') }}</td>
                                            <td class="px-6 py-3 text-gray-700 font-medium">£{{ number_format($quote->grand_total, 2) }}</td>
                                            <td class="px-6 py-3">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                    {{ $quote->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-6 text-center text-gray-500 text-sm">
                                <i class="fas fa-file-invoice text-2xl text-gray-300 mb-2 block"></i>
                                No quotes yet. Build a quote to get started.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
