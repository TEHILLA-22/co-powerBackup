{{-- resources/views/auth/pending-approval.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <img src="{{ asset('images/copower-logo.png') }}" alt="Copower Wholesale" class="h-12 mx-auto">
                <h2 class="mt-6 text-3xl font-bold text-gray-900">Account Pending Approval</h2>
                <p class="mt-2 text-sm text-gray-600">Thank you for registering with Copower Wholesale.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">Your account is being reviewed</h3>
                <p class="text-gray-600 text-center mb-6">
                    Our team will review your application and notify you via email once approved.
                    This typically takes 1-2 business days.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 text-sm">
                    <h4 class="font-medium text-gray-700 mb-2">What happens next?</h4>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>We review your company details</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>We'll send you an email with the decision</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>Once approved, you can access wholesale prices and place orders</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-6 space-y-3">
                    <a href="{{ route('home') }}" class="block w-full text-center bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">
                        Return to Home
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-sm text-gray-500 hover:text-gray-700 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>