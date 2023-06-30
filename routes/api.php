<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Auth\ZaloAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Business\OrderController;
use App\Http\Controllers\Api\Master\WarehouseController;
use App\Http\Controllers\Api\Master\DeliveryPartnerController;
use App\Http\Controllers\Api\Master\CompanyController;
use App\Http\Controllers\Api\Master\CustomerController;
use App\Http\Controllers\Api\Master\EmployeeController;

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

    //api Master
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
    Route::prefix('/partners')->group(function () {
        Route::get('/', [DeliveryPartnerController::class, 'getAvailablePartners']);
        Route::post('/', [DeliveryPartnerController::class, 'createNewPartner']);
        Route::put('/{id}', [DeliveryPartnerController::class, 'updateExistingPartner']);
        Route::delete('/{id}', [DeliveryPartnerController::class, 'deleteExistingPartner']);
    });
    Route::prefix('/companies')->group(function () {
        Route::get('/', [CompanyController::class, 'getAvailableCompanies']);
        Route::post('/', [CompanyController::class, 'createNewCompany']);
        Route::put('/{id}', [CompanyController::class, 'updateExistingCompany']);
        Route::delete('/{id}', [CompanyController::class, 'deleteExistingCompany']);
    });
    Route::prefix('/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'getAvailableCustomers']);
        Route::post('/', [CustomerController::class, 'createNewCustomer']);
        Route::put('/{id}', [CustomerController::class, 'updateExistingCustomer']);
        Route::delete('/{id}', [CustomerController::class, 'deleteExistingCustomer']);
    });
    Route::prefix('/employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'getAvailableEmployees']);
        Route::post('/', [EmployeeController::class, 'createNewEmployee']);
        Route::put('/{id}', [EmployeeController::class, 'updateExistingEmployee']);
        Route::delete('/{id}', [EmployeeController::class, 'deleteExistingEmployee']);
    });
});

//api 
Route::prefix('auth')->group(function () {
    Route::post('/zalo/exist_user', [ZaloAuthController::class, 'checkExistingUser']);
    Route::post('/zalo/login', [ZaloAuthController::class, 'login']);
    Route::post('/user/login', [UserAuthController::class, 'login']);
});

