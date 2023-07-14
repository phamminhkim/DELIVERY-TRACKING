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
    },
    {
        path: "/delivery-partners",
        component: DeliveryPartners,
        name: "DeliveryPartners",
    },
    {
        path: "/users",
        component: Users,
        name: "Users",
    },
    {
        path: "/customers",
        component: Customers,
        name: "Customers",
    },
    {
        path: "/menu-router-config",
        component: MenuRouterConfig,
        name: "MenuRouterConfig",
    },
];

export default routes;
