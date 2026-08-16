{{-- resources/views/admin/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Registration - Copower Wholesale</title>
    
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full bg-white rounded-xl shadow-xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="flex flex-col items-center">
                    <span class="text-2xl font-black tracking-tight text-copower-dark">COPOWER</span>
                    <span class="text-xs font-extrabold tracking-widest text-copower-banner uppercase">Admin Registration</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Create your admin account</p>
            </div>

            <!-- Check if registration is allowed -->
            @if(!$registrationAllowed)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm text-yellow-700">
                            <strong>Note:</strong> Admin registration is only available when no admin accounts exist.
                            Current admins: {{ App\Models\Admin::count() }}
                        </p>
                    </div>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('admin.login') }}" class="text-copower-banner hover:underline">
                        Already have an account? Login here
                    </a>
                </div>
            @else
                <!-- Errors -->
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

                @if(session('info'))
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-600">{{ session('info') }}</p>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('admin.register.submit') }}" class="space-y-5" id="registerForm">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                First Name
                            </label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}" 
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Name
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}" 
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                        <div id="emailStatus" class="text-xs mt-1 hidden"></div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number (Optional)
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
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
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" 
                               id="terms" 
                               name="terms" 
                               required
                               class="mt-1 h-4 w-4 text-copower-banner border-gray-300 rounded focus:ring-copower-banner">
                        <label for="terms" class="ml-2 text-sm text-gray-700">
                            I agree to the 
                            <a href="#" class="text-copower-banner hover:underline">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" 
                            id="registerBtn"
                            class="w-full bg-gray-700 text-white py-2.5 rounded-lg hover:bg-opacity-90 transition font-semibold">
                        Register Admin
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('admin.login') }}" class="text-copower-banner hover:underline">
                            Login here
                        </a>
                    </p>
                </div>
            @endif

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    This area is restricted. All access is logged and monitored.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const emailStatus = document.getElementById('emailStatus');
            let timeoutId = null;

            emailInput.addEventListener('input', function() {
                clearTimeout(timeoutId);
                const email = this.value.trim();
                
                if (email.length < 3 || !email.includes('@')) {
                    emailStatus.classList.add('hidden');
                    return;
                }

                timeoutId = setTimeout(() => {
                    checkEmailStatus(email);
                }, 500);
            });

            function checkEmailStatus(email) {
                fetch('{{ route('admin.check-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                .then(data => {
                    emailStatus.classList.remove('hidden');
                    
                    if (data.verified) {
                        emailStatus.className = 'text-xs mt-1 text-green-600';
                        emailStatus.innerHTML = '✓ ' + data.message + ' <a href="' + data.redirect + '" class="underline">Login here</a>';
                        document.getElementById('registerBtn').disabled = true;
                        document.getElementById('registerBtn').classList.add('opacity-50', 'cursor-not-allowed');
                    } else if (data.exists && !data.verified) {
                        emailStatus.className = 'text-xs mt-1 text-yellow-600';
                        emailStatus.innerHTML = '⚠ ' + data.message + ' <a href="' + data.redirect + '" class="underline">Verify now</a>';
                        document.getElementById('registerBtn').disabled = true;
                        document.getElementById('registerBtn').classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        emailStatus.className = 'text-xs mt-1 text-green-600';
                        emailStatus.innerHTML = '✓ ' + data.message;
                        document.getElementById('registerBtn').disabled = false;
                        document.getElementById('registerBtn').classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                })
                .catch(error => {
                    console.error('Error checking email:', error);
                });
            }
        });
    </script>
</body>
</html>