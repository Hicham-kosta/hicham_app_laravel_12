<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_order_id',
        'transaction_id',
        'type',
        'status',
        'amount',
        'original_amount',
        'original_currency',
        'converted_amount',
        'conversion_rate',
        'currency',
        'fee',
        'payer_id',
        'payer_email',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'converted_amount' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

