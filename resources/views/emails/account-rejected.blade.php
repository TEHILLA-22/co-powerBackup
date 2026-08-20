{{-- resources/views/emails/account-rejected.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h1 style="font-size:18px;margin:0 0 8px;">Account Update</h1>
    <p style="margin:0 0 12px;color:#475569;">Hello {{ $name }},</p>

    <p style="margin:0 0 12px;color:#475569;">Thank you for applying for a wholesale account with <strong>Copower Wholesale</strong>.</p>

    <p style="margin:0 0 12px;color:#475569;">After reviewing your application, we regret to inform you that we are unable to activate your account at this time.</p>

    <div style="background:#fff5f5;border:1px solid #fee2e2;padding:12px;border-radius:8px;margin:12px 0;color:#991b1b;">
        <strong>Reason:</strong>
        <div style="margin-top:6px;">{{ $reason }}</div>
    </div>

    <p style="margin:0 0 12px;color:#475569;">If you believe this is an error, or if your circumstances have changed, please <a href="{{ $contactUrl }}">contact us</a> and we will be happy to review your application again.</p>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you,<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent