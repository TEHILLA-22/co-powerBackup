{{-- resources/views/emails/order-rejected.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Wholesale</span>
    </div>
@endcomponent
@endslot

# Update on Your Order

Hello {{ $user->full_name }},

Unfortunately, we are unable to proceed with your order **{{ $order->order_number }}** at this time.

## Reason

> {{ $reason }}

If you have any questions or would like to discuss this further, please reply to this email or contact our sales team. We'd be happy to help you find an alternative.

@component('mail::button', ['url' => $productsUrl])
Browse Products
@endcomponent

Thank you for your understanding,<br>
**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent