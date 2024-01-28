<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('orderItems')->get();
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
        $customer = auth('customer')->user();
        $lineItems = $orderData['line_items'];
        $modifiedLineItems = $this->modifyLineItems($customer, $lineItems);

        $reference = $this->generateOrderReference();
        $order = $this->createOrder($customer, $reference, $request);

        $this->createOrderItems($order, $modifiedLineItems);

        return response()->json(['message' => 'Order created successfully']);
    }

    private function modifyLineItems($customer, $lineItems)
    {
        $modifiedLineItems = [];

        foreach ($lineItems as $lineItem) {
            $product = $customer->products->where('id', $lineItem['product_id'])->first();
            $unitPrice = $product->pivot->price;

            $modifiedLineItem = $lineItem;
            $modifiedLineItem['unit_price'] = $unitPrice;
            $modifiedLineItem['tax_percent'] = 15;
            $modifiedLineItems[] = $modifiedLineItem;
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
