{{-- resources/views/admin/partials/header.blade.php --}}
<header class="bg-white shadow-sm border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <!-- Mobile Menu Toggle -->
    <button id="mobileSidebarToggle" class="md:hidden text-gray-600 hover:text-copower-dark">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Page Title -->
    <div>
        <h1 class="text-lg font-bold text-copower-dark">@yield('page_title', 'Dashboard')</h1>
        <p class="text-xs text-gray-500">Welcome back, {{ auth()->guard('admin')->user()->full_name }}</p>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <button class="relative text-gray-600 hover:text-copower-dark transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @php $pendingCount = App\Models\User::where('is_approved', false)->count() + App\Models\Order::where('status', 'submitted')->count(); @endphp
            @if($pendingCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $pendingCount }}</span>
            @endif
        </button>

        <!-- Admin Profile -->
        <div class="flex items-center space-x-2">
            <div class="w-9 h-9 rounded-full bg-copower-dark text-white flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr(auth()->guard('admin')->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->guard('admin')->user()->last_name, 0, 1)) }}
            </div>
            <div class="hidden sm:block">
                <p class="text-sm font-medium text-copower-dark">{{ auth()->guard('admin')->user()->full_name }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ auth()->guard('admin')->user()->role }}</p>
            </div>
        </div>
    </div>
</header>