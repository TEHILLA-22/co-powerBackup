<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Super simple - just check if user is admin
        // We'll use a simple flag or role
        if (!$user || !$user->is_admin) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}