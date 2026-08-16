{{-- resources/views/admin/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Copower Wholesale</title>
    
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="flex flex-col items-center">
                    <span class="text-2xl font-black tracking-tight text-copower-dark">COPOWER</span>
                    <span class="text-xs font-extrabold tracking-widest text-copower-banner uppercase">Admin</span>
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-800">Admin Login</h2>
                <p class="text-sm text-gray-500 mt-1">Secure access to administrative panel</p>
            </div>

            <!-- Messages -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-600">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-yellow-600">{{ session('warning') }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           autocomplete="off"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="off"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" 
                               name="remember" 
                               class="h-4 w-4 text-copower-banner border-gray-300 rounded focus:ring-copower-banner">
                        <span class="text-sm text-gray-700">Remember me</span>
                    </label>
                    <a href="{{ route('admin.password.request') }}" class="text-sm text-copower-banner hover:underline">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" 
                        class="w-full bg-gray-700 text-white py-2.5 rounded-lg hover:bg-opacity-90 transition font-semibold">
                    Login to Admin
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Need an admin account?
                    <a href="{{ route('admin.register') }}" class="text-copower-banner hover:underline">
                        Register here
                    </a>
                </p>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    This area is restricted. All access is logged and monitored.
                </p>
            </div>
        </div>
    </div>
</body>
</html>