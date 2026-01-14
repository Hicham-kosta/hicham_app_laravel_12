<?php

namespace App\Services\Front;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\Front\CartService;
use App\Models\Address;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;
use App\Models\Product;
use App\Models\ProductsAttribute;

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

    /**
     * Return cart +shipping based on address
     */

    public function getCartForCheckout($user = null, $addressId = null)
    {
        $cart = $this->cartService->getCart();
        // Normalize
        $cart['subtotal_numeric'] = $cart['subtotal_numeric'] ?? (float)($cart['subtotal'] ?? 0);
        $cart['discount'] = $cart['discount'] ?? 0;
        $cart['wallet'] = $cart['wallet'] ?? 0;
        $cart['shipping'] = $cart['shipping'] ?? 0;
        // Calculate shipping if Address is provided
        if($user && $addressId){
            $address = Address::where('id', $addressId)
            ->where('user_id', $user->id)
            ->first();
            if($address){
                $result = $this->calculateShipping($cart, $address);
                $cart['shipping'] = $result['amount'];
                $cart['shipping_rule'] = $result['rule'];
            } 
        }
        // Recalculate total
        $cart['total_numeric'] = max(
            0,
            ($cart['subtotal_numeric'] + ($cart['shipping'] ?? 0) 
            - $cart['discount'] - $cart['wallet']),
        );
        return $cart;
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
    // Get cart data
    $cart = $this->getCartForCheckout($user, $payload['address_id'] ?? null);
    
    Log::info('Creating order from cart - Start', [
        'user_id' => $user->id,
        'cart_items_count' => count($cart['cartItems'] ?? []),
        'payload' => $payload
    ]);
    
    // Validate cart items
    if(empty($cart['cartItems']) || count($cart['cartItems']) === 0){
        Log::error('Cart is empty', ['user_id' => $user->id]);
        return ['success' => false, 'message' => 'Cart is empty'];
    }

    DB::beginTransaction();
    
    try {
        // Prepare order data
        $orderData = [
            'user_id' => $user?->id,
            'address_id' => $payload['address_id'] ?? null,
            'subtotal' => $cart['subtotal_numeric'] ?? 0,
            'discount' => $cart['discount'] ?? 0,
            'wallet' => $cart['wallet'] ?? 0,
            'shipping' => $cart['shipping'] ?? 0,
            'total' => $cart['total_numeric'] ?? 0,
            'payment_method' => $payload['payment_method'] ?? null,
            'transaction_id' => $payload['transaction_id'] ?? null,
            'paypal_order_id' => $payload['paypal_order_id'] ?? null,
            'status' => $payload['status'] ?? 'pending',
            'payment_status' => $payload['payment_status'] ?? 'pending'
        ];

        Log::info('Creating order with data:', $orderData);
        
        // Create order
        $order = Order::create($orderData);
        
        Log::info('Order created', ['order_id' => $order->id]);

        // Generate order number
        try {
            $orderNumber = 'SM' . now()->format('YmdHis') . str_pad($order->id, 6, '0', STR_PAD_LEFT);
            $order->order_number = $orderNumber;
            $order->save();
            
            Log::info('Order number generated', [
                'order_id' => $order->id,
                'order_number' => $orderNumber
            ]);
        } catch(\Throwable $e) {
            Log::warning("Failed to set order_number for order {$order->id}: " . $e->getMessage());
        }

        // Insert order items
        $productIds = array_unique(array_filter(array_column($cart['cartItems'], 'product_id')));
        
        Log::info('Processing cart items', [
            'product_ids' => $productIds,
            'cart_items' => $cart['cartItems']
        ]);

        // Collect sizes
        $sizes = [];
        foreach($cart['cartItems'] as $ci){
            $s = $ci['size'] ?? $ci['product_size'] ?? null;
            if($s !== null && $s !== ''){
                $sizes[] = trim((string)$s);
            }
        }

        $sizes = array_unique($sizes);
        $products = collect();
        $productAttributes = collect();

        if(!empty($productIds)){
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        }

        // Load Attributes matching product_id and size
        if(!empty($productIds) && !empty($sizes)){
            $attrs = ProductsAttribute::whereIn('product_id', $productIds)
                ->whereIn('size', $sizes)
                ->get();
            
            $productAttributes = $attrs->keyBy(function($item){
                return $item->product_id . '-' . strtolower(trim((string)$item->size));
            });
        }
        
        // Helper to build lookup key
        $makeAttrKey = function($productId, $size){
            return $productId . '-' . strtolower(trim((string)$size));
        };

        // Helper: attempt to decrement stock atomically
        $decrementStock = function($productId, $qty, $pa = null, $size = null){
            $qty = (int)max(1, $qty);
            $size = $size !== null ? trim((string)$size) : null;
            
            // 1) if we have an attribute model instance -- try id based decrement
            if($pa && isset($pa->id)){
                $updated = ProductsAttribute::where('id', $pa->id)
                ->where('status', 1)
                ->whereNotNull('stock')
                ->where('stock', '>=', $qty)
                ->decrement('stock', $qty);
                if($updated){
                    Log::info('Stock decremented successfully for attribute ID:', ['pa_id' => $pa->id, 
                'product_id' => $productId, 'qty' => $qty]);
                    return [true, null];
                }
                $current = ProductsAttribute::where('id', $pa->id)->value('stock');
                Log::warning('Failed to decrement stock for attribute ID:', ['pa_id' => $pa->id, 'requested' => 
                $qty, 'current_stock' => $current]);
                return [false, 'Insufficient stock for attribute ID: (id:{($pa->id)})'];
            }

            //2) if size provided try product_id + size lookup
            if($size){
                $updated = ProductsAttribute::where('product_id', $productId)
                ->where('size', $size)
                ->where('status', 1)
                ->whereNotNull('stock')
                ->where('stock', '>=', $qty)
                ->decrement('stock', $qty);
                if($updated){
                    Log::info('stock reduced by product and size', ['product_id' => $productId, 'size' => $size, 'qty' => $qty]);
                    return [true, null];
                }
                $current = ProductsAttribute::where('product_id', $productId)
                ->where('size', $size)->value('stock');
                Log::warning('Failed to decrement stock for product and size', ['product_id' => $productId, 'size' => $size, 
                'requested' => $qty, 'current_stock' => $current]);
                return [false, 'Insufficient stock for product and size: (product_id:{($productId)}, size:{($size)})'];
            }

            //3) Optional fallback: product-level decrement (only if you want to allow)
            $updated = Products::where('id', $productId)
            ->whereNotNull('stock')
            ->where('stock', '>=', $qty)
            ->decrement('stock', $qty);
            if($updated){
                Log::info('stock reduced by product', ['product_id' => $productId, 'qty' => $qty]);
                return [true, null];
            }
            $productStock = Products::where('id', $productId)->value('stock');
            Log::warning('Failed to decrement stock for product', ['product_id' => $productId, 
            'requested' => $qty, 'current_stock' => $productStock]);
            return [false, 'Insufficient stock for product: (product_id:{($productId)})'];
        };

        foreach($cart['cartItems'] as $ci){
            $productId = $ci['product_id'] ?? null;
            $qty = (int) max(1, $ci['qty'] ?? ($ci['product_qty'] ?? 1));
            $unitPrice = $ci['unit_price'] ?? ($ci['price'] ?? 0);
            $lineTotal = $ci['line_total'] ?? ($unitPrice * $qty);

            // Load Product
            $product = $products->get($productId);

            // Determine size from cart(support size or product_size)
            $sizeFromCart = $ci['size'] ?? $ci['product_size'] ?? null;
            $sizeFromCart = $sizeFromCart !== null ? trim((string)$sizeFromCart) : null;

            // Lookup product attribute by (product_id, size)
            $pa = null;
            if($productId && $sizeFromCart){
                $key = $makeAttrKey($productId, $sizeFromCart);
                $pa = $productAttributes->get($key); 
            }

            // Fallback to product attribute ID
            if(!$pa && !empty($ci['product_attribute_id'])){
                $pa = ProductsAttribute::find($ci['product_attribute_id']);
            }

            // Derive size (prefer cart then attribute)
            $sizeFromAttr = $pa?->size ?? $pa?->sizename ?? null;
            $size = $sizeFromCart ?? $sizeFromAttr;
            $size = $size !== null ? trim((string)$size) : null;

            // ATTEMPT TO REDUCE STOCK BEFORE CREATING ORDER ITEM
            list($ok, $errMsg) = $decrementStock($productId, $qty, $pa, $size);
            if(!$ok){
                Log::warning('Failed to decrement stock for product', [
                    'product_id' => $productId, 
                    'size' => $size, 
                    'qty' => $qty, 
                    'error' => $errMsg
                ]);
                DB::rollBack();
                return ['success' => false, 'message' => $errMsg ?? 'Unable to reduce stock'];
            }

            // Derive remaining fields
            $colorFromCart = $ci['color'] ?? null;
            $colorFromProduct = $product?->product_color ?? ($product?->color ?? null);
            $color = $colorFromCart ?? $colorFromProduct;

            $sku = $pa?->sku 
            ?? ($ci['sku'] ?? null) 
            ?? $product?->sku ?? 'N/A';

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'product_name' => $ci['product_name'] ?? ($ci['name'] ?? 'Unnamed Product'),
                'qty' => $qty,
                'price' => $unitPrice,
                'subtotal' => $lineTotal,
                'size' => $size,
                'color' => $color,
                'sku' => $sku
            ]);
            
            Log::info('Order item created', [
                'order_id' => $order->id,
                'product_id' => $productId,
                'product_name' => $ci['product_name'] ?? 'Unnamed',
                'qty' => $qty
            ]);
        }

        DB::commit();
        
        Log::info('Order creation completed successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $order->total,
            'items_count' => count($cart['cartItems'])
        ]);

        // Send email for COD orders
        if(strtolower($order->payment_method ?? '') === 'cod' && $order->user){
            try {
                Mail::to($order->user->email)->queue(new OrderPlaced($order));
            } catch(\Throwable $e) {
                Log::error("Failed to send order placed email for order {$order->id}: " . $e->getMessage());
            }   
        }
        
        return ['success' => true, 'order' => $order];
        
    } catch(\Throwable $e) {
        DB::rollBack();
        
        Log::error('Order creation failed', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return ['success' => false, 'message' => 'Order creation failed: ' . $e->getMessage()];
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

   /** 
    * Determine shipping charge for cart + delivery address
    */
   public function calculateShipping(array $cart, Address $address): array
   {
    $amount = 0.0;
    $rule = null;
    // Find country
    $country = Country::where('name', $address->country)->first();
    if(!$country){
        return ['amount' => 0.0, 'rule' => null];
    }

    // All shipping rules
    $rules = ShippingCharge::where('country_id', $country->id)
    ->where('status', true)
    ->orderBy('sort_order')
    ->get();

    if($rules->isEmpty()){
        return ['amount' => 0.0, 'rule' => null];
    }
    //Total cart weight in grams
    $items = $cart['cartItems'];
    $productIds = array_unique(array_filter(array_column($items, 'product_id')));
    $products = !empty($productIds)
        ? Product::whereIn('id', $productIds)->get()->keyBy('id')
        : collect();
        $totalWeight = 0.0;
        foreach($items as $ci){
            $pid = $ci['product_id'] ?? null;
            $qty = max(1, (int)($ci['qty'] ?? $ci['product_qty'] ?? 1));
            $product = $products->get($pid);
            $weight = $product ? (float) $product->product_weight : 0.0;
            $totalWeight += $weight * $qty;
        }
        
        // Subtotal
        $subtotal = (float)($cart['subtotal_numeric'] ?? 0);

        // Filter rules
        $matching = $rules->filter(function(ShippingCharge $r) use ($subtotal, $totalWeight){
            if(!is_null($r->min_weight_g) && $totalWeight < (float)$r->min_weight_g){
                return false;
            }
            if(!is_null($r->max_weight_g) && $totalWeight > (float)$r->max_weight_g){
                return false;
            }
            if(!is_null($r->min_subtotal) && $subtotal < (float)$r->min_subtotal){
                return false;
            }
            if(!is_null($r->max_subtotal) && $subtotal > (float)$r->max_subtotal){
                return false;
            }
            return true;
        });

        // Choose the best rule
        if($matching->count() > 0){
            $rule = $matching->firstWhere('is_default', true)
            ?? $matching->sortBy('sort_order')->first();
        }else{
            $rule = $rules->firstWhere('is_default', true);
        }
        $amount = $rule ? (float)$rule->rate : 0.0;

        return ['amount' => $amount, 'rule' => $rule];
    }
}
