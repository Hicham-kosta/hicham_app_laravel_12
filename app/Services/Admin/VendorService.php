<?php
namespace App\Services\Admin;

use App\Models\VendorDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorService
{
    /** 
     * Create or update Vendor Details
     */
    public function updateVendorDetails(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        VendorDetail::updateOrCreate(
            ['admin_id' => $admin->id],
            $request->validated()
        );
    }
}