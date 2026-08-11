{{-- resources/views/partials/mobile-menu.blade.php --}}
<div x-show="mobileMenuOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-x-full"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 -translate-x-full"
     class="fixed inset-0 z-50 md:hidden"
     @click.away="mobileMenuOpen = false">
    
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50"></div>
    
    <!-- Menu Panel -->
    <div class="relative w-80 max-w-[85%] h-full bg-white shadow-xl overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex flex-col">
                <span class="text-xl font-black tracking-tight text-copower-dark">COPOWER</span>
                <span class="text-[10px] font-extrabold tracking-widest text-copower-banner uppercase">Wholesale</span>
            </div>
            <button @click="mobileMenuOpen = false" class="text-gray-700 hover:text-copower-banner transition p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-home mr-3"></i> Home
            </a>
            <a href="{{ route('customer.products') }}" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-store mr-3"></i> All Products
            </a>
            @if(isset($navCategories) && $navCategories->count())
                <div class="px-4 py-2">
                    <p class="text-xs font-bold text-copower-dark uppercase tracking-wide mb-2">Categories</p>
                    <div class="space-y-1">
                        @foreach($navCategories as $parent)
                            <a href="{{ route('customer.products') }}?category={{ $parent->slug }}" class="flex items-center justify-between px-2 py-1.5 text-gray-700 hover:bg-copower-gray rounded-md transition text-sm">
                                <span>{{ $parent->name }}</span>
                                <span class="bg-copower-banner text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $parent->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-tags mr-3"></i> All Brands
            </a>
            <a href="{{ route('price-list') }}" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-file-invoice-dollar mr-3"></i> Price List
            </a>
            <a href="{{ route('quote.bulk') }}" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-truck-fast mr-3"></i> How To Order
            </a>
            <a href="{{ route('about') }}" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-info-circle mr-3"></i> About Us
            </a>
            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                <i class="fas fa-envelope mr-3"></i> Contact
            </a>
        </nav>
        
        <!-- Auth Actions -->
        <div class="border-t border-gray-200 p-4 space-y-2">
            @auth
                <a href="{{ route('quote.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-copower-gray rounded-lg transition">
                    <i class="fas fa-file-invoice mr-3"></i> My Quote
                    <span class="ml-2 bg-copower-banner text-white text-xs px-2 py-0.5 rounded-full">
                        {{ auth()->check() ? app(\App\Services\QuoteBasketService::class)->count() : 0 }}
                    </span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block w-full text-center bg-copower-dark text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition font-medium">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center border border-copower-dark text-copower-dark px-6 py-3 rounded-lg hover:bg-copower-gray transition font-medium">
                    Register
                </a>
            @endauth
        </div>
    </div>
</div>