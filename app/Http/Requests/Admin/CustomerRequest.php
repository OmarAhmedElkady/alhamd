<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'client_permissions'   =>  'required|in:pharmaceutical,special_customer,customer' ,
            'customer.*.name'   =>  'required|min:3|max:40|unique:customers,name,'.$this->translation_of.',translation_of' ,
            // 'phone'             =>  'numeric|min:11|max:11' ,
            // 'title'             =>  'string' ,
        ];
    }


    public function messages()
    {
        return [
            'client_permissions.required'  =>  __('customers.client_permissions_required') ,
            'client_permissions.in'  =>  __('customers.client_permissions_in') ,

            'customer.*.name.required'  =>  __('customers.customer_*_name_required') ,
            'customer.*.name.min'  =>  __('customers.customer_*_name_min') ,
            'customer.*.name.max'  =>  __('customers.customer_*_name_max') ,
            'customer.*.name.unique'  =>  __('customers.customer_*_name_unique') ,

            // 'phone.numeric'  =>  __('customers.phone_numeric') ,
            // 'phone.min'  =>  __('customers.phone_min') ,
            // 'phone.max'  =>  __('customers.phone_max') ,

            // 'title.string'  =>  __('customers.title_string') ,
        ] ;
    }

}
