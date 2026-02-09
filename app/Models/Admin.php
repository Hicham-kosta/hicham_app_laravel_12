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

    /**
     * Products relationship for vendors
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    /**
     * Scope for vendors only
     */
    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    /**
     * Check if admin is a vendor
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if vendor is approved
     */
    public function isApprovedVendor(): bool
    {
        return $this->isVendor() && 
               $this->vendorDetails && 
               $this->vendorDetails->is_verified == 1;
    }
}
