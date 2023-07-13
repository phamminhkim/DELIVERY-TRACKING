import MainScreen from "../components/home/MainScreen.vue";
import DeliveryPartners from "../components/home/DeliveryPartners.vue";
import MasterUsers from "../components/home/MasterUsers.vue";
import DeliveryCustomer from "../components/home/DeliveryCustomer.vue";
const routes = [
    { path: "/", component: MainScreen, name: "MainScreen" },
    { path: "/home", component: MainScreen, name: "MainScreen" },
    {
        path: "/delivery-partner",
        component: DeliveryPartners,
        name: "DeliveryPartners",
    },
    { path: "/master-users", component: MasterUsers, name: "MasterUsers" },
    {
        path: "/delivery-customer",
        component: DeliveryCustomer,
        name: "DeliveryCustomer",
    },
];

export default routes;
