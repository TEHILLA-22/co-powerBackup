{{-- resources/views/emails/order-approved.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Wholesale</span>
    </div>
@endcomponent
@endslot

# Order Approved!

Hello {{ $user->full_name }},

We're pleased to confirm that your order **{{ $order->order_number }}** has been **approved** and is now being prepared.

## Order Summary

**Order:** {{ $order->order_number }}
**Date:** {{ $order->submitted_at?->format('d M Y H:i') ?? $order->created_at->format('d M Y H:i') }}
**Total:** **£{{ number_format((float) $order->grand_total, 2) }}**
**Status:** {{ strtoupper($order->status) }}

@if($order->tracking_number)
Your tracking number is: **{{ $order->tracking_number }}** ({{ $order->carrier ?? 'Carrier' }})
@endif

@component('mail::button', ['url' => $orderUrl])
View Your Order
@endcomponent

Thank you for your business,<br>
**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent