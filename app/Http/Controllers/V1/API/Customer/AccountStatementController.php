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
        $per_page = $request->headers->get('per-page') ?? 10;

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
            $invoice['debit'] = $invoice->total;
            $invoice['credit'] = 0.00;
            $invoice['date'] = $invoice->issue_date;
            return $invoice;
        });

        $receipts = $receipts->map(function ($receipt) {
            $receipt['type'] = trans('locale.receipt');
            $receipt['debit'] = 0;
            $receipt['credit'] = $receipt->amount;
            return $receipt;
        });

        $result = $receipts->merge($invoices);
        $result = $result->sortBy('date');
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
