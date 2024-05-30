<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('invoices/{id}/{hash}', [\App\Http\Controllers\V1\API\Customer\InvoiceController::class, 'serveInvoice']);
Route::get('receipts/{id}/{hash}', [\App\Http\Controllers\V1\API\Customer\ReceiptController::class, 'serveReceipt']);
