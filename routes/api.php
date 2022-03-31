<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
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

Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [\App\Http\Controllers\AuthController::class, 'user']);
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});

Route::controller(InvoiceController::class)->group(function () {
    Route::get('/invoices', 'index');
    Route::get('/newInvoice', 'create');
    Route::post('/invoice', 'store');
    Route::get('/invoice/{id}', 'show');
    Route::get('/editInvoice/{id}', 'edit');
    Route::put('/invoice/{id}', 'update');
    Route::delete('/invoice/{id}', 'destroy');
});
