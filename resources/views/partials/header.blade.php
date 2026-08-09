{{-- resources/views/partials/header.blade.php --}}
<header class="sticky top-0 z-50">
    <!-- 1. Top Utility Header (Dark Navy Bar) -->
    <div class="bg-copower-dark text-white text-xs py-2 px-6">
        <div class="max-w-7xl mx-auto flex justify-end items-center space-x-6 font-semibold tracking-wider">
            @auth
                <a href="{{ route('customer.products') }}" class="hover:underline">DASHBOARD</a>
            @else
                <a href="{{ route('login') }}" class="hover:underline">REGISTER/LOGIN</a>
            @endauth
            <a href="{{ route('about') }}" class="hover:underline">ABOUT US</a>
            <a href="{{ route('faq') }}" class="hover:underline">FAQ'S</a>
            <a href="#" class="hover:underline">CONTACT US</a>
        </div>
    </div>

    <!-- 2. Main Header -->
    <div class="bg-white py-4 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center gap-6">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 shrink-0">
                <div class="flex flex-col">
                    <span class="text-3xl font-black tracking-tight text-copower-dark leading-none">COPOWER</span>
                    <span class="text-xs font-extrabold tracking-widest text-copower-banner uppercase">Wholesale</span>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-4 hidden md:block">
                <form action="{{ route('customer.products') }}" method="GET" class="relative flex items-center">
                    <input type="text" 
                           name="search" 
                           placeholder="Search for brands, barcodes, SKU'S..." 
                           value="{{ request('search') }}"
                           class="w-full py-2.5 px-4 border border-copower-dark rounded-md text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-copower-dark">
                    <button type="submit" class="absolute right-3 text-copower-dark hover:text-copower-banner transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Right Side Icons -->
            <div class="flex items-center space-x-6 shrink-0 text-copower-dark">
                
                <!-- Account Icon -->
                @auth
                    <a href="{{ route('customer.products') }}" class="flex items-center space-x-2 group">
                        <div class="w-9 h-9 rounded-full bg-copower-dark text-white flex items-center justify-center group-hover:bg-copower-banner transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-left text-xs font-bold leading-tight hidden sm:block">
                            <span class="block">Your</span>
                            <span class="block">Account</span>
                        </div>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center space-x-2 group">
                        <div class="w-9 h-9 rounded-full bg-copower-dark text-white flex items-center justify-center group-hover:bg-copower-banner transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-left text-xs font-bold leading-tight hidden sm:block">
                            <span class="block">Your</span>
                            <span class="block">Account</span>
                        </div>
                    </a>
                @endauth

                <!-- Quote Icon -->
                <a href="{{ route('quote.index') }}" class="flex items-center space-x-2 group relative">
                    <div class="w-9 h-9 text-copower-dark group-hover:text-copower-banner transition-colors">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h16"/>
                        </svg>
                    </div>
                    <div class="text-left text-xs font-bold leading-tight hidden sm:block">
                        <span class="block">Your</span>
                        <span class="block">Quote</span>
                    </div>
                    @php $count = session('quote_count', 0); @endphp
                    @if($count > 0)
                        <span class="absolute -top-1 -right-1 sm:right-0 bg-copower-banner text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            {{ $count }}
                        </span>
                    @endif
                </a>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-copower-dark hover:text-copower-banner transition p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Main Navigation Bar (desktop only) -->
    <nav class="hidden md:block border-y border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 flex items-center space-x-8 text-sm font-bold text-copower-dark py-3 overflow-x-auto">
            <a href="{{ route('customer.products') }}" class="flex items-center space-x-1 hover:text-copower-banner whitespace-nowrap">
                <span>All Products</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <span class="text-gray-300">|</span>
            <a href="#" class="flex items-center space-x-1 hover:text-copower-banner whitespace-nowrap">
                <span>All Brands</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('price-list') }}" class="flex items-center space-x-1 hover:text-copower-banner whitespace-nowrap">
                <span>Price List</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('quote.bulk') }}" class="flex items-center space-x-1 hover:text-copower-banner whitespace-nowrap">
                <span>How To Order</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </nav>
</header>