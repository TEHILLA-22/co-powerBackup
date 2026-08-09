{{-- resources/views/emails/new-quote.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Admin</span>
    </div>
@endcomponent
@endslot

# New Quote Request

A customer has submitted a new quote for review.

**Quote Number:** {{ $quote->quote_number }}
**Company:** {{ $quote->customer_company }}
**Customer:** {{ $quote->customer_email }}
**Tier:** {{ $quote->customer_tier }}
**Total:** **£{{ number_format((float) $quote->grand_total, 2) }}**
**Submitted:** {{ $quote->submitted_at?->format('d M Y H:i') ?? $quote->created_at->format('d M Y H:i') }}

@component('mail::button', ['url' => $quoteUrl])
Review Quote
@endcomponent

Thank you,<br>
**Copower Wholesale System**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent