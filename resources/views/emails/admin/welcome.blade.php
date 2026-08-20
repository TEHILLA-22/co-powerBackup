{{-- resources/views/emails/admin/welcome.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h1 style="font-size:18px;margin:0 0 8px;">Welcome, {{ $name }}!</h1>

    <p style="margin:0 0 12px;color:#475569;">Your admin account for the <strong>Copower Wholesale</strong> panel has been created successfully.</p>

    <p style="margin:0 0 12px;color:#475569;"><strong>Your role:</strong> {{ ucfirst($admin->role ?? 'admin') }}</p>

    <div style="text-align:center;margin:18px 0;">
        @component('mail::button', ['url' => $loginUrl, 'color' => 'primary'])
            Login to Admin Panel
        @endcomponent
    </div>

    <p style="margin:0 0 12px;color:#475569;">For security reasons, you will be asked to set a new password on your first login.</p>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you,<br><strong>Copower Wholesale Team</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent