<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'tenant' => \App\Http\Middleware\SetTenant::class,
            'central_user' => \App\Http\Middleware\CentralUserMiddleware::class,
            'super_admin' => \App\Http\Middleware\CentralUserMiddleware::class, // Legacy alias
            'onboarded' => \App\Http\Middleware\EnsureOnboarded::class,
            'client.auth' => \App\Http\Middleware\ClientAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
