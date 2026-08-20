{{-- resources/views/emails/admin/new-registration.blade.php --}}
@component('mail::layout')
@slot('header')
    @include('emails.partials.header')
@endslot

    @include('emails.partials.container-start')

    <h2 style="margin:0 0 8px;font-size:16px;color:#0b2540;">New Customer Registration</h2>
    <p style="margin:0 0 12px;color:#475569;">A new customer has just created an account.</p>

    <div style="margin:8px 0 12px;color:#475569;">
        <div><strong>Company:</strong> {{ $user->company_name }}</div>
        <div><strong>Contact:</strong> {{ $user->first_name }} {{ $user->last_name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Phone:</strong> {{ $user->mobile ?? $user->phone ?? 'N/A' }}</div>
        <div><strong>Registered:</strong> {{ $user->created_at->format('d M Y H:i') }}</div>
    </div>

    <div style="text-align:center;margin:14px 0;">
        @component('mail::button', ['url' => $userUrl, 'color' => 'primary'])
            Review Customer
        @endcomponent

        @component('mail::button', ['url' => $adminUrl])
            View Pending Customers
        @endcomponent
    </div>

    <p style="margin:18px 0 0;color:#6b7280;">Thank you,<br><strong>Copower Wholesale System</strong></p>

    @include('emails.partials.container-end')

@slot('footer')
    @include('emails.partials.footer')
@endslot
@endcomponent