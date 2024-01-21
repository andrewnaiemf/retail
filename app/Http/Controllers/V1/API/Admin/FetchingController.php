<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Role;
use App\Models\ShippingAddress;
use App\Models\UnitType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchingController extends Controller
{
    private $apiKey;
    private $baseUrl;
    const API_VERSION = '2.0';

    public function __construct()
    {
        $this->apiKey = env('API_KEY', '4710f93567073fb98566ffafc');
        $this->baseUrl = 'https://www.qoyod.com/api/' . self::API_VERSION . '/';
    }


    public function fetchData(Request $request)
    {
        set_time_limit(300);

        $data = $request->input('data');

        try {

            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
            ])->get($this->baseUrl . $data);

            $responseData = json_decode($response->body());

            $this->storeData($data, $responseData->$data);
            Log::info('Successfully fetched data from Qoyod API: ' . $data);

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
            unset($customer_data['id']);

            $customer = User::updateOrCreate(['id' => $customer_id], $customer_data);


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
            $product = Product::updateOrCreate(['id' => $product_data['id']], $product_data);

            $this->attachInventory($product, $product_data);
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

}
