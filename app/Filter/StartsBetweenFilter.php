<?php
namespace App\Filter;

use Carbon\Carbon;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class StartsBetweenFilter implements Filter
{


    public function __invoke(Builder $query, $value, string $property)
    {
        $start = Carbon::parse(trim($value[0]));
        $end = Carbon::parse(trim($value[1]));

        $modelName = class_basename($query->getModel());

        if ($modelName == 'Invoice'){
            $query->where('invoices.contact_id', auth('customer')->user()->id)
                ->where(function (Builder $query) use ($start , $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                })->with(['contact']);
        }else{
            $query->whereHas('account', function ($q) use ($start , $end) {
                $q->where('receipts.contact_id', auth('customer')->user()->id)
                    ->where(function (Builder $query) use ($start , $end) {
                        $query->whereBetween('created_at', [$start, $end]);
                    });
            })->with(['contact', 'account', 'allocates']);
        }
    }
}
