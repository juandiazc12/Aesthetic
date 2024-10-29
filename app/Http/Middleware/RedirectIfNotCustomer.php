<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotCustomer
{
    public function handle($request, Closure $next, $guard = 'customer')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('customer.login');
        }

        return $next($request);
    }
}
