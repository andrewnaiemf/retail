<?php

namespace App\Http\Controllers\V1\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use App\Models\DriverPassword;
use App\Models\Role;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->header('per_page') ?? 10;

        $drivers = Driver::whereRoleIs('driver')->paginate($per_page);

        return $this->returnData($drivers);
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
     * @param  \Illuminate\Http\DriverRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverRequest $request)
    {
        $role = Role::where('name', 'driver')->first();
        $password =  $request['password'];
        $request['password'] = bcrypt($password);

        $driver = Driver::updateOrCreate(['phone_number' => $request['phone_number']], $request->all());

        DriverPassword::updateOrCreate(['driver_id' => $request['driver_id']],['driver_id' => $driver->id,'password' => $password]);

        $driver->attachRole($role);

        $driver->attachPermissions($role->permissions);

        return $this->returnSuccessMessage('driver stored successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $driver = Driver::whereRoleIs('driver')->findOrFail($id);

        return $this->returnData($driver);
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
