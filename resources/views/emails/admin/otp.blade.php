{{-- resources/views/emails/admin/otp.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h1 style="font-size:18px;margin:0 0 8px;">Admin Verification</h1>
    <p style="margin:0 0 12px;color:#475569;">Hello {{ $name }},</p>

    <p style="margin:0 0 12px;color:#475569;">Thank you for registering as an admin for Copower Wholesale. To complete your registration, please use the verification code below.</p>

    <div style="background: #f1f7fb; padding: 16px; text-align: center; border-radius: 8px; margin: 20px 0; display:inline-block;">
        <span style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #0F3D5E; font-family: monospace;">{{ $otp }}</span>
    </div>

    <p style="text-align:center;font-size:14px;color:#6b7280;margin-top:8px;">This code will expire in <strong>{{ $expiry_minutes }} minutes</strong>.</p>

    <div style="text-align:center;margin:16px 0;">
        @component('mail::button', ['url' => route('admin.verify'), 'color' => 'primary'])
            Verify Your Email
        @endcomponent
    </div>

    <p style="margin:12px 0 0;color:#6b7280;">If you did not request this, please ignore this email.</p>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you,<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent
