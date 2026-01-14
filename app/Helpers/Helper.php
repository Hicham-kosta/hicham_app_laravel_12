<?php

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\Currency;

if (!function_exists('normalizeAmount')) {
    function normalizeAmount($amount): float {
        if (is_numeric($amount)) {
            return (float) $amount;
        }
        
        if (is_string($amount)) {
            // Remove everything except numbers, decimal point, and minus sign
            $cleaned = preg_replace('/[^0-9\.\-]/', '', $amount);
            if ($cleaned === '' || $cleaned === null) {
                return 0.0;
            }
            return (float) $cleaned;
        }
        
        return 0.0;
    }
}

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
    function getCurrentCurrency() {
        $code = Session::get('currency_code') ?: Cookie::get('currency_code');
        if($code){
            $c = Currency::where('code', $code)->where('status', 1)->first();
            if($c){
                return $c;
            }
        }
        return getBaseCurrency();
    }
}

if (!function_exists('getExchangeRate')) {
    function getExchangeRate($fromCurrencyCode, $toCurrencyCode) {
        if ($fromCurrencyCode === $toCurrencyCode) {
            return 1.0;
        }
        
        $fromCurrency = Currency::where('code', $fromCurrencyCode)->first();
        $toCurrency = Currency::where('code', $toCurrencyCode)->first();
        
        if (!$fromCurrency || !$toCurrency) {
            return 1.0;
        }
        
        // If converting from base to another currency
        if ($fromCurrency->is_base) {
            return $toCurrency->rate; // Changed from exchange_rate to rate
        }
        
        // If converting to base currency
        if ($toCurrency->is_base) {
            return 1 / $fromCurrency->rate; // Changed from exchange_rate to rate
        }
        
        // Convert between two non-base currencies
        // First convert from source to base, then from base to target
        return $toCurrency->rate / $fromCurrency->rate; // Changed from exchange_rate to rate
    }
}

if (!function_exists('convertCurrency')) {
    function convertCurrency($amount, $fromCurrencyCode, $toCurrencyCode, $decimals = 2) {
        $amount = normalizeAmount($amount);
        
        if ($fromCurrencyCode === $toCurrencyCode) {
            return round($amount, $decimals);
        }
        
        $exchangeRate = getExchangeRate($fromCurrencyCode, $toCurrencyCode);
        $converted = $amount * $exchangeRate;
        
        return round($converted, $decimals);
    }
}

if (!function_exists('convertFromBase')) {
    function convertFromBase($amount, $targetCurrencyCode, $decimals = 2) {
        $baseCurrency = getBaseCurrency();
        
        if (!$baseCurrency) {
            return round($amount, $decimals);
        }
        
        return convertCurrency($amount, $baseCurrency->code, $targetCurrencyCode, $decimals);
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $targetCode = null, $decimals = 2, $showCode = false) {
        // Check if current payment method is PayPal
        $paymentMethod = Session::get('payment_method') ?: Cookie::get('payment_method');
        
        // Force USD for PayPal payments
        if ($paymentMethod === 'paypal') {
            $currency = Currency::where('code', 'USD')->first();
            
            if (!$currency) {
                // If USD doesn't exist in database, create a fallback
                return '$' . number_format(normalizeAmount($amount), $decimals);
            }
            
            // Convert from base currency to USD
            $value = convertFromBase(normalizeAmount($amount), 'USD', $decimals);
            $formatted = $currency->symbol . number_format($value, $decimals, '.', ',');
            
            if ($showCode) {
                $formatted .= ' ' . $currency->code;
            }
            
            return $formatted;
        }
        
        // Normalize incoming amount
        $amountFloat = normalizeAmount($amount);
        
        // Get target currency
        $currency = $targetCode ? Currency::where('code', $targetCode)->first() : getCurrentCurrency();
        
        // Fallback to base currency
        if (!$currency) {
            $currency = getBaseCurrency();
        }
        
        if (!$currency) {
            return number_format($amountFloat, $decimals);
        }
        
        // Convert from base currency to target currency
        $value = convertFromBase($amountFloat, $currency->code, $decimals);
        
        $symbol = $currency->symbol ?? $currency->code;
        $formatted = $symbol . number_format($value, $decimals, '.', ',');
        
        if ($showCode) {
            $formatted .= ' ' . $currency->code;
        }
        
        return $formatted;
    }
    
}