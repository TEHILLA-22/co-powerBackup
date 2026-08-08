{{-- resources/views/emails/registration-pending.blade.php --}}
<x-mail::message>
# Welcome to Copower Wholesale!

Dear {{ $name }},

Thank you for registering with Copower Wholesale. We have received your application and it is now pending approval.

**Company:** {{ $company }}

Our team will review your application within 1-2 business days. You will receive a confirmation email once your account is approved.

If you have any questions, please contact our support team.

<x-mail::button :url="route('home')">
Visit Our Store
</x-mail::button>

Thanks,<br>
The Copower Wholesale Team
</x-mail::message>