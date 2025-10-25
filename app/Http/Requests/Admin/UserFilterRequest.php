<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // If you have admin guard, check it; otherwise check if user is admin
        if (Auth::guard('admin')->check()) {
            return true;
        }

        if (Auth::check()) {
            $user = Auth::user();
            if (isset($user->is_admin) && (int)$user->is_admin === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
        ];
    }
}
