<?php

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\Currency;

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

if (!function_exists('getBaseCurrency')) {
    function getBaseCurrency() {
        return Currency::where('is_base', 1)->first();
    }
}
if (!function_exists('getCurrentCurrency')) {
    function getCurrentCurrency(){
        $code = Session::get('currency_code') ? : Cookie::get('currency_code');
        if($code){
            $c = Currency::where('code', $code)->where('status', 1)->first();
            if($c){
                return $c;
            }
        }
        return getBaseCurrency();
    }
}

if(!function_exists('formatCurrency')){
    function formatCurrency($amount){
        $currency = getCurrentCurrency();
        if(!$currency){
            return number_format((float)$amount, 2);
        }
        $rate = (float)$currency->rate;
        $converted = (float)$amount * $rate;
        // Simople formating symbol immediately before the amount
        return ($currency->symbol ?? $currency->code).number_format((float)$converted, 2);
    }
}
