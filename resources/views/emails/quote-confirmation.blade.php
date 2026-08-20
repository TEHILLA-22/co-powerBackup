{{-- resources/views/emails/quote-confirmation.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    <!-- Hero image placeholder - drop your brand hero/email banner image here once ready -->
    @include('emails.partials.container-start')
@if(file_exists(public_path('images/email-hero.png')))
<div style="width: 100%; margin: 0 0 24px 0;">
    <img src="{{ asset('images/email-hero.png') }}" alt="Copower Wholesale" style="width: 100%; max-width: 600px; max-height: 240px; object-fit: cover; border-radius: 12px; display: block;">
</div>
@else
<div style="background: linear-gradient(135deg, #0F3D5E 0%, #00A3E0 100%); border-radius: 12px; padding: 32px 24px; text-align: center; margin: 0 0 24px 0;">
    <div style="font-size: 12px; letter-spacing: 2px; color: #7DD3F5; text-transform: uppercase; margin-bottom: 8px;">Quote Confirmation</div>
    <div style="font-size: 28px; font-weight: 900; color: #ffffff; letter-spacing: 1px; line-height: 1.3;">{{ $quote->quote_number }}</div>
    <div style="font-size: 13px; color: #BFEBFA; margin-top: 6px;">Keep this reference to track your quote anytime.</div>
</div>
@endif

# Quote {{ $quote->quote_number }} Received

Hello {{ $user->full_name }},

We have received your quote request and it is now with our sales team for review. You will receive an update by email once it has been processed.

<div style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 12px; padding: 20px 24px; margin: 24px 0;">
    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <tr style="background: #E0F2FE; border-radius: 8px;">
            <td style="padding: 12px 16px; font-weight: 600; color: #0F3D5E; border-bottom: 1px solid #BAE6FD;">Summary</td>
            <td style="padding: 12px 16px; font-weight: 600; color: #0F3D5E; border-bottom: 1px solid #BAE6FD; text-align: right;">Total</td>
        </tr>
        <tr>
            <td style="padding: 12px 16px; color: #334155;">
                <div style="font-size: 13px; color: #64748B;">Submitted</div>
                <div style="font-weight: 600;">{{ $quote->submitted_at?->format('d M Y, H:i') ?? $quote->created_at->format('d M Y, H:i') }}</div>
            </td>
            <td style="padding: 12px 16px; text-align: right;">
                <div style="font-size: 13px; color: #64748B;">Items</div>
                <div style="font-weight: 600;">{{ is_array($quote->items) ? count($quote->items) : 0 }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 16px; color: #334155; border-top: 1px solid #BAE6FD;">
                <div style="font-size: 13px; color: #64748B;">Valid Until</div>
                <div style="font-weight: 600;">{{ $quote->valid_until?->format('d M Y') ?? '7 days' }}</div>
            </td>
            <td style="padding: 12px 16px; text-align: right; border-top: 1px solid #BAE6FD;">
                <div style="font-size: 13px; color: #64748B;">Quote Total</div>
                <div style="font-size: 22px; font-weight: 800; color: #0F3D5E;">£{{ number_format((float) $quote->grand_total, 2) }}</div>
            </td>
        </tr>
    </table>
</div>

## Your Order Details

@if(is_array($quote->items) && count($quote->items) > 0)
<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <thead>
        <tr style="background-color: #f3f4f6;">
            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Product</th>
            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">SKU / EAN</th>
            <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Qty</th>
            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e5e7eb;">Unit Price</th>
            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e5e7eb;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quote->items as $item)
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; font-weight: 500;">
                {{ $item['product_name'] ?? 'Product' }}
                <div style="font-size: 12px; color: #6b7280; font-weight: normal;">
                    {{ $item['variant_type'] ?? 'Unit' }}
                </div>
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; font-family: monospace; font-size: 13px;">
                {{ $item['sku'] ?? '' }}
                <div style="font-size: 11px; color: #9ca3af; font-family: sans-serif;">{{ $item['ean'] ?? '' }}</div>
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; text-align: center;">
                {{ $item['quantity'] ?? 0 }}
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; text-align: right;">
                £{{ number_format((float) ($item['unit_price'] ?? 0), 2) }}
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; text-align: right; font-weight: 600; color: #0F3D5E;">
                £{{ number_format((float) ($item['total'] ?? 0), 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if((float) $quote->discount_total > 0)
        <tr>
            <td colspan="4" style="padding: 12px; text-align: right; font-weight: 500; border-top: 2px solid #e5e7eb;">Discount</td>
            <td style="padding: 12px; text-align: right; font-weight: 600; border-top: 2px solid #e5e7eb; color: #b91c1c;">
                -£{{ number_format((float) $quote->discount_total, 2) }}
            </td>
        </tr>
        @endif
        <tr>
            <td colspan="4" style="padding: 12px; text-align: right; font-weight: 600; border-top: 2px solid #e5e7eb; font-size: 15px;">Grand Total (excl. VAT)</td>
            <td style="padding: 12px; text-align: right; font-weight: 800; border-top: 2px solid #e5e7eb; color: #0F3D5E; font-size: 16px;">
                £{{ number_format((float) $quote->grand_total, 2) }}
            </td>
        </tr>
    </tfoot>
</table>
@endif

---

## Customer Information

| | |
|---|---|
| **Company** | {{ $user->company_name }} |
| **Contact** | {{ $user->full_name }} |
| **Email** | {{ $user->email }} |
| **Telephone** | {{ $user->phone ?? $user->mobile ?? 'N/A' }} |
| **Submitted** | {{ $quote->submitted_at?->format('d M Y, H:i') ?? $quote->created_at->format('d M Y, H:i') }} |

@if(!empty($quote->customer_notes))
---

## Notes

{{ $quote->customer_notes }}
@endif

@component('mail::button', ['url' => $trackResultUrl])
Track Your Quote
@endcomponent

If you have any questions about this quote, please reply to this email or contact our sales team at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> or call us at {{ config('app.phone', '+44 20 1234 5678') }}.

Thank you for choosing Copower Wholesale.<br>
**Copower Wholesale Team**

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent