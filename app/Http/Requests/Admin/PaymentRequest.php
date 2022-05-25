<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'amount'    =>  'required|numeric|min:1' ,
        ];
    }


    public function messages()
    {
        return [
            'amount.required'   =>  __('customers.amount_required') ,
            'amount.numeric'    =>  __('customers.amount_numeric') ,
            'amount.min'        =>  __('customers.amount_min') ,
        ] ;
    }
}
