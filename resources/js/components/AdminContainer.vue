<template>
    <div>
        <b-breadcrumb>
            <b-breadcrumb-item active
                ><b-icon
                    icon="house-fill"
                    scale="1.25"
                    shift-v="1.25"
                    aria-hidden="true"
                ></b-icon>
                Trang chủ</b-breadcrumb-item
            >
            <b-breadcrumb-item
                v-for="(crumb, index) in crumbs"
                :key="index"
                :class="{ active: index === crumbs.length - 1 }"
            >
                <router-link :to="crumb.to">{{ crumb.label }}</router-link>
            </b-breadcrumb-item>
        </b-breadcrumb>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: "AdminContainer",
    computed: {
        crumbs() {
            const routes = this.$route.matched;
            return routes.map((route) => ({
                to: route.path,
                label: route.meta.breadcrumb || route.name || "Unknown",
            }));
        },
    },
};
</script>
<style scoped>
.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}
.breadcrumb {
    background-color: transparent;
    font-style: italic;
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 0px !important;
}
</style>
