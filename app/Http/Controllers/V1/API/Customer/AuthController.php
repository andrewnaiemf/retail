<?php

namespace App\Http\Controllers\V1\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|exists:users,phone_number',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(401, $validator->errors()->all());
        }

        $customer = Customer::where('phone_number', $request->phone_number)->first();
        if ($customer->password) {
            return $this->returnData(['is_verified' => true]);
        }else{
            return $this->returnData(['is_verified' => false]);
        }

    }
    /**
     * Admin login.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginAdminRequest $request)
    {
        $valid_data = $request->validated();

        if(!Auth::guard('customer')->attempt($valid_data)) {
            return $this->unauthorized();
        }

        $user = Customer::where('phone_number', $request->phone_number)->whereRoleIs('user')->with(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->first();

        if (!$user) {
            return $this->unauthorized();
        }

        $token = JWTAuth::fromUser($user);
        $user->update(['locale' => $request->locale, 'is_android' => $request->is_android ?? '0']);

        $this->device_token($request->device_token, $user);

        return $this->returnData(['user' => $user, 'token' => $token], 'LogedIn successfully');
    }

    private function device_token($device_token, $user)
    {

        if(!isset($user->device_token)) {
            $user->update(['device_token' => json_encode($device_token)]);
        } else {
            $devices_token = $user->device_token;
            if(!in_array($device_token, $devices_token)) {
                array_push($devices_token, $device_token);
                $user->update(['device_token' => json_encode($devices_token)]);
            }
        }
    }

    public function logout()
    {

        Auth::guard('customer')->logout();

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
        $user = Customer::where('phone_number', $request->phone_number)->first();

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
        $user = Customer::where( 'id', auth()->user()->id)->whereRoleIs('user')->with(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
            $query->with(['orderItems', 'customer', 'driver']);
        }])->first();

        if (auth()->check() && $user) {
            return $this->returnData(['user' => $user]);

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
