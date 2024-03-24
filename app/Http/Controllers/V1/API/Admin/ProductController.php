<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsForCustomerRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Filter\FiltersProduct as FiltersProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        $per_page = $request->headers->get('per-page') ?? 10;

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(
                AllowedFilter::custom('name', new FiltersProduct)
            )->with(['category'])
            ->paginate($per_page);

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

//        foreach ($request->products as $productData) {

            $productId = $request['id'];
            $price = $request['price'];

            $product = Product::find($productId);

            if ($product && is_numeric($price)) {
                $pivotData = ['price' => $price];

                // Check if the relationship exists
                if (!$customer->products->contains($productId)) {
                    $pivotData['created_at'] = now();
                    $customer->products()->attach([$productId => $pivotData]);
                }else{
                    $pivotData['updated_at'] = now();
                    $pivotData['deleted_at'] = null;
                     $customer->products()->sync([$productId => $pivotData], false);
                }
//            }
        }

        return $this->returnSuccessMessage('Products attached to customer successfully');
    }


    public function detachProductFromCustomer($customerId, $productId)
    {
        $customer = Customer::findOrFail($customerId);
        $customer = Product::findOrFail($productId);

        \DB::table('customer_product')
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->update(['deleted_at' => now()]);

        return $this->returnSuccessMessage('Product detached from customer successfully');
    }

    public function attachPicture(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'picture' => 'required|file|mimes:jpeg,png,jpg,gif|max:20048'
        ]);
        if ($validator->fails()) {
            return $this->returnValidationError(401, $validator->errors()->all());
        }        $product = Product::findOrFail($id);
        $picture = $request['picture'] ?? null;

        $path = 'products/' . $product->id . '/';

        // Check if the product already has a picture
        if ($product->picture) {
            // If yes, delete the existing picture from storage
            Storage::delete('public/' . $product->picture);
        }

        // Store the new picture
        $imageName = $picture->hashName();
        $picture->storeAs('public/' . $path, $imageName);
        $fullPath = $path . $imageName;

        // Update the product record with the new picture path
        $product->picture = $fullPath;
        $product->save();
        return $this->returnSuccessMessage('Product picture uploaded successfully');

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
        $product = Product::findOrFail($id);

        // Delete associated picture if exists
        if ($product->picture) {
            Storage::delete('public/' . $product->picture);
        }

        // Detach the product from all customers
        $product->customers()->detach();

        return $this->returnSuccessMessage('Product deleted successfully');
    }
}
