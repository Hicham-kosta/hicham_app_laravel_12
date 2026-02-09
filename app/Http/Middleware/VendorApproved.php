<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorApproved
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated as admin
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();
        
        // Check if user is a vendor
        if ($admin->role !== 'vendor') {
            return redirect()->route('admin.dashboard')
                ->with('error_message', 'Access denied. Vendor only.');
        }
        
        // Check if vendor is approved
        if (!$admin->vendorDetails || $admin->vendorDetails->is_verified != 1) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error_message', 'Your vendor account is not approved yet.');
        }
        
        // Check if vendor is active
        if ($admin->status != 1) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error_message', 'Your vendor account is inactive.');
        }
        
        return $next($request);
    }
}