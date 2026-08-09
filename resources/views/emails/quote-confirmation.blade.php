{{-- resources/views/emails/quote-confirmation.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Wholesale</span>
    </div>
@endcomponent
@endslot

# Quote {{ $quote->quote_number }} Received

Hello {{ $user->full_name }},

We have received your quote request and it is now with our sales team for review. You will receive an update by email once it has been processed.

## Summary

**Date:** {{ $quote->created_at->format('d M Y H:i') }}
**Items:** {{ is_array($quote->items) ? count($quote->items) : 0 }}
**Quote Total:** **£{{ number_format((float) $quote->grand_total, 2) }}**

@if(is_array($quote->items) && count($quote->items) > 0)
| Product | Qty | Total |
| --- | ---: | ---: |
@foreach($quote->items as $item)
| {{ $item['product_name'] ?? 'Product' }} | {{ $item['quantity'] ?? 0 }} | £{{ number_format((float) ($item['total'] ?? 0), 2) }} |
@endforeach
@endif

If you have any questions about this quote, please reply to this email or contact our sales team.

@component('mail::button', ['url' => $quoteUrl])
View Your Quote
@endcomponent

Thank you for choosing Copower Wholesale.<br>
**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent