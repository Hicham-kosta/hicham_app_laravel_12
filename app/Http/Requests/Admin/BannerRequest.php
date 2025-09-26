<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
        $rules = [
            'type' => 'required|string|max:255',
            'link' => 'nullable|url|max:500',
            'title' => 'required|string|max:255',
            'alt' => 'nullable|string|max:255',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ];
         
        if($this->isMethod('post') || $this->hasFile('image')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        return $rules;
      }

      /**
       * Get the custom messages for the validation rules.
       *
       * @return array<string, string>
       */

      public function messages(): array
      {
          return [
              'type.required' => 'The banner type is required.',
              'link.url' => 'The link must be a valid URL.',
              'title.required' => 'The title is required.',
              'image.required' => 'Please upload a banner image.',
              'image.image' => 'The uploaded file must be an image.',
              'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
              'image.max' => 'The image size must not exceed 2MB.',
          ];
      }
}
