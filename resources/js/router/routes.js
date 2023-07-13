import MainScreen from "../components/home/MainScreen.vue";
import DeliveryPartners from "../components/home/DeliveryPartners.vue";
import Users from "../components/home/master/Users.vue";
import DeliveryCustomer from "../components/home/DeliveryCustomer.vue";
const routes = [
    { path: "/", component: MainScreen, name: "MainScreen" },
    { path: "/home", component: MainScreen, name: "MainScreen" },
    {
        path: "/master-delivery-partners",
        component: MasterDeliveryPartners,
        name: "MasterDeliveryPartners",
    },
    { path: "/users", component: Users, name: "Users" },
    {
        path: "/master-users",
        component: MasterUsers,
        name: "MasterUsers",
    },
    {
        path: "/master-customers",
        component: MasterCustomers,
        name: "MasterCustomers",
    },
];

export default routes;
