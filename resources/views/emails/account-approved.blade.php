{{-- resources/views/emails/account-approved.blade.php --}}
<x-mail::message>
# Account Approved!

Dear {{ $name }},

Great news! Your Copower Wholesale account has been approved.

You can now:
- View wholesale prices
- Place bulk orders
- Request quotes
- Track your orders

<x-mail::button :url="$loginUrl">
Login to Your Account
</x-mail::button>

Thanks,<br>
The Copower Wholesale Team
</x-mail::message>