<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Allocation;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\LoyaltyPoint;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\ShippingAddress;
use App\Models\UnitType;
use App\Models\User;
use App\Notifications\WhatsappNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FetchingController extends Controller
{
    private $apiKey;
    private $baseUrl;
    const API_VERSION = '2.0';

    public function __construct()
    {
        $this->apiKey = config('app.API_KEY');
        $this->baseUrl = 'https://www.qoyod.com/api/' . self::API_VERSION . '/';
    }


    public function fetchData(Request $request)
    {
        set_time_limit(300);

        $data = $request->input('data');

        try {
            if ($data == 'receipts'){
                $responseData = $this->updateReceipts();
            }else{
                $response = Http::withHeaders([
                    'API-KEY' => $this->apiKey,
                ])->get($this->baseUrl . $data);

                $responseData = json_decode($response->body());

                $this->storeData($data, $responseData->$data);
                Log::info('Successfully fetched data from Qoyod API: ' . $data);
            }
//            $response = Http::withHeaders([
//                'API-KEY' => $this->apiKey,
//            ])->get($this->baseUrl . $data);
//
//            $responseData = json_decode($response->body());
//
//            $this->storeData($data, $responseData->$data);
//            Log::info('Successfully fetched data from Qoyod API: ' . $data);

            return $this->returnData($responseData);

        }
         catch (\Exception $e) {

            Log::error('Error fetching data from Qoyod API: ' . $e->getMessage());

            return $this->returnError( 422,'Failed to fetch data from Qoyod API ' . $e->getMessage());
        }

    }

    public function storeData ($type, $qoyoud_data)
    {
        switch ($type) {
            case 'customers':
                $this->updateOrCreateCustomers($qoyoud_data);
                break;

            case 'categories':
                $this->updateOrCreateCategories($qoyoud_data);
                break;

            case 'product_unit_types':
                $this->updateOrCreateUnitTypes($qoyoud_data);
                break;

            case 'accounts':
                $this->updateOrCreateAccounts($qoyoud_data);
                break;

            case 'inventories':
                $this->updateOrCreateInventoriess($qoyoud_data);
                break;

            case 'products':
                $this->updateOrCreateProducts($qoyoud_data);
                break;

            case 'invoices':
                $this->updateOrCreateInvoices($qoyoud_data);
                break;

            case 'receipts':
                $this->updateOrCreateReceipts($qoyoud_data);
                break;
            default:
                # code...
                break;
        }
    }

    ////////////////////////////// fetch customers ///////////////////////////


    public function updateOrCreateCustomers($qoyoud_data)
    {
        $role = Role::where('name', 'user')->first();

        foreach ($qoyoud_data as $customer_data) {
            $customer_data = (array)$customer_data;

            $customer_id = $customer_data['id'];
            $customer_data['password'] = bcrypt($customer_data['phone_number']);
            $customer = Customer::updateOrCreate(['id' => $customer_id], $customer_data);


            $this->updateOrCreateAddress($customer, 'shippingAddress', $customer_data['shipping_address']);
            $this->updateOrCreateAddress($customer, 'billingAddress', $customer_data['billing_address']);

            $this->attachUserRoleAndPermissions($customer, $role);
        }
    }

    protected function updateOrCreateAddress($customer, $addressType, $address_data)
    {
        $address_data = (array)$address_data;
        if (isset($address_data['id'])) {
            $customer->$addressType()->updateOrCreate(['id' => $address_data['id']], $address_data);
        }
    }

    protected function attachUserRoleAndPermissions($customer, $role)
    {
        if (!$customer->hasRole('user')) {
            $customer->attachRole($role);
            $customer->attachPermissions($role->permissions);
        }
    }


    ////////////////////////////// fetch categories ///////////////////////////


    public function updateOrCreateCategories($qoyoud_data)
    {
        foreach ($qoyoud_data as $category_data) {
            $category_data = (array)$category_data;
            $category = Category::updateOrCreate(['id' => $category_data['id']], $category_data);
        }
    }

    ////////////////////////////// fetch unit types ///////////////////////////


    public function updateOrCreateUnitTypes($qoyoud_data)
    {
        foreach ($qoyoud_data as $unit) {
            $unit_data = (array)$unit;
            $unit = UnitType::updateOrCreate(['id' => $unit_data['id']], $unit_data);
        }
    }


    ////////////////////////////// fetch accounts ///////////////////////////


    public function updateOrCreateAccounts($qoyoud_data)
    {
        foreach ($qoyoud_data as $account) {
            $account_data = (array)$account;
            $account = Account::updateOrCreate(['id' => $account_data['id']], $account_data);
        }
    }

    public function updateOrCreateInventoriess($qoyoud_data)
    {
        foreach ($qoyoud_data as $inventory) {
            $inventory_data = (array)$inventory;
            unset($inventory_data['address']);

            $address = $this->inventoryAddressing($inventory);
            if (isset($address)) {
                $inventory_data['shipping_address_id'] = $address->id;
            }

            $inventory = Inventory::updateOrCreate(['id' => $inventory_data['id']], $inventory_data);
        }
    }

    protected function inventoryAddressing ($inventory)
    {
        if ($inventory->address){
            $address = ShippingAddress::find($inventory->address->id);
            if (!$address) {
                $address_data = (array)$inventory->address;
                $address_data['contact_id'] = $inventory->account_id;
                ShippingAddress::create($address_data);
            }
            return $address;
        }
    }

    ////////////////////////////// fetch products ///////////////////////////


    public function updateOrCreateProducts($qoyoud_data)
    {
        foreach ($qoyoud_data as $product_data) {
            $product_data = (array)$product_data;
            if ($product_data['type'] == 'Product') {
                $product = Product::updateOrCreate(['id' => $product_data['id']], $product_data);
                $this->attachInventory($product, $product_data);
            }
        }
    }

    protected function attachInventory($product, $product_data) {

        if (!empty($product_data['inventories'])) {
            foreach ($product_data['inventories'] as $inventory_data) {

                $inventoryId = $inventory_data->id;
                $stock = $inventory_data->stock;

                $product->inventories()->sync([$inventoryId => ['stock' => (float)$stock]]);
            }
        }
    }

    ////////////////////////////// fetch invoices ///////////////////////////


    public function updateOrCreateInvoices($qoyoud_data)
    {
        $customers_id = Customer::pluck('id')->toArray();

        foreach ($qoyoud_data as $invoice_data) {

            if ($invoice_data->contact_id) {
                if (!in_array($invoice_data->contact_id, $customers_id)) {
                    try {

                        $response = Http::withHeaders([
                            'API-KEY' => $this->apiKey,
                        ])->get($this->baseUrl . 'customers');

                        $responseData = json_decode($response->body());

                        $this->storeData('customers', $responseData->customers);
                        Log::info('Successfully fetched data from Qoyod API: customers');
                    } catch (\Exception $e) {

                        Log::error('Error fetching data from Qoyod API: ' . $e->getMessage());

                        return $this->returnError(422, 'Failed to fetch data from Qoyod API ' . $e->getMessage());
                    }
                }
            }

            try {
                $path = 'invoices/pdf/' . $invoice_data->id . '/invoice.pdf';

                if (!Storage::disk('public')->exists($path)) {

                    $response = Http::withHeaders([
                        'API-KEY' => $this->apiKey,
                    ])->get($this->baseUrl . '/invoices/' . $invoice_data->id . '/pdf');

                    $responseData = json_decode($response->body());

                    $response = Http::get($responseData->pdf_file);
                    $path = 'invoices/pdf/' . $invoice_data->id . '/';

                    if ($response->ok()) {
                        Storage::disk('public')->put($path . 'invoice.pdf', $response->body());
                        Log::info('PDF downloaded and stored successfully.');
                    } else {
                        Log::info('Failed to download PDF.');
                    }

                    $invoice_data->pdf = $path . 'invoice.pdf';

                    Log::info('Successfully fetched invoice pdf from Qoyod API: invoice pdf');
                } else {
                    Log::info('PDF already exists in storage. Skipping download.');
                }

            }
            catch (\Exception $e) {
                Log::error('Error fetching data from Qoyod API: ' . $e->getMessage());
//                return $this->returnError( 422,'Failed to fetch invoice pdf from Qoyod API ' . $e->getMessage());
            }
            $invoice = Invoice::updateOrCreate(['id' => $invoice_data->id], (array)$invoice_data);

            if ($invoice->wasRecentlyCreated)
            {
                $customer  = Customer::findOrFail($invoice->contact_id);
                $message = $message = 'عزيزي {{1}}

تم إصدار فاتورة جديدة رقم {{2}} بقيمة {{3}} ر.س

كما يمكنك الاطلاع على جميع فواتيرك وخدمات أخرى من خلال تطبيق DES.
رابط التطبيق: {{4}}';

                $message = str_replace('{{1}}', $customer->name, $message);
                $message = str_replace('{{2}}', $invoice->reference, $message);
                $message = str_replace('{{3}}', $invoice->total, $message);
                $message = str_replace('{{4}}', 'https://driveshield-d5a37.web.app/', $message);

                $customer_number = $customer->phone_number;
//                if (!$customer_number){
                    WhatsappNotification::sendWhatsAppMessage($message, '+201274696869');
//                }
//                WhatsappNotification::sendWhatsAppMessage($message, $customer_number);
            }

            $this->attachLineItems($invoice, (array)$invoice_data);
        }
    }

    protected function attachLineItems($invoice, $invoice_data) {

        if (!empty($invoice_data['line_items'])) {
            $lineItems = [];

            if ($invoice->lineItems->isEmpty()) {

                foreach ($invoice_data['line_items'] as $item) {

                    $item->discount = $item->discount_amount;
                    $product = Product::find($item->product_id);
                    if ( $product ) {
                        $item = (array)$item;
                        $item['invoice_id'] = $invoice->id;
                        $lineItems[] = new LineItem($item);
                    }else{
                        /////// return error message you should fetch products recently added.
                    }

                }

                $invoice->lineItems()->saveMany($lineItems);
            }else{
                ///////// handle update invoice.
            }

        }
    }


    ////////////////////////////// fetch receipts ///////////////////////////


    protected function updateReceipts(){
        $last_receipt_id = Receipt::orderBy('id', 'desc')->first()->id;

        $start = $last_receipt_id + 1;
        $end = $start + 10;
        $receipts = [];

        for ($i = $start; $i <= $end; $i++) {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
            ])->get($this->baseUrl . 'receipts/'.$i);

            $responseData = json_decode($response->body());
            if ($response->getStatusCode() == 200){
                array_push($receipts, $responseData);
            }
        }

        $this->updateOrCreateReceipts($receipts);
        return $receipts;
    }

    public function updateOrCreateReceipts($qoyoud_data)
    {
        foreach ($qoyoud_data as $receiptse_data) {
            $receiptse_data = (array)$receiptse_data;
            if ($receiptse_data['kind'] === 'received') {

                $account = Account::find($receiptse_data['account_id']);
                if ( $account)
                {
                    $receipt = Receipt::updateOrCreate(['reference' => $receiptse_data['reference']], $receiptse_data);
                    $customer = Customer::find($receipt->contact_id);
                    if ($receipt->wasRecentlyCreated) {
                        $this->addCustomerLoyalty($receipt->amount, $customer);
                        $this->sendWhatsappNotificationMessage($receipt, $customer);
                    }
                    $this->attachAllocates($receipt, $receiptse_data['allocations']);
                }else{
                    /////// return error message you should fetch products recently added
                }
            }
        }
    }

    protected function addCustomerLoyalty($amount, $customer)
    {
        if ($customer->category_id){
            $loyalty  = LoyaltyPoint::where(['customer_type' => $customer->type, 'customer_category_id' => $customer->category_id, 'status'=> 'active'])->first();
            $new_points = intval(($amount * $loyalty->points) / $loyalty->discount_amount);
            $customer->update(['points' => $new_points + $customer->points]);
        }
    }

    protected function sendWhatsappNotificationMessage($receipt, $customer)
    {
        $message = 'عزيزي {{1}}

نشكرك على سداد مبلغ {{2}} ر.س في السند رقم {{3}} بتاريخ {{4}}';

        $message = str_replace('{{1}}', $customer->name, $message);
        $message = str_replace('{{2}}', $receipt->amount, $message);
        $message = str_replace('{{3}}', $receipt->reference, $message);
        $message = str_replace('{{4}}', $receipt->date, $message);
//dd($message);
//        $customer_number = $customer->phone_number;
//                        if (!$customer_number){
        WhatsappNotification::sendWhatsAppMessage($message, '+201274696869');
//                        }
//                        WhatsappNotification::sendWhatsAppMessage($message, $customer_number);
    }

    protected function attachAllocates($receipt, $allocations_data)
    {
        if (!empty($allocations_data)) {

            if (!empty($allocations_data)) {
                if ($receipt->allocates->isEmpty()) {

                    foreach ($allocations_data as $allocation_data) {
                        if (!isset($allocation_data->source_id)){
                            $allocation_data->source_id = $receipt->id;
                        }
                        $existReceipt = Receipt::findOrFail($allocation_data->source_id);
                        $existInvoice = Invoice::findOrFail($allocation_data->allocatee_id);

                        if ($existReceipt && $existInvoice) {
                            $allocation_data = (array)$allocation_data;
                            Allocation::create($allocation_data);
                        }else{
                            /////// return error message you should fetch products recently added.
                        }
                    }

                }else{
                    ///////// handle update receipt.
                }
            }

        }
    }

}
