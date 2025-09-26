<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Brand;

class BrandRequest extends FormRequest
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
        return [
            'name' => 'required',
            'url' => 'required|regex:/^[\pL\s\-]+$/u',
        ];
    }
    /**
     * Custom error messages for validation
     */


    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required',
            'url.required' => 'Brand URL is required',  
        ];
    }

    /**
     * Prepare Request for validation
     */

    protected function prepareForValidation(){
        if($this->route('brand')){
            $this->merge([
                'id' => $this->route('brand'),
            ]);
        }
    }

    /**
     * Custom validator logic for cheking URL uniqueness
     */

     public function withValidator($validator)
     {
         $validator->after(function ($validator) {
             $brandCount = Brand::where('url', $this->input('url'));
              if($this->filled('id')){
                    $brandCount->where('id', '!=', $this->input('id'));
              }
              if ($brandCount->count() > 0) {
                    $validator->errors()->add('url', 'Brand URL already exists');
             }
         });
     }

     /**
      * Customise validation faillure response
      */

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
                redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
        );
   }
}
