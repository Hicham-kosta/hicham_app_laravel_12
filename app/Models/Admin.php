<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'role',
        'mobile',
        'email',
        'password',
        'image',
        'status',
        'confirm',
    ];

    public function vendorDetails()
    {
        return $this->hasOne(VendorDetail::class, 'admin_id');
    }
}
