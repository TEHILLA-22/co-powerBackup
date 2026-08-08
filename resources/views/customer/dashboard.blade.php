{{-- resources/views/customer/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Copower Wholesale')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->first_name }}!</h1>
            <p class="text-gray-600 mt-1">{{ auth()->user()->company_name }}</p>
            
            <div class="mt-4 flex flex-wrap gap-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Verified Account
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    Tier: {{ auth()->user()->customerTier->name ?? 'Standard' }}
                </span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-500">Total Orders</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-500">Pending Orders</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_orders'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-500">Total Spent</div>
                <div class="text-2xl font-bold text-gray-900">£{{ number_format($stats['total_spent'] ?? 0, 2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-500">Wishlist Items</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['wishlist_count'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('shop.products') }}" class="bg-blue-600 text-white rounded-lg shadow-sm p-6 hover:bg-blue-700 transition text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="font-medium">Browse Products</span>
            </a>
            <a href="{{ route('bulk-order.index') }}" class="bg-green-600 text-white rounded-lg shadow-sm p-6 hover:bg-green-700 transition text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="font-medium">Bulk Order Builder</span>
            </a>
            <a href="{{ route('customer.orders') }}" class="bg-purple-600 text-white rounded-lg shadow-sm p-6 hover:bg-purple-700 transition text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="font-medium">Order History</span>
            </a>
        </div>
    </div>
</div>
@endsection