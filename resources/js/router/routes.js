import MainScreen from "../components/home/MainScreen.vue";
import DeliveryPartners from "../components/home/master/DeliveryPartners.vue";
import Users from "../components/home/master/DeliveryUsers.vue";
import Customers from "../components/home/master/DeliveryCustomers.vue";
import MenuRouterConfig from "../components/home/master/MenuRouterConfig.vue";
import Orders from "../components/home/business/Orders.vue";
import Deliveries from "../components/home/business/Deliveries.vue";

import TestCRUDPage from "../components/home/general_components/crud_page/TestCRUDPage.vue"
const routes = [
    {
        path: "/dashboard",
        component: MainScreen,
        name: "MainScreen",
        meta: {
            breadcrumb: "Tổng quan",
        },
    },
    {
        path: "/delivery-partners",
        component: DeliveryPartners,
        name: "DeliveryPartners",
        meta: {
            breadcrumb: "Đơn vị vận chuyển",
        },
    },
    {
        path: "/users",
        component: Users,
        name: "Users",
        meta: {
            breadcrumb: "Danh sách người dùng",
        },
    },
    {
        path: "/customers",
        component: Customers,
        name: "Customers",
        meta: {
            breadcrumb: "Danh sách khách hàng",
        },
    },
    {
        path: "/menu-router-config",
        component: MenuRouterConfig,
        name: "MenuRouterConfig",
        meta: {
            breadcrumb: "Định tuyến menus",
        },
    },
    {
        path: "/deliveries",
        component: Deliveries,
        name: "Deliveries",
        meta: {
            breadcrumb: "Quản lí vận đơn",
        },
    },
    {
        path: "/orders",
        component: Orders,
        name: "Orders",
        meta: {
            breadcrumb: "Quản lí đơn hàng",
        },
    },
    {
        path: '/test',
        component: TestCRUDPage,
        name: 'TestCRUDPage'
    }
];

export default routes;
