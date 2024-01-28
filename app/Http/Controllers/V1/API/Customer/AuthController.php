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

        return $this->returnData(['user' => $user, 'token' => $token], 'LogedIn successfully');
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
        if (auth()->check()) {
            return $this->returnData(['user' => auth()->user()]);
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
