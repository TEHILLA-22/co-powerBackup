{{-- resources/views/pages/privacy-policy.blade.php --}}
@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('meta_description', 'Privacy policy for Copower Wholesale, explaining what personal information we collect and how we use it.')

@section('content')
    {{-- Trust Banner (consistent across all pages) --}}
    @include('partials.trust-banner')

    {{-- Page Title Area --}}
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-black text-copower-dark">Privacy Policy</h1>
            <p class="mt-1 text-sm text-gray-500">Last updated: {{ \Illuminate\Support\Carbon::now()->format('j F Y') }}</p>
        </div>
    </section>

    {{-- Content (single text-editor style block, matching reference layout) --}}
    <section class="py-10">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-xl shadow-sm p-8 md:p-12">
                <p class="text-sm text-gray-600 leading-relaxed">
                    This policy explains what personal information {{ config('app.name', 'Copower Wholesale Ltd') }} ("we", "us")
                    collects, why we collect it, how we use it, and the choices you have. It applies to the wholesale platform and
                    related services offered through our website, and to the account you register with us. By using the website you
                    agree to the practices described below, which together with our terms and conditions govern our relationship.
                </p>

                <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                    The use of this website is subject to the following privacy terms:
                </p>

                <ul class="mt-4 space-y-3 text-sm text-gray-600 leading-relaxed list-disc pl-5">
                    <li>
                        <strong>Information we collect.</strong> To operate your wholesale account we collect your first and last
                        name, email address, telephone and mobile numbers, company name, company registration number and VAT
                        number (where provided), your billing and delivery address, and account preferences such as language,
                        currency and timezone. We also store a one-way-hashed password, keep records of your quotations, orders,
                        order history and customer tier, and record technical information such as your IP address, browser, device
                        and session identifiers, and login activity.
                    </li>
                    <li>
                        <strong>How we use it.</strong> We use this information to create and manage your account, to verify your
                        registration through an email or one-time verification code, to complete and approve your orders (your
                        account is fully verified once our staff approve your first order), to apply your correct wholesale pricing
                        and discount tier, to contact you about your account and orders, to protect against fraud and misuse, and
                        to comply with our legal, accounting and tax obligations.
                    </li>
                    <li>
                        <strong>Passwords.</strong> Your password is stored as a one-way hash so that it can never be read in
                        plain text. You are responsible for keeping your credentials confidential and for activity on your account.
                    </li>
                    <li>
                        <strong>Account security.</strong> We apply sign-in rate limiting and automatic account locks after repeated
                        failed attempts, and restrict platform access to authorised staff only, to protect your account and our
                        systems.
                    </li>
                    <li>
                        <strong>Cookies and similar technologies.</strong> We use strictly necessary cookies and session tokens
                        to keep you signed in, to secure your login (such as CSRF protection) and to remember form details. We do
                        not use these mechanisms to display targeted advertising, and we do not sell your personal information.
                    </li>
                    <li>
                        <strong>Who we share it with.</strong> We share your information only within our own business with staff
                        who need it to service your account and fulfill your orders, with service providers (such as email/message
                        delivery and hosting) acting on our behalf and required to keep your information confidential, and where we
                        are required or permitted to do so by law.
                    </li>
                    <li>
                        <strong>Retention.</strong> We keep your information for as long as your account is active and for as long
                        as we are required to keep it for our records and legal, accounting and tax purposes. When it is no longer
                        needed, we delete or anonymise it.
                    </li>
                    <li>
                        <strong>Your rights.</strong> Subject to applicable law, you may request access to, a copy of, correction,
                        or deletion of your personal information, require that we restrict or object to certain processing, or
                        receive the information you have provided in a portable format, by contacting us. Deletion is subject to our
                        legal record-keeping obligations.
                    </li>
                    <li>
                        <strong>Contact.</strong> To exercise any of these rights or if you have questions about this policy, contact
                        {{ config('app.name', 'Copower Wholesale Ltd') }} at
                        <a href="mailto:{{ config('b2b.admin_notification_emails')[0] ?? 'support@copowerwholesale.com' }}" class="text-copower-banner underline">
                            {{ config('b2b.admin_notification_emails')[0] ?? 'support@copowerwholesale.com' }}
                        </a>.
                    </li>
                </ul>
            </div>
        </div>
    </section>
@endsection