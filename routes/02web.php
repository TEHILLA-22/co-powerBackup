<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CustomerApprovalController;
use App\Http\Middleware\B2BAccessMiddleware;
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Shop\QuoteController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Admin\OrderProcessingController;
use App\Http\Controllers\Shop\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/about', 'pages.about')->name('about');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/product', 'shop.catalog.index')->name('product');

// ==================== AUTH ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::get('/pending-approval', [RegisterController::class, 'pendingApproval'])->name('auth.pending-approval');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/resend-verification', [RegisterController::class, 'resendVerification'])->name('auth.resend-verification');
});

// ==================== CUSTOMER ROUTES (B2B Access) ====================
Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('customer')->name('customer.')->group(function () {
    // Product Catalog - This is the first page customers see after login
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/quick-search', [ProductController::class, 'quickSearch'])->name('product.quick-search');
    Route::get('/barcode-lookup', [ProductController::class, 'barcodeLookup'])->name('product.barcode-lookup');
    
    // Dashboard (optional - can be removed if not needed)
    Route::get('/dashboard', function () {
        return redirect()->route('customer.products');
    })->name('dashboard');
});

// ==================== QUOTE ROUTES ====================
Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('quote')->name('quote.')->group(function () {
    // Quote Summary
    Route::get('/', [QuoteController::class, 'index'])->name('index');
    
    // Bulk Order
    Route::get('/bulk', [QuoteController::class, 'bulkIndex'])->name('bulk');
    Route::post('/bulk-validate', [QuoteController::class, 'bulkValidate'])->name('bulk-validate');
    Route::post('/bulk-store', [QuoteController::class, 'bulkStore'])->name('bulk-store');
    Route::post('/parse-paste', [QuoteController::class, 'parsePaste'])->name('parse-paste');
    
    // Quote Item Management (AJAX)
    Route::post('/update/{key}', [QuoteController::class, 'updateItem'])->name('update');
    Route::delete('/remove/{key}', [QuoteController::class, 'removeItem'])->name('remove');
    Route::post('/clear', [QuoteController::class, 'clear'])->name('clear');
    
    // Quote Submission
    Route::post('/submit', [QuoteController::class, 'submit'])->name('submit');
    Route::get('/confirmation/{quote}', [QuoteController::class, 'confirmation'])->name('confirmation');
});

// ==================== CHECKOUT & ORDER ROUTES ====================
Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/submit', [CheckoutController::class, 'submit'])->name('submit');
});

Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('order')->name('order.')->group(function () {
    Route::get('/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('confirmation');
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Customer Approval
    Route::get('/customers/pending', [CustomerApprovalController::class, 'index'])->name('customers.pending');
    Route::get('/customers/{user}', [CustomerApprovalController::class, 'show'])->name('customers.show');
    Route::post('/customers/{user}/approve', [CustomerApprovalController::class, 'approve'])->name('customers.approve');
    Route::post('/customers/{user}/reject', [CustomerApprovalController::class, 'reject'])->name('customers.reject');
    Route::post('/customers/bulk-approve', [CustomerApprovalController::class, 'bulkApprove'])->name('customers.bulk-approve');

    // Product Management
    Route::get('/products', [ProductManagementController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductManagementController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductManagementController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductManagementController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductManagementController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductManagementController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/toggle-status', [ProductManagementController::class, 'toggleStatus'])->name('products.toggle-status');
    
    // Import/Export
    Route::get('/products/export', [ProductManagementController::class, 'export'])->name('products.export');
    Route::get('/products/import', [ProductManagementController::class, 'importForm'])->name('products.import-form');
    Route::post('/products/import', [ProductManagementController::class, 'import'])->name('products.import');
    Route::get('/products/template', [ProductManagementController::class, 'template'])->name('products.template');

    // Order Processing
    Route::get('/orders', [OrderProcessingController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderProcessingController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [OrderProcessingController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/reject', [OrderProcessingController::class, 'reject'])->name('orders.reject');
});