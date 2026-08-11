<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    /**
     * The admin route prefix (obscured)
     */
    protected $adminPrefix;

    public function __construct()
    {
        $this->adminPrefix = config('app.ADMIN_PREFIX', 'copower/sales_admin1');
    }

    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        // Check if admin already logged in
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        // Rate limiting
        $key = 'admin_login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ]);
        }

        // Normalize checkbox value ('on' -> boolean)
        if ($request->has('remember')) {
            $request->merge([
                'remember' => in_array($request->input('remember'), ['on', '1', 'true', 1, true], true),
            ]);
        }

        // Validate input with strict rules
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($validator);
        }

        // Find admin
        $admin = Admin::where('email', $request->email)->first();

        // Check if admin exists
        if (!$admin) {
            RateLimiter::hit($key, 300);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid credentials.']);
        }

        // Check if admin is active
        if (!$admin->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // Check if admin is locked
        if ($admin->isLocked()) {
            $minutes = $admin->remaining_lockout_time;
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Your account is locked. Try again in {$minutes} minutes."]);
        }

        // Attempt login
        if (Auth::guard('admin')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->remember ?? false
        )) {
            // Record successful login
            $admin->recordLogin($request->ip());
            RateLimiter::clear($key);

            // Regenerate session
            $request->session()->regenerate();

            // Check if password needs changing
            if ($admin->force_password_change) {
                return redirect()->route('admin.password.change');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        // Failed login attempt
        RateLimiter::hit($key, 300);
        $admin->incrementLoginAttempts();

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out.');
    }

    /**
     * Show change password page
     */
    public function showChangePassword()
    {
        return view('admin.auth.change-password');
    }

    /**
     * Handle password change
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $admin = auth()->guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->password = Hash::make($request->password);
        $admin->force_password_change = false;
        $admin->password_changed_at = now();
        $admin->save();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show forgot password page
     */
    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Handle forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:admins,email'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Generate reset token and send email (implement separately)
        // For now, just redirect back with success message

        return back()->with('success', 'Password reset link sent to your email.');
    }
}