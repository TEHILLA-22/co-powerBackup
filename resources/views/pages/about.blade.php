{{-- resources/views/pages/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About Us - Copower Wholesale')

@section('meta_description', 'Copower Wholesale is a leading B2B wholesale distributor for health, beauty and pharmaceutical products across the UK and Europe.')

@push('styles')
<style>
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.9s cubic-bezier(.16,.84,.44,1), transform 0.9s cubic-bezier(.16,.84,.44,1); }
    .reveal.is-visible { opacity: 1; transform: translateY(0); }
    .kinetic-img { transition: transform 1.2s cubic-bezier(.16,.84,.44,1); }
    .reveal.is-visible .kinetic-img, .group:hover .kinetic-img { transform: scale(1.06); }
    .ticker { animation: tickerMove 30s linear infinite; }
    @keyframes tickerMove { from { transform: translateX(0); } to { transform: translateX(-50%); } }
</style>
@endpush

@section('content')
    {{-- Trust Banner (consistent across all pages) --}}
    @include('partials.trust-banner')

    {{-- ===== Cinematic Hero ===== --}}
    <section class="relative h-[72vh] min-h-[480px] overflow-hidden flex items-center">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1561715276-a2d087060f1d?auto=format&fit=crop&w=1920&q=70"
                 alt="Modern distribution warehouse"
                 class="w-full h-full object-cover motion-reduce:transform-none" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-r from-copower-dark via-copower-dark/80 to-copower-dark/20"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 w-full">
            <div class="max-w-2xl">
                <p class="reveal is-visible inline-flex items-center gap-2 text-sm font-bold tracking-widest uppercase text-copower-banner">
                    <span class="h-px w-8 bg-copower-banner"></span> Copower Wholesale
                </p>
                <h1 class="reveal is-visible mt-6 text-5xl md:text-6xl font-black text-white leading-tight">
                    Wholesale, <span class="text-copower-banner">reimagined.</span>
                </h1>
                <p class="reveal is-visible mt-6 text-lg md:text-xl text-copower-gray leading-relaxed max-w-xl">
                    A modern B2B distribution partner for health, beauty and pharmaceutical products — built on
                    competitive pricing, a vast catalogue and logistics that perform.
                </p>
            </div>
        </div>
    </section>

    {{-- ===== Story / Who We Are ===== --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div class="relative">
                <div class="overflow-hidden rounded-2xl shadow-2xl group">
                    <img src="https://images.unsplash.com/photo-1586528116311-ad8dd6b2e94a?auto=format&fit=crop&w=900&q=62"
                         alt="Our team at work"
                         class="w-full h-[460px] object-cover kinetic-img">
                </div>
                <div class="absolute -bottom-6 -right-6 bg-copower-banner text-white rounded-2xl px-7 py-5 shadow-xl hidden md:block">
                    <p class="text-3xl font-black leading-none">£5,000</p>
                    <p class="text-xs font-semibold mt-1 uppercase tracking-wider">Minimum Order Value</p>
                </div>
            </div>
            <div>
                <p class="reveal is-visible text-sm font-bold tracking-widest uppercase text-copower-banner">Who we are</p>
                <h2 class="reveal is-visible mt-3 text-3xl md:text-4xl font-black text-copower-dark leading-tight">
                    Your partner behind the shelf.
                </h2>
                <p class="reveal is-visible mt-6 text-base text-gray-600 leading-relaxed">
                    Copower Wholesale connects independent retailers and resellers with the brands their customers ask for.
                    We operate as a wholesale distributor, keeping a deep catalogue of health, beauty, oral care, cosmetics and
                    personal care essentials ready to ship at scale.
                </p>
                <p class="reveal is-visible mt-4 text-base text-gray-600 leading-relaxed">
                    Because we sell wholesale to other businesses, every order is supported by clear pricing, a configured
                    customer discount tier and a dedicated team that reviews and approves each order before it lands with you.
                </p>
                <div class="reveal is-visible mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="bg-copower-banner text-white px-7 py-3 rounded-lg font-semibold hover:bg-copower-dark transition">Become a Customer</a>
                    <a href="{{ route('customer.products') }}" class="border border-copower-dark text-copower-dark px-7 py-3 rounded-lg font-semibold hover:bg-copower-dark hover:text-white transition">Explore the Catalogue</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== What We Distribute ===== --}}
    <section class="py-20 bg-copower-gray">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto">
                <p class="reveal inline-flex items-center justify-center gap-2 text-sm font-bold tracking-widest uppercase text-copower-banner">
                    <span class="h-px w-6 bg-copower-banner"></span> What we distribute <span class="h-px w-6 bg-copower-banner"></span>
                </p>
                <h2 class="reveal mt-3 text-3xl md:text-4xl font-black text-copower-dark">One source, endless aisles.</h2>
                <p class="reveal mt-4 text-gray-600">Curated categories, sourced and stocked for reliable trade supply.</p>
            </div>

            <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $pillars = [
                        ['cat' => 'Health & Pharma',   'img' => 'https://images.unsplash.com/photo-1631549916768-4119b2c24b57?auto=format&fit=crop&w=700&q=60', 'desc' => 'Supplements, pharmacy and everyday health essentials.'],
                        ['cat' => 'Hair & Oral Care',  'img' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=700&q=60', 'desc' => 'Haircare, oral hygiene and personal grooming ranges.'],
                        ['cat' => 'Cosmetics',         'img' => 'https://images.unsplash.com/photo-1522335789108-8d1b4e2a7b4c?auto=format&fit=crop&w=700&q=60', 'desc' => 'Cosmetics and colour lines for high-traffic shelves.'],
                        ['cat' => 'Skin Care',         'img' => 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=700&q=60', 'desc' => 'Skincare and toiletries trusted by your customers.'],
                    ];
                @endphp
                @foreach($pillars as $p)
                    <div class="reveal group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition">
                        <div class="overflow-hidden h-48">
                            <img src="{{ $p['img'] }}" alt="{{ $p['cat'] }}" class="w-full h-full object-cover kinetic-img">
                        </div>
                        <div class="p-6">
                            <h3 class="font-bold text-copower-dark">{{ $p['cat'] }}</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $p['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== Values Band ===== --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto">
                <p class="reveal inline-flex items-center justify-center gap-2 text-sm font-bold tracking-widest uppercase text-copower-banner">
                    <span class="h-px w-6 bg-copower-banner"></span> How we work <span class="h-px w-6 bg-copower-banner"></span>
                </p>
                <h2 class="reveal mt-3 text-3xl md:text-4xl font-black text-copower-dark">Principles behind every box.</h2>
            </div>
            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $values = [
                        ['icon' => 'fa-percent',     'title' => 'Wholesale-first pricing', 'text' => 'Tier-based discounts and clear minimum order values, built for trade and resale.'],
                        ['icon' => 'fa-boxes-stacked','title' => 'Depth of stock',          'text' => 'A broad, deep catalogue across the categories your business actually needs.'],
                        ['icon' => 'fa-globe',       'title' => 'Global logistics',         'text' => 'Free freight delivery and specialist logistics that move your stock reliably.'],
                    ];
                @endphp
                @foreach($values as $v)
                    <div class="reveal group rounded-2xl p-8 bg-copower-gray hover:bg-copower-dark transition-colors duration-500">
                        <div class="w-14 h-14 rounded-xl bg-copower-banner text-white flex items-center justify-center text-xl">
                            <i class="fas {{ $v['icon'] }}"></i>
                        </div>
                        <h3 class="mt-5 text-xl font-bold text-copower-dark group-hover:text-white transition">{{ $v['title'] }}</h3>
                        <p class="mt-2 text-sm text-gray-600 group-hover:text-copower-gray/80 transition">{{ $v['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== Full-Width Image CTA Band ===== --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1587293852726-70cb50285d9e?auto=format&fit=crop&w=1920&q=70"
                 alt="Distribution in action" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-copower-dark/85"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 py-24 text-center">
            <h2 class="reveal text-3xl md:text-5xl font-black text-white">Ready to stock smarter?</h2>
            <p class="reveal mt-4 max-w-2xl mx-auto text-lg text-copower-gray/90">
                Open a wholesale account and let us look after the supply, so you can look after the shelf.
            </p>
            <div class="reveal mt-8">
                <a href="{{ route('register') }}" class="inline-block bg-copower-banner text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-copower-dark transition">Start a Wholesale Account</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); observer.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endpush