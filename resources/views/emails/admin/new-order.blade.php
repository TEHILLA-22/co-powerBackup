{{-- resources/views/emails/admin/new-order.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h2 style="margin:0 0 8px;font-size:16px;color:#0b2540;">New Order Received</h2>

    <p style="margin:0 0 12px;color:#475569;">A new order has been placed on Copower Wholesale.</p>

    <div style="margin:12px 0;padding:12px;background:#f8fafc;border:1px solid #eef6ff;border-radius:8px;">
        <div><strong>Order #:</strong> {{ $order->order_number }}</div>
        <div><strong>Customer:</strong> {{ $user->company_name }} ({{ $user->email }})</div>
        <div><strong>Date:</strong> {{ $order->submitted_at->format('d M Y, H:i:s') }}</div>
        <div><strong>Total:</strong> £{{ number_format($order->grand_total, 2) }}</div>
    </div>

    <h3 style="margin:12px 0 8px;font-size:14px;color:#0b2540;">Order Items</h3>

    <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <thead>
        <tr style="background-color: #f3f4f6;">
            <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">Product</th>
            <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">SKU</th>
            <th style="padding: 10px; text-align: center; border-bottom: 2px solid #e5e7eb;">Qty</th>
            <th style="padding: 10px; text-align: right; border-bottom: 2px solid #e5e7eb;">Price</th>
            <th style="padding: 10px; text-align: right; border-bottom: 2px solid #e5e7eb;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">
                {{ $item->product_name }}
                <div style="font-size: 12px; color: #6b7280;">{{ $item->variant_type }}</div>
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-family: monospace;">{{ $item->product_sku }}</td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: center;">{{ $item->quantity }}</td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: right;">£{{ number_format($item->unit_price, 2) }}</td>
            <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 500;">£{{ number_format($item->line_total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="padding: 10px; text-align: right; font-weight: 600; border-top: 2px solid #e5e7eb;">
                Total
            </td>
            <td style="padding: 10px; text-align: right; font-weight: 700; border-top: 2px solid #e5e7eb; color: #0F3D5E;">
                £{{ number_format($order->grand_total, 2) }}
            </td>
        </tr>
    </tfoot>
</table>

---

## Customer Information

| | |
|---|---|
| **Company** | {{ $user->company_name }} |
| **Email** | {{ $user->email }} |
| **Phone** | {{ $user->phone ?? $user->mobile ?? 'N/A' }} |
| **Address** | {{ $order->shipping_address ?? 'N/A' }} |

---

@component('mail::button', ['url' => $adminUrl])
View Order in Admin
@endcomponent

Thank you,<br>
**Copower Wholesale System**

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent