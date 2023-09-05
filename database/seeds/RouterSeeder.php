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
                'name' => 'Bảng đối chiếu sản phẩm',
                'path' => '/sap-product-mappings',
                'component' => 'home/master/SapProductMapping'
            ],
            [
                'name' => 'Sản phẩm SAP',
                'path' => '/sap-products',
                'component' => 'home/master/SapProducts'
            ]

        ];

        foreach ($routes as $route) {
            if (!Route::where('path', $route['path'])->where('component', $route['component'])->exists()) {
                Route::create($route);
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
