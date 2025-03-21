<?php

use App\Models\Master\MenuRouter;
use App\Models\System\Role;
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
            $existing_menu = MenuRouter::where('title', $menu['title'])->where('link', $menu['link'])->first();
            if (!$existing_menu) {
                $menu['order'] = $order++;
                $menu['parent_id'] = 0;
                $menu['left'] = 0;
                $menu['right'] = 0;
                $menu['is_active'] = true;
                $clone_menu = $menu;
                unset($clone_menu['roles']);
                $existing_menu = MenuRouter::create($clone_menu);
            }
            $role_names =  isset($menu['roles']) ? $menu['roles'] : [];
            $roles = Role::whereIn('name', $role_names)->get();

            $existing_menu->guard_name = 'web';
            $existing_menu->assignRole($roles);
        }

        foreach ($this->getMenuLevel2() as $menu) {
            $existing_menu = MenuRouter::where('title', $menu['title'])->where('link', $menu['link'])->first();

            if (!$existing_menu) {
                $menu['order'] = $order++;
                $menu['parent_id'] = MenuRouter::where('title', $menu['parent'])->first()->id;
                $menu['left'] = 0;
                $menu['right'] = 0;
                $menu['is_active'] = true;
                unset($menu['parent']);
                $existing_menu = MenuRouter::create($menu);
            }

            $role_names =  isset($menu['roles']) ? $menu['roles'] : [];
            if ($existing_menu->parent) {
                $role_names = array_merge($role_names, $existing_menu->parent->roles->pluck('name')->toArray());
            }
            $roles = Role::whereIn('name', $role_names)->get();
            $existing_menu->guard_name = 'web';
            $existing_menu->assignRole($roles);
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
                'query_string' => "",
                'roles' => [
                    'admin-system',
                    'admin-warehouse',
                    'admin-partner',
                    'user-sap'
                ]
            ],
            [
                'title' => "Quản lí vận đơn",
                'icon' => "fas fa-truck-moving",
                'link' => "#",
                'query_string' => "",
                'roles' => [
                    'admin-system',
                    'admin-warehouse',
                    'admin-partner',
                    'user-sap'
                ]
            ],
            [
                'title' => "Trích xuất đơn hàng",
                'icon' => "fas fa-brain",
                'link' => "#",
                'query_string' => "",
                'roles' => [
                    'admin-system',
                    'user-sale'
                ]
            ],

            [
                'title' => "Quản lí dữ liệu",
                'icon' => "fas fa-database",
                'link' => "#",
                'query_string' => "",
                'roles' => [
                    'admin-system',
                ]
            ],
            [
                'title' => "Quản trị hệ thống",
                'icon' => "fas fa-user-secret",
                'link' => "#",
                'query_string' => "",
                'roles' => [
                    'admin-system',
                ]
            ],
            [
                'title' => "Sale - Thị trường",
                'icon' => "fas fa-user-secret",
                'link' => "#",
                'query_string' => "",
                'roles' => [
                    'admin-system',
                    'user-sale-marketing',
                ]
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
                'title' => "Chưa có vận đơn",
                'icon' => "nav-icon",
                'link' => "orders",
                'query_string' => "filter=pending",
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
                'title' => "Chưa hoàn tất",
                'icon' => "nav-icon",
                'link' => "orders",
                'query_string' => "filter=undone",
                'parent' => 'Quản lí đơn hàng',
            ],

            [
                'title' => "Tất cả",
                'icon' => "nav-icon",
                'link' => "orders",
                'query_string' => "filter=all",
                'parent' => 'Quản lí đơn hàng',
            ],
            [
                'title' => "Đang vận chuyển",
                'icon' => "nav-icon",
                'link' => "deliveries",
                'query_string' => "filter=delivering",
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
                'title' => "Tất cả",
                'icon' => "nav-icon",
                'link' => "deliveries",
                'query_string' => "filter=all",
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
            [
                'title' => "Sản phẩm SAP",
                'icon' => "fas fa-list",
                'link' => "sap-materials",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "Bảng mapping SAP",
                'icon' => "fas fa-network-wired",
                'link' => "sap-material-mappings",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],

            [
                'title' => "Cấu hình trích xuất",
                'icon' => "fas fa-file-pdf",
                'link' => "order-extract-configs",
                'query_string' => "",
                'parent' => 'Quản trị hệ thống',
            ],
            // [
            //     'title' => "Danh sách file",
            //     'icon' => "fas fa-pencil-square-o",
            //     'link' => "order-file-uploads",
            //     'query_string' => "",
            //     'parent' => 'Trích xuất đơn hàng',
            // ],

            // [
            //     'title' => "Khách hàng liên kết",
            //     'icon' => "fas fa-wrench",
            //     'link' => "customer-group-pivots",
            //     'query_string' => "",
            //     'parent' => 'Trích xuất đơn hàng',
            // ],
            // [
            //     'title' => "Khách hàng khuyến mãi",
            //     'icon' => "fas fa-project-diagram	",
            //     'link' => "customer-promotions",
            //     'query_string' => "",
            //     'parent' => 'Trích xuất đơn hàng',
            // ],

            // [
            //     'title' => "Upload đơn hàng",
            //     'icon' => "fas fa-upload",
            //     'link' => "order-uploads",
            //     'query_string' => "",
            //     'parent' => 'Trích xuất đơn hàng',
            // ],
            [
                'title' => "Nhóm khách hàng",
                'icon' => "fas fa-id-card-alt",
                'link' => "customer-groups",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            // [
            //     'title' => "Xử lý đơn hàng",
            //     'icon' => "fas fa-tasks",
            //     'link' => "order-processes",
            //     'query_string' => "",
            //     'parent' => 'Trích xuất đơn hàng',
            // ],
            [
                'title' => "KM combo",
                'icon' => "fas fa-gift",
                'link' => "material-combos",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "KM CLC",
                'icon' => "fas fa-gift",
                'link' => "material-CLCs",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "KM Bundle",
                'icon' => "fas fa-gift",
                'link' => "material-bundles",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "KM Parker",
                'icon' => "fas fa-gift",
                'link' => "material-parkers",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "KM hàng tặng hàng",
                'icon' => "fas fa-gift",
                'link' => "material-donateds",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "Khách hàng đối tác",
                'icon' => "fas fa-handshake",
                'link' => "customer-partners",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "Bảng quy cách",
                'icon' => "fas fa-times-circle",
                'link' => "sap-compliances",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "Danh sách đơn hàng",
                'icon' => "fas fa-bars",
                'link' => "sap-syncs",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "Chi tiết đơn hàng đồng bộ SAP",
                'icon' => "fas fa-times-circle",
                'link' => "sap-syncs-detail",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "V2 - Xử lý đơn hàng",
                'icon' => "fas fa-times-circle",
                'link' => "v2-order-processes",
                'query_string' => "",
                'parent' => 'Trích xuất đơn hàng',
            ],
            [
                'title' => "Thống kê đơn hàng",
                'icon' => "fas fa-chart-line",
                'link' => "v2-dashboard-order-processes",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Loại khuyến mãi",
                'icon' => "fas fa-ad",
                'link' => "material-category-type",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Nhà sách Mapping",
                'icon' => "fas fa-store",
                'link' => "book-store-home",
                'query_string' => "",
                'parent' => 'Quản lí dữ liệu',
            ],
            [
                'title' => "Sales - Xử lý đơn hàng",
                'icon' => "fas fa-truck-loading",
                'link' => "sales-order-processing",
                'query_string' => "",
                'parent' => 'Sale - Thị trường',
            ],
        ];
    }
}
