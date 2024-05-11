<?php

namespace App\Http\Requests;

use App\Traits\GeneralTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreProductsForCustomerRequest extends FormRequest
{
    use GeneralTrait;

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
            'customer' => 'required|exists:users,id',
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'required|numeric',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $this->returnValidationError(422,$validator->errors()->all());
        }
    }
}
