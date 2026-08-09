{{-- resources/views/admin/quotes/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Quotes - Copower Wholesale Admin')
@section('page_title', 'Quote Review')

@section('content')
<div class="mb-6 bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-500 font-medium">Pending Quote Review</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($pendingCount) }}</p>
    </div>
    <span class="text-sm text-gray-400">Quotes awaiting the sales team</span>
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
                    <th class="px-6 py-3">Quote #</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Tier</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Submitted</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($quotes as $quote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-copower-dark font-mono">{{ $quote->quote_number }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800">{{ $quote->customer_company }}</p>
                            <p class="text-xs text-gray-500">{{ $quote->customer_email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $quote->customer_tier ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-copower-dark">£{{ number_format($quote->grand_total, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $quote->submitted_at?->format('d M Y H:i') ?? $quote->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.quotes.show', $quote) }}" class="text-copower-banner hover:underline text-sm">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No quotes awaiting review.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $quotes->links() }}
    </div>
</div>
@endsection