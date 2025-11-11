<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    // Fix: Change method name from state() to states() and fix the relationship
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}