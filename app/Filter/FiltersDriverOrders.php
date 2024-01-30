<?php
namespace App\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersDriverOrders implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where('driver_id',auth('driver')->user()->id)->where(function (Builder $query) use ($value) {
            if ($value == 'Completed') {
                $query->where('status', 'Approved')->where('shipping_status', 'Delivered');
            }elseif ($value == 'New') {
                $query->where('status', 'Approved')->where('shipping_status', '!=', 'Delivered')->orWhereNull('shipping_status');
            }
        });
    }
}
