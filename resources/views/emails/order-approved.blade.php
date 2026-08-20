{{-- resources/views/emails/order-approved.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h1 style="font-size:18px;margin:0 0 8px;">Order Approved!</h1>

    <p style="margin:0 0 12px;color:#475569;">Hello {{ $user->full_name }},</p>

    <p style="margin:0 0 12px;color:#475569;">We're pleased to confirm that your order <strong>{{ $order->order_number }}</strong> has been <strong>approved</strong> and is now being prepared.</p>

    <div style="margin:12px 0;padding:12px;background:#f8fafc;border:1px solid #eef6ff;border-radius:8px;">
        <div><strong>Order:</strong> {{ $order->order_number }}</div>
        <div><strong>Date:</strong> {{ $order->submitted_at?->format('d M Y H:i') ?? $order->created_at->format('d M Y H:i') }}</div>
        <div><strong>Total:</strong> £{{ number_format((float) $order->grand_total, 2) }}</div>
        <div><strong>Status:</strong> {{ strtoupper($order->status) }}</div>
        @if($order->tracking_number)
            <div style="margin-top:8px;color:#475569;">Tracking: <strong>{{ $order->tracking_number }}</strong> ({{ $order->carrier ?? 'Carrier' }})</div>
        @endif
    </div>

    <div style="text-align:center;margin:18px 0;">
        @component('mail::button', ['url' => $orderUrl, 'color' => 'primary'])
            View Your Order
        @endcomponent
    </div>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you for your business,<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent