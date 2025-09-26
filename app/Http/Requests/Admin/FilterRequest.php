<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
        $filterId = $this->route('filter'); // Null on create
        return [
            'filter_name' => 'required|max:100|unique:filters,filter_name,' . $filterId,
            'filter_column' => 'required|string|max:100|unique:filters,filter_column,' . $filterId,
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'filter_name.required' => 'Filter Name is required.',
            'filter_name.unique' => 'This filter already exists.',
            'filter_column.required' => 'Filter column is required.',
            'filter_column.unique' => 'Filter column must be unique.',
            'category_ids.required' => 'At least one category must be selected.',
        ];
    }
}
