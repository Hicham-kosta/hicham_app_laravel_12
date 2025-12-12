<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'address_id', 
        'subtotal', 
        'discount', 
        'wallet', 
        'shipping', 
        'total',
        'payment_method',
        'payment_status',
        'status',
        'tracking_number',
        'shipping_partner',
        'transaction_id',
        'order_number',
    ];

    /**
     * Relationship with order items
     */
    public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'order_id');
}


    /**
     * Relationship with address
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Calculate order total from items (for verification)
     */
    public function calculateSubtotal(): float
    {
        return $this->items->sum('subtotal');
    }

    public function getShippingAmountAttribute()
    {
        return $this->attributes('shipping') ?? 0;
    }

    public function getGrandTotalAttribute()
    {
        return $this->attributes('total') ?? 0;
    }

    /**
     * Check if order belongs to authenticated user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }
}