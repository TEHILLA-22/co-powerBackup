{{-- resources/views/emails/admin/new-registration.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Admin</span>
    </div>
@endcomponent
@endslot

# New Customer Registration

A new customer has just created an account.

**Company:** {{ $user->company_name }}
**Contact:** {{ $user->first_name }} {{ $user->last_name }}
**Email:** {{ $user->email }}
**Phone:** {{ $user->mobile ?? $user->phone ?? 'N/A' }}
**Registered:** {{ $user->created_at->format('d M Y H:i') }}

@component('mail::button', ['url' => $userUrl])
Review Customer
@endcomponent

@component('mail::button', ['url' => $adminUrl])
View Pending Customers
@endcomponent

Thank you,<br>
**Copower Wholesale System**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent