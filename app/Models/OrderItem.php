<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_id', 
        'product_name', 
        'qty', 
        'price', 
        'subtotal', 
        'size',
        'color',
    ];

    /**
     * The table associated with the model.
     * Explicitly set since we're using singular model name
     */
    protected $table = 'order_items';

    /**
     * Relationship with order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate line total
     */
    public function calculateSubtotal(): float
    {
        return $this->qty * $this->price;
    }

    /**
     * Update subtotal when quantity or price changes
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            if ($orderItem->isDirty(['qty', 'price'])) {
                $orderItem->subtotal = $orderItem->calculateSubtotal();
            }
        });
    }
}