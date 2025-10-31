<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    protected $fillable = [
        'code',
        'symbol',
        'name',
        'rate',
        'status',
        'is_base',
        'flag'
    ];

    protected $casts = [
        'rate' => 'float',
        'status' => 'integer',
        'is_base' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function baseCurrency()
    {
        return self::where('is_base', true)->first();
    }

}

