<?php

namespace App\Services\Front;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;


class CouponService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function applyCoupon(string $rawCode): array
    {
        $code = strtoupper(trim((string)$rawCode));
        // if empty code : clear any applied coupon and return fresh cart fragments
        if($code === '') {
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
        $cart = $this->cartService->getCart();
        return [
            'status' => false,
            'message' => 'Coupon code is required',
            'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => $cart['cartItems']])->render(),
            'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
            $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
            'totalCartItems' => totalCartItems(),
        ];
        }
        // Load coupon (active only)
        $coupon = Coupon::where('coupon_code', $code)->where('status', 1)->first();

        if(!$coupon) {
            // Clear any applied coupon and return fresh cart fragments
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
            $cart = $this->cartService->getCart();
            return [
                'status' => false,
                'message' => 'Coupon code is invalid',
                'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                $cart['cartItems']])->render(),
                'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                'totalCartItems' => totalCartItems(),
            ];
        }
        // Normalise coupon properties that may be stored as JSON or Arrays
        $couponCategories = [];
        if(!empty($coupon->categories)) {
            if(is_array($coupon->categories)) {
                $couponCategories = $coupon->categories;
       }else{
            $couponCategories = json_decode($coupon->categories, true) ? : (array)$coupon->categories;
       }
    }
       $couponUsers = [];
        if(!empty($coupon->users)) {
            if(is_array($coupon->users)) {
                $couponUsers = $coupon->users;
            }else{
                $couponUsers = json_decode($coupon->users, true) ? : (array)$coupon->users;
            }
        }
       // Normalise Brands if you use them later
       $couponBrands = [];
        if(!empty($coupon->brands)) {
            if(is_array($coupon->brands)) {
                $couponBrands = $coupon->brands;
            }else{
                $couponBrands = json_decode($coupon->brands, true) ? : (array)$coupon->brands;
            }
        }
        // Expiry check (make safe with Carbon)
       if(!empty($coupon->expiry_date)) {
             try {
            $expiry = \Carbon\Carbon::parse($coupon->expiry_date)->endOfDay();
            if(\Carbon\Carbon::now()->gt($expiry)) {
                Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                $cart = $this->cartService->getCart();
                return [
                    'status' => false,
                    'message' => 'Coupon has expired',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];
           }
           }catch(\Exception $e) {
            // If invalid date format, treat as expired/invalid
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                $cart = $this->cartService->getCart();
                return [
                    'status' => false,
                    'message' => 'Invalid coupon expiry',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];
          }
       }

       // Global usage cap
       if(!empty($coupon->total_usage_limit) && (int)$coupon->total_usage_limit > 0) {
         if($coupon->used_count >= $coupon->total_usage_limit) {
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                $cart = $this->cartService->getCart();
                return [
                    'status' => false,
                    'message' => 'Coupon usage limit reached',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];
         }
       }

       // per-user usage cap (if applicable)
       if(!empty($coupon->usage_limit_per_user) && (int)$coupon->usage_limit_per_user > 0) {
          if(Auth::check()) {
            $userUses = DB::table('coupon_usages')
            ->where('coupon_id', $coupon->id)
            ->where('user_id', Auth::id())
            ->count();
            if($userUses >= $coupon->usage_limit_per_user){
                Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                $cart = $this->cartService->getCart();
                return [
                    'status' => false,
                    'message' => 'You have already used this coupon the maximum number of times allowed.',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];
            }
          }
       }

       // Get current cart and subtotal
       $cart = $this->cartService->getCart();
       $subtotal = (float)($cart['subtotal'] ?? 0);
       $cartQtyTotal = array_sum(array_column($cart['cartItems'], 'qty'));

       // min/max cart value checks
       if(!empty($coupon->min_cart_value) && $subtotal < (float)$coupon->min_cart_value) {
        Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                return [
                    'status' => false,
                    'message' => 'Cart total is less than the coupon minimum required amount ',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];
       }
       if(!empty($coupon->max_cart_value) && $subtotal > (float)$coupon->max_cart_value) {
        Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                return [
                    'status' => false,
                    'message' => 'Cart total is greater than the coupon maximum allowed amount ',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];
       }

       // min /max quantity checks (if present)
       if(!empty($coupon->min_qty) && $cartQtyTotal < (int)$coupon->min_qty) {
        Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                return [
                    'status' => false,
                    'message' => 'Cart item count is less than the coupon minimum quantity required ',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];  
       }
       if(!empty($coupon->max_qty) && $cartQtyTotal > (int)$coupon->max_qty) {
        Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                return [
                    'status' => false,
                    'message' => 'Cart item count exceeds the coupon maximum quantity required ',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];  
       }

       // Category applicability check (if coupon resstricted to specific categories)
       if(!empty($couponCategories)){
        $allowedCatIds = array_map('strval', $couponCategories); // Campare as string
        $matches = false;
        foreach ($cart['cartItems'] as $item) {
            $p = Product::select('id', 'category_id')->find($item['product_id']);
            if($p && in_array((string)$p->category_id, $allowedCatIds, true)){
                $matches = true;
                break;
         }
       }
          if(!$matches){
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                return [
                    'status' => false,
                    'message' => 'Coupon not applicable to any product in cart',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];  
          } 
       }

       // User applicability check
       if(!empty($couponUsers)){
        if(!Auth::check()){
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
                return [
                    'status' => false,
                    'message' => 'You must be logged in to use this coupon',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];  
           }
           $userIdOrEmail = Auth::id();
           $userEmail = Auth::user()->email ?? null;

           // normalize coupon users for comparison (strings)
           $normalizedUsers = array_map('strval', $couponUsers);
           if(!in_array((string)$userIdOrEmail, $normalizedUsers, true) && !($userEmail 
           && in_array($userEmail, $normalizedUsers, true))){
            Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
            $cart = $this->cartService->getCart();
                return [
                    'status' => false,
                    'message' => 'This coupon is not applicable to you',
                    'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
                    $cart['cartItems']])->render(),
                    'summary_html' => View::make('front.cart.ajax_cart_summary', ['subtotal' => 
                    $cart['subtotal'], 'discount' => 0, 'total' => $cart['total']])->render(),
                    'totalCartItems' => totalCartItems(), 
                ];  
           }
       }

       // Compute discount
       $discount = 0.0;
       $amountType = strtolower((string)($coupon->amount_type ?? 'fixed'));
       if($amountType === 'percentage' || $amountType === 'percent'){
           $discount = round($subtotal * ((float)$coupon->amount / 100), 2);
           // cap at max discount if set
           if(!empty($coupon->max_discount) && (float)$coupon->max_discount > 0){
            $discount = min($discount, (float)$coupon->max_discount);
           }
       }else{
        // fixed amount
           $discount = min((float)$coupon->amount, $subtotal);

       }

       // Persist applied coupon to session (so cart refresh uses it)
       Session::put('applied_coupon', $coupon->coupon_code);
       Session::put('applied_coupon_id', $coupon->id);
       Session::put('applied_coupon_discount', $discount);

       // Recompute cart fragments after setting session
       $cartAfter = $this->cartService->getCart();
       return [
        'status' => true,
        'message' => 'Coupon applied successfully',
        'items_html' => View::make('front.cart.ajax_cart_items', ['cartItems' => 
        $cartAfter['cartItems']])->render(),
        'summary_html' => View::make('front.cart.ajax_cart_summary', [
            'subtotal' => $cartAfter['subtotal'],
            'discount' => $discount,
            'total' => max(0, $cartAfter['subtotal'] - $discount),])->render(),
            'totalCartItems' => totalCartItems()
        ];
    }

    public function removeCoupon(): array
    {
        Session::forget(['applied_coupon', 'applied_coupon_id', 'applied_coupon_discount']);
        $cart = $this->cartService->getCart();
        $itemsHtml = View::make('front.cart.ajax_cart_items', ['cartItems' => $cart['cartItems']])->render();
        $summaryHtml = View::make('front.cart.ajax_cart_summary', [
            'subtotal' => $cart['subtotal'],
            'discount' => 0,
            'total' => $cart['total'],
        ])->render();
        return [
            'status' => true,
            'message' => 'Coupon removed successfully',
            'items_html' => $itemsHtml,
            'summary_html' => $summaryHtml,
            'totalCartItems' => totalCartItems(),
        ];
    }
}
