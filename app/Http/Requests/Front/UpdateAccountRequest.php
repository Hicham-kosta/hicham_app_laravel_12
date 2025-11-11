<?php

namespace App\Http\Requests\Front;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        $userId = Auth::id();
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'county_text' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => $this->input('name') ? trim($this->input('name')) : $this->input('name'),
            'email' => $this->input('email') ? trim($this->input('email')) : $this->input('email'),
        ]);
    }
}