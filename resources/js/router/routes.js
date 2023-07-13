import MainScreen from "../components/home/MainScreen.vue";
import DeliveryPartners from "../components/home/master/DeliveryPartners.vue";
import Users from "../components/home/master/DeliveryUsers.vue";
import Customers from "../components/home/master/DeliveryCustomers.vue";
const routes = [{
        path: "/",
        component: MainScreen,
        name: "MainScreen"
    },
    {
        path: "/home",
        component: MainScreen,
        name: "MainScreen"
    },
    {
        path: "/delivery-partners",
        component: DeliveryPartners,
        name: "DeliveryPartners",
    },
    {
        path: "/delivery-users",
        component: Users,
        name: "Users"
    },

    {
        path: "/delivery-customers",
        component: Customers,
        name: "Customers",
    },
];

export default routes;