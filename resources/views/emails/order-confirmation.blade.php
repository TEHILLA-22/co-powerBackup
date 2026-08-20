{{-- resources/views/emails/order-confirmation.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    {{-- Body --}}
# Copower Wholesale

Thank you for your quote request from Copower Wholesale. You can check the status of your quote request by logging into your account.

Or you can check the status of your quote request by opening the track link below.

@component('mail::button', ['url' => $trackUrl])
Track Your Order
@endcomponent

Your quotation details are below. Thank you again for your interest.

If you have questions about your quote request, you can email us at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> or call us at {{ config('app.phone', '+44 20 1234 5678') }}.

---

## Quote Request #{{ $order->order_number }}
**Placed on** {{ $order->submitted_at->format('d M Y, H:i:s') }}

---

## Order Items

<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <thead>
        <tr style="background-color: #f3f4f6;">
            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Image</th>
            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Name</th>
            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">SKU</th>
            <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Qty</th>
            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e5e7eb;">Price</th>
            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e5e7eb;">Note</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle;">
                @if($item->product && $item->product->main_image)
                    <img src="{{ asset('storage/' . $item->product->main_image) }}" 
                         alt="{{ $item->product_name }}" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                @else
                    <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 12px;">
                        No Image
                    </div>
                @endif
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; font-weight: 500;">
                {{ $item->product_name }}
                <div style="font-size: 12px; color: #6b7280; font-weight: normal;">
                    {{ $item->variant_type ?? 'Unit' }}
                </div>
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; font-family: monospace; font-size: 13px;">
                {{ $item->product_sku }}
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; text-align: center;">
                {{ $item->quantity }}
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; text-align: right; font-weight: 500;">
                £{{ number_format($item->unit_price, 2) }}
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; text-align: right; font-weight: 500; color: #6b7280;">
                —
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="padding: 12px; text-align: right; font-weight: 600; border-top: 2px solid #e5e7eb;">
                Quote Subtotal (excluding VAT)
            </td>
            <td style="padding: 12px; text-align: right; font-weight: 700; border-top: 2px solid #e5e7eb; color: #0F3D5E;">
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
| **Telephone** | {{ $user->phone ?? $user->mobile ?? 'N/A' }} |
| **Street** | {{ $order->shipping_address ?? 'N/A' }} |
| **Email** | {{ $user->email }} |

    <p style="margin:18px 0 0;color:#6b7280;">Thank you,<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent