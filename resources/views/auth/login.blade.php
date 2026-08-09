{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Copower Wholesale</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        copower: {
                            dark: '#0F3D5E',
                            banner: '#00A3E0',
                            gray: '#F8F9FA',
                        }
                    },
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(0, 163, 224, 0.1); }
            50% { box-shadow: 0 0 40px rgba(0, 163, 224, 0.25); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .animate-slide-in {
            animation: slideIn 0.8s ease-out forwards;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .pulse-glow {
            animation: pulseGlow 3s ease-in-out infinite;
        }
        .gradient-text {
            background: linear-gradient(135deg, #0F3D5E 0%, #00A3E0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -12px rgba(15, 61, 94, 0.3);
        }
        .input-focus-ring {
            transition: all 0.3s ease;
        }
        .input-focus-ring:focus {
            box-shadow: 0 0 0 4px rgba(0, 163, 224, 0.15), 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #00A3E0;
        }
        .bg-grid-pattern {
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .bg-gradient-radial {
            background: radial-gradient(ellipse at 30% 50%, rgba(0, 163, 224, 0.08) 0%, transparent 70%);
        }
    </style>
</head>
<body class="font-sans antialiased overflow-hidden h-screen bg-copower-dark">
    
    <div class="flex h-screen">
        
        <!-- ==================== LEFT PANEL - BRAND SHOWCASE ==================== -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-copower-dark">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-grid-pattern"></div>
            <div class="absolute inset-0 bg-gradient-radial"></div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-20 right-20 w-64 h-64 bg-copower-banner/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 left-20 w-96 h-96 bg-copower-banner/5 rounded-full blur-2xl" style="animation: float 8s ease-in-out infinite reverse;"></div>
            
            <!-- Brand Content -->
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <!-- Logo -->
                <div class="mb-12 animate-slide-in">
                    <div class="flex flex-col items-center">
                        <span class="text-5xl font-black tracking-tight text-white">COPOWER</span>
                        <span class="text-sm font-extrabold tracking-[0.3em] text-copower-banner uppercase mt-1">Wholesale</span>
                    </div>
                </div>
                
                <!-- Tagline -->
                <div class="text-center animate-slide-in delay-200">
                    <h1 class="text-3xl font-bold leading-tight mb-4">
                        Welcome Back to
                        <span class="text-copower-banner">Copower</span>
                    </h1>
                    <p class="text-gray-300 text-lg font-light max-w-md mx-auto leading-relaxed">
                        Your trusted partner for wholesale supply.<br>
                        Access exclusive pricing and bulk orders.
                    </p>
                </div>
                
                <!-- Features -->
                <div class="grid grid-cols-2 gap-4 mt-12 w-full max-w-md animate-slide-in delay-300">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-copower-banner/20 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-copower-banner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-white">Secure & Trusted</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-copower-banner/20 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-copower-banner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-white">Fast & Reliable</p>
                    </div>
                </div>
                
                <!-- Trust Badge -->
                <div class="mt-8 flex items-center space-x-6 text-xs text-gray-400 animate-slide-in delay-400">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 text-copower-banner mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        10,000+ Products
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 text-copower-banner mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        B2B Wholesale
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 text-copower-banner mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Global Logistics
                    </span>
                </div>
            </div>
            
            <!-- Bottom Decorative -->
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-copower-dark to-transparent"></div>
        </div>
        
        <!-- ==================== RIGHT PANEL - LOGIN FORM ==================== -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white px-6 py-12 lg:px-12 relative overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-copower-banner/5 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-copower-dark/5 rounded-full blur-2xl"></div>
            
            <!-- Mobile Logo (visible on small screens) -->
            <div class="lg:hidden absolute top-6 left-1/2 transform -translate-x-1/2">
                <div class="flex flex-col items-center">
                    <span class="text-2xl font-black tracking-tight text-copower-dark">COPOWER</span>
                    <span class="text-[10px] font-extrabold tracking-[0.3em] text-copower-banner uppercase">Wholesale</span>
                </div>
            </div>
            
            <!-- Form Container -->
            <div class="relative z-10 w-full max-w-md animate-fade-in-up">
                <!-- Header -->
                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-copower-dark">
                        Welcome Back
                    </h2>
                    <p class="text-gray-500 mt-2 text-sm">
                        Sign in to your wholesale account
                    </p>
                </div>
                
                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-600">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-600">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-yellow-600">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif
                
                <!-- Login Form -->
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="space-y-1">
                        <label for="email" class="block text-sm font-medium text-copower-dark">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="your@email.com"
                                   class="block w-full pl-10 pr-3 py-3.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-copower-dark">
                                Password
                            </label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-copower-banner hover:text-copower-dark transition">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   required 
                                   placeholder="••••••••"
                                   class="block w-full pl-10 pr-12 py-3.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            <button type="button" 
                                    onclick="togglePassword()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" 
                                   name="remember" 
                                   {{ old('remember') ? 'checked' : '' }}
                                   class="w-4 h-4 text-copower-banner border-gray-300 rounded focus:ring-copower-banner/20 focus:ring-2">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full flex items-center justify-center py-3.5 px-4 bg-copower-dark text-white rounded-xl font-semibold text-sm hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copower-banner transition-all duration-200 hover-lift group">
                        <span>Sign In</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-400">New to Copower?</span>
                    </div>
                </div>
                
                <!-- Register Link -->
                <div class="text-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center text-sm font-medium text-copower-banner hover:text-copower-dark transition">
                        Create an account
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <p class="mt-2 text-xs text-gray-400">
                        B2B wholesale accounts only. Approval required.
                    </p>
                </div>
            </div>
        </div>
        
    </div>
    
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                password.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }
    </script>
</body>
</html>