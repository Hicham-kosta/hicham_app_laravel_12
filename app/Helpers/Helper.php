<?php

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

if (!function_exists('totalCartItems')) {
    function totalCartItems(): int {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->sum('product_qty');
        } else {
            $session_id = Session::get('session_id');
            if (empty($session_id)) {
                $session_id = Session::getId();
                Session::put('session_id', $session_id);
            }
            return Cart::where('session_id', $session_id)->sum('product_qty');
        }
    }
}
