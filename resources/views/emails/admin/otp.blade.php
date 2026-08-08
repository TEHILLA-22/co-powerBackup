{{-- resources/views/emails/admin/otp.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Admin Verification</span>
    </div>
@endcomponent
@endslot

# Hello {{ $name }},

Thank you for registering as an admin for Copower Wholesale.

To complete your registration, please use the verification code below:

<div style="background: #f3f4f6; padding: 16px; text-align: center; border-radius: 8px; margin: 20px 0;">
    <span style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #0F3D5E; font-family: monospace;">
        {{ $otp }}
    </span>
</div>

<p style="text-align: center; font-size: 14px; color: #6b7280;">
    This code will expire in <strong>{{ $expiry_minutes }} minutes</strong>.
</p>

@component('mail::button', ['url' => route('admin.verify')])
Verify Your Email
@endcomponent

If you did not request this, please ignore this email.

Thank you,<br>
**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
    <br>
    <small style="color: #6b7280;">
        This is an automated message. Please do not reply to this email.
    </small>
@endcomponent
@endslot
@endcomponent