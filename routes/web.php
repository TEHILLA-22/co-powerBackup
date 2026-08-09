<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\QuoteController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PriceListController;
use App\Http\Middleware\B2BAccessMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

//==============PAGE ROUTES ===============
Route::view('/about', 'pages.about')->name('about');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::view('/price-list', 'pages.price-list')->name('price-list');
Route::get('/price-list/download', [PriceListController::class, 'download'])->name('price-list.download');



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
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/pending-approval', [RegisterController::class, 'pendingApproval'])->name('auth.pending-approval');
    Route::get('/verify-otp', [RegisterController::class, 'showVerifyOtp'])->name('auth.verify-otp');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('auth.verify-otp.submit');
    Route::post('/verify-otp/resend', [RegisterController::class, 'resendOtp'])->name('auth.verify-otp.resend');
});

// ==================== CUSTOMER ROUTES ====================
Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::post('/products/{slug}/quote', [\App\Http\Controllers\Shop\QuoteController::class, 'add'])->name('product.add-to-quote');
    Route::get('/quick-search', [ProductController::class, 'quickSearch'])->name('product.quick-search');
    Route::get('/barcode-lookup', [ProductController::class, 'barcodeLookup'])->name('product.barcode-lookup');
});

// ==================== QUOTE ROUTES ====================
Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('quote')->name('quote.')->group(function () {
    Route::get('/', [QuoteController::class, 'index'])->name('index');
    Route::post('/update/{key}', [QuoteController::class, 'updateItem'])->name('update');
    Route::delete('/remove/{key}', [QuoteController::class, 'removeItem'])->name('remove');
    Route::post('/clear', [QuoteController::class, 'clear'])->name('clear');
    Route::get('/bulk', [QuoteController::class, 'bulkIndex'])->name('bulk');
    Route::post('/bulk-validate', [QuoteController::class, 'bulkValidate'])->name('bulk-validate');
    Route::post('/bulk-store', [QuoteController::class, 'bulkStore'])->name('bulk-store');
    Route::post('/parse-paste', [QuoteController::class, 'parsePaste'])->name('parse-paste');
    Route::post('/submit', [QuoteController::class, 'submit'])->name('submit');
    Route::get('/confirmation/{quote}', [QuoteController::class, 'confirmation'])->name('confirmation');
});

// ==================== CHECKOUT ROUTES ====================
Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/submit', [CheckoutController::class, 'submit'])->name('submit');
});

Route::middleware(['auth', B2BAccessMiddleware::class])->prefix('order')->name('order.')->group(function () {
    Route::get('/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('confirmation');
});











// routes/web.php - Add all admin routes at the bottom

// ==================== ADMIN ROUTES ====================
Route::prefix('copower/sales_admin1')->name('admin.')->group(function () {
    // Guest Admin Routes
    Route::middleware(['admin.guest'])->group(function () {
        // Registration
        Route::get('/register', [\App\Http\Controllers\Admin\AdminRegistrationController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [\App\Http\Controllers\Admin\AdminRegistrationController::class, 'register'])->name('register.submit');
        Route::post('/check-status', [\App\Http\Controllers\Admin\AdminRegistrationController::class, 'checkStatus'])->name('check-status');
        
        // OTP Verification
        Route::get('/verify', [\App\Http\Controllers\Admin\AdminRegistrationController::class, 'showVerifyForm'])->name('verify');
        Route::post('/verify', [\App\Http\Controllers\Admin\AdminRegistrationController::class, 'verifyOtp'])->name('verify.submit');
        Route::post('/verify/resend', [\App\Http\Controllers\Admin\AdminRegistrationController::class, 'resendOtp'])->name('verify.resend');
        
        // Login
        Route::get('/login', [\App\Http\Controllers\Admin\AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Admin\AdminLoginController::class, 'login'])->name('login.submit');
        Route::get('/forgot-password', [\App\Http\Controllers\Admin\AdminLoginController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [\App\Http\Controllers\Admin\AdminLoginController::class, 'forgotPassword'])->name('password.email');
    });

    // Authenticated Admin Routes
    Route::middleware(['admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Password Change
        Route::get('/password/change', [\App\Http\Controllers\Admin\AdminLoginController::class, 'showChangePassword'])->name('password.change');
        Route::post('/password/change', [\App\Http\Controllers\Admin\AdminLoginController::class, 'changePassword'])->name('password.update');
        
        // Logout
        Route::post('/logout', [\App\Http\Controllers\Admin\AdminLoginController::class, 'logout'])->name('logout');
        
        // ==================== CUSTOMERS ====================
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/pending', [\App\Http\Controllers\Admin\CustomerApprovalController::class, 'index'])->name('pending');
            Route::get('/{user}', [\App\Http\Controllers\Admin\CustomerApprovalController::class, 'show'])->name('show');
            Route::post('/{user}/approve', [\App\Http\Controllers\Admin\CustomerApprovalController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [\App\Http\Controllers\Admin\CustomerApprovalController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [\App\Http\Controllers\Admin\CustomerApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        });

        // ==================== PRODUCTS ====================
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProductManagementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ProductManagementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ProductManagementController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [\App\Http\Controllers\Admin\ProductManagementController::class, 'edit'])->name('edit');
            Route::put('/{product}', [\App\Http\Controllers\Admin\ProductManagementController::class, 'update'])->name('update');
            Route::delete('/{product}', [\App\Http\Controllers\Admin\ProductManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductManagementController::class, 'toggleStatus'])->name('toggle-status');
            
            // Import/Export
            Route::get('/export', [\App\Http\Controllers\Admin\ProductManagementController::class, 'export'])->name('export');
            Route::get('/import', [\App\Http\Controllers\Admin\ProductManagementController::class, 'importForm'])->name('import-form');
            Route::post('/import', [\App\Http\Controllers\Admin\ProductManagementController::class, 'import'])->name('import');
            Route::post('/import-sian', [\App\Http\Controllers\Admin\ProductManagementController::class, 'importSian'])->name('import-sian');
            Route::get('/template', [\App\Http\Controllers\Admin\ProductManagementController::class, 'template'])->name('template');
        });

        // ==================== ORDERS ====================
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OrderProcessingController::class, 'index'])->name('index');
            Route::get('/{order}', [\App\Http\Controllers\Admin\OrderProcessingController::class, 'show'])->name('show');
            Route::post('/{order}/start-processing', [\App\Http\Controllers\Admin\OrderProcessingController::class, 'startProcessing'])->name('start-processing');
            Route::post('/{order}/approve', [\App\Http\Controllers\Admin\OrderProcessingController::class, 'approve'])->name('approve');
            Route::post('/{order}/reject', [\App\Http\Controllers\Admin\OrderProcessingController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [\App\Http\Controllers\Admin\OrderProcessingController::class, 'bulkApprove'])->name('bulk-approve');
        });

        // ==================== QUOTES ====================
        Route::prefix('quotes')->name('quotes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuoteController::class, 'index'])->name('index');
            Route::get('/{quote}', [\App\Http\Controllers\Admin\QuoteController::class, 'show'])->name('show');
            Route::post('/{quote}/approve', [\App\Http\Controllers\Admin\QuoteController::class, 'approve'])->name('approve');
            Route::post('/{quote}/reject', [\App\Http\Controllers\Admin\QuoteController::class, 'reject'])->name('reject');
        });

        // ==================== REPORTS ====================
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [\App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
        });

        // ==================== SETTINGS ====================
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
            Route::post('/store', [\App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('store');
            Route::delete('/{key}', [\App\Http\Controllers\Admin\SettingsController::class, 'destroy'])->name('destroy');
        });
    });
});