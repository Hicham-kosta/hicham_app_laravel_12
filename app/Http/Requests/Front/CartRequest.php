<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // allow guests too
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeName = $this->route() ? $this->route()->getName() : null;
        $method = $this->method();
        $rules = [];
        if($routeName === 'cart.store' || ($method === 'POST' && $this->routeIs('cart.store'))) {
            $rules = [
                'product_id' => 'required|exists:products,id',
                'size' => 'required|string',
                'qty' => 'required|integer|min:1',
            ];
        }else if($routeName === 'cart.update' || ($method === 'PATCH' && $this->routeIs('cart.update'))) {
            $rules = [
                'qty' => 'required|integer|min:1',
            ];
        }else if($routeName === 'cart.destroy' || ($method === 'DELETE' && $this->routeIs('cart.destroy'))) {
            // No additional rules needed for deletion
            $rules = [];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'size.required' => 'Product size is required.',
            'qty.required' => 'Quantity is required.',
        ];
    }
}
