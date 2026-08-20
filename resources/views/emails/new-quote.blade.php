{{-- resources/views/emails/new-quote.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h2 style="margin:0 0 8px;font-size:16px;color:#0b2540;">New Quote Request</h2>
    <p style="margin:0 0 12px;color:#475569;">A customer has submitted a new quote for review.</p>

    <div style="margin:8px 0 12px;color:#475569;">
        <div><strong>Quote Number:</strong> {{ $quote->quote_number }}</div>
        <div><strong>Company:</strong> {{ $quote->customer_company }}</div>
        <div><strong>Customer:</strong> {{ $quote->customer_email }}</div>
        <div><strong>Tier:</strong> {{ $quote->customer_tier }}</div>
        <div><strong>Total:</strong> £{{ number_format((float) $quote->grand_total, 2) }}</div>
        <div><strong>Submitted:</strong> {{ $quote->submitted_at?->format('d M Y H:i') ?? $quote->created_at->format('d M Y H:i') }}</div>
    </div>

    <div style="text-align:center;margin:18px 0;">
        @component('mail::button', ['url' => $quoteUrl, 'color' => 'primary'])
            Review Quote
        @endcomponent
    </div>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you,<br><strong>Copower Wholesale System</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent