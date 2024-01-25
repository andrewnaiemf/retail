<?php

namespace App\Http\Requests;

use App\Rules\ValidateStock;
use App\Traits\GeneralTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class ValidateOrderRequest extends FormRequest
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
            'line_items' => 'required|array',
            'line_items.*.product_id' => [
                'required',
                'exists:products,id',
                new ValidateStock(),
            ],
            'line_items.*.quantity' => 'required|numeric|min:1',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $this->returnValidationError(422,$validator->errors()->all());
        }
    }
}
