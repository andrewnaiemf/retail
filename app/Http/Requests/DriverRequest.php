<?php

namespace App\Http\Requests;

use App\Traits\GeneralTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
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
            'name' => 'required|string',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|string|min:6',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $this->returnValidationError(422,$validator->errors()->all());
        }
    }
}
