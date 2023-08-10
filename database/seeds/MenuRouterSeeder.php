<?php

use App\Models\Master\MenuRouter;
use App\Utilities\NestedSetSync;
use App\Utilities\RedisUtility;
use Illuminate\Database\Seeder;

class MenuRouterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order = 0;
        foreach ($this->getMenuLevel1() as $menu) {
            if (!MenuRouter::where('title', $menu['title'])->where('link', $menu['link'])->exists()) {
                $menu['order'] = $order++;
                $menu['parent_id'] = 0;
                $menu['left'] = 0;
                $menu['right'] = 0;
                $menu['is_active'] = true;
                MenuRouter::create($menu);
            }
        }

        foreach ($this->getMenuLevel2() as $menu) {
            if (!MenuRouter::where('title', $menu['title'])->where('link', $menu['link'])->exists()) {
                $menu['order'] = $order++;
                $menu['parent_id'] = MenuRouter::where('title', $menu['parent'])->first()->id;
                $menu['left'] = 0;
                $menu['right'] = 0;
                $menu['is_active'] = true;
                unset($menu['parent']);
                MenuRouter::create($menu);
            }
        }

        $transformer = new NestedSetSync();
        $transformer->traverseUpdate();
        RedisUtility::deleteByCategory('menu-tree');

        $this->command->info('MenuRouterSeeder completed');
    }

    private function getMenuLevel1()
    {
        return [
            [
                'title' => "Tổng quan",
                'icon' => "fas fa-chart-line",
                'link' => "dashboard",
                'query_string' => ""
            ],
            [
                'title' => "Quản lí đơn hàng",
                'icon' => "fas fa-shopping-cart",
                'link' => "#",
                'query_string' => ""
            ],
            [
                'title' => "Quản lí vận đơn",
                'icon' => "fas fa-truck-moving",
                'link' => "#",
                'query_string' => ""
            ],
            [
                'title' => "Quản lí dữ liệu",
                'icon' => "fas fa-database",
                'link' => "#",
                'query_string' => ""
            ],
            [
                'title' => "Quản trị hệ thống",
                'icon' => "fas fa-user-secret",
                'link' => "#",
                'query_string' => ""
            ]
        ];
    }

    private function getMenuLevel2()
    {
        return [
            [
                'title' => "Người dùng",
                'icon' => "fas fa-users",
                'link' => "users",
                'query_string' => "",
                'parent' => 'Quản trị hệ thống',
            ],
            [
                'title' => "Định tuyến menus",
                'icon' => "fas fa-bars",
                'link' => "menu-router-configs",
                'query_string' => "",
                'parent' => 'Quản trị hệ thống',
            ],
            [
                'title' => "Vai trò",
                'icon' => "fas fa-user-tag",
                'link' => "roles",
                'query_string' => "",
                'parent' => 'Quản trị hệ thống',
            ],
            [
                'title' => "Tất cả",
                'icon' => "nav-icon",
                'link' => "orders",
                'query_string' => "filter=all",
                'parent' => 'Quản lí đơn hàng',
            ],
            [
                'title' => "Chưa hoàn tất",
                'icon' => "nav-icon",
                'link' => "orders",
                'query_string' => "filter=undone",
                'parent' => 'Quản lí đơn hàng',
            ],
            [
                'title' => "Đang vận chuyển",
                'icon' => "nav-icon",
                'link' => "orders",
                'query_string' => "filter=delivering",
                'parent' => 'Quản lí đơn hàng',
            ],
            [
                'title' => "Tất cả",
                'icon' => "nav-icon",
                'link' => "deliveries",
                'query_string' => "filter=all",
                'parent' => 'Quản lí vận đơn',
            ],
            [
                'title' => "Chưa hoàn tất",
                'icon' => "nav-icon",
                'link' => "deliveries",
                'query_string' => "filter=undone",
                'parent' => 'Quản lí vận đơn',
            ],
            [
                'title' => "Đang vận chuyển",
                'icon' => "nav-icon",
                'link' => "deliveries",
                'query_string' => "filter=delivering",
                'parent' => 'Quản lí vận đơn',
            ],
            [
                'title' => "Khách hàng",
                'icon' => "fas fa-people-arrows",
                'link' => "customers",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "SĐT khách hàng",
                'icon' => "fas fa-phone",
                'link' => "customer-phones",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Đối tác vận chuyển",
                'icon' => "fas fa-truck",
                'link' => "delivery-partners",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Công ty",
                'icon' => "fas fa-building",
                'link' => "companies",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Kho hàng",
                'icon' => "fas fa-warehouse",
                'link' => "warehouses",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Kho hàng",
                'icon' => "fas fa-warehouse",
                'link' => "warehouses",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Kênh phân phối",
                'icon' => "fas fa-universal-access",
                'link' => "distribution-channels",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Khu vực sale",
                'icon' => "fas fa-object-group",
                'link' => "sale-districts",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Nhóm sale",
                'icon' => "fas fa-object-group",
                'link' => "sale-groups",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
        ];
    }
}
