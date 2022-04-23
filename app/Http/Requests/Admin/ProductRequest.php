<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category_id'       =>  'required' ,
            'product.*.name'    =>  'required|min:3|max:100|unique:products,name,'.$this->translation_of.',translation_of' ,
            'image'             =>  'required_without:translation_of|image' ,
            'purchasing_price'  =>  'required|numeric' ,
            'pharmacist_price'  =>  'required|numeric' ,
            'selling_price'     =>  'required|numeric' ,
            'store'             =>  'required|numeric' ,
        ];
    }


    public function messages()
    {
        return[
            'category_id.required'      =>  __('products.category_id_required') ,

            'product.*.name.required'   =>  __('products.product_*_name_required') ,
            'product.*.name.min'        =>  __('products.product_*_name_min') ,
            'product.*.name.max'        =>  __('products.product_*_name_max') ,
            'product.*.name.unique'     =>  __('products.product_*_name_unique') ,

            'image.required_without'            =>  __('products.image_required') ,
            'image.image'               =>  __('products.image_image') ,

            'purchasing_price.required' =>  __('products.purchasing_price_required') ,
            'purchasing_price.numeric'  =>  __('products.purchasing_price_numeric') ,

            'pharmacist_price.required' =>  __('products.pharmacist_price_required') ,
            'pharmacist_price.numeric'  =>  __('products.pharmacist_price_numeric') ,

            'selling_price.required'    =>  __('products.selling_price_required') ,
            'selling_price.numeric'     =>  __('products.selling_price_numeric') ,

            'store.required'            =>  __('products.store_required') ,
            'store.numeric'             =>  __('products.store_numeric') ,
        ] ;
    }
}
