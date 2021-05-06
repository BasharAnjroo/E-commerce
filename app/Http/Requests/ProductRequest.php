<?php

namespace App\Http\Requests;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
            'name'              => 'required|max:255',
            'description'       => 'required|min:3',
            'price'             => 'required|numeric|between:0,9999999999999.999999',
            'tax_id'        => 'required',
            'category_id'        => 'required',
            'sku'               => 'required|unique:products,sku'.($this->request->get('id') ? ','.$this->request->get('id') : ''),
            'stock'             => 'required|numeric',
            'active'            => 'required|numeric|between:0,1',
            'attribute_set_id' => 'required',
            'attributes'        => 'sometimes|required',
            'attributes.*'      => 'sometimes|required',
            'fileSource'        => 'sometimes|required',
            'fileSource.*'      => 'sometimes|required',
            'city_id' => '',
            'address'=> ''
        ];
    }
            /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [];

        if ($this->input('attributes')) {
            foreach ($this->input('attributes') as $key) {
                $attributes[$key] = Attribute::find($key)->name;
            }
        }

        return $attributes;
    }



    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'A name is required',
            'description.required' => 'A description is required',
            'price.required' => 'A price is required',
            'description.required' => 'A description is required',
            'categories.required' => 'A categories is required',
            'sku.required' => 'A sku is required',
            'stock.required' => 'A stock is required',
            'active.required' => 'A active is required',
            'attribute_set_id.required' => 'A attribute_set_id is required',
            'attributes.required' => 'A attributes is required',

        ];
    }
}
