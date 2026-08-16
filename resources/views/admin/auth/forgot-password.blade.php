{{-- resources/views/admin/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Copower Wholesale Admin</title>

    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="flex flex-col items-center">
                    <span class="text-2xl font-black tracking-tight text-copower-dark">COPOWER</span>
                    <span class="text-xs font-extrabold tracking-widest text-copower-banner uppercase">Admin</span>
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-800">Forgot Password</h2>
                <p class="text-sm text-gray-500 mt-1">Enter your email to receive a reset link</p>
            </div>

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

            <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
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
                           autocomplete="email"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                </div>

                <button type="submit"
                        class="w-full bg-gray-700 text-white py-2.5 rounded-lg hover:bg-opacity-90 transition font-semibold">
                    Send Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('admin.login') }}" class="text-sm text-copower-banner hover:underline">
                    Back to login
                </a>
            </div>
        </div>
    </div>
</body>
</html>