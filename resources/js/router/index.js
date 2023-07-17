import VueRouter from "vue-router";

const router = new VueRouter({
    base: "/",
    mode: "history",
});

getRoutes().then((routes) => {
    console.log("Fetched routes data:", routes);
    routes.forEach((route) => {
        addRoute(route);
    });
});

async function addRoute(route) {
    const component_name = route.component;

    const component = await import(`../components/${component_name}.vue`);
    route.component = component.default;

    router.addRoute(route); // Add fetched routes to the router
}

async function getRoutes() {
    try {
        return window.Laravel.routes;
    } catch (error) {
        console.error("Error fetching routes data:", error);
        return []; // Return an empty array if there's an error
    }
}

export default router;
