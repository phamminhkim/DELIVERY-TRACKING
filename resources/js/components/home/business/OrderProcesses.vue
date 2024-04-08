<template>
    <div class="container-header bg-white p-3 shadow-sm rounded">
        <HeaderTabOrderProcesses @changeTab="getTab" :count_order_lack="count_order_lack"></HeaderTabOrderProcesses>
        <HeaderOrderProcesses ref="headerOrderProcesses" @listOrders="getOrders" :tab_value="tab_value"  @openModalSearchOrderProcesses="openModalSearchOrderProcesses"></HeaderOrderProcesses>
        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes" @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses"></DialogSearchOrderProcesses>
        <TableOrderLack :tab_value="tab_value" @countOrderLack="getCountOrderLack"></TableOrderLack>
        <TableOrderSuffice :orders="orders" :tab_value="tab_value" @onChangeCategoryType="getOnChangeCategoryType"></TableOrderSuffice>
        <ParentMaterialDonated v-show="tab_value == 'order_donated'" :tab_value="tab_value" :count_order_lack="count_order_lack"
         @listMaterialDonated="getListMaterialDonated"
        ></ParentMaterialDonated>
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
        ParentMaterialDonated
    },
    data() {
        return {
            is_open_modal_search_order_processes: false,
            tab_value: 'order',
            count_order_lack: 0,
            orders: []
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
            console.log(data,'kim');
        }
       
       
    }
}
</script>
<style lang="scss" scoped>

</style>