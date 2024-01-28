<?php

namespace App\Http\Requests;

use App\Rules\ValidateMakeOrder;
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
            'order.branch_id' => [
                'required',
                'exists:users,id',
                new ValidateMakeOrder()
            ],
            'order.notes' => 'nullable|string',
            'order.location' => 'required|string',
            'order.date' => 'required|date|greater_than_yesterday',
            'order.line_items' => 'required|array',
            'line_items.*.product_id' => [
                'required',
                'exists:products,id',
                new ValidateStock(),
            ],
            'order.line_items.*.quantity' => 'required|numeric|min:1',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $this->returnValidationError(422,$validator->errors()->all());
        }
    }

    public function messages()
    {
        return [
            'order.notes.string' => 'The notes must be a string.',
            'order.location.required' => 'The location field is required.',
            'order.location.string' => 'The location must be a string.',
            'order.date.required' => 'The order date field is required.',
            'order.date.date' => 'The order date must be a valid date.',
            'order.date.greater_than_yesterday' => 'The order date must be greater than yesterday.',
            'order.line_items.required' => 'The line items field is required.',
            'line_items.*.product_id.required' => 'The product ID is required.',
            'line_items.*.product_id.exists' => 'The selected product is invalid.',
            'line_items.*.quantity.required' => 'The quantity is required.',
            'line_items.*.quantity.numeric' => 'The quantity must be a number.',
            'line_items.*.quantity.min' => 'The quantity must be at least :min.',
        ];
}
}
