import MainScreen from '../components/home/MainScreen.vue'
import DeliveryPartners from '../components/home/DeliveryPartners.vue'
import DeliveryUser from '../components/home/DeliveryUser.vue'
const routes = [
    { path: '/', component: MainScreen, name: 'MainScreen' },
    { path: '/home', component: MainScreen, name: 'MainScreen' },
    { path: '/delivery-partner', component: DeliveryPartners, name: 'DeliveryPartners' },
    { path: '/delivery-user', component: DeliveryUser, name: 'DeliveryUser' },
    
  ];

  export default routes;
