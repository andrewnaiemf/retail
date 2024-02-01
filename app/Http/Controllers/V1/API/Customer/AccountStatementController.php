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
        $per_page = $request->header('per_page') ?? 10;

        $receipts = QueryBuilder::for(Receipt::class)->where('contact_id', $request->branch_id)
        ->allowedFilters([
            AllowedFilter::custom('date_range', new StartsBetweenFilter),
            ])
        ->paginate($per_page);

        return $this->returnData($receipts);
    }
}
