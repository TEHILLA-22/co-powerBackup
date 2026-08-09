{{-- resources/views/admin/customers/pending.blade.php --}}
@extends('layouts.admin')

@section('title', 'Pending Customers - Copower Wholesale Admin')
@section('page_title', 'Pending Customer Verification')

@section('content')
<div class="mb-6 bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-500 font-medium">Awaiting Verification</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($pendingCount) }}</p>
    </div>
    <form method="GET" action="{{ route('admin.customers.pending') }}" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, company..."
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72">
        <button type="submit" class="bg-copower-dark text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
            <i class="fas fa-search mr-1"></i>Search
        </button>
        @if(request('search'))
            <a href="{{ route('admin.customers.pending') }}" class="text-sm text-copower-banner hover:underline">Clear</a>
        @endif
    </form>
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
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Company</th>
                    <th class="px-6 py-3">Registration #</th>
                    <th class="px-6 py-3">Registered</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendingUsers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-copower-dark">{{ $customer->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->company_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $customer->company_registration_number ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-copower-banner hover:underline text-sm font-medium">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">No customers awaiting verification.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $pendingUsers->links() }}
    </div>
</div>
@endsection