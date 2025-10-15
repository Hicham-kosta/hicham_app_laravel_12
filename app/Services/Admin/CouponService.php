<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;
use App\Models\AdminsRole;

class CouponService
{
    public function coupons()
    {
        $admin = Auth::guard('admin')->user();
        $coupons = Coupon::orderBy('id', 'desc')->get();
        $couponsModuleCount = AdminsRole::where([
            'subadmin_id' => $admin->id,
            'module' => 'coupons',
        ])->count();
        if($admin->role == "admin"){
            $couponsModule = ['view_access' => 1, 'edit_access' => 1, 'full_access' => 1];
        }elseif($couponsModuleCount == 0){
            return ['status' => 'error', 'message' => 'You do not have permission to access this module'];
        }else{
            $couponsModule = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'coupons',
            ])->first()->toArray();
        }
        return ['status' => 'success', 'coupons' => $coupons, 'couponsModule' => $couponsModule];
    }
}