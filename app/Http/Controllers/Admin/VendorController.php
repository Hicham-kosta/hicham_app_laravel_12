<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\VendorDetailRequest;
use App\Services\Admin\VendorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Models\VendorDetail;

class VendorController extends Controller
{
    public function __construct(protected VendorService $vendorService)
    {}

    /**
     * Show Vendor details form
     */
    public function edit()
    {
        // Highlight sidebar menu
        Session::put('page', 'vendor-details');

        return view('admin.vendors.edit');
    }

    /**
     * Update Vendor details
     */
    public function update(VendorDetailRequest $request)
    {
        // Keep sidebar active after submit
        Session::put('page', 'vendor-details');


        $this->vendorService->updateVendorDetails($request);

        return redirect()->back()
            ->with('success_message', 'Vendor details updated successfully. Await admin approval');
    }

    public function deleteAddressProof(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $vendorDetail = $admin->vendorDetails;
        if($vendorDetail && $vendorDetail->address_proof_image){
            $path = public_path('front/images/vendor-docs/'.$vendorDetail->address_proof_image);
            if(File::exists($path)){
                File::delete($path);
            }
            $vendorDetail->update([
                'address_proof_image' => null,
                'is_verified' => 0
            ]);
        }
        return response()->json(['status' => true]);
    }
}
