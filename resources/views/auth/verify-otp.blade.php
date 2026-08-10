{{-- resources/views/auth/verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - Copower Wholesale</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        copower: {
                            dark: '#0F3D5E',
                            banner: '#00A3E0',
                        }
                    },
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-copower-dark min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <span class="text-3xl font-black tracking-tight text-white">COPOWER</span>
            <span class="text-xs font-extrabold tracking-[0.3em] text-copower-banner uppercase block mt-1">Verify Email</span>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-copower-dark">Verify your email</h2>
            <p class="text-sm text-gray-500 mt-2">
                We sent a 6-digit verification code to
                <span class="font-medium text-copower-dark">{{ $user->email }}</span>.
            </p>

            @if($errors->any())
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm text-green-600">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('warning'))
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <p class="text-sm text-yellow-600">{{ session('warning') }}</p>
                </div>
            @endif

            <!-- OTP Form -->
            <form method="POST" action="{{ route('auth.verify-otp.submit') }}" class="mt-6 space-y-6">
                @csrf

                <div class="space-y-1">
                    <label for="otp_code" class="block text-sm font-medium text-copower-dark">
                        Verification Code
                    </label>
                    <input id="otp_code"
                           name="otp_code"
                           type="text"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           pattern="[0-9]{6}"
                           maxlength="6"
                           placeholder="000000"
                           required
                           autofocus
                           class="block w-full text-center text-2xl tracking-[0.5em] px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50">
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center py-3.5 px-4 bg-copower-dark text-white rounded-xl font-semibold text-sm hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copower-banner transition-all duration-200">
                    Verify Email
                </button>
            </form>

            <!-- Resend -->
            <form method="POST" action="{{ route('auth.verify-otp.resend') }}" class="mt-6 text-center">
                @csrf
                <p class="text-xs text-gray-500">Haven't received it?</p>
                <button type="submit" id="resendBtn"
                        class="mt-1 text-sm font-medium text-copower-banner hover:text-copower-dark transition disabled:text-gray-400 disabled:cursor-not-allowed">
                    Resend verification code
                </button>
            </form>
            <script>
                (function () {
                    var btn = document.getElementById('resendBtn');
                    if (!btn) return;
                    var COOLDOWN = 60;
                    var key = 'otp_resend_cooldown';
                    var stored = parseInt(sessionStorage.getItem(key) || '0', 10);
                    var remaining = stored ? Math.max(0, stored - Math.floor(Date.now() / 1000)) : 0;

                    function tick() {
                        if (remaining <= 0) {
                            btn.disabled = false;
                            btn.textContent = 'Resend verification code';
                            sessionStorage.removeItem(key);
                            return;
                        }
                        btn.disabled = true;
                        btn.textContent = 'Resend in ' + remaining + 's';
                        remaining--;
                        if (remaining <= 0) {
                            sessionStorage.removeItem(key);
                            btn.disabled = false;
                            btn.textContent = 'Resend verification code';
                        } else {
                            setTimeout(tick, 1000);
                        }
                    }

                    if (remaining > 0) {
                        tick();
                    }

                    btn.addEventListener('click', function () {
                        if (remaining > 0) return;
                        sessionStorage.setItem(key, Math.floor(Date.now() / 1000) + COOLDOWN);
                        remaining = COOLDOWN;
                        tick();
                    });
                })();
            </script>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-300 hover:text-white transition">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</body>
</html>