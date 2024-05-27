<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{

    private function generateHash($invoiceId) {
        return hash('sha256', $invoiceId . config('app.INVOICE_SECRET_KEY'));
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
        $invoice = Invoice::where('contact_id', $customer->id)
        ->with(['payments', 'inventory', 'contact', 'lineItems.product'])
        ->orderBy('id', 'desc')
        ->simplePaginate($per_page);

        return $this->returnData($invoice);
    }

    public function serveInvoice($id, $hash)
    {
        $expectedHash = $this->generateHash($id);

        if ($hash !== $expectedHash) {
            return response('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $filePath = "invoices/pdf/{$id}/invoice.pdf";

        if (!Storage::disk('public')->exists($filePath)) {dd('a');
            return response('File not found', Response::HTTP_NOT_FOUND);
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }

    public function generateInvoiceUrl($invoiceId)
    {
        $hash = $this->generateHash($invoiceId);
        return URL::to("/invoices/{$invoiceId}/{$hash}");
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
        $customer = auth('customer')->user();
        $invoice = Invoice::whereHas('contact', function ($q) use ($customer) {
            $q->where('invoices.contact_id', $customer->id);
        })->with(['payments', 'inventory', 'contact', 'lineItems.product'])->findOrFail($id);

        return $this->returnData($invoice);
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
