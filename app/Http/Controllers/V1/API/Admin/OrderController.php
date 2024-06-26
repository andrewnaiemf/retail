<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Filter\FiltersOrders;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOrderRequest;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\Order;
use App\Notifications\PushNotification;
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
        $per_page = $request->headers->get('per-page') ?? 10000;
        $orders = QueryBuilder::for(Order::class)->with(['orderItems.product','driver','customer'])
            ->allowedFilters(
                AllowedFilter::custom('status', new FiltersOrders)
            )
            ->orderBy('id', 'desc')
            ->paginate($per_page);

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
        $order = Order::with(['orderItems.product','driver','customer'])->findOrFail($id);

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
    public function update(Request $request, $id)
    {
        $errors = [];
        $order = Order::find($id);
        foreach ($request['line_items'] as $index => $lineItem) {

            $existingOrderItem = $order->orderItems()->where('product_id', $lineItem['product_id'])->first();
            if ($lineItem['quantity'] > 0){
                if ($existingOrderItem) {
                    $existingOrderItem->update([
                        'quantity' => $lineItem['quantity'],
                    ]);
                }else{
                    $lineItem ['tax_percent'] = 15;
                    $order->orderItems()->create($lineItem);
//                    $errors[$index] = sprintf("The selected line_items %d product_id is invalid", $index);
                }
            }else{
                $existingOrderItem->delete();
            }
        }

        if ($errors) {
            return $this->returnError(422, $errors);
        }

        return $this->returnSuccessMessage('order updated successfully');
    }

    public function updateStatus(Request $request, $order_id, $status){

        $order = Order::findOrFail($order_id);
        if ($order->status == 'Draft'){
            $order->update([
                'status' => $status
            ]);
            if ($request->status == 'Approved' && $order->loyalty_points > 0){
                $customer = $order->customer;
                $customer->update(['points' => $customer->points - $order->loyalty_points]);
            }
            $sender_id = auth()->user()->id;
            PushNotification::send($sender_id, $order->customer_id, $order, $status);
        }

        return $this->returnSuccessMessage('order ' . $status . ' Successfully');
    }

    public function assignDriver(Request $request , $order_id){

        $request->validate([
            'driver_id' => 'required|exists:users,id',

        ]);

        $driver = Driver::findOrFail($request->driver_id);

        $order = Order::where(['status' => 'Approved', 'id' => $order_id])->first();

        if (!isset($order)) {
            return $this->returnError(422, 'Invalid data');
        }

        $order->driver()->associate($driver);

        $order->save();
        $sender_id = auth()->user()->id;
        PushNotification::send($sender_id, $driver->id, $order, 'assignToDriver');

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
