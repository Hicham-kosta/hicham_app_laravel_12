<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Front\VendorRegisterRequest;
use App\Http\Requests\Front\RegisterRequest;
use App\Mail\VendorRegistered;
use App\Mail\VendorConfirmed;
use App\Services\Front\VendorService;
use Illuminate\Support\Facades\Mail;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $vendorService)
    {}

    /**
     * Vendor Registration
     */
    // In VendorController.php
public function register(RegisterRequest $request)
{
    $vendor = $this->vendorService->createVendor($request->validated());
    Mail::to($vendor->email)->send(new VendorRegistered($vendor));
    return response()->json([
        'success' => true,
        'message' => 'Thanks for registering as Vendor. Please confirm your email to activate your account.',
        'redirect' => route('user.login')
    ]);
}

    /**
     * Vendor email confirmation
     */
    public function confirm($code)
    {
        $email = base64_decode($code);
        $vendor = $this->vendorService->activateVendor($email);
        Mail::to($vendor->email)->send(new VendorConfirmed($vendor));

        return redirect()->route('user.login')
        ->with('success_message', 'Your Vendor account is activated. You can login now.');

    }
}
