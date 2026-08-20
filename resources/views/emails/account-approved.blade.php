{{-- resources/views/emails/account-approved.blade.php --}}
@component('mail::layout')
@slot('header')
	@include('emails.partials.header')
@endslot

	@include('emails.partials.container-start')

	<h1 style="font-size:18px;margin:0 0 8px;">Account Approved!</h1>
	<p style="margin:0 0 12px;color:#475569;">Dear {{ $name }},</p>

	<p style="margin:0 0 12px;color:#475569;">Great news! Your Copower Wholesale account has been approved.</p>

	<p style="margin:0 0 12px;color:#475569;">You can now:</p>
	<ul style="margin:0 0 12px;padding-left:18px;color:#475569;">
		<li>View wholesale prices</li>
		<li>Place bulk orders</li>
		<li>Request quotes</li>
		<li>Track your orders</li>
	</ul>

	<div style="text-align:center;margin:18px 0;">
		@component('mail::button', ['url' => $loginUrl, 'color' => 'primary'])
			Login to Your Account
		@endcomponent
	</div>

	<p style="margin:18px 0 0;color:#6b7280;">Thanks,<br>The Copower Wholesale Team</p>

	@include('emails.partials.container-end')

@slot('footer')
	@include('emails.partials.footer')
@endslot
@endcomponent