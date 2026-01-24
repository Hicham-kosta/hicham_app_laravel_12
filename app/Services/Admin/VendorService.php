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
    public function updateVendorDetails($request)
    {
        $admin = Auth::guard('admin')->user();
        $data = $request->validated();
        if($request->hasFile('address_proof_image')){
            $file = $request->file('address_proof_image');
            $filename = 'address_proof_'.$admin->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('front/images/vendor-docs');

            if(!file_exists($destination)){
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $data['address_proof_image'] = $filename;
        }

        // Reset verification after update
        $data['is_verified'] = 0;

        VendorDetail::updateOrCreate(
            ['admin_id' => $admin->id],
            $data
        );
    }
}