{{-- resources/views/admin/customers/show.blade.php --}}
@extends('layouts.admin')

@section('title', $user->full_name . ' - Copower Wholesale Admin')
@section('page_title', 'Customer: ' . $user->full_name)

@section('content')
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Customer Profile -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="font-bold text-copower-dark">Customer Profile</h3>
        </div>
        <div class="p-6 space-y-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Name</span>
                <span class="font-medium text-copower-dark">{{ $user->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Email</span>
                <span class="font-medium text-copower-dark">{{ $user->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Phone</span>
                <span class="font-medium">{{ $user->phone ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Company</span>
                <span class="font-medium">{{ $user->company_name ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Registration #</span>
                <span class="font-medium font-mono">{{ $user->company_registration_number ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">VAT #</span>
                <span class="font-medium font-mono">{{ $user->vat_number ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Email Verified</span>
                <span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->is_verified ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_verified ? 'Yes' : 'No' }}
                    </span>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Account Verified</span>
                <span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->is_admin_verified ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $user->is_admin_verified ? 'Yes' : 'No' }}
                    </span>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tier</span>
                <span class="font-medium">{{ $user->customerTier?->name ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                <span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Active' : 'Suspended' }}
                    </span>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Registered</span>
                <span class="font-medium">{{ $user->created_at->format('d M Y') }}</span>
            </div>

            @if($user->suspension_reason)
                <div class="p-3 bg-red-50 rounded-lg text-xs text-red-700 mt-2">Suspon reason: {{ $user->suspension_reason }}</div>
            @endif
        </div>
    </div>

    <!-- Addresses & Orders -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="font-bold text-copower-dark">Addresses</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
                @forelse($user->addresses as $address)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="font-medium text-copower-dark text-sm mb-1">{{ $address->address_type ?? 'Address' }}</p>
                        <p class="text-sm text-gray-600">{{ $address->address_line1 }}</p>
                        @if($address->address_line2)<p class="text-sm text-gray-600">{{ $address->address_line2 }}</p>@endif
                        <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->postal_code ?? '' }}</p>
                        <p class="text-sm text-gray-600">{{ $address->county ?? '' }} {{ $address->country ?? '' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 col-span-2">No addresses on file.</p>
                @endforelse
            </div>
        </div>

        <!-- Verify / Deactivate actions -->
        @if(!$user->is_admin_verified && $user->is_active)
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h4 class="font-bold text-copower-dark mb-4">Manual Verification</h4>
                <form method="POST" action="{{ route('admin.customers.approve', $user) }}" class="space-y-3 mb-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1" for="customer_tier_id">Assign Customer Tier</label>
                        <select name="customer_tier_id" id="customer_tier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Select a tier</option>
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}">{{ $tier->name }} ({{ $tier->discount_percentage }}% off)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1" for="notes">Notes (optional)</label>
                        <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>Verify Customer Account
                    </button>
                </form>
            </div>
        @endif

        @if($user->is_active)
            <form method="POST" action="{{ route('admin.customers.reject', $user) }}" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                @csrf
                <h4 class="font-bold text-red-600 mb-4">Deactivate Customer</h4>
                <label class="block text-xs font-medium text-gray-500 mb-1" for="rejection_reason">Reason (required, min 10 chars)</label>
                <textarea name="rejection_reason" id="rejection_reason" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3"></textarea>
                <button type="submit" onclick="return confirm('Deactivate this customer?')" class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    <i class="fas fa-ban mr-2"></i>Deactivate
                </button>
            </form>
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center text-sm text-gray-500">
                This customer account is currently suspended.
            </div>
        @endif

        <!-- Orders -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="font-bold text-copower-dark">Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Order #</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($user->orders as $order)
                            <tr>
                                <td class="px-6 py-3 text-sm font-medium">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-copower-banner hover:underline">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-6 py-3 text-sm">£{{ number_format($order->grand_total, 2) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $order->status_label }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('admin.customers.pending') }}" class="inline-block text-sm text-copower-banner hover:underline"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
</div>
@endsection