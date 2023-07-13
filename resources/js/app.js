require("./bootstrap");
import Vue from "vue";
import { BootstrapVue, BootstrapVueIcons } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import ElementUI from "element-ui";
import "element-ui/lib/theme-chalk/index.css";

window.Vue = require("vue");
import router from "./router";
import VueRouter from "vue-router";
import moment from "moment";

Vue.filter("formatDate", function (value) {
    if (value) {
        return moment(String(value)).format("DD/MM/YYYY");
    }
});
Vue.use(VueRouter);

Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);

Vue.use(ElementUI);

Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);
Vue.component("home", require("./components/home/MainScreen.vue").default);
Vue.component(
    "delivery-partner",
    require("./components/home/DeliveryPartners.vue").default
);
Vue.component(
    "master-users",
    require("./components/home/MasterUsers.vue").default
);
Vue.component(
    "delivery-customer",
    require("./components/home/DeliveryCustomer.vue").default
);
Vue.component(
    "admin-container",
    require("./components/AdminContainer.vue").default
);

const app = new Vue({
    el: "#app",
    data() {
        return {};
    },
    router,
});

Vue.config.productionTip = false;
