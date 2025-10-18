<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\CouponService;
use App\Models\ColumnPreference;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService){
        $this->couponService = $couponService;
    }

    public function index()
    {
        Session::put('page', 'coupons');
        $result = $this->couponService->coupons();
        if($result['status'] === 'error'){
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }
        $coupons = $result['coupons'];
        $couponsModule = $result['couponsModule'];
        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
        ->where('table_name', 'coupons')->first();
        $couponsSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $couponsHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];
        return view('admin.coupons.index', 
        compact('coupons', 'couponsModule', 'couponsSavedOrder', 'couponsHiddenCols'));
    }

    public function updateCouponStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            $status = ($data['status'] === 'Active') ? 0 : 1;
            $coupon = Coupon::find($data['coupon_id']);
            if($coupon){
                $coupon->status = $status;
                $coupon->save();           
            }
            return response()->json(['status' => $status, 'coupon_id' => $data['coupon_id']]);
        } 
    }
}
