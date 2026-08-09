<?php

use App\Http\Middleware\AdminGuestMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\B2BAccessMiddleware;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\XssProtectionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'admin.guest' => AdminGuestMiddleware::class,
            'b2b.access' => B2BAccessMiddleware::class,
            'rate.limit' => RateLimitMiddleware::class,
            'xss.protection' => XssProtectionMiddleware::class,
            'security.headers' => SecurityHeadersMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
