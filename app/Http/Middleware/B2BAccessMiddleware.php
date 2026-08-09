<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class B2BAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('warning', 'Please login to access this page.');
        }

        // Check if user is active (not suspended)
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }

        // Email must be verified (OTP) before accessing the shop
        if (!$user->is_verified) {
            return redirect()->route('auth.verify-otp')
                ->with('warning', 'Please verify your email address to continue.');
        }

        return $next($request);
    }
}