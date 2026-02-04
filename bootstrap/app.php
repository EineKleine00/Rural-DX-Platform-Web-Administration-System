<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // Routing configuration
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Middleware configuration
    ->withMiddleware(function (Middleware $middleware) {
        /*
        |--------------------------------------------------------------------------
        | Global Middleware (opsional)
        |--------------------------------------------------------------------------
        | Kamu bisa tambahkan middleware global yang akan dijalankan di setiap request.
        | Contoh:
        | $middleware->append(\App\Http\Middleware\CheckForMaintenanceMode::class);
        */

        $middleware->web(append: [
            // middleware khusus web routes (opsional)
        ]);

        /*
        |--------------------------------------------------------------------------
        | Alias Middleware
        |--------------------------------------------------------------------------
        | Di Laravel 12, custom middleware seperti 'role' harus didaftarkan di sini.
        | Kamu bisa memanggilnya di route dengan: ->middleware('role:admin')
        */
         $middleware->alias([
             'auth' => \App\Http\Middleware\Authenticate::class,
             'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
             'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

             // ✅ Custom middleware kamu
             'role' => \App\Http\Middleware\RoleMiddleware::class,
         ]);
    })

    // Exception handler (biarkan default)
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
