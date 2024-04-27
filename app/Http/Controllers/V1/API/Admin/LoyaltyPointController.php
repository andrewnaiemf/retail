<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoyaltyPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loyalties = LoyaltyPoint::with('customerCategory')->get();

        return  $this->returnData($loyalties);

    }

    public function customerCategory()
    {
        $categories = CustomerCategory::all();

        return  $this->returnData($categories);
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
        $validator = Validator::make($request->all(), [
            'customer_category_id' => 'required|exists:customer_categories,id',
        ]);
        if ($validator->fails()) {
            return $this->returnValidationError(401, $validator->errors()->all());
        }

        $loyalty = LoyaltyPoint::where(['customer_type' => $request['customer_type'], 'customer_category_id' => $request['customer_category_id']])->first();

        if ($loyalty) {
            $loyalty->update($request->all());
            $message = 'loyalty point updated successfully';
        } else {
            LoyaltyPoint::create($request->all());
            $message = 'loyalty point created successfully';

        }

        return  $this->returnSuccessMessage($message);
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
