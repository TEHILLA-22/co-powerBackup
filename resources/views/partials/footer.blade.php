{{-- resources/views/partials/footer.blade.php --}}
<footer class="bg-gray-900 text-gray-300 mt-auto">
    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div>
                <img src="{{ asset('images/copower-logo.png') }}" alt="Copower Wholesale" class="h-10 w-auto mb-4">
                <p class="text-sm text-gray-400 leading-relaxed">
                    Leading B2B wholesale distributor for health, beauty, and pharmaceutical products across the UK and Europe.
                </p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">Home</a></li>
                    <li><a href="{{ route('customer.products') }}" class="text-gray-400 hover:text-white transition">All Products</a></li>
                    <li><a href="{{ route('quote.bulk') }}" class="text-gray-400 hover:text-white transition">Bulk Order</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition">About Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Contact</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Customer Service</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Delivery Information</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Returns Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="text-gray-400 hover:text-white transition">Terms & Conditions</a></li>
                    <li><a href="{{ route('privacy-policy') }}" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Get In Touch</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt text-blue-400 mr-3 mt-1"></i>
                        <span class="text-gray-400">123 Business Park, London, UK, EC1A 1BB</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone text-blue-400 mr-3"></i>
                        <span class="text-gray-400">+44 20 1234 5678</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope text-blue-400 mr-3"></i>
                        <span class="text-gray-400">sales@copower.com</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-clock text-blue-400 mr-3"></i>
                        <span class="text-gray-400">Mon-Fri: 8:00 - 17:00</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-gray-800 py-4">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Copower Wholesale. All rights reserved.</p>
        </div>
    </div>
</footer>