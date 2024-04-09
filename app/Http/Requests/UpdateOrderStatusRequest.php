<?php

namespace App\Http\Requests;

use App\Rules\ValidOrderStatusTransition;
use App\Traits\GeneralTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
        $order_id = $this->route('order');
        return [
            'status' => [new ValidOrderStatusTransition($order_id)],
            'confirmation_image' =>  [
                'image',
//                'dimensions:min_width=100,min_height=100,max_width=1920,max_height=1080',
//                'max:20480'
            ],
            'driver' => 'exists:users,id',
        ];
    }
}
