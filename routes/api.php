<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\StripeWebhookController;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);