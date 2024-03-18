<?php

namespace App\Rules;

use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Contracts\Validation\Rule;
-
class ValidateStock implements Rule
{
    use GeneralTrait;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $errorMessages = [];

        foreach (request('line_items') as $index => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $product_stock = $product->inventories->first()->pivot->stock;
                if ($product_stock < $item['quantity']) {
                    $errorMessages[$index] = sprintf('The selected quantity for line item %d is not available in stock.', $index + 1);
                }
            }
        }

        if (!empty($errorMessages)) {
            return $this->returnValidationError(422, $errorMessages);
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Validation failed.';
    }
}
