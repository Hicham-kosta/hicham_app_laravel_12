<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {

            $event = Webhook::constructEvent($payload, $sigHeader, $secret);

            Log::info('Stripe webhook received', [
                'type' => $event->type
            ]);

        } catch (\Exception $e) {

            Log::error('Stripe webhook signature failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {

            case 'payment_intent.succeeded':

                $paymentIntent = $event->data->object;

                $order = Order::where(
                    'payment_intent_id',
                    $paymentIntent->id
                )->first();

                if ($order && $order->payment_status !== 'paid') {

                    $order->update([
                        'payment_status' => 'paid',
                        'order_status'   => 'processing'
                    ]);

                    Cart::where('user_id', $order->user_id)->delete();

                    Log::info('Order marked as paid', [
                        'order_id' => $order->id
                    ]);
                }

                break;


            case 'payment_intent.payment_failed':

                $paymentIntent = $event->data->object;

                $order = Order::where(
                    'payment_intent_id',
                    $paymentIntent->id
                )->first();

                if ($order) {

                    $order->update([
                        'payment_status' => 'failed',
                        'order_status'   => 'cancelled'
                    ]);

                    Log::info('Order marked as failed', [
                        'order_id' => $order->id
                    ]);
                }

                break;
        }

        return response()->json(['status' => 'success']);
    }
}