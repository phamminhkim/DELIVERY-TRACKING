<template>
    <ul class="nav nav-treeview">
        <li
            v-for="menu in menus"
            :key="menu.id"
            v-bind:value="menu.id"
            :class="getItemClass(menu)"
        >
            <a :href="getLinkHref(menu)" :class="getLinkClass(menu)">
                <i
                    :style="'font-size:' + (1.2 - level * 0.3) + 'rem'"
                    :class="
                        menu.icon
                            ? 'nav-icon ' + menu.icon
                            : 'nav-icon far fa-circle'
                    "
                >
                </i>

                <p>
                    {{ menu.title }}
                    <i
                        v-if="menu.has_children"
                        class="fas fa-angle-right right"
                    ></i>
                    <span
                        v-if="hasMenuPendingCount(menu)"
                        class="badge badge-warning right"
                    >
                        {{ getMenuPendingCount(menu) }}
                    </span>
                </p>
            </a>
            <menu-router-children
                v-if="menu.has_children"
                :level="level + 1"
                :menus="menu.children"
                :is_opening="getIsOpening(menu)"
                :menu_current="menu_current"
                :menu_current_root="menu_current_root"
                :pending_counts="pending_counts"
            />
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        menus: Array,
        menu_current_root: Object,
        menu_parent: Object,
        menu_current: Object,
        pending_counts: Object,
        is_opening: Boolean,
        level: Number,
    },
    data() {
        return {
            has_pending_count: false,
            pending_count: 0,
        };
    },
    methods: {
        getItemClass(menu) {
            var item_class = "nav-item";
            if (!menu.has_children) {
                item_class += " has-treeview";
            } else if (
                this.is_opening ||
                (menu.left > this.menu_current_root.left &&
                    menu.right < this.menu_current_root.right)
            ) {
                item_class += " menu-open";
            }
            return item_class;
        },
        getLinkClass(menu) {
            var item_class = "nav-link";
            if (menu.id == this.menu_current.id) {
                item_class += " active";
            }
            return item_class;
        },
        getIsOpening(menu) {
            if (menu.id == this.menu_current_root.id) {
                return true;
            }
            return false;
        },
        getLinkHref(menu) {
            if (!menu.has_children) {
                return menu.link;
            } else return "#";
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
