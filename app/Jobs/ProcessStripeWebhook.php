<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\Stripe;
use Stripe\Event;

class ProcessStripeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventId;

    public $tries = 5;
    public $timeout = 60;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function handle(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Retrieve fresh event from Stripe
        $event = Event::retrieve($this->eventId);

        if ($event->type !== 'payment_intent.succeeded') {
            return;
        }

        $paymentIntent = $event->data->object;

        DB::transaction(function () use ($paymentIntent) {

            $order = Order::find($paymentIntent->metadata->order_id ?? null);

            if (!$order) {
                Log::error('Order not found for Stripe event', [
                    'payment_intent' => $paymentIntent->id
                ]);
                return;
            }

            // IDEMPOTENCY CHECK
            if ($order->payment_status === 'paid') {
                return;
            }

            $order->update([
                'payment_status'    => 'paid',
                'order_status'      => 'processing',
                'payment_intent_id' => $paymentIntent->id,
            ]);

            Cart::where('user_id', $order->user_id)->delete();

            Log::info('Stripe order processed successfully', [
                'order_id' => $order->id
            ]);
        });
    }
}