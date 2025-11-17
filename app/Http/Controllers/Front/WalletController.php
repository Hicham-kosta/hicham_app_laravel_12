<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Front\WalletService;
use App\Services\Front\CartService;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService, 
        protected CartService $cartService)
    {

    }

    // POST /Cart/apply wallet
    public function apply(Request $request)
    {
        $raw = $request->input('coupon_code'); // can be wallet or '10'
        $resp = $this->walletService->applyWallet($this->cartService, $raw);
        return response()->json($resp, $resp['status'] ? 200 : 422);
    }

    // POST /Cart/remove wallet
public function remove(Request $request)
{
    Log::debug('Remove wallet called');
    $resp = $this->walletService->removeWallet($this->cartService);
    Log::debug('Remove wallet response', $resp);
    return response()->json($resp, 200);
}
}
