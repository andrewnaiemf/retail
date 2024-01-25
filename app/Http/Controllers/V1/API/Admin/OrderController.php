<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOrderRequest;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Http\Request;
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
        $per_page = $request->header('per_page') ?? 10;
        $orders = QueryBuilder::for(Order::class)->with(['orderItems','driver'])
            ->allowedFilters(['status'])
            ->simplePaginate($per_page);

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
        $order = Order::with(['orderItems','driver'])->findOrFail($id);

        return $this->returnData($order);
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
     * @param  \Illuminate\Http\ValidateOrderRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\ValidateOrderRequest
     */
    public function update(ValidateOrderRequest $request, $id)
    {
        $errors = [];
        $order = Order::find($id);
        foreach ($request['line_items'] as $index => $lineItem) {

                $existingOrderItem = $order->orderItems()->where('product_id', $lineItem['product_id'])->first();

            if ($existingOrderItem) {
                $existingOrderItem->update([
                    'quantity' => $lineItem['quantity'],
                ]);

            }else{
                $errors[$index] = sprintf("The selected line_items %d product_id is invalid", $index);
            }

        }

        if ($errors) {
            return $this->returnError(422, $errors);
        }

        return $this->returnSuccessMessage('order updated successfully');
    }

    public function updateStatus(Request $request, $order_id, $status){

        $order = Order::where('status','Draft')->findOrFail($order_id)->first();

        $order->update([
            'status' => $status
        ]);

        return $this->returnSuccessMessage('order ' . $status . ' Successfully');
    }

    public function assignDriver(Request $request , $order_id){

        $request->validate([
            'driver_id' => 'required|exists:users,id',

        ]);

        $driver = Driver::findOrFail($request->driver_id);

        $order = Order::where('status','Draft')->findOrFail($order_id)->first();

        $order->driver()->associate($driver);

        $order->save();

        return $this->returnSuccessMessage('driver attached to order successfully');


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
