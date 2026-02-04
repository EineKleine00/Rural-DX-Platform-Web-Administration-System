<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        // kalau belum login, lanjutkan request
        if (! Auth::check()) {
            return $next($request);
        }

        // kalau sudah login, arahkan ke dashboard
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
