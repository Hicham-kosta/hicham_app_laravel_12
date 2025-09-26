<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Category;

class CategoryRequest extends FormRequest
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
            'category_name' => 'required',
            'url' => 'required|regex:/^[\pL\s\-]+$/u'
        ];
    }

    /** Custom error message for validation 
     * 
     */

    public function messages(){
        return [
        'category_name_required' => 'Category Name is Required',
        'url.required' => 'Category URL is Required'
        ];
    }

    /** 
     * Prepare request before validation
     * 
     */
    protected function prepareForValidation(){
        if($this->route('category')){
            $this->merge([
                'id' => $this->route('category')
            ]);
        }
    }

    /** 
     * Custom validator logic for checking URL uniquiness
     */
    public function withValidator($validator){
        $validator->after(function($validator){
            $categoryCount = Category::where('url', $this->input('url'));
            if($this->filled('id')){
                $categoryCount->where('id', '!=', $this->input('id'));
            }
            if($categoryCount->count() > 0){
                $validator->errors()->add('url', 'Category already exists');
            }
        });
    }

    /**
     * Customise validation failure response
     */
    protected function failedValidation(Validator $validator){
        throw new HttpRespnseException(
            redirect()->back()
            ->withErrors($validator)
            ->withInput()
        );
    }
    
}
