<?php
namespace App\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersProductByCustomer implements Filter
{
    protected $customer_id;

    public function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('customers', function ($q) use ($value) {
            $q->where('customer_product.customer_id', $this->customer_id)
              ->where(function (Builder $query) use ($value) {
                  $query->where('products.name_ar', 'like', "%$value%")
                        ->orWhere('products.name_en', 'like', "%$value%");
              });
        })
        ->with(['category','customers' => function ($q) {
            $q->select('customer_product.price');
        }]);
    }
}
