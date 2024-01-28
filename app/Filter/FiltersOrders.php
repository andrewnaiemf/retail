<?php
namespace App\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersOrders implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function (Builder $query) use ($value) {
            if ($value == 'Completed') {
                $query->where('status', 'Approved')->where('shipping_status', 'Delivered')->orWhere('status','Declined');
            }elseif ($value == 'New') {
                $query->where('status', 'Draft');
            }elseif ($value == 'Current') {
                $query->where('status', 'Approved')->where( function ($q) {
                    $q->where('shipping_status', 'Delivered')
                    ->orWhereNull('shipping_status');
                });
            }

        });
    }
}
