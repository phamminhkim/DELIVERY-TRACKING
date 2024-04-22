<template>
    <div>
        <TableOrderSuffice ref="tableOrderSuffice" @deleteRow="getDeleteRow" :current_page="current_page" :per_page="per_page"
            :material_combos="material_combos" :material_donateds="material_donateds" :orders="orders"
            :order_lacks="order_lacks"
            :tab_value="tab_value" @onChangeCategoryType="getOnChangeCategoryType"
            :is_loading_detect_sap_code="is_loading_detect_sap_code" @checkBoxRow="getCheckBoxRow"></TableOrderSuffice>
        <PaginationTable :rows="row_orders" :per_page="per_page" :page_options="page_options"
            :current_page="current_page" @pageChange="getPageChange" @perPageChange="getPerPageChange">
        </PaginationTable>
    </div>
</template>
<script>
import TableOrderSuffice from '../tables/TableOrderSuffice.vue';
import PaginationTable from '../paginations/PaginationTable.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order'
        },
        getOnChangeCategoryType: {
            type: Function
        },
        material_donateds: {
            type: Array
        },
        material_combos: {
            type: Array
        },
        orders: {
            type: Array
        },
        getDeleteRow: {
            type: Function
        },
        row_orders: {
            type: Number,
            default: 0
        },
        is_loading_detect_sap_code: {
            type: Boolean,
            default: false
        },
        order_lacks: {
            type: Array,
            default: () => []
        }
    },
    components: {
        TableOrderSuffice,
        PaginationTable
    },
    data() {
        return {
            per_page: 10,
            page_options: [10, 20, 50, 100],
            current_page: 1,
            case_data_temporary: {
                item_selecteds: [],
                orders: [],
                material_donateds: [],
                material_combos: [],
            },

        }
    },
    methods: {
        getPerPageChange(per_page) {
            this.per_page = per_page;
        },
        getPageChange(page) {
            this.current_page = page;
        },
        getCheckBoxRow(items, index) {
            this.$emit('checkBoxRow', items, index);
        },
        refeshCheckBox() {
            this.$refs.tableOrderSuffice.refeshCaseCheckBox();
        }

    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>