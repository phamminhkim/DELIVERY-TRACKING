<?php

use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Auth\ZaloAuthController;
use App\Http\Controllers\Api\Business\AiController;
use App\Http\Controllers\Api\Business\DashboardController;
use App\Http\Controllers\Api\Master\SapMaterialMappingController;
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
use App\Http\Controllers\Api\Master\SapMaterialController;
use App\Http\Controllers\Api\Master\SapUnitController;
use App\Http\Controllers\Api\Business\DeliveryController;
use App\Http\Controllers\Api\Business\RawSoController;
use App\Http\Controllers\Api\Business\UploadedFileController;
use App\Http\Controllers\Api\Business\CheckDataController;
use App\Http\Controllers\Api\Business\SoDataController;

use App\Http\Controllers\Api\Master\CustomerGroupController;
use App\Http\Controllers\Api\System\RouteController;
use App\Http\Controllers\Api\Master\UserController;
use App\Http\Controllers\Api\Master\OrderReviewOptionController;
use App\Http\Controllers\Api\Master\CustomerMaterialController;
use App\Http\Controllers\Api\Master\CustomerGroupPivotController;
use App\Http\Controllers\Api\Master\CustomerPromotionController;
use App\Http\Controllers\Api\Master\CustomerPartnerController;
use App\Http\Controllers\Api\Master\SapComplianceController;
use App\Http\Controllers\Api\Master\MasterDataController;
use App\Http\Controllers\Api\Master\MaterialCategoryTypeController;
use App\Http\Controllers\Api\Master\MaterialComboController;
use App\Http\Controllers\Api\Master\MaterialDonatedController;
use App\Http\Controllers\Api\Master\MenuRouterController;
use App\Models\Business\Delivery;
use App\Models\Business\UploadedFile;


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

    Route::prefix('dashboard')->group(function () {
        Route::get('/criteria', [DashboardController::class, 'getCriteriaStatistic']);
        Route::get('/report', [DashboardController::class, 'getReportStatistic']);
        Route::get('/orders', [DashboardController::class, 'getOrdersStatistic']);
        Route::get('/', [DashboardController::class, 'getStatistic']);
        Route::post('/holidays', [DashboardController::class, 'createPublicHoliday']);
    });
    Route::prefix('master')->group(function () {
        Route::prefix('/material-donateds')->group(function () {
            Route::get('/', [MaterialDonatedController::class, 'getAll']);
            Route::get('/minified', [MaterialDonatedController::class, 'getAllMinified']);
            Route::post('/excel', [MaterialDonatedController::class, 'createMaterialDonatedFormExcel']);
            Route::get('/exportToExcel', [MaterialDonatedController::class, 'exportToExcel']);
            Route::post('/', [MaterialDonatedController::class, 'store']);
            Route::put('/{id}', [MaterialDonatedController::class, 'update']);
            Route::delete('/{id}', [MaterialDonatedController::class, 'destroy']);
        });
        Route::prefix('/material-combos')->group(function () {
            Route::get('/minified', [MaterialComboController::class, 'getAllMinified']);
            Route::get('/exportToExcel', [MaterialComboController::class, 'exportToExcel']);
            Route::get('/', [MaterialComboController::class, 'getAll']);
            Route::post('/excel', [MaterialComboController::class, 'createMaterialComboFormExcel']);
            Route::post('/', [MaterialComboController::class, 'store']);
            Route::put('/{id}', [MaterialComboController::class, 'update']);
            Route::delete('/{id}', [MaterialComboController::class, 'destroy']);
        });
        Route::prefix('/material-category')->group(function () {
            Route::get('/', [MaterialCategoryTypeController::class, 'getAvailableCategoryTypes']);
            Route::post('/', [MaterialCategoryTypeController::class, 'createNewCategoryType']);
            Route::put('/{id}', [MaterialCategoryTypeController::class, 'updateExistingCategoryType']);
            Route::delete('/{id}', [MaterialCategoryTypeController::class, 'deleteExistingCategoryType']);
        });
        Route::prefix('/warehouses')->group(function () {
            Route::get('/minified', [WarehouseController::class, 'getAvailableWarehousesMinified']);
            Route::get('/active', [WarehouseController::class, 'getAvailableWarehousesActive']);
            Route::get('/', [WarehouseController::class, 'getAvailableWarehouses']);
            Route::post('/', [WarehouseController::class, 'createNewWarehouse']);
            Route::put('/{id}', [WarehouseController::class, 'updateExistingWarehouse']);
            Route::delete('/{id}', [WarehouseController::class, 'deleteExistingWarehouse']);
        });
        Route::prefix('/delivery-partners')->group(function () {
            Route::get('/external', [DeliveryPartnerController::class, 'getAvailableExternalPartners']);
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
            Route::get('/minified', [CustomerController::class, 'getAvailableCustomersMinified']);
            Route::get('/', [CustomerController::class, 'getAvailableCustomers']);
            Route::get('/{id}', [CustomerController::class, 'getCustomerById']);
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
            Route::get('/active', [DistributionChannelController::class, 'getAvailableDistributionChannelsActive']);
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
        Route::prefix('/sap-materials')->group(function () {
            Route::get('/excel', [SapMaterialController::class, 'exportToExcel']);
            Route::get('/minified', [SapMaterialController::class, 'getAvailableSapMaterialsMinified']);
            Route::get('/', [SapMaterialController::class, 'getAvailableSapMaterials']);
            Route::post('/excel', [SapMaterialController::class, 'createSapMaterialFormExcel']);
            Route::get('/exportToExcel', [SapMaterialController::class, 'exportToExcel']);
            Route::post('/', [SapMaterialController::class, 'createNewSapMaterial']);
            Route::put('/{id}', [SapMaterialController::class, 'updateExistingSapMaterial']);
            Route::delete('/{id}', [SapMaterialController::class, 'deleteExistingSapMaterial']);
        });
        Route::prefix('/sap-material-mappings')->group(function () {
            Route::post('/excel', [SapMaterialMappingController::class, 'createSapMateriasMappingsFromExcel']);
            Route::get('/exportToExcel', [SapMaterialMappingController::class, 'exportToExcel']);
            Route::get('/', [SapMaterialMappingController::class, 'getAvailableSapMaterialMappings']);
            Route::post('/', [SapMaterialMappingController::class, 'createNewSapMaterialMappings']);
            Route::put('/{id}', [SapMaterialMappingController::class, 'updateSapMaterialMapping']);
            Route::delete('/{id}', [SapMaterialMappingController::class, 'deleteExistingSapMaterialMapping']);
        });
        Route::prefix('/sap-units')->group(function () {
            Route::get('/', [SapUnitController::class, 'getAvailableSapUnits']);
            Route::post('/', [SapUnitController::class, 'createNewSapUnit']);
            Route::put('/{id}', [SapUnitController::class, 'updateExistingSapUnit']);
            Route::delete('/{id}', [SapUnitController::class, 'deleteExistingSapUnit']);
        });
        Route::prefix('/sap-compliances')->group(function () {
            Route::get('/minified', [SapComplianceController::class, 'getAvailableSapCompliancesMinified']);
            Route::get('/', [SapComplianceController::class, 'getAvailableSapCompliances']);
            Route::post('/excel', [SapComplianceController::class, 'createSapComplianceFormExcel']);
            Route::get('/exportToExcel', [SapComplianceController::class, 'exportToExcel']);
            Route::post('/', [SapComplianceController::class, 'createNewSapCompliance']);
            Route::put('/{id}', [SapComplianceController::class, 'updateExistingSapCompliance']);
            Route::delete('/{id}', [SapComplianceController::class, 'deleteExistingSapCompliance']);
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

        Route::prefix('/customer-groups')->group(function () {
            Route::get('/', [CustomerGroupController::class, 'getAllCustomerGroups']);
            Route::post('/', [CustomerGroupController::class, 'createNewGroup']);
            Route::put('/{id}', [CustomerGroupController::class, 'updateExistingGroup']);
            Route::delete('/{id}', [CustomerGroupController::class, 'deleteExistingGroup']);
        });
        Route::prefix('/customer-group-pivots')->group(function () {
            Route::get('/', [CustomerGroupPivotController::class, 'getAvailableCustomerGroupPivots']);
            Route::post('/', [CustomerGroupPivotController::class, 'createNewCustomerGroupPivot']);
            Route::put('/{id}', [CustomerGroupPivotController::class, 'updateExistingCustomerGroupPivot']);
            Route::delete('/{id}', [CustomerGroupPivotController::class, 'deleteExistingCustomerGroupPivot']);
        });

        Route::prefix('/customer-materials')->group(function () {
            Route::get('/', [CustomerMaterialController::class, 'getCustomerMaterials']);
            Route::get('/minified', [CustomerMaterialController::class, 'getCustomerMaterialsMinified']);
        });
        Route::prefix('/customer-partners')->group(function () {
            Route::get('/minified', [CustomerPartnerController::class, 'getAvailableCustomerPartnersMinified']);
            Route::get('/', [CustomerPartnerController::class, 'getAvailableCustomerPartners']);
            Route::post('/', [CustomerPartnerController::class, 'createNewCustomerPartner']);
            Route::get('/exportToExcel', [CustomerPartnerController::class, 'exportToExcel']);
            Route::post('/excel', [CustomerPartnerController::class, 'createCustomerPartnerFormExcel']);
            Route::put('/{id}', [CustomerPartnerController::class, 'updateExistingCustomerPartner']);
            Route::delete('/{id}', [CustomerPartnerController::class, 'deleteExistingCustomerPartner']);
        });

        Route::prefix('/customer-promotions')->group(function () {
            Route::get('/', [CustomerPromotionController::class, 'getAvailableCustomerPromotions']);
            Route::post('/', [CustomerPromotionController::class, 'createNewCustomerPromotion']);
            Route::put('/{id}', [CustomerPromotionController::class, 'updateExistingCustomerPromotion']);
            Route::delete('/{id}', [CustomerPromotionController::class, 'deleteExistingCustomerPromotion']);
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
        Route::post('/confirm-delivery/{delivery_id}', [DeliveryController::class, 'confirmDelivery']);
        Route::post('/complete-delivery/{delivery_id}', [DeliveryController::class, 'completeDelivery']);
    });

    Route::prefix('customer')->group(function () {
        Route::get('/scan-qr/{qr_code}', [DeliveryController::class, 'getCustomerDeliveryByQrScan']);

        Route::prefix('/orders')->group(function () {
            Route::get('/', [OrderController::class, 'getOrdersByCustomer']);
            Route::get('/statistics', [OrderController::class, 'getOrderStatistics']);
            Route::get('/{order_id}', [OrderController::class, 'getOrderById']);
            Route::get('/{order_id}/expanded', [OrderController::class, 'getOrderExpandedById']);
            Route::post('/{order_id}/confirm', [OrderController::class, 'confirmOrder']);
            Route::post('/confirm-multiple', [OrderController::class, 'confirmMultipleOrders']);
            Route::post('/{order_id}/review', [OrderController::class, 'reviewOrder']);
        });
    });

    Route::prefix('partner')->group(function () {
        Route::prefix('/deliveries')->group(function () {
            Route::get('/', [DeliveryController::class, 'getDeliveries']);
            Route::get('/print-configs', [DeliveryController::class, 'getPrintConfigsOfUser']);
            Route::get('/{id}', [DeliveryController::class, 'getDeliveryById']);
            Route::post('/print-configs', [DeliveryController::class, 'createPrintQRConfig']);
            Route::delete('/print-configs/{print_config_id}', [DeliveryController::class, 'deletePrintQRConfig']);
            Route::post('/print-qrs', [DeliveryController::class, 'printDeliveriesQrCodeByIds']);
            Route::post('/{id}/print-qr', [DeliveryController::class, 'printDeliveryQrCodeById']);
            Route::post('/excel', [DeliveryController::class, 'createExternalDeliveryFromExcel']);
            Route::post('/', [DeliveryController::class, 'createDelivery']);
            Route::patch('/{id}', [DeliveryController::class, 'updateDelivery']);
            Route::delete('/{id}', [DeliveryController::class, 'deleteDelivery']);
        });

        Route::prefix('/orders')->group(function () {
            Route::get('/', [OrderController::class, 'getOrders']);
            Route::get('/minified', [OrderController::class, 'getMinifiedOrders']);
            Route::get('/expanded', [OrderController::class, 'getExpandedOrders']);
        });
    });

    Route::prefix('admin')->group(function () {
        Route::prefix('/roles')->group(function () {
            Route::get('/', [RoleController::class, 'getAvailableRoles']);
            Route::post('/', [RoleController::class, 'createNewRole']);
            Route::put('/{id}', [RoleController::class, 'updateExistingRole']);
            Route::delete('/{id}', [RoleController::class, 'deleteExistingRole']);
        });
    });

    Route::prefix('ai')->group(function () {
        Route::prefix('/extract-order')->group(function () {
            Route::delete('/clean', [AiController::class, 'clean']);
            Route::post('/file/{id}', [AiController::class, 'extractOrderFromUploadedFile']);
            Route::post('/', [AiController::class, 'extractOrder']);
            Route::post('/reconvert/{id}', [AiController::class, 'reconvertUploadedFile']);
            Route::post('test', [AiController::class, 'extractOrderDirect']);
        });
        Route::prefix('config')->group(function () {
            Route::get('/customer-groups', [AiController::class, 'getExtractOrderConfigs']);
            Route::post('/extract', [AiController::class, 'extractDataForConfig']);
            Route::post('/convert', [AiController::class, 'convertToTableForConfig']);
            Route::post('/', [AiController::class, 'createExtractOrderConfigs']);
            Route::post('/restructure', [AiController::class, 'restructureDataForConfig']);
            Route::put('/{id}', [AiController::class, 'updateExtractOrderConfig']);
        });
        Route::prefix('file')->group(function () {
            Route::post('/batch', [UploadedFileController::class, 'prepareUploadFile']);
            Route::post('/upload', [UploadedFileController::class, 'uploadFile']);
            Route::get('/{id}', [UploadedFileController::class, 'getFilesById']);
            Route::get('/download/{id}', [UploadedFileController::class, 'downloadFile']);
            Route::get('/', [UploadedFileController::class, 'getFiles']);
            Route::delete(('/{id}'), [UploadedFileController::class, 'deleteFile']);
        });

    });

    Route::prefix('sales-order')->group(function () {
        Route::post('convert-orders', [AiController::class, 'extractOrderDirect']);
        Route::post('save-so', [SoDataController::class, 'saveSoData']);
        Route::put('/{id}', [SoDataController::class, 'updateSoData']);
        Route::delete('/{id}', [SoDataController::class, 'deleteSoData']);
        Route::get('/{id}', [SoDataController::class, 'getSoData']);
        Route::get('/', [SoDataController::class, 'getOrderProcessList']);
    });
    Route::prefix('check-data')->group(function () {
        Route::post('check-material-sap', [CheckDataController::class, 'checkMaterialSAP']);
        Route::post('check-promotion', [CheckDataController::class, 'checkPromotions']);
        Route::post('check-inventory', [CheckDataController::class, 'checkInventory']);
        Route::post('check-price', [CheckDataController::class, 'checkPrice']);
    });

    Route::prefix('raw-so-headers')->group(function () {
        Route::get('/push-sap', [RawSoController::class, 'getRawSoHeadersToPushSap']);
        Route::get('/{id}', [RawSoController::class, 'getRawSoHeaderById']);
        Route::get('/', [RawSoController::class, 'getRawSoHeaders']);
        Route::post('/promotive', [RawSoController::class, 'createPromotiveRawSoHeader']);
        Route::patch('/sync-sap', [RawSoController::class, 'syncRawSoHeaderFromSap']);
        Route::patch('/waiting-sync', [RawSoController::class, 'makeRawSoHeadersWatingToSync']);
        Route::patch('/{id}', [RawSoController::class, 'updateRawSoHeader']);
        Route::delete('/{id}', [RawSoController::class, 'deleteRawSoHeader']);
        Route::prefix('/raw-so-items')->group(function () {
            Route::post('/', [RawSoController::class, 'addRawSoItemToRawSoHeader']);
            Route::post('/copy', [RawSoController::class, 'copyRawSoItem']);
            Route::delete('/{id}', [RawSoController::class, 'deleteRawSoItem']);
        });
    });
    Route::post('/expand-left-menu', [UserController::class, 'expandLeftMenuUser']);
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
