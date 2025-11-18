<?php 

namespace App\Services\Front;

use App\Models\WalletCredit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;;
use Illuminate\Support\Facades\Log;

class WalletService
{
    public function activeBalance(int $userId): float
    {
        return (float)WalletCredit::query()
        ->where('user_id', $userId)
        ->where('is_active', 1)
        ->where(function($q){
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        })
        ->sum('amount');
    }

    /**
     * Apply Wallet to current cart
     * Requires login
     * Applies After coupon discount
     * stores applied wallet amount in session('applied_wallet_amount') (Base currency)
     */
    public function applyWallet(CartService $cartService, ?string $raw = null): array
{
    Log::debug('=== APPLY WALLET START ===');
    Log::debug('Raw input:', ['raw' => $raw, 'user_id' => Auth::id()]);
    
    if (!Auth::check()) {
        Log::debug('User not logged in');
        return $this->respond(false, 'Please login to apply wallet');
    }
    
    $cart = $this->safeCart($cartService);
    Log::debug('Initial cart data:', [
        'items_count' => count($cart['items'] ?? []),
        'items' => $cart['items'] ?? 'none',
        'session_id' => session()->getId()
    ]);
    
    $subtotal = (float)($cart['subtotal_numeric'] ?? 0.0);
    $couponDisc = (float)($cart['discount'] ?? 0.0);
    $remaining = max(0.0, round($subtotal - $couponDisc, 2));
    
    Log::debug('Wallet calculation', ['subtotal' => $subtotal, 'couponDisc' => $couponDisc, 'remaining' => $remaining]);

    if($remaining <= 0) {
        Session::forget('applied_wallet_amount');
        return $this->rebuild($cartService, true, 'Nothing to pay after coupon, wallet not needed');
    }
    
    $balance = $this->activeBalance(Auth::id());
    if($balance <= 0) {
        Session::forget('applied_wallet_amount');
        return $this->rebuild($cartService, true, 'No active wallet found');
    }

    // Parse optional requested amount
    $requestedAmt = null;
    if(is_string($raw)){
        $raw = trim($raw);
        if(preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $raw)){
            $requestedAmt = (float)$raw;
        }
    }
    
    $apply = $requestedAmt !== null ? $requestedAmt : $balance;
    $apply = min($apply, $balance, $remaining);
    $apply = round(max(0.0, $apply), 2);

    if($apply <= 0) {
        Session::forget('applied_wallet_amount');
        return $this->rebuild($cartService, true, 'No active wallet found');
    }
    
    Session::put('applied_wallet_amount', $apply);
    Log::debug('Wallet applied amount:', ['amount' => $apply]);
    
    $response = $this->rebuild($cartService, true, 'Wallet applied successfully');
    Log::debug('=== APPLY WALLET END ===', $response);
    
    return $response;
}

    public function removeWallet(CartService $cartService): array
{
    Log::debug('Removing wallet from session');
    Session::forget('applied_wallet_amount');
    
    // Verify it's removed
    $walletAfterRemoval = Session::get('applied_wallet_amount');
    Log::debug('Wallet after removal attempt', ['wallet' => $walletAfterRemoval]);
    
    return $this->rebuild($cartService, true, 'Wallet removed successfully');
}

    protected function safeCart(CartService $cartService): array
{
    try { 
        $cart = $cartService->getCart();
        // Ensure the cart has all required keys with proper fallbacks
        return array_merge([
            'cartItems' => [],
            'subtotal_numeric' => 0.0,
            'discount' => 0.0,
            'total_numeric' => 0.0,
            'subtotal' => '₹0.00',
            'total' => '₹0.00'
        ], $cart);
    } catch(\Throwable $e) { 
        return [
            'items' => [],
            'subtotal_numeric' => 0.0,
            'discount' => 0.0, 
            'total_numeric' => 0.0,
            'subtotal' => '₹0.00',
            'total' => '₹0.00'
        ];
    }
}

    protected function rebuild(CartService $cartService, bool $success, string $message): array
{
    $cart = $cartService->getCart();
    
    Log::debug('Rebuild cart data', [
        'items_count' => count($cart['cartItems'] ?? []),
        'items' => $cart['cartItems'] ?? 'none',
        'subtotal' => $cart['subtotal'] ?? 'none',
        'total' => $cart['total'] ?? 'none'
    ]);
    
    $itemsHtml = View::make('front.cart.ajax_cart_items', [
        'cartItems' => $cart['cartItems'] ?? [], 
    ])->render();

    $summaryHtml = View::make('front.cart.ajax_cart_summary', [
        'subtotal_numeric' => $cart['subtotal_numeric'] ?? 0,
        'discount' => $cart['discount'] ?? 0,
        'wallet' => $cart['wallet'] ?? 0,
        'total_numeric' => $cart['total_numeric'] ?? 0,
        'subtotal' => $cart['subtotal'] ?? '₹0.00',
        'total' => $cart['total'] ?? '₹0.00',
        'discount_formatted' => $cart['discount_formatted'] ?? '₹0.00',
        'wallet_formatted' => $cart['wallet_formatted'] ?? '₹0.00',
        'currency_symbol' => $cart['currency_symbol'] ?? '₹',
    ])->render();

    $response = [
        'status' => $success,
        'message' => $message, 
        'items_html' => $itemsHtml, 
        'summary_html' => $summaryHtml,
        'totalCartItems' => totalCartItems(),
    ];
    
    Log::debug('Rebuild response', ['items_html_length' => strlen($itemsHtml)]);
    
    return $response;
}

    protected function respond(bool $ok, string $msg): array
    {
        return ['status' => $ok, 'message' => $msg];
    }

    
}



