<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Filter\FiltersOrders;
use App\Filter\FiltersOrdersByCustomer;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOrderRequest;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->headers->get('per-page') ?? 10;
        $customer = auth('customer')->user();

        $query = QueryBuilder::for(Order::class)->whereHas('customer', function ($q) use ($customer)
        {
            $q->where('id', $customer->id);
        })->with(['orderItems.product', 'driver', 'customer'])->latest();;

        if (isset($request['filter']['status']) && $request['filter']['status']) {
           $query->allowedFilters(
                AllowedFilter::custom('status', new FiltersOrdersByCustomer($customer->id))
            );
        }
        $orders =  $query->simplePaginate($per_page);

        return $this->returnData($orders);
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
    public function store(ValidateOrderRequest $request)
    {
        $orderData = $request->input('order');
        $customer = Customer::findOrFail($orderData['branch_id']);
        $lineItems = $orderData['line_items'];
        $modifiedLineItems = $this->modifyLineItems($customer, $lineItems);

        $reference = $this->generateOrderReference();
        $order = $this->createOrder($customer, $reference, $request);

        $this->createOrderItems($order, $modifiedLineItems);
//        $this->reduceStock($modifiedLineItems);

        //TODO Notify whatsapp message to owner that he has new order from customer name.
        return response()->json(['message' => 'Order created successfully']);
    }

    public function reduceStock($items)
    {
        foreach ($items as $product) {
            $productId = $product['product_id'];
            $quantity = $product['quantity'];
            $inventory = Inventory::find(1);
            // Retrieve the inventory record for the product
            $productInInventory = $inventory->products()->where('product_id', $productId)->first();

            if ($productInInventory) {
                // Ensure that the available quantity is greater than or equal to the quantity to be deducted
                if ($productInInventory->pivot->stock>= $quantity) {
                    // Deduct the quantity from the available stock
                    $productInInventory->pivot->stock -= $quantity;
                    $productInInventory->pivot->update(['stock'=> $productInInventory->pivot->stock]);
                } else {
                    // Handle the case where the available stock is insufficient
                    // You may throw an exception, log a message, or handle it based on your application's logic
                    // For example:
                    // throw new Exception("Insufficient stock for product ID: $productId");
                    // Log::error("Insufficient stock for product ID: $productId");
                }
            } else {
                // Handle the case where the inventory record does not exist
                // You may throw an exception, log a message, or handle it based on your application's logic
                // For example:
                // throw new Exception("Inventory record not found for product ID: $productId");
                // Log::error("Inventory record not found for product ID: $productId");
            }
        }
    }

    private function modifyLineItems($customer, $lineItems)
    {
        $modifiedLineItems = [];

        if (!empty($customer->products)) {
            foreach ($lineItems as $lineItem) {

                $product = $customer->products->where('id', $lineItem['product_id'])->first();
                $unitPrice = $product->pivot->price;

                $modifiedLineItem = $lineItem;
                $modifiedLineItem['unit_price'] = $unitPrice;
                $modifiedLineItem['tax_percent'] = 15;
                $modifiedLineItems[] = $modifiedLineItem;
            }
        }else{
                //handle not exist product to this customer
        }

        return $modifiedLineItems;
    }

    private function generateOrderReference()
    {
        $lastOrderId = Order::max('id') ?? 0;
        return 'order' . ($lastOrderId + 1);
    }

    private function createOrder($customer, $reference, $request)
    {
        return Order::create([
            'customer_id' => $customer->id,
            'inventory_id' => 1,
            'reference' => $reference,
            'status' => 'Draft',
            'notes' => $request->notes,
            'location' => $request->location,
            'created_at' => now(),
        ]);
    }

    private function createOrderItems($order, $modifiedLineItems)
    {
        $order->orderItems()->createMany($modifiedLineItems);
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
