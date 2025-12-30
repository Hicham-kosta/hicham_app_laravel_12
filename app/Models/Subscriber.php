<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $table = 'subscribers';
    protected $fillable = ['email', 'status'];

    protected $cats = ['status' => 'integer'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
