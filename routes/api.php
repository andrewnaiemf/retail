<?php

use App\Http\Controllers\V1\API\Admin\AuthController;
use App\Http\Controllers\V1\API\Admin\CustomerController;
use App\Http\Controllers\V1\API\Admin\FetchingController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'admin'], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('admin.login');
    Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('admin.forgetPassword');

    Route::group(['middleware' => ['role:superadministrator', 'auth:api']], function () {
        Route::get('me', [AuthController::class, 'me'])->name('admin.data');
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('fetch', [FetchingController::class, 'fetchData'])->name('fetch');

        Route::resource('customers', CustomerController::class);

    });

});
