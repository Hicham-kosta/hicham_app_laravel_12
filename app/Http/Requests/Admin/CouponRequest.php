<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'coupon_option' => 'required|in:Automatic,Manual',
            'coupon_code' => 'required_if:coupon_option,Manual|string|max:50',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|integer',
            'brands' => 'nullable|array', 
            'brands.*' => 'nullable|integer',
            'users' => 'nullable|array',
            'users.*' => 'nullable|string|email',
            'coupon_type' => 'required|in:Multiple,Single',
            'amount_type' => 'required|in:percentage,fixed',
            'amount' => 'required|numeric|min:0',
            'min_qty' => 'nullable|integer|min:1',
            'max_qty' => 'nullable|integer|min:1|gt:min_qty',
            'min_cart_value' => 'nullable|numeric|min:0',
            'max_cart_value' => 'nullable|numeric|min:0|gt:min_cart_value',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'total_usage_limit' => 'nullable|integer|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'expiry_date' => 'required|date|after:today',
            'visible' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'coupon_code.required_if' => 'Coupon code is required when coupon option is Manual',
            'expiry_date.after' => 'Expiry date must be a future date',
        ];
    }
}