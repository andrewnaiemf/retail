<?php

namespace App\Http\Controllers\V1\API\Admin;

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
        //
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
