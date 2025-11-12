<?php

namespace App\Http\Controllers\Front;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Front\UpdateAccountRequest;
use App\Http\Requests\Front\UpdatePasswordRequest;
use App\Models\Country;
use App\Models\State;
use App\Services\Front\AuthService;

class AccountController extends BaseController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->middleware('auth');
        $this->authService = $authService;
    }

    public function showAccount()
    {
        $user = Auth::user();
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $us = Country::where('name', 'United States')->first();
        $usStates = $us ? $us->states()->where('is_active', true)->orderBy('name')->get() : collect();
        
        return view('front.auth.account', compact('user', 'countries', 'usStates'));
    }

    public function updateAccount(UpdateAccountRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();
            $user = $this->authService->updateAccount($user, $data);
            
            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true, 
                'message' => 'Account updated successfully', 
                'user' => $user->only([
                    'name', 'address_line1', 'address_line2', 'city', 'county', 
                    'postcode', 'country', 'phone', 'company'
                ])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating account: ' . $e->getMessage()
            ], 500);
        }
    }

    // In your controller
public function showChangePasswordForm()
{
    $user = Auth::user();
    return view('front.auth.change_password', compact('user'));
}

public function changePassword(UpdatePasswordRequest $request)
{
    $user = Auth::user();
    $this->authService->changePassword($user, $request->validated()['password']);
    
    if($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
    return redirect()->route('user.change.password')->with('success', 'Password changed successfully');
}
}