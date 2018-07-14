<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        return Auth::guard($guard)->check()
            ? redirect()->route('admin')
            : $next($request);
    }
}
