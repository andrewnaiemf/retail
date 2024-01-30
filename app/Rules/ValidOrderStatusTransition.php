<?php

namespace App\Rules;

use App\Models\Order;
use App\Traits\GeneralTrait;
use Illuminate\Contracts\Validation\Rule;

class ValidOrderStatusTransition implements Rule
{
    use GeneralTrait;

    private $order_id ;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $shiping_status = [null, 'Received', 'Processing', 'Delivery', 'Delivered'];
        $order = Order::find($this->order_id);

        if ($order && in_array($order->shipping_status, $shiping_status)) {
            $currentStatusIndex = array_search($order->shipping_status, $shiping_status);
            $targetStatusIndex = array_search($value, $shiping_status);

            if ($currentStatusIndex !== false && $targetStatusIndex !== false &&   $targetStatusIndex - $currentStatusIndex == 1) {
                return true;
            }
        }
        return $this->returnValidationError(422,'Invalid order status transition.');

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $this->returnError(422,'Invalid order status transition.');
    }
}
