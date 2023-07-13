import MainScreen from "../components/home/MainScreen.vue";
import MasterDeliveryPartners from "../components/home/DeliveryPartners.vue";
import MasterUsers from "../components/home/MasterUsers.vue";
import MasterCustomers from "../components/home/DeliveryCustomer.vue";
const routes = [
    { path: "/", component: MainScreen, name: "MainScreen" },
    { path: "/home", component: MainScreen, name: "MainScreen" },
    {
        path: "/master-delivery-partners",
        component: MasterDeliveryPartners,
        name: "MasterDeliveryPartners",
    },
    {
        path: "/master-users",
        component: MasterUsers,
        name: "MasterUsers" },
    {
        path: "/master-customers",
        component: MasterCustomers,
        name: "MasterCustomers",
    },
];

export default routes;
