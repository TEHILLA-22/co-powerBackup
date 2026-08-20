{{-- resources/views/emails/order-processing.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h1 style="font-size:18px;margin:0 0 8px;">Your Order is Being Processed</h1>

    <p style="margin:0 0 12px;color:#475569;">Dear {{ $user->first_name }},</p>

    <p style="margin:0 0 12px;color:#475569;">Your order <strong>#{{ $order->order_number }}</strong> is now being processed by our team. We will notify you once it is approved and ready for dispatch.</p>

    <div style="margin:12px 0;padding:12px;background:#f8fafc;border:1px solid #eef6ff;border-radius:8px;">
        <div><strong>Order #:</strong> {{ $order->order_number }}</div>
        <div><strong>Date:</strong> {{ $order->submitted_at->format('d M Y, H:i:s') }}</div>
        <div><strong>Total:</strong> £{{ number_format($order->grand_total, 2) }}</div>
    </div>

    <div style="text-align:center;margin:18px 0;">
        @component('mail::button', ['url' => route('order.confirmation', $order), 'color' => 'primary'])
            View Order Details
        @endcomponent
    </div>

    <p style="margin:12px 0 0;color:#475569;">If you have any questions, please contact us at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you for choosing Copower Wholesale!<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent