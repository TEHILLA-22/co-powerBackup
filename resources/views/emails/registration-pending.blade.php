{{-- resources/views/emails/registration-pending.blade.php --}}
@component('mail::layout')
@slot('header')
	@include('emails.partials.header')
@endslot

	@include('emails.partials.container-start')

	<h1 style="font-size:18px;margin:0 0 8px;">Welcome to Copower Wholesale!</h1>
	<p style="margin:0 0 12px;color:#475569;">Dear {{ $name }},</p>

	<p style="margin:0 0 12px;color:#475569;">Thank you for registering with Copower Wholesale. We have received your application and it is now pending approval.</p>

	<p style="margin:0 0 12px;color:#0b2540;font-weight:600;">Company: {{ $company }}</p>

	<p style="margin:0 0 12px;color:#475569;">Our team will review your application within 1-2 business days. You will receive a confirmation email once your account is approved.</p>

	<p style="margin:0 0 12px;color:#475569;">If you have any questions, please contact our support team.</p>

	<div style="text-align:center;margin:18px 0;">
		@component('mail::button', ['url' => route('home'), 'color' => 'primary'])
			Visit Our Store
		@endcomponent
	</div>

	<p style="margin:18px 0 0;color:#6b7280;">Thanks,<br>The Copower Wholesale Team</p>

	@include('emails.partials.container-end')

@slot('footer')
	@include('emails.partials.footer')
@endslot
@endcomponent