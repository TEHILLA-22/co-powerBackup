{{-- resources/views/pages/faq.blade.php --}}
@extends('layouts.app')

@section('title', 'Frequently Asked Questions - Copower Wholesale')

@section('meta_description', 'Answers to common questions about Copower Wholesale: opening an account, minimum orders, pricing, delivery and international shipping.')

@push('styles')
<style>
    .faq-head { border-bottom: 2px solid #e5e7eb; }
    .faq-item { border: 1px solid #e5e7eb; border-radius: .75rem; overflow: hidden; }
    .faq-q { transition: color .2s ease, background-color .2s ease; }
    .faq-q:hover { color: #00A3E0; }
    .faq-item.is-open { border-color: #00A3E0; box-shadow: 0 10px 30px -12px rgba(0,163,224,.35); }
    .faq-chevron { transition: transform .3s cubic-bezier(.16,.84,.44,1); }
    .faq-item.is-open .faq-chevron { transform: rotate(180deg); }
</style>
@endpush

@section('content')
    {{-- Trust Banner (consistent across all pages) --}}
    @include('partials.trust-banner')

    {{-- ===== Page Hero ===== --}}
    <section class="relative bg-copower-dark text-white overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background: radial-gradient(ellipse at 20% 0%, #00A3E0 0%, transparent 60%);"></div>
        <div class="relative max-w-7xl mx-auto px-6 py-16 md:py-20">
            <nav class="text-xs uppercase tracking-widest text-copower-banner font-semibold space-x-2">
                <span>Home</span><span>/</span><span>FAQ</span>
            </nav>
            <h1 class="mt-4 text-4xl md:text-5xl font-black leading-tight">Frequently Asked Questions</h1>
            <p class="mt-4 max-w-2xl text-lg text-copower-gray/90">Answers to the questions our wholesale customers ask most often. Can't find what you need? Our team is happy to help.</p>
        </div>
    </section>

    {{-- ===== Intro ===== --}}
    <section class="py-10 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl font-bold text-copower-dark">We're here to help</h2>
            <p class="mt-2 max-w-3xl text-gray-600">We've compiled answers to our customers' most common questions. If you can't find what you're looking for, don't hesitate to get in touch and we'll respond as quickly as we can.</p>
        </div>
    </section>

    {{-- ===== FAQ Accordion ===== --}}
    <section class="py-16 bg-copower-gray">
        <div class="max-w-4xl mx-auto px-6" x-data="{ open: null }">
            @php
                $groups = [
                    'Accounts & Registration' => [
                        ['How do I open a wholesale account?', 'Sign up through the registration page with your contact and business details. Your registration is verified by email or a one-time code, after which you can browse the catalogue and place orders. Your account is fully verified once our team approves your first order.'],
                        ['What information do I need to provide?', 'We ask for your name, email, telephone and mobile numbers, your company name, company registration and VAT numbers (where applicable), and your billing and delivery address. This lets us set up your wholesale account and price your orders correctly.'],
                        ['Are there account approval requirements?', 'Yes. Accounts are business-to-business wholesale accounts. We verify your registration before you can order, and we review and approve each order you place with us.'],
                    ],
                    'Minimum Orders & Pricing' => [
                        ['What is your minimum order value?', 'Our minimum order value (MOQ) is £5,000. Orders at or above the MOQ qualify for our free UK delivery terms.'],
                        ['Are prices inclusive of VAT?', 'No. Our wholesale prices are quoted excluding VAT, which is applied at the prevailing rate where applicable.'],
                        ['Do you offer trade discounts?', 'Yes. Customers are assigned a discount tier (Standard, Premium or VIP). The applicable discount is reflected on your quotations and orders.'],
                        ['Are wholesale prices competitive?', 'Our aim is to offer fair, competitive wholesale pricing across our range, backed by clear, tier-based pricing on every quotation.'],
                    ],
                    'Orders & Delivery' => [
                        ['Can I place an order online?', 'Yes. You can build a quotation online and submit it, and our team will confirm your order and delivery details with you.'],
                        ['How quickly are orders processed and delivered?', 'Stock orders are processed within 72 hours, subject to availability. Delivery times for deals or supply-chain items may follow the specific offer.'],
                        ['How are orders approved?', 'Our admin team reviews each order before it is fulfilled. When we approve your first order, your wholesale account is fully verified.'],
                        ['Can you source products that are not listed?', 'Often, yes. If a product you need is not listed, contact us with the details and we will do our best to source it for you.'],
                    ],
                    'Shipping & Export' => [
                        ['Do you deliver outside the UK?', 'Yes, we export globally. Minimum order and delivery terms vary by destination, and our team can advise on the specifics for your location.'],
                        ['Can you consolidate shipments with other orders?', 'Yes, we can consolidate orders to any destination. Get in touch and we will arrange the most efficient option for you.'],
                        ['Can you help with import documentation?', 'In many cases we can assist with the import documents your country requires. Please contact us to confirm what you need before ordering.'],
                    ],
                ];
            @endphp

            @php $faqIndex = 0; @endphp
            @foreach($groups as $groupTitle => $questions)
                <div class="mb-12">
                    <h2 class="faq-head text-xl md:text-2xl font-black text-copower-dark pb-3 mb-6 flex items-center gap-3">
                        <span class="inline-block w-1.5 h-6 rounded-full bg-copower-banner"></span>
                        {{ $groupTitle }}
                    </h2>
                    <ul class="space-y-4">
                        @foreach ($questions as $faq)
                            @php $faqIndex++; $i = $faqIndex; @endphp
                            <li class="faq-item bg-white transition-all duration-300" :class="open === {{ $i }} ? 'is-open' : ''">
                                <button type="button"
                                        class="faq-q w-full flex items-center justify-between gap-4 text-left px-6 py-5"
                                        :aria-expanded="open === {{ $i }}"
                                        @click="open = (open === {{ $i }}) ? null : {{ $i }}">
                                    <span class="font-semibold text-copower-dark">{{ $faq[0] }}</span>
                                    <span class="faq-chevron shrink-0 w-8 h-8 rounded-full bg-copower-gray flex items-center justify-center text-copower-dark">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                    </span>
                                </button>
                                <div x-show="open === {{ $i }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    <div class="px-6 pb-6 text-gray-600 leading-relaxed">{{ $faq[1] }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===== CTA band ===== --}}
    <section class="bg-copower-dark text-white">
        <div class="max-w-7xl mx-auto px-6 py-16 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-black">Still have a question?</h2>
                <p class="mt-2 text-copower-gray/90 max-w-xl">Our team is ready to help with account, order and pricing questions. Get in touch or register to start ordering.</p>
            </div>
            <div class="flex flex-wrap gap-4 shrink-0">
                <a href="{{ route('register') }}" class="bg-copower-banner text-white px-7 py-3 rounded-lg font-semibold hover:bg-white hover:text-copower-dark transition">Become a Customer</a>
                <a href="{{ route('about') }}" class="border-2 border-white/80 text-white px-7 py-3 rounded-lg font-semibold hover:bg-white hover:text-copower-dark transition">Contact Us</a>
            </div>
        </div>
    </section>
@endsection