<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        $orderData = $request->input('order');
        $customer = auth('customer')->user();
        $lineItems = $orderData['line_items'];
        $modifiedLineItems = [];

        foreach ($lineItems as $lineItem) {
            $product = $customer->products->where('id', $lineItem['product_id'])->first();
            $unit_price = $product->pivot->price;
            // Add the modified line item to the array
            $modifiedLineItem = $lineItem;
            $modifiedLineItem['unit_price'] = $unit_price;
            $modifiedLineItem['tax_percent'] = 15;
            $modifiedLineItems[] = $modifiedLineItem;
        }
        $last_order_id = 1;
        $last_order = Order::orderBy('id','desc')->first();
        if ($last_order) {
            $last_order_id = $last_order->id + 1 ;
        }

        $reference = 'order' . $last_order_id;

        $order = Order::create([
            'customer_id' => $customer->id,
            'inventory_id' => 1,
            'reference' => $reference,
            'status' => 'Draft',
            'notes' => $request->notes,
            'created_at' =>now()
        ]);


        $order->orderItems()->createMany($modifiedLineItems);

        return response()->json(['message' => 'Order created successfully']);
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
