{{-- resources/views/emails/otp-verification.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

# Hello {{ $name }},

Welcome to **Copower Wholesale**. To verify your business email and activate your account, please use the 6-digit code below:

<div style="background: #f3f4f6; padding: 16px; text-align: center; border-radius: 8px; margin: 20px 0;">
    <span style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #0F3D5E; font-family: monospace;">
        {{ $otp }}
    </span>
</div>

<p style="text-align: center; font-size: 14px; color: #6b7280;">
    This code will expire in <strong>{{ $expiry_minutes }} minutes</strong>.
</p>

@component('mail::button', ['url' => route('auth.verify-otp')])
Verify Your Email
@endcomponent

If you did not create an account, please ignore this email.

Thank you,<br>
**Copower Wholesale Team**

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent