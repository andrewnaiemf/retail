<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Filter\StartsBetweenFilter;
use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AccountStatementController extends Controller
{
    public function index(Request $request)
    {
        $receipts = QueryBuilder::for(Receipt::class)
        ->allowedFilters([
            AllowedFilter::custom('date_range', new StartsBetweenFilter),
            ])
        ->paginate(10);

        return $this->returnData($receipts);
    }
}
