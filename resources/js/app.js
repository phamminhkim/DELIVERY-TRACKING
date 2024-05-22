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
import drag from "v-drag"
import 'viewerjs/dist/viewer.css'
import VueViewer from 'v-viewer'
Vue.filter("formatDate", function (value) {
    if (value) {
        return moment(String(value)).format("DD/MM/YYYY");
    }
});
Vue.prototype.$formatDate = function (value) {
    if (value) {
        return moment(String(value)).format("DD/MM/YYYY");
    }
};
Vue.use(VueRouter);

Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);

Vue.use(ElementUI);

Vue.use(drag, {

});
Vue.use(VueViewer)
Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);
Vue.component("home", require("./components/home/MainScreen.vue").default);
Vue.component(
    "delivery-partners",
    require("./components/home/master/DeliveryPartners.vue").default
);
Vue.component("users", require("./components/home/master/Users.vue").default);
Vue.component(
    "customers",
    require("./components/home/master/Customers.vue").default
);
Vue.component(
    "menu-router-config",
    require("./components/home/master/MenuRouterConfigs.vue").default
);
Vue.component(
    "menu-router",
    require("./components/home/menu/MenuRouter.vue").default
);
Vue.component(
    "menu-router-children",
    require("./components/home/menu/MenuRouterChildren.vue").default
);
Vue.component(
    "deliveries",
    require("./components/home/business/Deliveries.vue").default
);
Vue.component(
    "orders",
    require("./components/home/business/Orders.vue").default
);
Vue.component(
    "order-processes",
    require("./components/home/business/OrderProcesses.vue").default
);
Vue.component(
    "admin-container",
    require("./components/AdminContainer.vue").default
);


// Vue.component(
//     'sale-groups',
//     require("./components/home/master/SaleGroups.vue").default
// );

Vue.prototype.$showMessage = function (type, title, message) {
    if (!title) title = "Information";
    toastr.options = {
        positionClass: "toast-bottom-right",
        toastClass: this.$getToastClassByType(type),
    };
    toastr[type](message, title);
};

Vue.prototype.$getToastClassByType = function (type) {
    switch (type) {
        case "success":
            return "toastr-bg-green";
        case "error":
            return "toastr-bg-red";
        case "warning":
            return "toastr-bg-yellow";
        default:
            return "";
    }
};
toastr.options = {
    positionClass: "toast-bottom-right",
};

Vue.filter("formatDateTime", function (value) {
    if (value) {
        return moment.utc(String(value)).format("DD/MM/YYYY HH:mm");
    }
});

const app = new Vue({
    el: "#app",
    data() {
        return {};
    },
    router,
});

Vue.config.productionTip = false;
