{{-- resources/views/emails/account-rejected.blade.php --}}
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
    <div style="text-align: center;">
        <span style="font-size: 24px; font-weight: 900; color: #0F3D5E;">COPOWER</span>
        <span style="font-size: 12px; font-weight: 800; color: #00A3E0; display: block;">Wholesale</span>
    </div>
@endcomponent
@endslot

# Hello {{ $name }},

Thank you for applying for a wholesale account with **Copower Wholesale**.

After reviewing your application, we regret to inform you that we are unable to activate your account at this time.

**Reason:**
> {{ $reason }}

If you believe this is in error, or if your circumstances have changed, please contact us and we will be happy to review your application again.

[Contact Copower Wholesale]({{ $contactUrl }})

Thank you,<br>
**Copower Wholesale Team**

@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} Copower Wholesale. All rights reserved.
@endcomponent
@endslot
@endcomponent