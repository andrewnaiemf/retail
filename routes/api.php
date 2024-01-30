<?php

use App\Http\Controllers\V1\API\Admin\AuthController;
use App\Http\Controllers\V1\API\Admin\CustomerController;
use App\Http\Controllers\V1\API\Admin\DriverController;
use App\Http\Controllers\V1\API\Admin\FetchingController;
use App\Http\Controllers\V1\API\Admin\InvoiceController;
use App\Http\Controllers\V1\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\V1\API\Admin\ProductController;
use App\Http\Controllers\V1\API\Admin\ReceiptController;
use App\Http\Controllers\V1\API\Customer\AccountStatementController;
use App\Http\Controllers\V1\API\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\V1\API\Customer\CustomerController as CustomerCustomerController;
use App\Http\Controllers\V1\API\Customer\InvoiceController as CustomerInvoiceController;
use App\Http\Controllers\V1\API\Customer\OrderController;
use App\Http\Controllers\V1\API\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\V1\API\Customer\ReceiptController as CustomerReceiptController;
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

    Route::group(['middleware' => ['role:superadministrator|administrator', 'auth:api']], function () {
        Route::get('me', [AuthController::class, 'me'])->name('admin.data');
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('fetch', [FetchingController::class, 'fetchData'])->name('fetch');

        Route::resource('customers', CustomerController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::resource('receipts', ReceiptController::class);

        Route::put('customer/products', [ProductController::class, 'storeProductsForCustomer']);
        Route::resource('products', ProductController::class);

        Route::resource('drivers', DriverController::class);

        Route::resource('orders', AdminOrderController::class);
        Route::get('orders/{order_id}/{status}', [AdminOrderController::class, 'updateStatus']);
        Route::post('orders/{order_id}/assign-driver', [AdminOrderController::class ,'assignDriver']);


    });

});

Route::group(['prefix' => 'user'], function () {
    Route::post('login', [CustomerAuthController::class, 'login'])->name('customer.login');
    Route::post('forget-password', [CustomerAuthController::class, 'forgetPassword'])->name('customer.forgetPassword');

    Route::group(['middleware' => ['role:user', 'auth:customer']], function () {



        Route::get('me', [CustomerAuthController::class, 'me'])->name('customer.data');
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');


        Route::resource('customers', CustomerCustomerController::class)->except(['show'])->names([
            'index' => 'customer.customers.index',
            'create' => 'customer.customers.create',
            'store' => 'customer.customers.store',
            'edit' => 'customer.customers.edit',
            'update' => 'customer.customers.update',
            'destroy' => 'customer.customers.destroy',
        ]);

        Route::resource('orders', OrderController::class)->except(['show'])->names([
            'index' => 'customer.orders.index',
            'create' => 'customer.orders.create',
            'store' => 'customer.orders.store',
            'edit' => 'customer.orders.edit',
            'update' => 'customer.orders.update',
            'destroy' => 'customer.orders.destroy',
        ]);

        Route::resource('products', CustomerProductController::class)->names([
            'index' => 'customer.products.index',
            'show' => 'customer.products.show',
            'create' => 'customer.products.create',
            'store' => 'customer.products.store',
            'edit' => 'customer.products.edit',
            'update' => 'customer.products.update',
            'destroy' => 'customer.products.destroy',
        ]);


        Route::resource('invoices', CustomerInvoiceController::class)->names([
            'index' => 'customer.invoices.index',
            'show' => 'customer.invoices.show',
            'create' => 'customer.invoices.create',
            'store' => 'customer.invoices.store',
            'edit' => 'customer.invoices.edit',
            'update' => 'customer.invoices.update',
            'destroy' => 'customer.invoices.destroy',
        ]);

        Route::resource('receipts', CustomerReceiptController::class)->names([
            'index' => 'customer.receipts.index',
            'show' => 'customer.receipts.show',
            'create' => 'customer.receipts.create',
            'store' => 'customer.receipts.store',
            'edit' => 'customer.receipts.edit',
            'update' => 'customer.receipts.update',
            'destroy' => 'customer.receipts.destroy',
        ]);

        Route::get('account-statement', [AccountStatementController::class, 'index']);


    });

});
