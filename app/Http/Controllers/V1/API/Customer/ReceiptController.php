<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    private function generateHash($receiptId) {
        return hash('sha256', $receiptId . config('app.INVOICE_SECRET_KEY'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->headers->get('per-page') ?? 10;
        $customer = auth('customer')->user();
        $receipts = Receipt::where('contact_id', $customer->id)
            ->with(['contact', 'account', 'allocates.allocatee'])
            ->orderBy('id', 'desc')
            ->simplePaginate($per_page);

        return $this->returnData($receipts);
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
        public function serveReceipt($id, $hash)
    {
        $expectedHash = $this->generateHash($id);

        if ($hash !== $expectedHash) {
            return response('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $filePath = "receipts/pdf/{$id}/receipt.pdf";

        if (!Storage::disk('public')->exists($filePath)) {
            return response('File not found', Response::HTTP_NOT_FOUND);
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }

    public function generateReceiptUrl($receiptId)
    {
        $hash = $this->generateHash($receiptId);
        return URL::to("/receipts/{$receiptId}/{$hash}");
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
        $receipt = Receipt::with(['contact', 'account', 'allocates.allocatee'])->findOrFail($id);

        return $this->returnData($receipt);
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
