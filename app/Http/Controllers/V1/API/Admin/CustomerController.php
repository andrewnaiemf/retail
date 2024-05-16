<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->headers->get('per-page') ?? 10;
        $customers = QueryBuilder::for(Customer::class)->with(['billingAddress','shippingAddress'])
            ->allowedFilters(['name', 'status'])->whereRoleIs('user')
            ->paginate($per_page);

        return $this->returnData($customers);
    }

    public  function categories()
    {
        $categories = CustomerCategory::all();
        return $this->returnData($categories);

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
        $customer = Customer::with(['products' => function ($query) {
            $query->whereNull('customer_product.deleted_at');
        }])->whereRoleIs('user')->find($id);

        if (!$customer) {
            return $this->returnError(422, 'invalid customer id');
        }

        $invoices = $customer->invoices()->orderBy('id', 'desc')->paginate(10);
        $receipts = $customer->receipts()->orderBy('id', 'desc')->paginate(10);


        return $this->returnData(['customer' => $customer,'invoices' => $invoices,'receipts' => $receipts]);
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

        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:customer_categories,id',
        ]);
        if ($validator->fails()) {
            return $this->returnValidationError(401, $validator->errors()->all());
        }
        $customer = Customer::find($id);
        $customer->update($request->all());

        return $this->returnSuccessMessage("customer updated successfully");
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
