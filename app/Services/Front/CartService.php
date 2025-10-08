<?php

namespace App\Services\Front;

use App\Models\Product;
use App\Models\Cart;
use App\Models\productsAttribute;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function addToCart($data){
        // Check stock
        $stock = ProductsAttribute::productStock($data['product_id'], $data['size']);
        if($data['qty'] > $stock){
            return ['status' => false, 'message' => 'Out of stock'];
    }
       // Check product status
       // Dans ton service
       $status = Product::productStatus($data['product_id']);   
       if($status == 0){
           return ['status' => false, 'message' => 'This product is not available'];
       }
       // Generate session id if not exists
       $session_id = Session::get('session_id');
       if(empty($session_id)){
           $session_id = Session::getId();
              Session::put('session_id', $session_id);
       }

       // Check if product already exists in cart

       if(Auth::check()){
           $user_id = Auth::id();
           $exists = Cart::where([
            'product_id' => $data['product_id'], 
            'product_size' => $data['size'], 
            'user_id' => $user_id
           ])->exists();
       }else{
           $user_id = 0;
           $exists = Cart::where([
            'product_id' => $data['product_id'], 
            'product_size' => $data['size'], 
            'session_id' => $session_id
           ])->exists();
       }
       if($exists){
        return ['status' => false, 'message' => 'Product already in cart'];
      }  
       //Save cart
       Cart::create([
        'session_id' => $session_id,
        'user_id' => $user_id,
        'product_id' => $data['product_id'],
        'product_size' => $data['size'],
        'product_qty' => $data['qty']
       ]);
         return ['status' => true, 'message' => 'Product added to cart successfully <a href="/cart">View Cart</a>'];
       
   }

   public function getCart(): array
   {
    // Ensure we have a session id for guest cart
    $sessionId = Session::get('session_id');
    if($sessionId){
        $sessionId = Session::getId();
        Session::put('session_id', $sessionId);
    }

    // Base query: get cart items include product and image for thumbnails
    $query = $query = Cart::with(['product.product_images']);


    if(Auth::check()){
        $userId = Auth::id();
        $query->where('user_id', $userId);
    } else {
        $query->where('session_id', $sessionId);
    }

    $rows = $query->orderBy('id', 'DESC')->get();
    $items = [];
    $subtotal = 0;
    foreach($rows as $row){
        $product = $row->product;
        if(!$product){
            continue; // Skip if product not found
        }
        // Price per selected size using helper
        $pricing = Product::getAttributePrice($product->id, $row->product_size);
        $unit = ($pricing['status'] ?? false) 
        ? ($pricing['final_price'] ?? $product['product_price'])
        : ($product->final_price ?? $product->product_price);
        $unit = (int) $unit;

        // Image Resolution (same pattern as listing)
        $fallbackImage = asset('front/images/products/small/no-image.png');
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
        ];
    }
    $discount = 0; // Placeholder for future discount logic
    $total = $subtotal - $discount;
    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total' => $total,
    ];
  }
}