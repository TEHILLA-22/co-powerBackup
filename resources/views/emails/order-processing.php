{{-- resources/views/emails/order-processing.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <img src="{{ asset('images/copower-logo.png') }}" alt="Copower Wholesale" style="max-height: 50px;">
    </div>
@endcomponent
@endslot

# Your Order is Being Processed

Dear {{ $user->first_name }},

Your order #{{ $order->order_number }} is now being processed by our team.

We will notify you once your order has been approved and is ready for dispatch.

## Order Summary

**Order #:** {{ $order->order_number }}  
**Date:** {{ $order->submitted_at->format('d M Y, H:i:s') }}  
**Total:** £{{ number_format($order->grand_total, 2) }}

@component('mail::button', ['url' => route('order.confirmation', $order)])
View Order Details
@endcomponent

If you have any questions, please contact us at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.

Thank you for choosing Copower Wholesale!

**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent