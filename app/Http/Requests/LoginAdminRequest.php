<?php

namespace App\Http\Requests;

use App\Traits\GeneralTrait;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginAdminRequest extends FormRequest
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
            'phone_number' => 'required|exists:users,phone_number',
            'password' => 'required|string|min:6',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'email.required' => 'The email field is required.',
    //         'email.exists' => 'Invalid email or account type.',
    //         'password.required' => 'The password field is required.',
    //         'password.string' => 'The password field must be a string.',
    //         'password.min' => 'The password must be at least 6 characters long.',
    //     ];
    // }

    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $this->returnValidationError(401,$validator->errors()->all());
        }
    }
}
