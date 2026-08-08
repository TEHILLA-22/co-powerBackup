// routes/admin.php
<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerApprovalController;
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\OrderProcessingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes (Obscured)
|--------------------------------------------------------------------------
*/

// ==================== ADMIN GUEST ROUTES ====================
Route::middleware(['admin.guest'])->group(function () {
    // Registration
    Route::get('/register', [AdminRegistrationController::class, 'showRegistrationForm'])->name('admin.register');
    Route::post('/register', [AdminRegistrationController::class, 'register'])->name('admin.register.submit');
    Route::post('/check-status', [AdminRegistrationController::class, 'checkStatus'])->name('admin.check-status');
    
    // OTP Verification
    Route::get('/verify', [AdminRegistrationController::class, 'showVerifyForm'])->name('admin.verify');
    Route::post('/verify', [AdminRegistrationController::class, 'verifyOtp'])->name('admin.verify.submit');
    Route::post('/verify/resend', [AdminRegistrationController::class, 'resendOtp'])->name('admin.verify.resend');
    
    // Login
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
    Route::get('/forgot-password', [AdminLoginController::class, 'showForgotPassword'])->name('admin.password.request');
    Route::post('/forgot-password', [AdminLoginController::class, 'forgotPassword'])->name('admin.password.email');
});

// ==================== ADMIN AUTHENTICATED ROUTES ====================
// Using auth:admin guard and then checking role in controller
Route::middleware(['auth:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Password Change
    Route::get('/password/change', [AdminLoginController::class, 'showChangePassword'])->name('admin.password.change');
    Route::post('/password/change', [AdminLoginController::class, 'changePassword'])->name('admin.password.update');
    
    // Logout
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    
    // ==================== CUSTOMER MANAGEMENT ====================
    Route::prefix('customers')->name('admin.customers.')->group(function () {
        Route::get('/pending', [CustomerApprovalController::class, 'index'])->name('pending');
        Route::get('/{user}', [CustomerApprovalController::class, 'show'])->name('show');
        Route::post('/{user}/approve', [CustomerApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [CustomerApprovalController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [CustomerApprovalController::class, 'bulkApprove'])->name('bulk-approve');
    });

    // ==================== PRODUCT MANAGEMENT ====================
    Route::prefix('products')->name('admin.products.')->group(function () {
        Route::get('/', [ProductManagementController::class, 'index'])->name('index');
        Route::get('/create', [ProductManagementController::class, 'create'])->name('create');
        Route::post('/', [ProductManagementController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductManagementController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductManagementController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/toggle-status', [ProductManagementController::class, 'toggleStatus'])->name('toggle-status');
        
        // Import/Export
        Route::get('/export', [ProductManagementController::class, 'export'])->name('export');
        Route::get('/import', [ProductManagementController::class, 'importForm'])->name('import-form');
        Route::post('/import', [ProductManagementController::class, 'import'])->name('import');
        Route::get('/template', [ProductManagementController::class, 'template'])->name('template');
    });

    // ==================== ORDER MANAGEMENT ====================
    Route::prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', [OrderProcessingController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderProcessingController::class, 'show'])->name('show');
        Route::post('/{order}/approve', [OrderProcessingController::class, 'approve'])->name('approve');
        Route::post('/{order}/reject', [OrderProcessingController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [OrderProcessingController::class, 'bulkApprove'])->name('bulk-approve');
    });
});