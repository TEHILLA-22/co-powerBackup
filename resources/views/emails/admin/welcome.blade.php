{{-- resources/views/emails/admin/welcome.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Admin</span>
    </div>
@endcomponent
@endslot

# Welcome, {{ $name }}!

Your admin account for the **Copower Wholesale** panel has been created successfully.

Your role: **{{ ucfirst($admin->role ?? 'admin') }}**

@component('mail::button', ['url' => $loginUrl])
Login to Admin Panel
@endcomponent

For security reasons, you will be asked to set a new password on your first login.

Thank you,<br>
**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent