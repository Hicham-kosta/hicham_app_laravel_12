<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\VendorDetailRequest;
use App\Services\Admin\VendorService;
use Illuminate\Support\Facades\Session;

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
}
