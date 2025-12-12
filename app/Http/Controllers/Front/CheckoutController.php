<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Front\AddAddressRequest;
use App\Http\Requests\Front\CheckoutRequest;
use App\Models\Country;
use App\Models\Order;
use App\Services\Front\CheckoutService;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Show checkout page
     */
    public function index(Request $request)
{
    $user = Auth::user();
    
    // Debug: Check which variables are null
    $cart = $this->checkoutService->getCartForCheckout($user);
    $addresses = $this->checkoutService->getUserAddresses($user);
    
    // Add these debug lines temporarily
    Log::debug('Cart data:', ['cart' => $cart]);
    Log::debug('Addresses data:', ['addresses' => $addresses]);
    
    // Or dd() to see immediately
    // dd(['cart' => $cart, 'addresses' => $addresses]);
    
    $countries = Country::where('is_active', true)->orderBy('name')->get();
    $uk = Country::where('name', 'United Kingdom')->first();
    $ukStates = $uk ? $uk->states()->orderBy('name')->get() : collect();

    return view('front.checkout.index', compact('cart', 'addresses', 'countries', 'ukStates'));
}

    
public function placeOrder(CheckoutRequest $request)
{
    $user = Auth::user();
    
    Log::debug('=== PLACE ORDER CONTROLLER START ===');
    Log::debug('User: ' . ($user ? $user->id : 'null'));

    // FIX: Use Address model with correct table reference
    $addressCount = \App\Models\Address::where('user_id', $user->id)->count();
    Log::debug('User address count: ' . $addressCount);
    
    if(!$user || $addressCount === 0){
        Log::debug('NO ADDRESS FOUND - redirecting to checkout');
        return redirect()->route('checkout.index')->with('error', 'Please add a delivery address first');
    }
    
    $payload = $request->validated();
    Log::debug('Validated payload:', $payload);
    
    if(empty($payload['address_id'])){
        Log::debug('NO ADDRESS SELECTED - returning back');
        return back()->with('error', 'Please select a delivery address before place an order!');
    }
    
    Log::debug('Calling checkoutService->createOrderFromCart...');
    $result = $this->checkoutService->createOrderFromCart($user, $payload);
    Log::debug('Order creation result:', $result);

    if ($result['success']) {
        Log::debug('ORDER SUCCESS - clearing cart and redirecting to thanks page');
        $this->checkoutService->clearCart($user);

        return redirect()->route('checkout.thanks', ['orderId' => $result['order']])
            ->with('success', 'Order placed successfully. Order ID: '.$result['order']);
    }
    
    Log::debug('ORDER FAILED - returning back with error');
    return back()->with('error', $result['message'] ?? 'Order could not be placed');
}

    public function thanks($orderId){
        $user = Auth::user();
        $order = Order::where('id', $orderId)
        ->where('user_id', $user->id)->first();

        if(!$order){
            return redirect()->route('checkout.index')->with('error', 'Order not found');
        }
        return view('front.checkout.thanks', compact('order'));
    }

    /**
     * Add new address
     */
    public function addAddress(AddAddressRequest $request)
    {
        $user = Auth::user();

        $address = $this->checkoutService->addAddress(
        $user,
        $request->validated());

      if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'address' => $address,
        ]);
      }

    return redirect()
        ->route('checkout.index')
        ->with('success', 'Address added successfully');
    }
    
    /** 
     * Update existing delivery address
    */
    public function updateAddress(AddAddressRequest $request)
{
    $user = Auth::user();
    $payload = $request->validated();
    $addressId = $request->input('address_id');

    if(empty($addressId)){
        if($request->ajax()){
            return response()->json(['success' => false, 'message' => 'Address ID is missing'], 400);
        }
        return back()->with('error', 'Address ID is missing');
    }

    try {
        $address = $this->checkoutService->updateAddress($user, $addressId, $payload);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'address' => $address,
            ]);
        }
        return redirect()->route('checkout.index')->with('success', 'Address updated successfully');
        
    } catch (\Exception $e) {
        Log::error('Update address error: ' . $e->getMessage());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address: ' . $e->getMessage(),
            ], 422);
        }
        return back()->with('error', 'Failed to update address');
    }
}

    /**
     * Delete delivery address
     */

    public function deleteAddress(Request $request)
    {
        $user = Auth::user();
        $addressId = $request->input('address_id');

        if(empty($addressId)){
            return response()->json(['success' => false, 'message' => 'Address ID is missing'], 400);
        }
        $deleted = $this->checkoutService->deleteAddress($user, $addressId);

        if($deleted){
            return response()->json(['success' => true, 'message' => 'Address deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Address could not be deleted'], 500);
    }
}