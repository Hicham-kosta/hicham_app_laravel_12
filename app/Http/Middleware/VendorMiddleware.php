<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // app/Http/Middleware/VendorMiddleware.php
public function handle($request, Closure $next)
{
    if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'vendor') {
        return $next($request);
    }
    abort(403, 'Unauthorized access.');
}
}
