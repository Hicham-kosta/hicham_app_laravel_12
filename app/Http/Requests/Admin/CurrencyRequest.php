<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->has('code') ? strtoupper(trim($this->input('code'))) : null,
            'name' => $this->has('name') ? trim($this->input('name')) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencyId = $this->getCurrencyId();
        
        return [
            'code' => [
                'required', 
                'string', 
                'max:3', // Changed from 10 to 3 (standard currency codes are 3 chars)
                Rule::unique('currencies', 'code')->ignore($currencyId)
            ],
            'symbol' => ['nullable', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:255'], // Changed from nullable to required
            'rate' => ['required', 'numeric', 'min:0.000000001'], // More specific min value
            'status' => ['required', 'in:0,1'], // Changed from sometimes to required
            'is_base' => ['sometimes', 'boolean'],
            'flag' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048']
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Currency code is required',
            'code.max' => 'Currency code must not exceed 3 characters',
            'code.unique' => 'This currency code already exists',
            'name.required' => 'Currency name is required',
            'rate.required' => 'Exchange rate is required',
            'rate.numeric' => 'Exchange rate must be a valid number',
            'rate.min' => 'Exchange rate must be greater than 0',
            'flag.image' => 'The flag must be a valid image file',
            'flag.mimes' => 'The flag must be a PNG, JPG, JPEG, SVG, or WEBP file',
            'flag.max' => 'The flag file size must not exceed 2MB',
        ];
    }

    /**
     * Get the currency ID from route parameter.
     */
    private function getCurrencyId()
    {
        $currency = $this->route('currency') ?? $this->route('id');
        
        if (is_object($currency) && method_exists($currency, 'getKey')) {
            return $currency->getKey();
        }
        
        return $currency;
    }
}