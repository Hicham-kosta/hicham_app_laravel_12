<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'mobile'         => 'required|string|max:20',
            'address_line1'  => 'required|string|max:255',
            'address_line2'  => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'country'        => 'required|string|max:255',
            'postcode'       => 'required|string|max:20',
        ];

       /* if ($this->input('country') === 'United Kingdom') {
            $rules['county'] = 'required|string|max:255';
        } else {
            $rules['county_text'] = 'required|string|max:255';
        }*/

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $country = trim($this->input('country'));

        $countyFinal =
            ($country === 'United Kingdom')
            ? trim($this->input('county'))
            : trim($this->input('county_text'));

        $this->merge([
            'country'      => $country,
            'first_name'   => trim($this->input('first_name')),
            'last_name'    => trim($this->input('last_name')),
            'mobile'       => trim($this->input('mobile')),
            'address_line1'=> trim($this->input('address_line1')),
            'address_line2'=> trim($this->input('address_line2')),
            'city'         => trim($this->input('city')),
            'postcode'     => trim($this->input('postcode')),
            'county'       => $countyFinal,  // unified field
        ]);
    }
}
