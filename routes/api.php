<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Auth\ZaloAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Business\OrderController;
use App\Http\Controllers\Api\Master\WarehouseController;
use App\Http\Controllers\Api\Master\DeliveryPartnerController;

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

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    //api order
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'getAvailableOrders']);
        Route::post('/manual-create', [OrderController::class, 'store']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
    });

    Route::prefix('/warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'getAvailableWarehouses']);
        Route::post('/', [WarehouseController::class, 'createNewWarehouse']);
        Route::patch('/{id}', [WarehouseController::class, 'updateExistingWarehouse']);
        Route::delete('/{id}', [WarehouseController::class, 'deleteExistingWarehouse']);
    });
    Route::prefix('partners')->group(function () {
        Route::get('/', [DeliveryPartnerController::class, 'getAvailablePartners']);
        Route::post('/', [DeliveryPartnerController::class, 'createNewPartner']);
        Route::put('/{id}', [DeliveryPartnerController::class, 'updateExistingPartner']);
        Route::delete('/{id}', [DeliveryPartnerController::class, 'deleteExistingPartner']);
    });
});

//api 
Route::prefix('auth')->group(function () {
    Route::post('/zalo/exist_user', [ZaloAuthController::class, 'checkExistingUser']);
    Route::post('/zalo/login', [ZaloAuthController::class, 'login']);
    Route::post('/user/login', [UserAuthController::class, 'login']);
});

