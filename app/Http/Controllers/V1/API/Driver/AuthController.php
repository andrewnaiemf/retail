<?php

namespace App\Http\Controllers\V1\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Admin login.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginAdminRequest $request)
    {
        $valid_data = $request->validated();

        if(!Auth::guard('driver')->attempt($valid_data)) {
            return $this->unauthorized();
        }

        $driver = Driver::where('phone_number', $request->phone_number)
            ->whereRoleIs('driver')
            ->with(['orders' => function ($query) {
                $query->where('status', 'Approved')
                ->where('shipping_status' ,'!=', 'Delivered')
                ->orWhereNull('shipping_status')
                ->orderBy('created_at', 'desc');
            }])->first();

        if (!$driver) {
            return $this->unauthorized();
        }

        $token = JWTAuth::fromUser($driver);

        return $this->returnData(['driver' => $driver, 'token' => $token], 'LogedIn successfully');
    }

    public function logout()
    {

        Auth::guard('driver')->logout();

        return $this->returnSuccessMessage('Successfully logged out');
    }


    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|exists:users,phone_number',
            'password' => 'required|confirmed|string|min:6',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(401, $validator->errors()->all());
        }
        $user = Driver::where('phone_number', $request->phone_number)->first();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->returnSuccessMessage(trans("api.Password_updated_successfully"));
    }

    /**
     * Display the current resource.
     *
     */
    public function me()
    {
        $driver = Driver::where( 'id', auth()->user()->id)->whereRoleIs('driver')
            ->with(['orders' => function ($query) {
                $query->where('status', 'Approved')
                ->where('shipping_status' ,'!=', 'Delivered')
                ->orWhereNull('shipping_status')
                ->orderBy('created_at', 'desc');
            }])->first();

        if (auth()->check() && $driver) {
            return $this->returnData(['driver' => $driver]);
        } else {
            return $this->unauthorized();
        }
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
