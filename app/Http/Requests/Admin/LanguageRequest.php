<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      =>  'required|min:3|max:15|unique:languages,name,' . $this->id ,
            'abbr'      =>  'required|min:2|max:5|unique:languages,abbr,' . $this->id ,
        ];
    }

    public function messages()
    {
        return [
            'name.required'     =>  __('language.name_required') ,
            'name.min'          =>  __('language.name_min') ,
            'name.max'          =>  __('language.name_max') ,
            'name.unique'       =>  __('language.name_unique') ,

            'abbr.required'     =>  __('language.abbr_required') ,
            'abbr.min'          =>  __('language.abbr_min') ,
            'abbr.max'          =>  __('language.abbr_max') ,
            'abbr.unique'       =>  __('language.abbr_unique') ,
        ] ;
    }
}
