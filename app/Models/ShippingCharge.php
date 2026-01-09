<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'min_weight_g',
        'max_weight_g',
        'min_subtotal',
        'max_subtotal',
        'rate',
        'is_default',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
