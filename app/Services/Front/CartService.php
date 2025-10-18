<?php

namespace App\Services\Front;

use App\Models\Product;
use App\Models\Cart;
use App\Models\ProductsAttribute;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class CartService
{
    public function getCart(): array
   {
    $rows = $this->currentCartQuery()
    ->with(['product' =>function($q){
        $q->with('product_images', 'category'); // Product->category may be useful
    }])
    ->orderBy('id', 'DESC')
    ->get();
    $items = [];
    $subtotal = 0;
    foreach($rows as $row){
        $product = $row->product;
        if(!$product) continue; // Skip if product not found
        
        // compute price based on attribute price or final price
        $pricing = Product::getAttributePrice($product->id, $row->product_size);
        $unit = ($pricing['status'] ?? false) 
        ? ($pricing['final_price'] ?? $product['product_price'])
        : ($product->final_price ?? $product->product_price);
        $unit = (int)$unit;

        // Image Selection
        $fallbackImage = asset('front/images/products/no-image.png');
        if(!empty($product->main_image)){
            $image = asset('product-image/medium/' . $product->main_image);
        }elseif(!empty($product->product_images[0]['image'])){
            $image = asset('product-image/medium/' . $product->product_images[0]['image']);
        }else{
            $image = $fallbackImage;
        }
        $lineTotal = $unit * (int)$row->product_qty;
        $subtotal += $lineTotal;
        $items[] = [
            'cart_id' => $row->id,
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'product_url' => $product->product_url,
            'image' => $image,
            'size' => $row->product_size,
            'qty' => $row->product_qty,
            'unit_price' => $unit,
            'line_total' => $lineTotal,
            'category_id' => $product->category_id ?? null, // helpful later
        ];
    }

    // ---------------------------
    // Compute coupon discount dynamic
    // ---------------------------

    $couponDiscount = 0.0;
    $appliedCouponId = session('applied_coupon_id');
    if ($appliedCouponId) {
        // Defensive: import coupon at top: use App\Models\Coupon
        $coupon = \App\Models\Coupon::find($appliedCouponId);
        // if coupon not found 
        if (!$coupon || !$coupon->status || 
        ($coupon->expiry_date && now()->gt(\Carbon\Carbon::parse($coupon->expiry_date)->endOfDay()))) {
            // Invalid coupon - remove from session
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
            $coupon = null;

        }else {
            // Determinate applicable amount for the coupon
            // if coupon has category restriction only sum those items line totals
            $applicableAmount = $subtotal;
            if(!empty($coupon->categories)) {
                // categories might be stored as array or JSON string: handle both
                $allowedCats = $coupon->categories;
                if(is_string($allowedCats)) {
                    $decoded = @json_decode($allowedCats, true);
                    if(is_array($decoded)) $allowedCats = $decoded;
            }
            if(is_array($allowedCats) && count($allowedCats) > 0) {
                $applicableAmount = 0;
                foreach($items as $it) {
                    if(!empty($it['category_id']) && in_array($it['category_id'], $allowedCats)) {
                        $applicableAmount += $it['line_total'];
                }
            }
        }
     }
     // also check min_cart_value if set on coupon
     if(!empty($coupon->min_cart_value) && $subtotal < (float)$coupon->min_cart_value) {

        // not eligible clear coupon session
        Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
        $coupon = null;
     }else{
        // compute discount based on amount type
        if($coupon){
            if($coupon->amount_type === 'percentage') {
                $couponDiscount = round($applicableAmount * ($coupon->amount/100), 2);
            }else{
               // Fixed amount but not exceed applicable amount
               $couponDiscount = min((float)$coupon->amount, $applicableAmount);
            }
            // Optional if coupon has max discount (cap) field enforce it
            if(!empty($coupon->max_discount)) {
                $couponDiscount = min($couponDiscount, (float)$coupon->max_discount);
            }
            // Store recomputed discount in session if you still want it there (optional)
            Session::put('applied_coupon_discount', $couponDiscount);
         }
       }
     }
   }
     // Finalize Totals
     $subtotal = (float)$subtotal;
     $couponDiscount = (float)$couponDiscount;
     $total = max(0, $subtotal - $couponDiscount);
     return [
         'cartItems' => $items,
         'subtotal' => $subtotal,
         'discount' => $couponDiscount,
         'total' => $total,
     ];
 }

    public function addToCart(array $data): array{
        // Check Product status
        $product = Product::find($data['product_id']);
        if(!$product || $product->status != 1){
            return ['status' => false, 'message' => 'Product is not available'];
        }
        // Check stock for size
        $productStock = ProductsAttribute::productStock($data['product_id'], $data['size']);
        if($data['qty'] > $productStock){
            return ['status' => false, 'message' => 'This product is Out of stock'];
    }
       // Generate session id for guests users
       $session_id = Session::get('session_id');
       if(empty($session_id)){
           $session_id = Session::getId();
              Session::put('session_id', $session_id);
       }

       // Check if product already exists in cart

       if(Auth::check()){
    $user_id = Auth::id();
    $count = Cart::where([
        'product_id' => $data['product_id'], 
        'product_size' => $data['size'], 
        'user_id' => $user_id
    ])->count();
    }else{
    $user_id = 0;
    $count = Cart::where([
        'product_id' => $data['product_id'], 
        'product_size' => $data['size'], 
        'session_id' => $session_id
    ])->count();
  }
  if($count > 0){
    return ['status' => false, 'message' => 'Product already in cart'];
  }
  
       //Save cart
       $item = new Cart();
       $item->session_id = $session_id;
       $item->user_id = $user_id;
       $item->product_id = $data['product_id'];
       $item->product_size = $data['size'];
       $item->product_qty = $data['qty'];
         $item->save();
         $cart = $this->getCart();
         $itemsHtml = View::make('front.cart.ajax_cart_items', [
            'cartItems' => $cart['cartItems'] 
         ])->render();
         $summaryHtml = View::make('front.cart.ajax_cart_summary', [
            'subtotal' => $cart['subtotal'],
            'discount' => $cart['discount'],
            'total' => $cart['total'],
        ])->render();

      return ['status' => true, 'message' => 'Product added to cart successfully <a href="'.url('/cart').'">View Cart</a>',
      'totalCartItems' => totalCartItems(),
      'items_html' => $itemsHtml,
      'summary_html' => $summaryHtml
    ];
       
   }

   public function updateQty(int $cartId, int $qty): array{

    if($qty < 1){
        return ['status' => false, 'message' => 'Quantity must be at least 1'];
    }
    $row = $this->currentCartQuery()->where('id', $cartId)->first();
    if(!$row){
        return ['status' => false, 'message' => 'Cart item not found'];
    }
    $size = $row->product_size;
    if($size === 'NA'){
        $productStock = Product::where('id', $row->product_id)->value('stock') ?? 0;
    }else{
        $productStock = ProductsAttribute::productStock($row->product_id, $size);
    }
    if($qty > $productStock){
        return ['status' => false, 'message' => 'This product is Out of stock'];
    }
    $row->product_qty = $qty;
    $row->save();
    $cart = $this->getCart();
    $itemsHtml = View::make('front.cart.ajax_cart_items', 
    ['cartItems' => $cart['cartItems'],
    ])->render();
    $summaryHtml = View::make('front.cart.ajax_cart_summary', 
    ['subtotal' => $cart['subtotal'],
    'discount' => $cart['discount'],
    'total' => $cart['total'],
    ])->render();

    return ['status' => true, 
    'message' => 'Cart item updated successfully', 
    'totalCartItems' => totalCartItems(),
    'items_html' => $itemsHtml,
    'summary_html' => $summaryHtml,
];
   }

  public function removeItem(int $cartId): array{

   $deleted = $this->currentCartQuery()->where('id', $cartId)->delete();
   $cart = $this->getCart();
   $itemsHtml = View::make('front.cart.ajax_cart_items', 
    ['cartItems' => $cart['cartItems'],
    ])->render();
    $summaryHtml = View::make('front.cart.ajax_cart_summary', [
    'subtotal' => $cart['subtotal'],
    'discount' => $cart['discount'],
    'total' => $cart['total'],
    ])->render();
    
    return $deleted
     ? [
        'status' => true, 
        'message' => 'Item Removed from Cart', 
        'totalCartItems' => totalCartItems(),
        'items_html' => $itemsHtml,
        'summary_html' => $summaryHtml,] 
: 
    ['status' => false, 'message' => 'unable to delete item'];

   }

   protected function currentCartQuery()
 {
    $sessionId = Session::get('session_id');

    if (empty($sessionId)) {
        $sessionId = Session::getId();
        Session::put('session_id', $sessionId);
    }
    $q = Cart::query();

    if (Auth::check()) {
        $q->where('user_id', Auth::id());
    } else {
        $q->where('session_id', $sessionId);
    }
    return $q;
 }

}