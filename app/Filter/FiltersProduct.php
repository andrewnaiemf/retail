<?php
namespace App\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersProduct implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function (Builder $query) use ($value) {
            $query->where('name_ar', 'like', "%$value%")
                ->orWhere('name_en', 'like', "%$value%");
        });
    }
}
