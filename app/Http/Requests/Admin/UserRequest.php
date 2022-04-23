<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'                  =>  'required|string|min:3|max:20|unique:users,name,' . $this->id ,
            'email'                 =>  'required|email|max:50|unique:users,email,' . $this->id ,
            'photo'                 =>  'required_without:id|image' ,
            'password'              =>  'required_without:id|max:30|confirmed' ,
            'password_confirmation' =>  'required_without:id|max:30' ,
        ];
    }

    public function messages()
    {
        return [
            'name.required'             =>  __('user.name_required') ,
            'name.string'               =>  __('user.name_string') ,
            'name.min'                  =>  __('user.name_min') ,
            'name.max'                  =>  __('user.name_max') ,
            'name.unique'               =>  __('user.name_unique') ,

            'email.required'            =>  __('user.email_required') ,
            'email.email'               =>  __('user.email_email') ,
            'email.max'                 =>  __('user.email_max') ,
            'email.unique'              =>  __('user.email_unique') ,

            'photo.required_without'    =>  __('user.photo_required_without') ,
            'photo.image'               =>  __('user.photo_image') ,

            'password.required_without'         =>  __('user.password_required_without') ,
            // 'password.min'              =>  __('user.password_min') ,
            'password.max'              =>  __('user.password_max') ,
            'password.confirmed'        =>  __('user.password_confirmed') ,

            'password_confirmation.required_without'    =>  __('user.password_confirmation_required_without') ,
            // 'password_confirmation.min'         =>  __('user.password_confirmation_min') ,
            'password_confirmation.max'         =>  __('user.password_confirmation_max') ,

        ] ;
    }
}
