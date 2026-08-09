<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        // Rate limiting
        $key = 'login.' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Find user
        $user = User::where('email', $validated['email'])->first();

        // Check if user exists and is locked
        if ($user && $user->isLocked()) {
            $minutes = $user->remaining_lockout_time;
            throw ValidationException::withMessages([
                'email' => "Your account is locked. Please try again in {$minutes} minutes.",
            ]);
        }

        // Attempt login
        if (Auth::attempt(
            ['email' => $validated['email'], 'password' => $validated['password']],
            $validated['remember'] ?? false
        )) {
            $user = auth()->user();

            // Check if email is verified
            if (!$user->is_verified) {
                Auth::logout();
                
                // Clear rate limiter
                RateLimiter::clear($key);
                
                return redirect()->route('auth.verify-otp')
                    ->with('warning', 'Please verify your email address before continuing.');
            }

            // Check if user is active (not suspended)
            if (!$user->is_active) {
                Auth::logout();
                
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
            }

            // Record successful login
            $user->recordLogin($request->ip());
            
            // Clear rate limiter
            RateLimiter::clear($key);

            // Log the login
            AuditLog::log(
                'login',
                'user',
                $user->id,
                null,
                ['email' => $user->email, 'ip' => $request->ip()],
                'User logged in'
            );

            // Regenerate session
            $request->session()->regenerate();

            return redirect()->intended(route('customer.products'))
                ->with('success', 'Welcome back, ' . $user->first_name . '!');

        } else {
            // Failed login attempt
            RateLimiter::hit($key, 300); // 5 minutes

            // If user exists, increment login attempts
            if ($user) {
                $user->incrementLoginAttempts();
            }

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            AuditLog::log(
                'logout',
                'user',
                $user->id,
                null,
                ['email' => $user->email],
                'User logged out'
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out.');
    }

    /**
     * Show pending approval page (legacy, retained for safety)
     */
    public function pendingApproval()
    {
        $user = auth()->user();

        // If no user, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // If verified, redirect to the shop
        if ($user->is_verified) {
            return redirect()->route('customer.products');
        }

        return redirect()->route('auth.verify-otp');
    }
}