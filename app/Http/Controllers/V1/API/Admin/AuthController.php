<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if(!Auth::attempt($valid_data)) {
            return $this->unauthorized();
        }

        $user = User::where('email', $request->email)->first();
        $token = JWTAuth::fromUser($user);
        if (!$user->hasRole('superadministrator')) {
            return $this->unauthorized();
        }

        return $this->returnData(['user' => $user, 'token' => $token], 'LogedIn successfully');
    }

    public function logout()
    {
        Auth::logout();

        return $this->returnSuccessMessage('Successfully logged out');
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
