{{-- resources/views/shop/quote-track.blade.php --}}
@extends('layouts.app')

@section('title', 'Track Your Quote - Copower Wholesale')

@section('content')
<div class="bg-copower-gray py-10">
    <div class="max-w-3xl mx-auto px-6">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-copower-dark">Track Your Quote</h1>
            <p class="text-gray-500 mt-2 text-sm">
                Enter your quote reference and the email address you used to submit it.
            </p>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <form method="GET" action="{{ route('quote.track') }}" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="reference" class="block text-xs font-bold text-copower-dark uppercase tracking-wider mb-1">Quote Reference</label>
                        <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 focus-within:ring-2 focus-within:ring-copower-banner/20 focus-within:border-copower-banner overflow-hidden">
                            <span class="pl-4 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </span>
                            <input type="text"
                                   id="reference"
                                   name="reference"
                                   value="{{ old('reference', $reference) }}"
                                   placeholder="e.g. QT2026080001"
                                   class="flex-1 px-3 py-3 text-sm bg-transparent focus:outline-none font-mono">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-copower-dark uppercase tracking-wider mb-1">Email Address</label>
                        <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 focus-within:ring-2 focus-within:ring-copower-banner/20 focus-within:border-copower-banner overflow-hidden">
                            <span class="pl-4 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $email) }}"
                                   placeholder="you@company.co.uk"
                                   class="flex-1 px-3 py-3 text-sm bg-transparent focus:outline-none">
                        </div>
                    </div>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center py-3.5 px-4 bg-copower-dark text-white rounded-xl font-semibold text-sm hover:bg-copower-banner focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copower-banner transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Search Quote
                </button>
            </form>
        </div>

        @if($searched)
            @if($quote)
                <!-- Quote Found -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-copower-dark px-6 py-5 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-widest text-copower-banner font-bold">Quote Reference</div>
                            <div class="text-2xl font-mono font-bold text-white mt-1">{{ $quote->quote_number }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs uppercase tracking-widest text-copower-banner font-bold">Status</div>
                            <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold
                                @if($quote->status === 'approved') bg-green-100 text-green-700
                                @elseif($quote->status === 'rejected') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ $quote->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <!-- Timeline -->
                        <div class="mb-8">
                            @php
                                $steps = [
                                    'submitted' => [
                                        'label' => 'Quote Submitted',
                                        'time'  => $quote->submitted_at?->format('d M Y, H:i') ?? $quote->created_at->format('d M Y, H:i'),
                                        'active' => true,
                                    ],
                                    'reviewed' => [
                                        'label' => 'Under Review',
                                        'time'  => 'With our sales team',
                                        'active' => in_array($quote->status, ['submitted']),
                                        'done'   => in_array($quote->status, ['approved', 'rejected']),
                                    ],
                                    'resolved' => null,
                                ];

                                if ($quote->status === 'approved') {
                                    $steps['reviewed'] = [
                                        'label' => 'Reviewed',
                                        'time'  => $quote->approved_at?->format('d M Y, H:i'),
                                        'done'  => true,
                                    ];
                                    $steps['resolved'] = [
                                        'label' => 'Approved',
                                        'time'  => $quote->approved_at?->format('d M Y, H:i'),
                                        'done'  => true,
                                        'note'  => 'Our team will be in touch to confirm next steps.',
                                    ];
                                } elseif ($quote->status === 'rejected') {
                                    $steps['reviewed'] = [
                                        'label' => 'Reviewed',
                                        'time'  => $quote->updated_at?->format('d M Y, H:i'),
                                        'done'  => true,
                                    ];
                                    $steps['resolved'] = [
                                        'label' => 'Not Approved',
                                        'time'  => $quote->updated_at?->format('d M Y, H:i'),
                                        'done'  => true,
                                        'note'  => $quote->rejection_reason ?? 'Please contact our sales team for more details.',
                                    ];
                                }
                            @endphp
                            <ol class="relative border-l border-gray-200 ml-3 space-y-6">
                                @foreach($steps as $step)
                                    @if(!$step || !isset($step['label']))
                                        @continue
                                    @endif
                                    <li class="ml-6">
                                        <span class="absolute -left-3.5 mt-1 flex h-7 w-7 items-center justify-center rounded-full ring-4 ring-white
                                            {{ ($step['done'] ?? false) ? 'bg-green-500' : (($step['active'] ?? false) ? 'bg-copower-banner' : 'bg-gray-200') }}">
                                            @if($step['done'] ?? false)
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span class="block w-2.5 h-2.5 rounded-full {{ ($step['active'] ?? false) ? 'bg-copower-dark' : 'bg-gray-300' }}"></span>
                                            @endif
                                        </span>
                                        <div>
                                            <p class="font-semibold text-copower-dark text-sm">{{ $step['label'] }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $step['time'] ?? '' }}</p>
                                            @if(!empty($step['note']))
                                                <p class="text-xs text-gray-600 mt-1">@if(($step['done'] ?? false) && ($quote->status === 'approved')){{ $step['note'] }}@elseif(($step['done'] ?? false) && ($quote->status === 'rejected')){{ $step['note'] }}@endif</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <!-- Summary -->
                        <div class="bg-copower-gray rounded-xl p-5 mb-6">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Company</p>
                                    <p class="font-semibold text-copower-dark">{{ $quote->customer_company }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Submitted</p>
                                    <p class="font-semibold text-copower-dark">{{ $quote->submitted_at?->format('d M Y, H:i') ?? $quote->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Items</p>
                                    <p class="font-semibold text-copower-dark">{{ is_array($quote->items) ? count($quote->items) : 0 }} product(s)</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Quote Total</p>
                                    <p class="font-semibold text-copower-dark text-base font-bold">&pound;{{ number_format((float) $quote->grand_total, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        @if(is_array($quote->items) && count($quote->items) > 0)
                        <h3 class="text-sm font-bold text-copower-dark uppercase tracking-wider mb-3">Items</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-sm">
                                <thead class="bg-copower-gray">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-xs font-bold text-copower-dark uppercase">Product</th>
                                        <th class="px-4 py-2.5 text-center text-xs font-bold text-copower-dark uppercase">Qty</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-bold text-copower-dark uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($quote->items as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-copower-dark">{{ $item['product_name'] ?? 'Product' }}</span>
                                                <span class="block text-xs text-gray-500">{{ $item['variant_type'] ?? 'Unit' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-600">{{ $item['quantity'] ?? 0 }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-copower-dark">&pound;{{ number_format((float) ($item['total'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Not Found -->
                <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-copower-dark">No Quote Found</h2>
                    <p class="text-sm text-gray-500 mt-2">
                        We couldn't find a quote matching that reference and email address. Please double-check your details and try again.
                    </p>
                </div>
            @endif
        @endif

        <!-- Help -->
        <div class="text-center mt-8">
            <p class="text-xs text-gray-400">
                Having trouble tracking your quote? Email us at
                <a href="mailto:{{ config('mail.from.address') }}" class="text-copower-banner hover:underline">{{ config('mail.from.address') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection