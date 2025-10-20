<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('coupon') instanceof \App\Models\Coupon
            ? $this->route('coupon')->id
            : $this->route('coupon');

        return [
            'coupon_option' => 'nullable|string|in:Automatic,Manual,automatic,manual',
            'coupon_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'coupon_code')->ignore($id),
            ],
            'coupon_type' => 'required|string|in:Single,Multiple,single,multiple',
            'amount_type' => 'required|string|in:Percentage,Fixed,percentage,fixed',
            'amount' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'min_qty' => 'nullable|integer|min:0',
            'max_qty' => 'nullable|integer|min:0',
            'min_cart_value' => 'nullable|numeric|min:0',
            'max_cart_value' => 'nullable|numeric|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'total_usage_limit' => 'nullable|integer|min:0',
            'visible' => 'nullable|in:0,1',
            'status' => 'nullable|in:0,1',
            'categories' => 'nullable|array',
            'brands' => 'nullable|array',
            'users' => 'nullable|array',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'visible' => $this->has('visible') ? (int)$this->input('visible') : 0,
            'status' => $this->has('status') ? (int)$this->input('status') : 0,
        ]);
    }
}
