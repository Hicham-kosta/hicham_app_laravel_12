<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use  HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        /*email',*/
        'mobile',
        'address_line1',
        'address_line2',
        'country',
        'city',
        'county',    // This stores the county/state
        'postcode',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}