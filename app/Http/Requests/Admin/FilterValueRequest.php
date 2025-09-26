<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FilterValueRequest extends FormRequest
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
        // Mutch route parameter name exactly
        $filterValueId = $this->route('value'); // Null on create
        $filterId = $this->route('filter'); // Always present

        return [
            'value' => 'required|string|max:255|unique:filter_values,value,' . $filterValueId . ',id,filter_id,' . $filterId,
            'sort' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'value.required' => 'Filter value is required.',
            'value.unique' => 'This value already exists for the selected filter.',
        ];
    }
}
