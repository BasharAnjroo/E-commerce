<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProductUpdateRequest extends FormRequest
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
            'categories'        => 'required',
            'sku'               => 'required|unique:products,sku'.($this->request->get('id') ? ','.$this->request->get('id') : ''),
            'stock'             => 'required|numeric',
            'active'            => 'required|numeric|between:0,1',
            'attribute_set_id' => 'required',
            'attributes'        => 'sometimes|required',
            'attributes.*'      => 'sometimes|required'
        ];
    }
            /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
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
            'slug.required' => 'A slug is required',
        ];
    }
}
