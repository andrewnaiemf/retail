<?php

namespace App\Http\Controllers\V1\API\Driver;

use App\Filter\FiltersDriverOrders;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\ValidateOrderRequest;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Notifications\PushNotification;
use App\Notifications\WhatsappNotification;
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
        $orders = QueryBuilder::for(Order::class)->with(['orderItems.product','customer','inventory'])
            ->allowedFilters(
                AllowedFilter::custom('status', new FiltersDriverOrders)
            )->orderBy('id', 'desc')
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
     * @param  \Illuminate\Http\UpdateOrderStatusRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(UpdateOrderStatusRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $customer  = Customer::findOrFail($order->customer_id);
        $confirmation_image = request()->file('confirmation_image');
        $path = 'orders/' . $id . '/';

        if ($confirmation_image)
        {
            if ($order->shipping_status != 'Delivered') {
                return $this->returnError(422, 'can not upload order confirmation image before order delivered');
            }

            $imageName = $confirmation_image->hashName();
            $confirmation_image->storeAs('public/'.$path, $imageName);
            $fullPath = $path . $imageName;

            $order->update([
                'confirmation_image' => $fullPath
            ]);
        }

        $sender_id = auth()->user()->id;
        if ($request->status){
            if ( $request->status == 'Delivered') {
                $message = "لقد وصل الطلب الخاص بك";
                $customer_number = $customer->phone_number;
                if (!$customer_number){
                    WhatsappNotification::sendWhatsAppMessage($message, '+201274696869');
                }
                WhatsappNotification::sendWhatsAppMessage($message, $customer_number);
            }
            $order->update(['shipping_status' => $request->status]);

            PushNotification::send($sender_id, $order->customer_id, $order, $request->status);
        }

        if ($request->driver){
            $this->reassignDriver($request->driver, $order->id);
        }

        return $this->returnSuccessMessage('Order updated successfully');
    }

    protected function reassignDriver($driver_id, $order_id)
    {
        $driver = Driver::findOrFail($driver_id);

        $order = Order::where(['status' => 'Approved', 'id' => $order_id])->first();

        if (!isset($order)) {
            return $this->returnError(422, 'Invalid data');
        }

        $order->driver()->associate($driver);

        $order->save();
        $sender_id = auth()->user()->id;
        PushNotification::send($sender_id, $driver->id, $order, 'reassignToDriver');
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
