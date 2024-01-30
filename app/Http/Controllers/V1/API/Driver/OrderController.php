<?php

namespace App\Http\Controllers\V1\API\Driver;

use App\Filter\FiltersDriverOrders;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\ValidateOrderRequest;
use App\Models\Order;
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
        $per_page = $request->header('per_page') ?? 10;
        $orders = QueryBuilder::for(Order::class)->with(['orderItems','customer'])
            ->allowedFilters(
                AllowedFilter::custom('status', new FiltersDriverOrders)
            )
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

        $order->update(['shipping_status' => $request->status]);

        return $this->returnSuccessMessage('Order status updated successfully');
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
