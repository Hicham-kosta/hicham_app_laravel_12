<?php

namespace App\Services\Front;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\Front\CartService;
use App\Models\Address;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Return cart structure for checkout (delegates to CartService->getCart())
     * This returns the exact structure CartService provides
     * items(array), subtotal_numeric, subtotal (formatted), discount (numeric), Wallet (numeric), total (numeric), total (formatted)
     */

    public function getCartForCheckout($user = null)
    {
        // If your CartService requires a user, pass it here
        return $this->cartService->getCart();
    }
    
    /**
     * Return user addresses
     */
    public function getUserAddresses($user)
    {
        if(!$user) return collect([]);

        return Address::where('user_id', $user->id)->get();
    }

    /**
     * Return user wallets
     */
    public function getUserWallets($user)
    {
        if(!$user) return collect([]);

        return Address::where('user_id', $user->id)->get();
    }

    /**
     * Create a new order
     * payload contains at least 'address_id' and 'payment_method'
     */
    public function createOrderFromCart($user, array $payload)
{
    $cart = $this->getCartForCheckout($user);
    
    // DEBUG: Log cart structure
    Log::debug('=== CREATE ORDER DEBUG START ===');
    Log::debug('User ID: ' . ($user?->id ?? 'null'));
    Log::debug('Cart keys: ' . json_encode(array_keys($cart)));
    Log::debug('Cart items count: ' . count($cart['cartItems'] ?? []));
    Log::debug('Cart subtotal_numeric: ' . ($cart['subtotal_numeric'] ?? 'NOT FOUND'));
    Log::debug('Cart total_numeric: ' . ($cart['total_numeric'] ?? 'NOT FOUND'));
    Log::debug('Payload address_id: ' . ($payload['address_id'] ?? 'NOT FOUND'));

    // validate cart items
    if(empty($cart['cartItems']) || count($cart['cartItems']) === 0){
        Log::debug('CART VALIDATION FAILED: Cart is empty');
        return ['success' => false, 'message' => 'Cart is empty'];
    }

    DB::beginTransaction();
    
    try {
        Log::debug('Starting order creation...');
        
        $orderData = [
            'user_id' => $user?->id,
            'address_id' => $payload['address_id'] ?? null,
            'subtotal' => $cart['subtotal_numeric'] ?? 0,
            'discount' => $cart['discount'] ?? 0,
            'wallet' => $cart['wallet'] ?? 0,
            'shipping' => $cart['shipping'] ?? 0,
            'total' => $cart['total_numeric'] ?? 0,
            'payment_method' => $payload['payment_method'] ?? null,
            'status' => 'pending',
            'payment_status' => 'pending'
        ];

        Log::debug('Order data to create:', $orderData);

        $order = Order::create($orderData);
        Log::debug('Order created with ID: ' . $order->id);

        // Insert order items
        $itemCount = 0;
        foreach($cart['cartItems'] as $ci){
            Log::debug('Processing cart item:', $ci);
            
            $orderItemData = [
                'order_id' => $order->id,
                'product_id' => $ci['product_id'] ?? null,
                'product_name' => $ci['product_name'] ?? ($ci['name'] ?? 'Unnamed'),
                'qty' => $ci['qty'] ?? 1,
                'size' => $ci['size'] ?? null,
                'color' => $ci['color'] ?? null,
                'price' => $ci['unit_price'] ?? 0,
                'subtotal' => $ci['line_total'] ?? (($ci['qty'] ?? 1) * ($ci['unit_price'] ?? 0)),
            ];

            Log::debug('Order item data:', $orderItemData);
            
            // FIX: Use the correct model name (OrderItem vs OrderItems)
            OrderItem::create($orderItemData);
            $itemCount++;
        }

        Log::debug("Created $itemCount order items");
        DB::commit();

        if (strtolower($order->payment_method ?? '') === 'cod' && $order->user) {
    try {
        Mail::to($order->user->email)->queue(new OrderPlaced($order));
    } catch (\Throwable $e) {
        \Log::error('Failed to queue OrderPlaced email for order ' . $order->id . ': ' . $e->getMessage());
    }
}

        return ['success' => true, 'order' => $order];
        
    } catch(\Throwable $e) {
        DB::rollBack();
        Log::error('ORDER CREATION FAILED: ' . $e->getMessage());
        Log::error('Exception trace: ' . $e->getTraceAsString());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
    /**
     * Clear cart delegate to session or CartService
     */

    public function clearCart($user = null)
    {
        // If your CartService provides a clear() use it, else clear session keys
        try{
            if(method_exists($this->cartService, 'clear')){
                $this->cartService->clear();
                return;
            }
        }catch(\Throwable $e){
            // ignore and fallback
        }

        // Fallback clear session keys used by CartService
        Session::forget([
            'cart', 'cart_items', 'applied_coupon_id', 'applied_coupon', 'applied_coupon_discount',
            'applied_wallet_amount', 'cart_contents' 
        ]);
    }

   public function addAddress($user, array $data)
    {
      Log::debug('Add Address Data:', $data);
    
      try {
        // FIX: use correct field names
        $county = $data['country'] === 'United Kingdom'
        ? ($data['county'] ?? null)            // UK → county_select (name="county")
        : ($data['county_text'] ?? null);      // non-UK → county_text

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'mobile' => $data['mobile'],
            'address_line1' => $data['address_line1'],
            'address_line2' => $data['address_line2'],
            'city' => $data['city'],
            'county' => $county,
            'postcode' => $data['postcode'] ?? null,
            'country' => $data['country'],
        ]);

        Log::debug('Address created:', ['id' => $address->id]);
        return $address;

    } catch (\Exception $e) {
        Log::error('Address creation failed:', [
            'error' => $e->getMessage(),
            'data' => $data
        ]);
        throw $e;
    }
   }

   /**  
    * Update existing address belonging to the user
    * Return null if not authorized to update
   */

   public function updateAddress($user, $address_id, array $data)
   {
    $address = Address::where('id', $address_id)->where('user_id', $user->id)->first();
    if(!$address){
        return null;
    }

    $county = $data['country'] === 'United Kingdom'
        ? ($data['county'] ?? null)            // UK → county_select (name="county")
        : ($data['county_text'] ?? null); 
         
            $address->first_name = $data['first_name'];
            $address->last_name = $data['last_name'] ?? null;
            $address->mobile = $data['mobile'];
            $address->address_line1 = $data['address_line1'];
            $address->address_line2 = $data['address_line2'] ?? null;
            $address->city = $data['city'];
            $address->county = $county;
            $address->postcode = $data['postcode'] ?? null;
            $address->country = $data['country']; // non-UK → county_text

            $address->save();
            return $address;

   }

   /**
    * Delete delevery address belonging to the user
    * Return null if not authorized to delete
    */

   public function deleteAddress($user, $address_id)
   {
      $address = Address::where('id', $address_id)->where('user_id', $user->id)->first();

      if(!$address){
          return false;
      }

      try{
        $address->delete();
        return true;
      }catch(\Throwable $e){
        return false;
      }
   }

}
