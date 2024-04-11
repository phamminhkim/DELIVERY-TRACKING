<template>
    <div class="container-header bg-white p-3 shadow-sm rounded css-font-size">
        <HeaderTabOrderProcesses @changeTab="getTab" :count_order_lack="count_order_lack"></HeaderTabOrderProcesses>
        <HeaderOrderProcesses ref="headerOrderProcesses" @listMaterialCombo="getListMaterialCombo" @listMaterialDonated="getListMaterialDonated" @listOrders="getOrders" :tab_value="tab_value"  @openModalSearchOrderProcesses="openModalSearchOrderProcesses"></HeaderOrderProcesses>
        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes" @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses"></DialogSearchOrderProcesses>
        <TableOrderLack :tab_value="tab_value" @countOrderLack="getCountOrderLack"></TableOrderLack>
        <TableOrderSuffice @deleteRow="getDeleteRow" :orders="orders"  :material_donateds="material_donateds" :material_combos="material_combos" :tab_value="tab_value" @onChangeCategoryType="getOnChangeCategoryType"></TableOrderSuffice>
        <ParentMaterialDonated v-show="tab_value == 'order_donated'" :tab_value="tab_value" :count_order_lack="count_order_lack"></ParentMaterialDonated>
        <ParentMaterialCombo v-show="tab_value == 'order_combo'" :tab_value="tab_value" :count_order_lack="count_order_lack"></ParentMaterialCombo>
    </div>
</template>
<script>
import HeaderOrderProcesses from './headers/HeaderOrderProcesses.vue';
import DialogSearchOrderProcesses from './dialogs/DialogSearchOrderProcesses.vue';
import HeaderTabOrderProcesses from './headers/HeaderTabOrderProcesses.vue';
import TableOrderLack from './tables/TableOrderLack.vue';
import TableOrderSuffice from './tables/TableOrderSuffice.vue';
import HeaderMaterialDonated from './headers/HeaderMaterialDonated.vue';
import TableMaterialDonated from './tables/TableMaterialDonated.vue';
import DialogMaterialDonated from '../master/dialogs/DialogMaterialDonated.vue';
import ParentMaterialDonated from './parents/ParentMaterialDonated.vue';
import ParentMaterialCombo from './parents/ParentMaterialCombo.vue';
export default {
    components: {
        HeaderOrderProcesses,
        DialogSearchOrderProcesses,
        HeaderTabOrderProcesses,
        HeaderMaterialDonated,
        TableOrderLack,
        TableOrderSuffice,
        TableMaterialDonated,
        DialogMaterialDonated,
        ParentMaterialDonated,
        ParentMaterialCombo
    },
    data() {
        return {
            is_open_modal_search_order_processes: false,
            tab_value: 'order',
            count_order_lack: 0,
            orders: [],
            material_donateds: [],
            material_combos: [],
        }
    },
    methods: {
        openModalSearchOrderProcesses() {
            this.is_open_modal_search_order_processes = true;
        },
        closeModalSearchOrderProcesses() {
            this.is_open_modal_search_order_processes = false;
        },
        getTab(tab) {
            this.tab_value = tab;
        },
        getCountOrderLack(count) {
            this.count_order_lack = count;
        },
        getOrders(orders) {
            this.orders = orders;
        },
        getOnChangeCategoryType(index, item) {
            this.$refs.headerOrderProcesses.updateMaterialCategoryTypeInOrder(index, item);
        },
        getListMaterialDonated(data) {
           this.material_donateds = data;
        },
        getListMaterialCombo(data) {
           this.material_combos = data;
        },
        getDeleteRow(index) {
            this.orders.splice(index, 1);
        }

    }
}
</script>
<style lang="scss" scoped>
.input-group-text {
    font-size: smaller !important;
}
</style>