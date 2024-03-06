<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Filter\StartsBetweenFilter;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
        ->get();

        $invoices = QueryBuilder::for(Invoice::class)->where('contact_id', $request->branch_id)
            ->allowedFilters([
                AllowedFilter::custom('date_range', new StartsBetweenFilter),
            ])
            ->get();

        $invoices = $invoices->map(function ($invoice) {
            $invoice['type'] = trans('locale.invoice');
            return $invoice;
        });

        $receipts = $receipts->map(function ($receipt) {
            $receipt['type'] = trans('locale.receipt');
            return $receipt;
        });

        $result = $receipts->merge($invoices);
        $result = $result->sortByDesc('created_at');
        $result = $result->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $per_page;
        $currentPageItems = $result->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedResult = new LengthAwarePaginator(
            $currentPageItems,
            $result->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $this->returnData($paginatedResult);
    }
}
