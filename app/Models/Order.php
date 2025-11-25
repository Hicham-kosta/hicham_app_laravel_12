<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'transaction_id',
        'order_number',
    ];

    /**
     * Relationship with order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with address
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Calculate order total from items (for verification)
     */
    public function calculateTotal(): float
    {
        return $this->orderItems->sum('subtotal');
    }

    /**
     * Check if order belongs to authenticated user
     */
    public function belongsToUser($user): bool
    {
        return $this->user_id === $user->id;
    }
}