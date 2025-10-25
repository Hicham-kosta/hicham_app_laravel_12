<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // All users can attempt login
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'user_type' => ['required', 'in:Customer,Vendor'], // lowercase ✅
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'password.min' => 'Your password must be at least 6 characters long.',
            'user_type.required' => 'Please select a user type.',
            'user_type.in' => 'The selected user type is invalid.',
        ];
    }
}
