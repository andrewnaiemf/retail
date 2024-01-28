<?php

namespace App\Rules;

use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Contracts\Validation\Rule;

class ValidateMakeOrder implements Rule
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
        $overdue = auth('customer')->user()->overdue;
        if ($overdue && $overdue > 0) {
            return $this->returnValidationError(422, 'can not make order before pay overdue  invoices');
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
