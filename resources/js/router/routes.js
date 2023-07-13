import MainScreen from "../components/home/MainScreen.vue";
import DeliveryPartners from "../components/home/master/DeliveryPartners.vue";
import Users from "../components/home/master/Users.vue";
import DeliveryCustomer from "../components/home/master/DeliveryCustomers.vue";
const routes = [
    { path: "/", component: MainScreen, name: "MainScreen" },
    { path: "/home", component: MainScreen, name: "MainScreen" },
    {
        path: "/users",
        component: Users,
        name: "Users"
    },
    {
        path: '/delivery-customers',
        component: DeliveryCustomer,
        name: 'DeliveryCustomer'
    },
    {
        path: '/delivery-partners',
        component: DeliveryPartners,
        name: 'DeliveryPartners'
    }
];

export default routes;