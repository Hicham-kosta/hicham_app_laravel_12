<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'product_size',
        'product_qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
