<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsForCustomerRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Filter\FiltersProductByCustomer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->header('per_page') ?? 10;

        $customer = auth('customer')->user();
        $query = QueryBuilder::for(Product::class)->with('customers','tax')->whereHas('customers', function ($q) use ($customer){

                $q->where('customer_product.customer_id', $customer->id);
        });

        if (isset($request['filter']['name']) && $request['filter']['name']) {
            $query->allowedFilters(
                AllowedFilter::custom('name', new FiltersProductByCustomer($customer->id))
            );
        }

        $products = $query->paginate($per_page);

        return $this->returnData($products);
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
        $customer = auth('customer')->user();

        $product = Product::where('id', $id)->whereHas('customers', function ($q) use ($customer) {
            $q->where('customer_product.customer_id', $customer->id);
        })->with(['category','customers' => function ($q) {
            $q->select('customer_product.price');
        }])->first();

        if (! $product) {
            return $this->returnError(422, 'invalid product id');
        }
        return $this->returnData($product);
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
     * @param  \Illuminate\Http\StoreProductsForCustomerRequest  $request
     * @return \Illuminate\Http\StoreProductsForCustomerRequest
     */
    public function storeProductsForCustomer(StoreProductsForCustomerRequest $request)
    {
        $customer = Customer::whereRoleIs('user')->find($request->customer);

        if ( !$customer) {
            return $this->returnError(422, 'invalid customer id');
        }

        foreach ($request->products as $productData) {

            $productId = $productData['id'];
            $price = $productData['price'];

            $product = Product::find($productId);

            if ($product && is_numeric($price)) {
                $pivotData = ['price' => $price];

                // Check if the relationship exists
                if (!$customer->products->contains($productId)) {
                    $pivotData['created_at'] = now();
                }

                // Always update updated_at
                $pivotData['updated_at'] = now();

                $customer->products()->sync([$productId => $pivotData], false);

            }
        }

        return $this->returnSuccessMessage('Products attached to customer successfully');
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
