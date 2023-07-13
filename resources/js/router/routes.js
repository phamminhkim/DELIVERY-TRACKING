import MainScreen from "../components/home/MainScreen.vue";
import MasterDeliveryPartners from "../components/home/MasterDeliveryPartners.vue";
import MasterUsers from "../components/home/MasterUsers.vue";
import MasterCustomers from "../components/home/MasterCustomers.vue";
const routes = [
    { path: "/", component: MainScreen, name: "MainScreen" },
    { path: "/home", component: MainScreen, name: "MainScreen" },
    {
        path: "/delivery-partners",
        component: MasterDeliveryPartners,
        name: "DeliveryPartners",
    },
    {
        path: "/users",
        component: MasterUsers,
        name: "Users" },
    {
        path: "/customers",
        component: MasterCustomers,
        name: "Customers",
    },
];

export default routes;
