{{-- resources/views/emails/order-rejected.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h1 style="font-size:18px;margin:0 0 8px;">Update on Your Order</h1>

    <p style="margin:0 0 12px;color:#475569;">Hello {{ $user->full_name }},</p>

    <p style="margin:0 0 12px;color:#475569;">Unfortunately, we are unable to proceed with your order <strong>{{ $order->order_number }}</strong> at this time.</p>

    <div style="background:#fff5f5;border:1px solid #fee2e2;padding:12px;border-radius:8px;margin:12px 0;color:#991b1b;">
        <strong>Reason</strong>
        <div style="margin-top:6px;">{{ $reason }}</div>
    </div>

    <p style="margin:0 0 12px;color:#475569;">If you have any questions or would like to discuss this further, please reply to this email or contact our sales team.</p>

    <div style="text-align:center;margin:14px 0;">
        @component('mail::button', ['url' => $productsUrl, 'color' => 'primary'])
            Browse Products
        @endcomponent
    </div>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you for your understanding,<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent