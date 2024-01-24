<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsForCustomerRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Filter\FiltersProduct as FiltersProduct;
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

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(
                AllowedFilter::custom('name', new FiltersProduct)
            )->with(['category'])
            ->simplePaginate($per_page);

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
        $product = Product::with(['category'])->findOrFail($id);

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
