<?php

use App\Http\Controllers\Api\Auth\ZaloController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//api order
Route::get('order', [OrderController::class, 'index']);
Route::post('order_store', [OrderController::class, 'store']);
Route::put('order_put/{id}', [OrderController::class, 'update']);
Route::delete('order_delete/{id}', [OrderController::class, 'destroy']);

//api 

Route::prefix('auth')->group(function () {
    Route::post('/zalo/exist_user', [ZaloController::class, 'existUser']);
    Route::post('/zalo/login', [ZaloController::class, 'login']);
});  