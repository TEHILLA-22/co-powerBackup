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

        // Check if user is approved
        if (!$user->is_approved) {
            return redirect()->route('auth.pending-approval')
                ->with('warning', 'Your account is pending approval. Please wait for admin approval.');
        }

        // Check if user is active
        if ($user->is_suspended ?? false) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }

        return $next($request);
    }
}