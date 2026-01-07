<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\PayPalRedirectController;
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
    
    $countries = Country::where('is_active', true)->orderBy('name')->get();
    $uk = Country::where('name', 'United Kingdom')->first();
    $ukStates = $uk ? $uk->states()->orderBy('name')->get() : collect();

    // Paypal Previews: Compute USD converted amount and conversion rate
    $paypalPreview = null;
    
    try{
        $currentCurrency = getCurrentCurrency();
        $originalCurrencyCode = $currentCurrency->code ?? 'USD';

        $originalAmount = $cart['total_numeric'] ?? ($cart['total'] ?? 0);
        $originalAmount = is_numeric($originalAmount) ? (float)$originalAmount : (float)normalizeAmount($originalAmount);

        if($originalAmount > 0){
            // Simple fallback 1:1 conversion
            $paypalPreview = [
                'original_amount' => round($originalAmount, 2),
                'original_currency' => $originalCurrencyCode,
                'converted_amount' => round($originalAmount, 2),
                'conversion_rate' => 1.0,
            ];
        }
    }catch(\Throwable $e){
        Log::error('Paypal preview conversion error: ' . $e->getMessage());
        $paypalPreview = null;
    }

    return view('front.checkout.index', compact('cart', 'addresses', 'countries', 'ukStates', 'paypalPreview'));
}

    
public function placeOrder(CheckoutRequest $request)
{
    $user = Auth::user();

    if (!$user || Address::where('user_id', $user->id)->count() === 0) {
        return redirect()->route('checkout.index')->with('error', 'Please add a delivery address first');
    }

    $payload = $request->validated();

    if(empty($payload['address_id'])){
        if($request->ajax()){
            return response()->json([
                'success' => false,
                'message' => 'Please select a delivery address before place an order!',
            ], 422);
        }
        return back()->with('error', 'Please select a delivery address before place an order!');
    }

    if(isset($payload['payment_method']) && strtolower($payload['payment_method']) === 'paypal'){

        $msg = 'Please complete payment using the paypal button on the checkout page.';
        if($request->ajax()){
            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 422);
        }
        return back()->with('error', $msg);
    }
    $result = $this->checkoutService->createOrderFromCart($user, $payload);
    if($result['success']){
        $this->checkoutService->clearCart($user);
        if($request->ajax()){
            return response()->json([
                'success' => true,
                'order_id' => $result['order']->id,
            ]);
        }
        return redirect()->route('user.checkout.thanks', ['orderId' => $result['order']->id]);
    }

    if($request->ajax()){
        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Order could not be placed',
        ], 500);
    }
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