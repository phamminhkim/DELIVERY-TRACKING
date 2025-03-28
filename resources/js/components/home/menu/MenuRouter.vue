<template>
    <div>
        <ul
            class="nav nav-pills nav-sidebar flex-column nav-legacy"
            data-widget="treeview"
            role="menu"
            data-accordion="false"
        >
            <li
                v-for="menu in menus"
                :key="menu.id"
                :class="getItemClass(menu)"
                :style="getItemStyle(menu)"
            >
                <strong v-if="menu.link == '#' && menu.icon == ''">
                    {{ menu.title }}</strong
                >
                <router-link
                    v-else
                    :to="getLinkHref(menu)"
                    :class="getLinkClass(menu)"
                    style="cursor: pointer"
                >
                    <i v-if="menu.icon" :class="'nav-icon ' + menu.icon"></i>
                    <p>
                        {{ menu.title }}
                        <i
                            v-if="menu.has_children"
                            class="fas fa-angle-right right"
                        ></i>
                        <span
                            v-if="hasMenuPendingCount(menu)"
                            class="badge badge-danger right"
                        >
                            {{ getMenuPendingCount(menu) }}
                        </span>
                    </p>
                </router-link>

                <menu-router-children
                    v-if="menu.has_children"
                    :level="1"
                    :menus="menu.children"
                    :is_opening="getIsOpening(menu)"
                    :menu_parent="menu"
                    :menu_current="menu_current"
                    :menu_current_root="menu_current_root"
                    :pending_counts="pending_counts"
                />
            </li>
        </ul>
    </div>
</template>

<script>
import MenuRouterChildren from "./MenuRouterChildren.vue";
export default {
    components: { MenuRouterChildren },
    props: {
        menus: Array,
    },
    data() {
        return {
            pending_counts: {},
            menu_current: {},
            menu_current_root: {},
        };
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.updateRoute(this.$route);
    },
    watch: {
        $route(to, from) {
            this.updateRoute(to);
        },
    },
    methods: {
        updateRoute(route_to) {
            const route = route_to.path.replace("/", "");
            const params = new URLSearchParams(route_to.query);

            this.menu_current = {};
            this.menu_current_root = {};

            for (const menu of this.menus) {
                const menu_current = menu.children.find(
                    (child) =>
                        child.link === route &&
                        params.toString() ===
                            new URLSearchParams(child.query_string).toString()
                );

                if (menu_current) {
                    this.menu_current = menu_current;
                    this.menu_current_root = menu_current;

                    while (this.menu_current_root.parent_id !== 0) {
                        this.menu_current_root = this.menus.find(
                            (menu) =>
                                menu.id === this.menu_current_root.parent_id
                        );
                    }

                    return;
                }
            }
        },
        getLinkHref(menu) {
            if (!menu.has_children) {
                return (
                    menu.link +
                    (menu.query_string != "" ? "?" : "") +
                    menu.query_string
                );
            } else return "#";
        },
        getItemClass(menu) {
            if (menu.link == "#" && menu.icon == "") {
                return "nav-header";
            } else {
                var item_class = "nav-item";
                if (!menu.has_children) {
                    item_class += " has-treeview";
                } else if (menu.id == this.menu_current_root.id) {
                    item_class += " menu-open";
                }
                return item_class;
            }
        },
        getIsOpening(menu) {
            if (menu.link == "#" && menu.icon == "") {
                return false;
            } else if (menu.id == this.menu_current_root.id) {
                return true;
            }
            return false;
        },
        getItemStyle(menu) {
            if (menu.link == "#" && menu.icon == 0) {
                return "text-transform: uppercase";
            }
            return "";
        },
        getLinkClass(menu) {
            var item_class = "nav-link";
            if (
                menu.id == this.menu_current_root.id ||
                menu.id == this.menu_current.id ||
                (menu.left > this.menu_current_root.left &&
                    menu.right < this.menu_current_root.right)
            ) {
                item_class += " active";
            }
            return item_class;
        },
        hasMenuPendingCount(menu) {
            if (this.pending_counts) {
                return (
                    Object.keys(this.pending_counts).indexOf(
                        menu.id.toString()
                    ) != -1
                );
            }
        },
        getMenuPendingCount(menu) {
            return this.pending_counts[menu.id];
        },
    },
};
</script>
