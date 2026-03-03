<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessStripeWebhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $secret = config('services.stripe.webhook_secret');

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
    } catch (\Exception $e) {
        Log::error('Stripe webhook signature failed');
        return response()->json(['error' => 'Invalid webhook'], 400);
    }

    // Pass only event ID (cleaner & safer)
    ProcessStripeWebhook::dispatch($event->id);

    return response()->json(['status' => 'queued']);
}
}