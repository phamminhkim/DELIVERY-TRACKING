import MainScreen from "../components/home/MainScreen.vue";
import DeliveryPartners from "../components/home/master/DeliveryPartners.vue";
import Users from "../components/home/master/DeliveryUsers.vue";
import Customers from "../components/home/master/DeliveryCustomers.vue";
import MenuRouterConfig from "../components/home/master/MenuRouterConfig.vue";
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
];

export default routes;
