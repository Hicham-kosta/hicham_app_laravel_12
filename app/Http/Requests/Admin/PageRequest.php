<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PageRequest extends FormRequest
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
        $id = $this->input('id'); // This is set by prepareForValidation from the route parameter

        return [
            'title' => ['required','string','max:255'],
            'url' => ['required','string','max:255', 'regex:/^[a-z0-9-]+$/',Rule::unique('pages')->ignore($id)],
            'description' => ['nullable','string'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:500'],
            'meta_keywords' => ['nullable','string','max:500'],
            'status' => ['nullable'],
            'sort_order' => ['nullable','integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'url.required' => 'The url field is required.',
            'url.regex' => 'The url may only contain only lowercase letters, numbers, and hyphens (e.g., about-us, contact-us).',
            'url.unique' => 'The url has already been taken.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // If we are updating, the route parameter 'page' will be set to the page id.
        if($this->route('page')){
            $this->merge([
                'id' => $this->route('page'),
            ]);
        }

        // If the url is provided, slugify it.
        if($this->filled('url')){
            $this->merge([
                'url' => Str::slug($this->input('url'), '-'),
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}