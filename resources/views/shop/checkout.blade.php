{{-- resources/views/shop/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout - Copower Wholesale')

@section('content')
<div class="bg-copower-gray py-8" x-data="checkoutApp()">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6">
            <ol class="list-none p-0 inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="text-copower-dark hover:text-copower-banner">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('customer.products') }}" class="text-copower-dark hover:text-copower-banner">Products</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('quote.index') }}" class="text-copower-dark hover:text-copower-banner">Quote</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-copower-dark font-medium">Checkout</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-copower-dark">Checkout</h1>
            <p class="text-gray-600 mt-1">Review your order and complete your purchase</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Minimum Order Value Alert -->
        @if(!$meetsMinimum)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                <div>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Minimum order value is £{{ number_format($minimumOrderValue, 2) }}. Your current total is £{{ number_format($subtotal, 2) }}.</span>
                </div>
                <a href="{{ route('quote.index') }}" class="text-yellow-700 hover:text-yellow-900 font-medium underline">
                    Update Quote
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('checkout.submit') }}" method="POST" id="checkoutForm">
                    @csrf

                    <!-- Order Summary (Mobile/Tablet) -->
                    <div class="lg:hidden bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Order Summary</h3>
                        <div class="space-y-3">
                            @foreach($items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $item['product_name'] }} x {{ $item['quantity'] }}</span>
                                    <span class="font-medium text-copower-dark">£{{ number_format($item['total'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="border-t border-gray-200 pt-3 flex justify-between font-bold text-copower-dark">
                                <span>Total</span>
                                <span>£{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Contact Information</h3>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $userEmail) }}" 
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Order confirmation will be sent to this email</p>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Shipping Address</h3>
                        @if($addresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($addresses as $address)
                                    <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-copower-gray transition cursor-pointer">
                                        <input type="radio" 
                                               name="shipping_address_id" 
                                               value="{{ $address->id }}"
                                               {{ $defaultAddress && $defaultAddress->id == $address->id ? 'checked' : '' }}
                                               required>
                                        <div>
                                            <p class="font-medium text-copower-dark">{{ $address->recipient_name ?? $address->company_name }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->full_address }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-600 mb-3">No saved addresses found.</p>
                                <a href="#" class="text-copower-banner hover:underline text-sm">
                                    <i class="fas fa-plus mr-1"></i> Add New Address
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Billing Address -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Billing Address</h3>
                        <div class="mb-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       id="same_as_shipping" 
                                       checked
                                       @change="toggleBillingAddress()">
                                <span class="text-sm text-gray-700">Same as shipping address</span>
                            </label>
                        </div>
                        <div id="billingAddressContainer" style="display: none;">
                            @if($addresses->count() > 0)
                                <div class="space-y-3">
                                    @foreach($addresses as $address)
                                        <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-copower-gray transition cursor-pointer">
                                            <input type="radio" 
                                                   name="billing_address_id" 
                                                   value="{{ $address->id }}">
                                            <div>
                                                <p class="font-medium text-copower-dark">{{ $address->recipient_name ?? $address->company_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $address->full_address }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Shipping Method</h3>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-copower-gray transition cursor-pointer">
                                <input type="radio" 
                                       name="shipping_method" 
                                       value="standard" 
                                       checked
                                       required>
                                <div>
                                    <p class="font-medium text-copower-dark">Standard Delivery</p>
                                    <p class="text-sm text-gray-600">3-5 business days</p>
                                </div>
                            </label>
                            <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-copower-gray transition cursor-pointer">
                                <input type="radio" 
                                       name="shipping_method" 
                                       value="express">
                                <div>
                                    <p class="font-medium text-copower-dark">Express Delivery</p>
                                    <p class="text-sm text-gray-600">1-2 business days</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Payment Method</h3>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-copower-gray transition cursor-pointer">
                                <input type="radio" 
                                       name="payment_method" 
                                       value="bank_transfer" 
                                       checked
                                       required>
                                <div>
                                    <p class="font-medium text-copower-dark">Bank Transfer</p>
                                    <p class="text-sm text-gray-600">You will receive bank details after order confirmation</p>
                                </div>
                            </label>
                            <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-copower-gray transition cursor-pointer">
                                <input type="radio" 
                                       name="payment_method" 
                                       value="credit_account">
                                <div>
                                    <p class="font-medium text-copower-dark">Credit Account</p>
                                    <p class="text-sm text-gray-600">For approved credit account holders</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Customer Notes -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="font-bold text-copower-dark mb-4">Additional Notes</h3>
                        <textarea name="customer_notes" 
                                  rows="3" 
                                  placeholder="Any special instructions or notes for your order..."
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-copower-banner focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Terms -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" 
                                   id="terms" 
                                   name="terms" 
                                   required
                                   class="mt-1 h-4 w-4 text-copower-banner border-gray-300 rounded focus:ring-copower-banner">
                            <label for="terms" class="text-sm text-gray-700">
                                I agree to the 
                                <a href="#" class="text-copower-banner hover:underline">Terms & Conditions</a> 
                                and 
                                <a href="#" class="text-copower-banner hover:underline">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary (Desktop) -->
            <div class="hidden lg:block">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-copower-dark text-lg mb-4">Order Summary</h3>
                    
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                        @foreach($items as $item)
                            <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                                <div>
                                    <p class="font-medium text-copower-dark">{{ $item['product_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['variant_type'] }} x {{ $item['quantity'] }}</p>
                                </div>
                                <span class="font-medium text-copower-dark">£{{ number_format($item['total'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-copower-dark">£{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-copower-dark">Calculated at checkout</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium text-copower-dark">Included</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-copower-dark border-t border-gray-200 pt-3">
                            <span>Total</span>
                            <span>£{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Minimum Order Indicator -->
                    <div class="mt-4 p-3 rounded-lg {{ $meetsMinimum ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                        <div class="flex items-center justify-between text-sm">
                            <span class="{{ $meetsMinimum ? 'text-green-700' : 'text-yellow-700' }}">
                                <i class="fas {{ $meetsMinimum ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-2"></i>
                                Minimum Order: £{{ number_format($minimumOrderValue, 2) }}
                            </span>
                            <span class="font-medium {{ $meetsMinimum ? 'text-green-700' : 'text-yellow-700' }}">
                                {{ $meetsMinimum ? '✓ Met' : '£' . number_format($minimumOrderValue - $subtotal, 2) . ' needed' }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" 
                            form="checkoutForm" 
                            class="w-full mt-4 bg-copower-banner text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(!$meetsMinimum) disabled @endif>
                        <i class="fas fa-check mr-2"></i>
                        Submit Order
                    </button>
                    
                    @if(!$meetsMinimum)
                        <p class="text-xs text-red-500 mt-2 text-center">Please update your quote to meet the minimum order value</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkoutApp() {
    return {
        toggleBillingAddress() {
            const container = document.getElementById('billingAddressContainer');
            const checkbox = document.getElementById('same_as_shipping');
            container.style.display = checkbox.checked ? 'none' : 'block';
        }
    }
}
</script>
@endpush
@endsection