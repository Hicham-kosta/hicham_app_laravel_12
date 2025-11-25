<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckoutRequest extends FormRequest
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
        return [
            'address_id' => [
                'required', 
                'integer', 
                Rule::exists('addresses', 'id')->where(function($query){
                    $userId = Auth::id();
                    if(!$userId){
                        $query->whereRaw('1 = 0');
                    }else{
                        $query->where('user_id', $userId);
                    }
               }),
            ],
            'payment_method' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'address_id.required' => 'Please select a delevery address before placing the order',
            'address_id.exists' => 'Selected Address not found or does not belong to you',
            'payment_method.required' => 'Please select a payment method before placing the order'
        ];
    }
}
