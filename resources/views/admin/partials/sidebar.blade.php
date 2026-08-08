{{-- resources/views/admin/partials/sidebar.blade.php --}}
<aside class="w-64 bg-copower-dark text-white flex-shrink-0 hidden md:block min-h-screen">
    <div class="p-4 border-b border-white/10">
        <div class="flex flex-col items-center">
            <span class="text-xl font-black tracking-tight text-white">COPOWER</span>
            <span class="text-xs font-extrabold tracking-widest text-copower-banner uppercase">Admin Panel</span>
        </div>
    </div>
    
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.dashboard') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Orders -->
        <a href="{{ route('admin.orders.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.orders*') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span>Orders</span>
            @php $pendingOrders = App\Models\Order::where('status', 'submitted')->count(); @endphp
            @if($pendingOrders > 0)
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingOrders }}</span>
            @endif
        </a>

        <!-- Customers -->
        <a href="{{ route('admin.customers.pending') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.customers*') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Customers</span>
            @php $pendingCustomers = App\Models\User::where('is_approved', false)->count(); @endphp
            @if($pendingCustomers > 0)
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCustomers }}</span>
            @endif
        </a>

        <!-- Products -->
        <a href="{{ route('admin.products.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.products*') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>Products</span>
        </a>

        <!-- Quotes -->
        <a href="{{ route('admin.quotes.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.quotes*') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Quotes</span>
        </a>

        <!-- Reports -->
        <a href="{{ route('admin.reports.sales') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.reports*') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Reports</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('admin.settings.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.settings*') ? 'bg-white/10' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="absolute bottom-0 w-64 p-4 border-t border-white/10 text-xs text-gray-400">
        <p>Logged in as</p>
        <p class="font-medium text-white">{{ auth()->guard('admin')->user()->full_name }}</p>
        <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="text-red-400 hover:text-red-300 transition text-xs">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </button>
        </form>
    </div>
</aside>