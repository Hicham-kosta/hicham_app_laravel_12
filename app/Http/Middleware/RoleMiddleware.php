<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated with admin guard
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();
        
        // If no specific roles provided, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($admin->role === $role) {
                return $next($request);
            }
        }

        // User doesn't have required role
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Redirect to dashboard with error message
        return redirect()->route('admin.dashboard')
            ->with('error_message', 'You are not authorized to access this page.');
    }
}