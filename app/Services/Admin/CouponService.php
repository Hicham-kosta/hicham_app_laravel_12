<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;
use App\Models\AdminsRole;
use Illuminate\Support\Arr;

class CouponService
{
    /**
     * add or update coupon
     * 
     * @param array $data
     * @return string (success message)
     */

    public function addEditCoupon(array $data): string
{
    // Normalize arrays to JSON for storage
    $store = [
        'coupon_option' => $data['coupon_option'] ?? 'Automatic',
        'coupon_code' => $data['coupon_code'] ?? strtoupper(substr(md5(uniqid()), 0, 8)),
        'categories' => !empty($data['categories']) ? json_encode(array_values($data['categories'])) : null,
        'brands' => !empty($data['brands']) ? json_encode(array_values($data['brands'])) : null,
        'users' => !empty($data['users']) ? json_encode(array_values($data['users'])) : null,
        'coupon_type' => $data['coupon_type'] ?? 'Multiple',
        'amount_type' => strtolower($data['amount_type'] ?? 'fixed'),
        'amount' => $data['amount'] ?? 0,
        'min_qty' => $data['min_qty'] ?? null,
        'max_qty' => $data['max_qty'] ?? null,
        'min_cart_value' => $data['min_cart_value'] ?? null,
        'max_cart_value' => $data['max_cart_value'] ?? null,
        'usage_limit_per_user' => $data['usage_limit_per_user'] ?? 0, // Fixed typo: usage_limi_per_user -> usage_limit_per_user
        'total_usage_limit' => $data['total_usage_limit'] ?? 0,
        'max_discount' => $data['max_discount'] ?? null,
        'expiry_date' => $data['expiry_date'] ?? null,
        'visible' => isset($data['visible']) ? (int)$data['visible'] : 0,
        'status' => isset($data['status']) ? (int)$data['status'] : 1, // Changed default to 1 (active)
    ];

    $coupon = Coupon::updateOrCreate(
        ['id' => $data['id'] ?? null],
        $store
    );
    
    return isset($data['id'])
        ? 'Coupon Updated Successfully'
        : 'Coupon Created Successfully';
}


    public function coupons()
    {
        $admin = Auth::guard('admin')->user();
        $coupons = Coupon::orderBy('id', 'desc')->get();
        $couponsModuleCount = AdminsRole::where([
            'subadmin_id' => $admin->id,
            'module' => 'coupons',
        ])->count();
        if($admin->role == "admin"){
            $couponsModule = [
                'view_access' => 1, 
                'edit_access' => 1, 
                'full_access' => 1
            ];
        }elseif($couponsModuleCount == 0){
            return [
                'status' => 'error', 
                'message' => 'You do not have permission to access this module'];
        }else{
            $couponsModule = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'coupons',
            ])->first()->toArray();
        }
        return [
            'status' => 'success', 
            'coupons' => $coupons, 
            'couponsModule' => $couponsModule
        ];
    }
}