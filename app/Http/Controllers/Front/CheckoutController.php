<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\PayPalRedirectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Front\AddAddressRequest;
use App\Http\Requests\Front\CheckoutRequest;
use App\Models\Country;
use App\Models\Address;
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
    
    $addresses = $this->checkoutService->getUserAddresses($user);
    //Pick selected address->old value-> first address
    $selectedAddressId = $request->old('address_id');

    if(!$selectedAddressId && $addresses->count() > 0){
        $selectedAddressId = $addresses->first()->id;
    }

    //Cart with shipping
    $cart = $this->checkoutService->getCartForCheckout($user, $selectedAddressId);

    $countries = Country::where('is_active', true)->orderBy('name')->get();
    $us = Country::where('name', 'United States')->first(); // Fixed variable name
    $usStates = $us ? $us->states()->orderBy('name')->get() : collect();

    // Get current currency for display
    $currentCurrency = getCurrentCurrency();
    $currCode = $currentCurrency->code ?? 'USD';

    // Paypal Previews: Compute USD converted amount and conversion rate
    $paypalPreview = null;
    
    try{
        $originalCurrencyCode = $currCode;

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

    return view('front.checkout.index', compact('cart', 'addresses', 'countries', 'usStates', 'paypalPreview', 'currCode'));
}

    
public function placeOrder(CheckoutRequest $request)
{
    $user = Auth::user();

    if (!$user || Address::where('user_id', $user->id)->count() === 0) {
        return redirect()->route('checkout.index')
            ->with('error', 'Please add a delivery address first');
    }

    $payload = $request->validated();

    if (empty($payload['address_id'])) {
        return $request->ajax()
            ? response()->json([
                'success' => false,
                'message' => 'Please select a delivery address before placing an order!',
            ], 422)
            : back()->with('error', 'Please select a delivery address before placing an order!');
    }

    // ✅ ALWAYS CREATE ORDER FIRST (including PayPal)
    $result = $this->checkoutService->createOrderFromCart($user, $payload);

    if (!$result['success']) {
        return $request->ajax()
            ? response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Order could not be placed',
            ], 500)
            : back()->with('error', $result['message'] ?? 'Order could not be placed');
    }

    $order = $result['order'];

    // ✅ PayPal flow
    if (strtolower($payload['payment_method']) === 'paypal') {

        // Clear cart AFTER order is created
        $this->checkoutService->clearCart($user);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'paypal_data' => [
                'subtotal' => (float) $order->subtotal,
                'shipping' => (float) $order->shipping,
                'total' => (float) $order->total,
                'currency' => getCurrentCurrency()->code ?? 'USD',
            ],
        ]);
    }

    // Non-PayPal payments (COD, bank transfer, etc.)
    $this->checkoutService->clearCart($user);

    return $request->ajax()
        ? response()->json([
            'success' => true,
            'order_id' => $order->id,
        ])
        : redirect()->route('checkout.thanks', ['orderId' => $order->id]);
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

    public function calculateShipping(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 401);
    }

    $addressId = $request->input('address_id'); // ✅ FIX

    if (!$addressId) {
        return response()->json([
            'success' => false,
            'message' => 'Address ID missing'
        ], 422);
    }

    $cart = $this->checkoutService->getCartForCheckout($user, $addressId);

    $curr = getCurrentCurrency();
    $currCode = $curr->code ?? 'USD';

    return response()->json([
        'success' => true,
        'shipping' => $cart['shipping'],
        'total' => $cart['total_numeric'],
        'shipping_formatted' => formatCurrency($cart['shipping'], $currCode),
        'total_formatted' => formatCurrency($cart['total_numeric'], $currCode),
    ]);
}

}