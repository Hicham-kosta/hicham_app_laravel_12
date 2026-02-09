<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $admin = Auth::guard('admin')->user();
        $rules = [
            'category_id' => 'required',
            'brand_id' => 'required',
            'product_name' => 'required|max:200',
            'product_code' => 'required|max:30',
            'product_price' => 'required|numeric|gt:0',
            'product_color' => 'required|max:200',
            'family_color' => 'required|max:200',
            'vendor_id' => $admin->role == 'admin' ? 'required|exists:admins,id,role,vendor' : 'nullable',
        ];

        $productId = $this->route('product');
        if ($this->isMethod('post')) {
            $rules['product_url'] = [
                'nullable',
                Rule::unique('products', 'product_url')
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['product_url'] = [
                'required',
                Rule::unique('products', 'product_url')->ignore($productId)
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Category is required',
            'brand_id.required' => 'Brand is required',
            'product_name.required' => 'Product name is required',
            'product_code.required' => 'Product code is required',
            'product_price.required' => 'Product price is required',
            'product_price.numeric' => 'Product price must be a number',
            'product_color.required' => 'Product color is required',
            'family_color.required' => 'Family color is required',
            'product_url.required' => 'Product URL is required when updating',
            'product_url.unique' => 'Product URL must be unique',
            'vendor_id.required' => 'Please select a vendor',
            'vendor_id.exists' => 'Selected vendor does not exist or is not approved',
        ];
    }
}
