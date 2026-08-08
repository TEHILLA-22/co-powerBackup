{{-- resources/views/admin/auth/verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - Copower Wholesale Admin</title>
    
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
                    <span class="text-xs font-extrabold tracking-widest text-copower-banner uppercase">Verify OTP</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Enter the verification code sent to your email</p>
                @if(isset($email))
                    <p class="text-xs text-gray-400 mt-1">{{ $email }}</p>
                @endif
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

            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-600">{{ session('info') }}</p>
                </div>
            @endif

            <!-- OTP Form -->
            <form method="POST" action="{{ route('admin.verify.submit') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                        Verification Code
                    </label>
                    <input type="text" 
                           id="otp_code" 
                           name="otp_code" 
                           required
                           maxlength="6"
                           pattern="[0-9]{6}"
                           placeholder="Enter 6-digit code"
                           autofocus
                           class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-copower-banner focus:border-transparent text-center text-3xl tracking-[0.5em] font-mono">
                    <div class="flex justify-center mt-3 space-x-2">
                        <div class="w-2 h-2 rounded-full bg-gray-300" id="dot1"></div>
                        <div class="w-2 h-2 rounded-full bg-gray-300" id="dot2"></div>
                        <div class="w-2 h-2 rounded-full bg-gray-300" id="dot3"></div>
                        <div class="w-2 h-2 rounded-full bg-gray-300" id="dot4"></div>
                        <div class="w-2 h-2 rounded-full bg-gray-300" id="dot5"></div>
                        <div class="w-2 h-2 rounded-full bg-gray-300" id="dot6"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Enter the 6-digit code sent to your email. Code expires in 10 minutes.
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-copower-dark text-white py-3 rounded-lg hover:bg-opacity-90 transition font-semibold">
                    <i class="fas fa-check mr-2"></i> Verify Email
                </button>
            </form>

            <div class="mt-4 text-center space-y-3">
                <p class="text-sm text-gray-600">
                    Didn't receive the code?
                    <form method="POST" action="{{ route('admin.verify.resend') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-copower-banner hover:underline font-medium">
                            Resend OTP
                        </button>
                    </form>
                </p>
                <p class="text-xs text-gray-500">
                    <a href="{{ route('admin.register') }}" class="hover:underline">Back to registration</a>
                    <span class="mx-2">|</span>
                    <a href="{{ route('admin.login') }}" class="hover:underline">Already verified? Login</a>
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

    <script>
        // Auto-submit on 6 digits entered
        const otpInput = document.getElementById('otp_code');
        const dots = [
            document.getElementById('dot1'),
            document.getElementById('dot2'),
            document.getElementById('dot3'),
            document.getElementById('dot4'),
            document.getElementById('dot5'),
            document.getElementById('dot6')
        ];

        otpInput.addEventListener('input', function(e) {
            // Update dots
            const length = this.value.length;
            dots.forEach((dot, index) => {
                if (index < length) {
                    dot.classList.remove('bg-gray-300');
                    dot.classList.add('bg-copower-banner');
                } else {
                    dot.classList.add('bg-gray-300');
                    dot.classList.remove('bg-copower-banner');
                }
            });

            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Only allow numbers
        otpInput.addEventListener('keypress', function(e) {
            if (e.key < '0' || e.key > '9') {
                e.preventDefault();
            }
        });

        // Auto-focus on page load
        otpInput.focus();

        // Handle paste
        otpInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = pastedData.replace(/\D/g, '').slice(0, 6);
            this.value = numbers;
            this.dispatchEvent(new Event('input'));
            if (numbers.length === 6) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>