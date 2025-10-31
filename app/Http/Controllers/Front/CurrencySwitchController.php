<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\cookie;


class CurrencySwitchController extends Controller
{
    public function switch(Request $request)
    {
        $code = $request->input('code');
        if(!$code) return response()->json(['status' => 'error', 'message' => 'Invalid Currency Code'],400);
        $currency = Currency::where('code', $code)->where('status', 1)->first();
        if(!$currency) return response()->json(['status' => 'error', 'message' => 'Currency not available'],400);
        Session::put('currency_code', $currency->code);
        cookie::queue('currency_code', $currency->code, 7*24*60); // 7 days in minutes
        return response()->json(['status' => 'success', 'code' => 
        $currency->code, 'symbol' => $currency->symbol, 'rate' => $currency->rate]);
    }
}
