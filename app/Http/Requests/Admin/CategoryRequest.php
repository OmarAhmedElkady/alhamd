<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CategoryRequest extends FormRequest
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
            'category.*.name'   =>  'required|min:3|max:30|unique:categories,name,'.$this->translation_of.',translation_of'
        ];
    }

    public function messages()
    {
        return [
            'category.*.name.required'  =>  __('categories.name_required') ,
            'category.*.name.min'       =>  __('categories.name_min') ,
            'category.*.name.max'       =>  __('categories.name_max') ,
            'category.*.name.unique'    =>  __('categories.name_unique') ,
        ] ;
    }
}
