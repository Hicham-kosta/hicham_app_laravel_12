<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'], // validates against logged-in user
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
        ];
    }

    public function messages()
    {
        return [
            'current_password.current_password' => 'Your current password is incorrect.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ];
    }
}
