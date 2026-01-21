<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDetail extends Model
{
    protected $fillable = [
        'admin_id',
        'shop_name',
        'shop_address',
        'shop_city',
        'shop_state',
        'shop_pincode',
        'shop_country',
        'shop_mobile',
        'shop_email',
        'shop_website',
        'gst_number',
        'pan_number',
        'business_license_number',
        'address_proof',
        'address_proof_image',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'is_verified'
    ];
}
