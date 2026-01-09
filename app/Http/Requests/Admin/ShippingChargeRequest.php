<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShippingChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:100',
            'min_weight_g' => 'nullable|integer|min:0',
            'max_weight_g' => 'nullable|integer|min:0',
            'min_subtotal' => 'nullable|numeric|min:0',
            'max_subtotal' => 'nullable|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|in:0,1',
            'status' => 'nullable|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'country_id.required' => 'Please select a country',
            'country_id.exists' => 'Selected country does not exist',
            'rate.required' => 'Please enter a rate',
            'rate.numeric' => 'Rate must be a number',
        ];
    }
}
