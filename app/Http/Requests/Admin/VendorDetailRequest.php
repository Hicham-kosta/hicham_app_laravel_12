<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VendorDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('admin')->check() 
        && auth()->guard('admin')->user()->role == 'vendor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Business
            'shop_name' => 'required|string|max:255',
            'shop_mobile' => 'required|digits:10',
            'shop_address' =>'nullable|string',

            // Bank
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',

            // KYC
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
            'address_proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
