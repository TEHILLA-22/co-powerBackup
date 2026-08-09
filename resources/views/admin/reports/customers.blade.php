{{-- resources/views/admin/reports/customers.blade.php --}}
@extends('layouts.admin')

@section('title', 'Customer Report - Copower Wholesale Admin')
@section('page_title', 'Customer Report')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-200">
        <h3 class="font-bold text-copower-dark">Customers</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Company</th>
                    <th class="px-6 py-3">Tier</th>
                    <th class="px-6 py-3">Orders</th>
                    <th class="px-6 py-3">Verified</th>
                    <th class="px-6 py-3">Registered</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-copower-dark">{{ $customer->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->company_name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->customerTier?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-copower-dark">{{ $customer->orders_count }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $customer->is_verified ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                Email {{ $customer->is_verified ? '✓' : '✗' }}
                            </span>
                            @if($customer->is_admin_verified)
                                <span class="ml-1 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Tier ✓</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No customers registered.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection