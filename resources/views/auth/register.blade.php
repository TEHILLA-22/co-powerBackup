{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Copower Wholesale</title>

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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(0, 163, 224, 0.1); }
            50% { box-shadow: 0 0 40px rgba(0, 163, 224, 0.25); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .animate-slide-in {
            animation: slideIn 0.8s ease-out forwards;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .pulse-glow {
            animation: pulseGlow 3s ease-in-out infinite;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
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
        .gradient-text {
            background: linear-gradient(135deg, #0F3D5E 0%, #00A3E0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #00A3E0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0F3D5E;
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
                        Join <span class="text-copower-banner">Copower</span>
                    </h1>
                    <p class="text-gray-300 text-lg font-light max-w-md mx-auto leading-relaxed">
                        Create your wholesale account today.<br>
                        Access exclusive pricing and bulk orders.
                    </p>
                </div>

                <!-- Trust Badges -->
                <div class="grid grid-cols-2 gap-4 mt-12 w-full max-w-md animate-slide-in delay-300">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-copower-banner/20 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-copower-banner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-white">MOQ £5,000</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-copower-banner/20 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-copower-banner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-white">Free UK Delivery</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-copower-banner/20 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-copower-banner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-white">Bespoke Software</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-copower-banner/20 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-copower-banner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-white">Global Logistics</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Decorative -->
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-copower-dark to-transparent"></div>
        </div>

        <!-- ==================== RIGHT PANEL - REGISTRATION FORM ==================== -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white px-6 py-8 lg:px-12 relative overflow-y-auto">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-copower-banner/5 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-copower-dark/5 rounded-full blur-2xl"></div>

            <!-- Mobile Logo -->
            <div class="lg:hidden absolute top-4 left-1/2 transform -translate-x-1/2">
                <div class="flex flex-col items-center">
                    <span class="text-2xl font-black tracking-tight text-copower-dark">COPOWER</span>
                    <span class="text-[10px] font-extrabold tracking-[0.3em] text-copower-banner uppercase">Wholesale</span>
                </div>
            </div>

            <!-- Form Container -->
            <div class="relative z-10 w-full max-w-md animate-fade-in-up mt-12 lg:mt-[1000px]">

                <!-- Header -->
                <div class="mb-6 text-center lg:text-left">
                    <h2 class="text-2xl font-bold text-copower-dark">Create An Online Account</h2>
                    <p class="text-sm text-gray-500 mt-1">Join Copower Wholesale today</p>
                </div>

                <!-- Errors -->
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

                <!-- Success/Warning -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-600">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <p class="text-sm text-yellow-600">{{ session('warning') }}</p>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- ===== PERSONAL INFORMATION ===== -->
                    <div>
                        <h3 class="text-sm font-semibold text-copower-dark uppercase tracking-wider mb-4">
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="first_name"
                                       value="{{ old('first_name') }}"
                                       required
                                       placeholder="John"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="last_name"
                                       value="{{ old('last_name') }}"
                                       required
                                       placeholder="Smith"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-copower-dark mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="you@company.com"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                    </div>

                    <!-- ===== COMPANY INFORMATION ===== -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-copower-dark uppercase tracking-wider mb-4">
                            Company Information
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-copower-dark mb-1">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="company_name"
                                   value="{{ old('company_name') }}"
                                   required
                                   placeholder="Your Company Ltd"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-copower-dark mb-1">
                                Company Website
                            </label>
                            <input type="url"
                                   name="company_website"
                                   value="{{ old('company_website') }}"
                                   placeholder="https://yourcompany.com"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    Registration Number
                                </label>
                                <input type="text"
                                       name="company_registration_number"
                                       value="{{ old('company_registration_number') }}"
                                       placeholder="12345678"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    VAT Number
                                </label>
                                <input type="text"
                                       name="vat_number"
                                       value="{{ old('vat_number') }}"
                                       placeholder="GB123456789"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- ===== MOBILE NUMBER ===== -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-copower-dark uppercase tracking-wider mb-4">
                            Contact Number
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-copower-dark mb-1">
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   name="mobile"
                                   value="{{ old('mobile') }}"
                                   required
                                   placeholder="+44 7700 900123"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                        <input type="hidden" name="phone" value="{{ old('phone') }}">
                    </div>

                    <!-- ===== PASSWORD ===== -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-copower-dark uppercase tracking-wider mb-4">
                            Password
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       name="password"
                                       required
                                       placeholder="••••••••"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                                <p class="mt-1 text-xs text-gray-400">Minimum 8 characters</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       required
                                       placeholder="••••••••"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- ===== ADDRESS ===== -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-copower-dark uppercase tracking-wider mb-4">
                            Address
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-copower-dark mb-1">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="address_line_1"
                                   value="{{ old('address_line_1') }}"
                                   required
                                   placeholder="123 Business Street"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-copower-dark mb-1">
                                Address Line 2
                            </label>
                            <input type="text"
                                   name="address_line_2"
                                   value="{{ old('address_line_2') }}"
                                   placeholder="Suite, Floor, etc."
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="city"
                                       value="{{ old('city') }}"
                                       required
                                       placeholder="London"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    State/County
                                </label>
                                <input type="text"
                                       name="state_province"
                                       value="{{ old('state_province') }}"
                                       placeholder="Greater London"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    Postal Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="postal_code"
                                       value="{{ old('postal_code') }}"
                                       required
                                       placeholder="EC1A 1BB"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-copower-dark mb-1">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <select name="country_code"
                                        required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-copower-banner/20 focus:border-copower-banner transition-all duration-200 bg-gray-50 hover:bg-white">
                                    <option value="">Select Country</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country['code'] }}" {{ old('country_code') == $country['code'] ? 'selected' : '' }}>
                                            {{ $country['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TERMS ===== -->
                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-start">
                            <input type="checkbox"
                                   name="terms"
                                   id="terms"
                                   required
                                   class="mt-1 h-4 w-4 text-copower-banner border-gray-300 rounded focus:ring-copower-banner/20">
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                I agree to the
                                <a href="#" class="text-copower-banner hover:underline font-medium">Terms & Conditions</a>
                                and
                                <a href="#" class="text-copower-banner hover:underline font-medium">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- ===== SUBMIT ===== -->
                    <button type="submit"
                            class="w-full flex items-center justify-center py-3.5 px-4 bg-copower-dark text-white rounded-xl font-semibold text-sm hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copower-banner transition-all duration-200 hover-lift group">
                        <span>Create Account</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>

                <!-- ===== LOGIN LINK ===== -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-copower-banner hover:text-copower-dark transition">
                            Sign in
                        </a>
                    </p>
                    <p class="mt-2 text-xs text-gray-400">
                        B2B wholesale accounts only. Approval required.
                    </p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
