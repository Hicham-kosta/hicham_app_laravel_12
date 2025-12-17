<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    protected $fillable = [
        'order_id',
        'order_status_id', // Make sure this is included
        'tracking_number',
        'tracking_link',
        'shipping_partner',
        'remarks',
        'updated_by'
    ];
    
    // Relationships
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
