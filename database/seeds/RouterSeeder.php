<?php

use App\Models\Master\MenuRouter;
use App\Models\System\Route;
use App\Utilities\RedisUtility;
use Illuminate\Database\Seeder;

class RouterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = [
            [
                'name' => 'Tổng quan',
                'path' => '/dashboard',
                'component' => 'home/MainScreen'
            ],
            [
                'name' => 'Danh sách người dùng',
                'path' => '/users',
                'component' => 'home/master/Users'
            ],
            [
                'name' => 'Danh sách khách hàng',
                'path' => '/customers',
                'component' => 'home/master/Customers'
            ],
            [
                'name' => 'Định tuyến menus',
                'path' => '/menu-router-configs',
                'component' => 'home/master/MenuRouterConfigs'
            ],
            [
                'name' => 'Quản lí vận đơn',
                'path' => '/deliveries',
                'component' => 'home/business/Deliveries'
            ],
            [
                'name' => 'Quản lí đơn hàng',
                'path' => '/orders',
                'component' => 'home/business/Orders'
            ],
            [
                'name' => 'Cấu hình đơn hàng',
                'path' => '/order-extract-configs',
                'component' => 'home/business/OrderExtractConfigs'
            ],

            // [
            //     'name' => 'Danh sách file',
            //     'path' => '/order-file-uploads',
            //     'component' => 'home/business/OrderFileUploads'
            // ],

            // [
            //     'name' => 'Khách hàng liên kết',
            //     'path' => '/customer-group-pivots',
            //     'component' => 'home/sap/CustomerGroupPivots/CustomerGroupPivots'
            // ],
            // [
            //     'name' => 'Khách hàng khuyến mãi',
            //     'path' => '/customer-promotions',
            //     'component' => 'home/sap/CustomerPromotion/CustomerPromotion'
            // ],

            // [
            //     'name' => 'Upload đơn hàng',
            //     'path' => '/order-uploads',
            //     'component' => 'home/business/OrderUploads'
            // ],

            // Master Data
            [
                'name' => 'Đơn vị vận chuyển',
                'path' => '/delivery-partners',
                'component' => 'home/master/DeliveryPartners'
            ],
            [
                'name' => 'Công ty',
                'path' => '/companies',
                'component' => 'home/master/Companies'
            ],
            [
                'name' => 'Kho hàng',
                'path' => '/warehouses',
                'component' => 'home/master/Warehouses'
            ],
            [
                'name' => 'Số điện thoại khách hàng',
                'path' => '/customer-phones',
                'component' => 'home/master/CustomerPhones'
            ],
            [
                'name' => 'Kênh phân phối',
                'path' => '/distribution-channels',
                'component' => 'home/master/DistributionChannels'
            ],
            [
                'name' => 'Loại đánh giá',
                'path' => '/order-review-options',
                'component' => 'home/master/OrderReviewOptions'
            ],
            [
                'name' => 'Khu vực sale',
                'path' => '/sale-districts',
                'component' => 'home/master/SaleDistricts'
            ],
            [
                'name' => 'Nhóm sale',
                'path' => '/sale-groups',
                'component' => 'home/master/SaleGroups'
            ],
            [
                'name' => 'Vai trò',
                'path' => '/roles',
                'component' => 'home/admin/Roles'
            ],
            [
                'name' => 'Bảng mapping SAP',
                'path' => '/sap-material-mappings',
                'component' => 'home/sap/SapMaterialMapping/SapMaterialMapping'
            ],
            [
                'name' => 'Sản phẩm SAP',
                'path' => '/sap-materials',
                'component' => 'home/sap/SapMaterial/SapMaterials'
            ],
            [
                'name' => 'Nhóm khách hàng',
                'path' => '/customer-groups',
                'component' => 'home/master/CustomerGroup'
            ],
            [
                'name' => 'Xử lý đơn hàng',
                'path' => '/order-processes',
                'component' => 'home/business/OrderProcesses'
            ],
            [
                'name' => 'KM combo',
                'path' => '/material-combos',
                'component' => 'home/sap/MaterialCombo/MaterialCombos'
            ],
            [
                'name' => 'KM hàng tặng hàng',
                'path' => '/material-donateds',
                'component' => 'home/sap/MaterialDonated/MaterialDonateds'
            ],
            [
                'name' => 'Khách hàng đối tác',
                'path' => '/customer-partners',
                'component' => 'home/sap/CustomerPartner/CustomerPartners'
            ],
            [
                'name' => 'Bảng Quy cách',
                'path' => '/sap-compliances',
                'component' => 'home/sap/SapCompliance/SapCompliances'
            ],
            [
                'name' => 'Đồng bộ SAP',
                'path' => '/sap-syncs',
                'component' => 'home/business/OrderSyncSAP'
            ],
            [
                'name' => 'Chi tiết đơn hàng đồng bộ SAP',
                'path' => '/sap-syncs-detail',
                'component' => 'home/business/tables/TableOrderSyncDetail'
            ],
        ];

        foreach ($routes as $routeData) {
            $route = Route::where('path', $routeData['path'])->first();

            if ($route) {
                // Kiểm tra nếu component đã thay đổi
                if ($route->component !== $routeData['component']) {
                    $route->component = $routeData['component'];
                    $route->save();
                }
            } else {
                Route::create($routeData);
            }
        }

        $this->assignRouteIntoMenuRouter();
        RedisUtility::deleteByCategory('routes');
        $this->command->info('Routes seeded!');
    }

    public function assignRouteIntoMenuRouter()
    {
        $menu_routers = MenuRouter::all();

        foreach ($menu_routers as $menu_router) {
            $route = Route::where('path', '/' . $menu_router->link)->first();

            if ($route) {
                $menu_router->route_id = $route->id;
                $menu_router->save();
            }
        }
    }
}
