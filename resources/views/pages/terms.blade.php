{{-- resources/views/pages/terms.blade.php --}}
@extends('layouts.app')

@section('title', 'Terms and Conditions of Website Usage')

@section('meta_description', 'Terms and conditions of website usage for Copower Wholesale.')

@section('content')
    {{-- Trust Banner (consistent across all pages) --}}
    @include('partials.trust-banner')

    {{-- Page Title Area --}}
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-black text-copower-dark">Terms and Conditions of Website</h1>
            <p class="mt-1 text-sm text-gray-500">Last updated: {{ \Illuminate\Support\Carbon::now()->format('j F Y') }}</p>
        </div>
    </section>

    {{-- Content (single text-editor style block, matching reference layout) --}}
    <section class="py-10">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-xl shadow-sm p-8 md:p-12">
                <p class="text-sm text-gray-600 leading-relaxed">
                    Welcome to the {{ config('app.name', 'Copower Wholesale Ltd') }} website. If you continue to browse and
                    use this website, you are agreeing to compile with and be bound by the following terms and conditions of
                    use, which together with our privacy policy govern
                    {{ config('app.name', 'Copower Wholesale Ltd') }}'s relationship with you in relation to this website. If you
                    disagree with any part of these terms and conditions, please do not use our website.
                </p>

                <p class="mt-5 text-sm text-gray-600 leading-relaxed">
                    The term "{{ config('app.name', 'Copower Wholesale') }}" or "us" or "we" refers to the owner of the website.
                    The term "you" refers to the user or viewer of our website.
                </p>

                <p class="mt-5 text-sm text-gray-600 leading-relaxed">
                    The use of this website is subject to the following terms of use:
                </p>

                <ul class="mt-4 space-y-3 text-sm text-gray-600 leading-relaxed list-disc pl-5">
                    <li>
                        The content of the pages of this website is for your general information and use only. It is subject to
                        change without notice. We reserve the right to modify, suspend or withdraw the website, any product,
                        price, discount or tier at any time.
                    </li>
                    <li>
                        This website operates a business-to-business wholesale model. Products and services are intended for
                        registered wholesale customers aged 18 and over, trading in their own name or on behalf of a business.
                    </li>
                    <li>
                        Registration is subject to verification. Your request for an account is verified at registration, and your
                        account is fully verified once our staff approve your first order. We may reject or suspend registration
                        for trading or verify the information you provide is inaccurate.
                    </li>
                    <li>
                        Neither we nor any third party provides any warranty or guarantee as to the accuracy, timeliness,
                        performance, completeness or suitability of the information and materials found or offered on this
                        website for any particular purpose. You acknowledge that such information, including the prices,
                        discounts and tiers displayed, may contain inaccuracies or errors, and we expressly exclude liability for
                        any such inaccuracies or errors to the fullest extent permitted by law.
                    </li>
                    <li>
                        Your use of any information or materials on this website is entirely at your own risk, for which we shall
                        not be liable. It shall be your own responsibility to ensure that any products, services or information
                        available through this website meet your specific requirements.
                    </li>
                    <li>
                        This website contains material which is owned by or licensed to us. This material includes, but is not
                        limited to, the design, layout, look, appearance, graphics, logos and product images. Any reproduction
                        is prohibited other than with written permission.
                    </li>
                    <li>
                        Unauthorised use, or the submission of false or misleading personal or business information, may give rise
                        to a claim for damages and/or be a criminal offence.
                    </li>
                    <li>
                        This website may include links to other websites. We provide these links for user convenience. We have
                        no responsibility for the content of the linked website(s), and their presence does not imply an
                        endorsement of the linked site.
                    </li>
                    <li>
                        Your use of this website and any dispute arising out of such use is subject to the laws of England,
                        Northern Ireland, Scotland and Wales.
                    </li>
                </ul>
            </div>
        </div>
    </section>
@endsection