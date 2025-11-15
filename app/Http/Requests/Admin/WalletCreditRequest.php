<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class WalletCreditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Map route (for update) if you need it later
        if($this->route('wallet_credit')) {
            $this->merge([
                'id' => $this->route('wallet_credit')
            ]);
        }
        // Convert UI fields into a signed amount for validation & service
        if($this->filled('amount_abs')){
            $abs = (float) $this->input('amount_abs');
            $sign = ($this->input('action') === 'debit') ? 1 : -1;
            $this->merge([
                'amount' => abs($abs) * $sign,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'between:-100000000,100000000', 'not_in:0'],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'reason' => ['nullable', 'string', 'max:255'],
            //'added_by' => ['required', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'amount.required' => 'Please enter the amount.',
            
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
