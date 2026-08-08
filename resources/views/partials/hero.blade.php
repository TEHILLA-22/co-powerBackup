{{-- resources/views/partials/hero.blade.php --}}
<!-- 5. Main Hero Slider Banner -->
<section class="relative bg-copower-dark text-white overflow-hidden min-h-[420px] flex items-center">
    <!-- Background Overlay / Grid graphic -->
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
    
    <!-- Decorative Gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-copower-dark via-copower-dark/90 to-copower-dark/70"></div>

    <div class="max-w-7xl mx-auto px-6 py-16 w-full relative z-10 grid md:grid-cols-2 items-center gap-8">
        
        <!-- Dynamic Left Graphics placeholder (Product Showcase) -->
        <div class="relative hidden md:block">
            <div class="w-full h-80 bg-white/5 rounded-xl border border-white/10 p-4 flex items-center justify-center text-white/40">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-2 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    <span>Product Showcase</span>
                </div>
            </div>
        </div>

        <!-- Hero Text Content -->
        <div class="md:pl-12 text-left">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                COPOWER Wholesale
            </h1>
            <p class="text-xl font-medium text-gray-200 leading-snug">
                Your Partner for Global Brands.<br>
                Wholesale Supply for Retail & Export.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('customer.products') }}" class="bg-copower-banner text-white px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition transform hover:scale-105">
                        Start Shopping <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-copower-banner text-white px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition transform hover:scale-105">
                        Register Now <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                    <a href="{{ route('login') }}" class="bg-white/10 text-white border border-white/30 px-8 py-3 rounded-lg font-semibold hover:bg-white/20 transition">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Right Carousel Arrow -->
    <button class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-copower-banner focus:outline-none transition">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</section>