<template>
    <div class="container-header bg-white p-3 shadow-sm rounded css-font-size">
        <HeaderTabOrderProcesses @changeTab="getTab" :count_order_lack="count_order_lack"></HeaderTabOrderProcesses>
        <HeaderOrderProcesses ref="headerOrderProcesses" @listMaterialCombo="getListMaterialCombo"
            @listMaterialDonated="getListMaterialDonated" @listOrders="getOrders" @getInventory="getInventory"
            @checkPrice="getCheckPrice" @getListMaterialDetect="getListMaterialDetect" :tab_value="tab_value"
            @openModalSearchOrderProcesses="openModalSearchOrderProcesses"
            @isLoadingDetectSapCode="getIsLoadingDetectSapCode"></HeaderOrderProcesses>
        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes"
            @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses"
            :item_selecteds="case_data_temporary.item_selecteds"></DialogSearchOrderProcesses>
        <TableOrderLack :tab_value="tab_value" @countOrderLack="getCountOrderLack"></TableOrderLack>
        <!-- Parent -->
        <ParentOrderSuffice v-show="tab_value == 'order'" :row_orders="row_orders" :orders="orders"
            :getDeleteRow="getDeleteRow" :material_donateds="material_donateds" :material_combos="material_combos"
            :getOnChangeCategoryType="getOnChangeCategoryType" :tab_value="tab_value"
            :is_loading_detect_sap_code="case_is_loading.detect_sap_code" @checkBoxRow="getCheckBoxRow">
        </ParentOrderSuffice>
        <ParentMaterialDonated v-show="tab_value == 'order_donated'" :tab_value="tab_value"
            :count_order_lack="count_order_lack"></ParentMaterialDonated>
        <ParentMaterialCombo v-show="tab_value == 'order_combo'" :tab_value="tab_value"
            :count_order_lack="count_order_lack"></ParentMaterialCombo>
    </div>
</template>
<script>
import HeaderOrderProcesses from './headers/HeaderOrderProcesses.vue';
import DialogSearchOrderProcesses from './dialogs/DialogSearchOrderProcesses.vue';
import HeaderTabOrderProcesses from './headers/HeaderTabOrderProcesses.vue';
import TableOrderLack from './tables/TableOrderLack.vue';
import DialogMaterialDonated from '../master/dialogs/DialogMaterialDonated.vue';
import ParentMaterialDonated from './parents/ParentMaterialDonated.vue';
import ParentMaterialCombo from './parents/ParentMaterialCombo.vue';
import ParentOrderSuffice from './parents/ParentOrderSuffice.vue';
export default {
    components: {
        HeaderOrderProcesses,
        DialogSearchOrderProcesses,
        HeaderTabOrderProcesses,
        TableOrderLack,
        DialogMaterialDonated,
        ParentMaterialDonated,
        ParentMaterialCombo,
        ParentOrderSuffice
    },
    data() {
        return {
            is_open_modal_search_order_processes: false,
            tab_value: 'order',
            count_order_lack: 0,
            orders: [],
            material_donateds: [],
            material_combos: [],
            material_saps: [],
            material_inventories: [],
            material_prices: [],
            case_is_loading: {
                detect_sap_code: false
            },
            case_data_temporary: {
                item_selecteds: [],

            }
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
        },
        getListMaterialDetect(data) {
            this.material_saps = [...data];
            this.material_saps.forEach(tmp => {
                for (var i = 0; i < this.orders.length; i++) {
                    if (tmp.customer_sku_code === this.orders[i].customer_sku_code && tmp.customer_sku_unit === this.orders[i].customer_sku_unit) {
                        this.orders[i]['sku_sap_code'] = tmp.sap_code;
                        this.orders[i]['sku_sap_name'] = tmp.name;
                        this.orders[i]['sku_sap_unit'] = tmp.unit_code;
                        this.orders[i]['barcode'] = tmp.bar_code;
                    }
                }

            });
        },
        getInventory(data) {
            this.material_inventories = [...data];
            console.log(this.material_inventories);
            var orders = [...this.orders];
            this.material_inventories.forEach(tmp => {
                for (var i = 0; i < this.orders.length; i++) {
                    if (tmp['Material'] == this.orders[i]['sku_sap_code']) {
                        orders[i]['check_ton'] = tmp['ATP_Quantity'];
                    }
                }
            });
            this.orders = [...orders];
        },
        getCheckPrice(data) {
            this.material_prices = [...data];
            var orders = [...this.orders];
            this.material_prices.forEach(tmp => {
                for (var i = 0; i < this.orders.length; i++) {
                    if (tmp['bar_code'] !== "" && tmp['bar_code'] == this.orders[i]['barcode']) {
                        orders[i]['price_company'] = tmp['price'];
                    }
                }
            });
            this.orders = [...orders];
        },
        getIsLoadingDetectSapCode(is_loading) {
            this.case_is_loading.detect_sap_code = is_loading;
            console.log(this.case_is_loading.detect_sap_code, is_loading);
        },
        getCheckBoxRow(items) {
            this.case_data_temporary.item_selecteds = items;
        }

    },
    computed: {
        row_orders() {
            return this.orders.length;

        }
    },
}
</script>
<style lang="scss" scoped>
.input-group-text {
    font-size: smaller !important;
}
</style>