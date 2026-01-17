<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PaymentTransaction;
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
        'taxes', 
        'total',
        'payment_method',
        'payment_status',
        'status',
        'tracking_number',
        'tracking_link',
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
        return $this->attributes['shipping'] ?? 0;
    }

    public function getGrandTotalAttribute()
    {
        return $this->attributes['total'] ?? 0;
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

    /**
     * Latest single log for quick access in listing
     */
    public function latestLog()
    {
        return $this->hasOne(OrderLog::class)->latestOfMany();
    }

    // ShortCut to get last successful payment transaction
    public function latestTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    // All payment transactions for this order
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'order_id');
    }

    // Currency resolver accessor: $order->currency_code
    public function getCurrencyCodeAttribute()
    {
        //1. If currency table has a currency column
        if(!empty($this->currency)){
            return strtoupper($this->currency);
        }
        
        //2. Otherwise use latest payment transaction currency
        $txnCurrency = $this->paymentTransactions()->latest()->value('currency');
        if(!empty($txnCurrency)){
            return strtoupper($txnCurrency);
        }

        //3. Fallback to app currency
        return strtoupper(config('app.currency', 'USD'));
    }

}