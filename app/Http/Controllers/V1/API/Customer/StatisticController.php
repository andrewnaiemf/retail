<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentYear = now()->year;

        $customer = auth('customer')->user();

        $invoiceData = Invoice::where('contact_id', $customer->id)
            ->whereYear('issue_date', $currentYear)
            ->select('total', 'issue_date as date', \DB::raw('MONTH(issue_date) as month'))
            ->get();

        $receiptData = Receipt::where('contact_id', $customer->id)
            ->whereYear('date', $currentYear)
            ->select('amount as paid_amount', 'date', \DB::raw('MONTH(date) as month'))
            ->get();

        $results = $invoiceData->concat($receiptData)->groupBy('month');
        // Check if there are any results
        if ($results->isEmpty()) {
            // If no results, return empty data structure
            return response()->json(['data' => $this->getEmptyStatisticsData()]);
        }

        $statisticsData = $this->processInvoicesData($results,$currentYear);
        return response()->json(['data' => $statisticsData]);
    }

    private function processInvoicesData($statistics,$currentYear)
    {
        $statisticsData = [];
        $year = 'Year';

        foreach ($statistics as $month => $statistic) {

            if (!isset($statisticsData[$year])) {
                $statisticsData[$year] = [];
            }

            if (!isset($statisticsData[$year][$month])) {
                $statisticsData[$year][$month] = [
                    'total_due_amount' => 0,
                    'total_paid_amount' => 0,
                ];
            }
            foreach ($statistic as $item) {
                $modelName = class_basename(get_class($item->first()));

                if ($modelName == 'Invoice'){
                    $statisticsData[$year][$month]['total_due_amount'] += $item->total;

                }
                if ($modelName == 'Receipt'){
                    $statisticsData[$year][$month]['total_paid_amount'] += $item->paid_amount;
                }
            }

        }

        // Fill missing months with empty data
        $allMonths = range(1, 12);
        foreach ($statisticsData as &$yearData) {
            $missingMonths = array_diff($allMonths, array_keys($yearData));
            foreach ($missingMonths as $missingMonth) {
                $yearData[$missingMonth] = [
                    'total_paid_amount' => 0,
                    'total_due_amount' => 0,
                ];
            }
            ksort($yearData); // Sort months in ascending order
        }
        if (isset($statisticsData) && ! empty($statisticsData)){
            $statisticsData[$year] = array_values($statisticsData['Year']); // Convert Year to indexed array

        }

        return $statisticsData;
    }

    private function getEmptyStatisticsData()
    {
        $emptyData = [];
        $year = 'Year';
        $allMonths = range(1, 12);

        foreach ($allMonths as $month) {
            $emptyData[$year][$month] = [
                'total_due_amount' => 0,
                'total_paid_amount' => 0,
            ];
        }
        $emptyData['Year'] = array_values($emptyData['Year']);
        return $emptyData;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
