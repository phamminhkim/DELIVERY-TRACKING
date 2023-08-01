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
use App\Http\Controllers\Api\Master\CustomerPhoneController;
use App\Http\Controllers\Api\Master\EmployeeController;
use App\Http\Controllers\Api\Master\DistributionChannelController;
use App\Http\Controllers\Api\Master\SaleDistrictController;
use App\Http\Controllers\Api\Master\SaleGroupController;
use App\Http\Controllers\Api\Business\DeliveryController;
use App\Http\Controllers\Api\System\RouteController;
use App\Http\Controllers\Api\Master\UserController;
use App\Http\Controllers\Api\Master\OrderReviewOptionController;


use App\Http\Controllers\Api\Master\MasterDataController;
use App\Http\Controllers\Api\Master\MenuRouterController;
use App\Models\Business\Delivery;

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
    Route::get('/routes', [RouteController::class, 'getRoutes']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::prefix('master')->group(function () {
        Route::prefix('/warehouses')->group(function () {
            Route::get('/', [WarehouseController::class, 'getAvailableWarehouses']);
            Route::post('/', [WarehouseController::class, 'createNewWarehouse']);
            Route::put('/{id}', [WarehouseController::class, 'updateExistingWarehouse']);
            Route::delete('/{id}', [WarehouseController::class, 'deleteExistingWarehouse']);
        });
        Route::prefix('/delivery-partners')->group(function () {
            Route::get('/', [DeliveryPartnerController::class, 'getAvailablePartners']);
            Route::post('/', [DeliveryPartnerController::class, 'createNewPartner']);
            Route::put('/{id}', [DeliveryPartnerController::class, 'updateExistingPartner']);
            Route::delete('/{id}', [DeliveryPartnerController::class, 'deleteExistingPartner']);
        });
        Route::prefix('/companies')->group(function () {
            Route::get('/', [CompanyController::class, 'getAvailableCompanies']);
            Route::post('/', [CompanyController::class, 'createNewCompany']);
            Route::put('/{code}', [CompanyController::class, 'updateExistingCompany']);
            Route::delete('/{code}', [CompanyController::class, 'deleteExistingCompany']);
        });
        Route::prefix('/customers')->group(function () {
            Route::get('/', [CustomerController::class, 'getAvailableCustomers']);
            Route::post('/', [CustomerController::class, 'createNewCustomer']);
            Route::put('/{id}', [CustomerController::class, 'updateExistingCustomer']);
            Route::delete('/{id}', [CustomerController::class, 'deleteExistingCustomer']);
        });
        Route::prefix('/customer-phones')->group(function () {
            Route::get('/', [CustomerPhoneController::class, 'getAvailableCustomerPhones']);
            Route::post('/', [CustomerPhoneController::class, 'createNewCustomerPhone']);
            Route::put('/{id}', [CustomerPhoneController::class, 'updateExistingCustomerPhone']);
            Route::delete('/{id}', [CustomerPhoneController::class, 'deleteExistingCustomerPhone']);
        });
        Route::prefix('/employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'getAvailableEmployees']);
            Route::post('/', [EmployeeController::class, 'createNewEmployee']);
            Route::put('/{id}', [EmployeeController::class, 'updateExistingEmployee']);
            Route::delete('/{id}', [EmployeeController::class, 'deleteExistingEmployee']);
        });
        Route::prefix('/distribution-channels')->group(function () {
            Route::get('/', [DistributionChannelController::class, 'getAvailableDistributionChannels']);
            Route::post('/', [DistributionChannelController::class, 'createNewDistributionChannel']);
            Route::put('/{id}', [DistributionChannelController::class, 'updateExistingDistributionChannel']);
            Route::delete('/{id}', [DistributionChannelController::class, 'deleteExistingDistributionChannel']);
        });
        Route::prefix('/sale-districts')->group(function () {
            Route::get('/', [SaleDistrictController::class, 'getAvailableSaleDistricts']);
            Route::post('/', [SaleDistrictController::class, 'createNewSaleDistrict']);
            Route::put('/{id}', [SaleDistrictController::class, 'updateExistingSaleDistrict']);
            Route::delete('/{id}', [SaleDistrictController::class, 'deleteExistingSaleDistrict']);
        });
        Route::prefix('/sale-groups')->group(function () {
            Route::get('/', [SaleGroupController::class, 'getAvailableSaleGroups']);
            Route::post('/', [SaleGroupController::class, 'createNewSaleGroup']);
            Route::put('/{id}', [SaleGroupController::class, 'updateExistingSaleGroup']);
            Route::delete('/{id}', [SaleGroupController::class, 'deleteExistingSaleGroup']);
        });
        Route::prefix('/users')->group(function () {
            Route::get('/', [UserController::class, 'getAvailableUsers']);
            Route::post('/', [UserController::class, 'createNewUser']);
            Route::put('/{id}', [UserController::class, 'updateExistingUser']);
            Route::delete('/{id}', [UserController::class, 'deleteExistingUser']);
        });
        Route::prefix('/order-review-options')->group(function () {
            Route::get('/', [OrderReviewOptionController::class, 'getAvailableOrderReviewOptions']);
            Route::post('/', [OrderReviewOptionController::class, 'createNewOrderReviewOption']);
            Route::put('/{id}', [OrderReviewOptionController::class, 'updateExistingOrderReviewOption']);
            Route::delete('/{id}', [OrderReviewOptionController::class, 'deleteExistingOrderReviewOption']);
        });
        Route::prefix('/menu-routers')->group(function () {
            Route::delete('/{id}', [MenuRouterController::class, 'deleteConfigMenu']);
            Route::get('/configs', [MenuRouterController::class, 'getConfigMenus']);
            Route::post('/save-configs', [MenuRouterController::class, 'saveConfigMenus']);
        });
    });

    Route::prefix('sap')->group(function () {
        Route::post('/sync-category/{category}', [MasterDataController::class, 'syncFromSAP']);
        Route::post('/sync-order', [OrderController::class, 'syncFromSAP']);
    });

    Route::prefix('shipment')->group(function () {
        Route::get('/scan-qr/{qr_code}', [DeliveryController::class, 'getDeliveryByQrScan']);
        Route::get('/delivery/{delivery_id}', [DeliveryController::class, 'getDeliveryById']);
        Route::post('/confirm-pickup/{delivery_id}', [DeliveryController::class, 'confirmPickupDelivery']);
        Route::post('/confirm-delivery/{delivery_id}/{order_id}', [DeliveryController::class, 'confirmOrderDelivery']);
        Route::post('/complete-delivery/{delivery_id}', [DeliveryController::class, 'completeDelivery']);
    });

    Route::prefix('customer')->group(function () {
        Route::get('/scan-qr/{qr_code}', [DeliveryController::class, 'getCustomerDeliveryByQrScan']);

        Route::prefix('/orders')->group(function () {
            Route::get('/', [OrderController::class, 'getOrdersByCustomer']);
            Route::get('/{order_id}', [OrderController::class, 'getOrderById']);
            Route::post('/{order_id}/confirm', [OrderController::class, 'confirmOrder']);
        });
    });

    Route::prefix('admin')->group(function () {
        Route::prefix('/deliveries')->group(function () {
            Route::get('/', [DeliveryController::class, 'getDeliveries']);
            Route::get('/{id}', [DeliveryController::class, 'getDeliveryById']);
            Route::post('/print-qrs', [DeliveryController::class, 'printDeliveriesQrCodeByIds']);
            Route::post('/{id}/print-qr', [DeliveryController::class, 'printDeliveryQrCodeById']);
            Route::post('/', [DeliveryController::class, 'createDelivery']);
            Route::patch('/{id}', [DeliveryController::class, 'updateDelivery']);
            Route::delete('/{id}', [DeliveryController::class, 'deleteDelivery']);
        });

        Route::prefix('/orders')->group(function () {
            Route::get('/', [OrderController::class, 'getOrders']);
            Route::get('/minified', [OrderController::class, 'getMinifiedOrders']);
        });
    });
});

//api
Route::prefix('auth')->group(function () {
    Route::post('/user/login', [UserAuthController::class, 'login']);

    Route::prefix('zalo')->group(function () {
        Route::post('/exist-user', [ZaloAuthController::class, 'checkExistUser']);
        Route::post('/verify-user-phone', [ZaloAuthController::class, 'verifyUserPhone']);
        Route::post('/login', [ZaloAuthController::class, 'login']);
        Route::post('/update-phone-number', [ZaloAuthController::class, 'updatePhoneNumber']);
    });
});
Route::prefix('webhook')->group(function () {
    Route::post('zalo/process', [ZaloAuthController::class, 'processWebHook']);
});
