<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletCredit extends Model
{
    protected $table = 'wallet_credits';

    protected $fillable = [
        'user_id',
        'amount',
        'expires_at',
        'reason',
        'added_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'date',
    ];


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
