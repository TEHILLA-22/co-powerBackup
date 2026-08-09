{{-- resources/views/admin/orders/processing.blade.php --}}
@extends('layouts.admin')

@section('title', 'Orders - Copower Wholesale Admin')
@section('page_title', 'Order Processing')

@section('content')
<div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Pending Review</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($pendingCount) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Processing</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($processingCount) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Total Shown</p>
        <p class="text-2xl font-bold text-copower-dark mt-1">{{ number_format($orders->total()) }}</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.orders.bulk-approve') }}" id="bulkApproveForm">
    @csrf
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-bold text-copower-dark">Submitted Orders</h3>
            <div class="flex items-center space-x-3">
                <select name="bulk_tier" class="hidden" aria-hidden="true">
                    <option value=""></option>
                </select>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition disabled:opacity-50" id="bulkApproveBtn" disabled>
                    Approve Selected
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="px-6 py-3 bg-green-50 text-green-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="px-6 py-3 bg-yellow-50 text-yellow-700 text-sm font-medium">
                {{ session('warning') }}
            </div>
        @endif
        @if($errors->any())
            <div class="px-6 py-3 bg-red-50 text-red-700 text-sm font-medium">
                @foreach($errors->all() as $error) {{ $error }} @endforeach
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3">Order #</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Items</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Submitted</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="row-check rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-copower-dark">{{ $order->order_number }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-800">{{ $order->customer_company }}</p>
                                <p class="text-xs text-gray-500">{{ $order->customer_email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->items_count ?? $order->items->count() }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-copower-dark">£{{ number_format($order->grand_total, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->submitted_at?->format('d M Y H:i') ?? $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Submitted</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-copower-banner hover:underline text-sm">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 text-sm">No orders awaiting review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $orders->links() }}
        </div>
    </div>
</form>

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAll');
    const rows = document.querySelectorAll('.row-check');
    const bulkBtn = document.getElementById('bulkApproveBtn');

    function updateBulk() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        bulkBtn.disabled = checked === 0;
    }

    selectAll.addEventListener('change', function () {
        rows.forEach(r => { r.checked = selectAll.checked; });
        updateBulk();
    });

    rows.forEach(r => r.addEventListener('change', updateBulk));

    bulkBtn.addEventListener('click', function (e) {
        const checked = document.querySelectorAll('.row-check:checked').length;
        if (checked === 0) {
            e.preventDefault();
            return;
        }
        if (!confirm('Approve ' + checked + ' selected order(s)? This will also approve the customer account if it is their first order.')) {
            e.preventDefault();
        }
    });
</script>
@endpush
@endsection