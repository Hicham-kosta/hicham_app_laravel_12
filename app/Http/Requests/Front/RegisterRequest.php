<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all visitors to register
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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:6'],
            'user_type' => ['required', 'in:Customer,Vendor'], // lowercase ✅
        ];
    }

    /**
     * Custom error messages for validation
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Your email address is required.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'Passwords do not match.',
            'password_confirmation.required' => 'Please confirm your password.',
            'user_type.in' => 'The selected user type is invalid. Please choose Customer or Vendor.',
        ];
    }
}
