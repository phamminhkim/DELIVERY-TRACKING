<template>
    <div class="container-header bg-white p-3 shadow-sm rounded css-font-size">
        <HeaderTabOrderProcesses @changeTab="getTab" :count_order_lack="case_data_temporary.order_lacks.length">
        </HeaderTabOrderProcesses>
        <HeaderOrderProcesses ref="headerOrderProcesses" @listMaterialCombo="getListMaterialCombo"
            @listMaterialDonated="getListMaterialDonated" @listOrders="getOrders" @getInventory="getInventory"
            @checkPrice="getCheckPrice" @getListMaterialDetect="getListMaterialDetect" :tab_value="tab_value"
            @openModalSearchOrderProcesses="openModalSearchOrderProcesses"
            @isLoadingDetectSapCode="getIsLoadingDetectSapCode" @changeEventOrderLack="getEventOrderLack"
            @saveOrderProcess="getSaveOrderProcesses" @changeEventOrderDelete="getEventOrderDelete"
            @listOrderProcessSO="getListOrderProcessSO"
            @getCustomerGroupId="getCustomerGroupId"
            @checkPromotion="getCheckPromotion"
            :item_selecteds="case_data_temporary.item_selecteds">
        </HeaderOrderProcesses>
        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes"
            @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses" @itemReplace="getReplaceItem"
            :item_selecteds="case_data_temporary.item_selecteds"></DialogSearchOrderProcesses>
        <DialogTitleOrderSO ref="dialogTitleOrderSo" :orders="orders" :customer_group_id="case_save_so.customer_group_id" @saveOrderSO="getSaveOrderSO" :case_save_so="case_save_so">
        </DialogTitleOrderSO>
        <DialogListOrderProcessSO ref="dialogListOrderProcessSo" @fetchOrderProcessSODetail="getFetchOrderProcessSODetail"></DialogListOrderProcessSO>
        <!-- Parent -->
        <ParentOrderSuffice ref="parentOrderSuffice" v-show="tab_value == 'order'" :row_orders="row_orders"
            :orders="orders" :getDeleteRow="getDeleteRow" :material_donateds="material_donateds"
            :material_combos="material_combos" :order_lacks="case_data_temporary.order_lacks"
            :getOnChangeCategoryType="getOnChangeCategoryType" :tab_value="tab_value" :case_save_so="case_save_so"
            :is_loading_detect_sap_code="case_is_loading.detect_sap_code" @checkBoxRow="getCheckBoxRow">
        </ParentOrderSuffice>
        <ParentOrderLack :tab_value="tab_value" :order_lacks="case_data_temporary.order_lacks"
            @countOrderLack="getCountOrderLack"></ParentOrderLack>


    </div>
</template>
<script>
import HeaderOrderProcesses from './headers/HeaderOrderProcesses.vue';
import DialogSearchOrderProcesses from './dialogs/DialogSearchOrderProcesses.vue';
import DialogTitleOrderSO from './dialogs/DialogTitleOrderSO.vue';
import DialogListOrderProcessSO from './dialogs/DialogListOrderProcessSO.vue';
import HeaderTabOrderProcesses from './headers/HeaderTabOrderProcesses.vue';
import TableOrderLack from './tables/TableOrderLack.vue';
import ParentOrderSuffice from './parents/ParentOrderSuffice.vue';
import ParentOrderLack from './parents/ParentOrderLack.vue';
import ApiHandler, { APIRequest } from '../ApiHandler';
export default {
    components: {
        HeaderOrderProcesses,
        DialogSearchOrderProcesses,
        HeaderTabOrderProcesses,
        TableOrderLack,
        ParentOrderSuffice,
        ParentOrderLack,
        DialogTitleOrderSO,
        DialogListOrderProcessSO
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_open_modal_search_order_processes: false,
            tab_value: 'order',
            count_order_lack: 0,
            orders: [],
            material_donateds: [],
            material_combos: [],
            material_saps: [],
            material_inventories: [],
            material_prices: [],
            case_index: {
                check_box: [],
            },
            case_save_so: {
                id: '',
                title: '',
                serial_number: '',
                customer_group_id: -1
            },
            case_is_loading: {
                detect_sap_code: false
            },
            case_data_temporary: {
                item_selecteds: [],
                order_lacks: []
            },
            api_order_process_so: '/api/sales-order',

        }
    },
    created() {
        this.getUrl();
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
        getDeleteRow(index, item) {

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
            var orders = [...this.orders];
            this.material_inventories.forEach(tmp => {
                for (var i = 0; i < this.orders.length; i++) {
                    if (tmp['Material'] == this.orders[i]['sku_sap_code']) {
                        orders[i]['inventory_quantity'] = tmp['ATP_Quantity'];
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
                        orders[i]['company_price'] = tmp['price'];
                    }
                }
            });
            this.orders = [...orders];
        },
        getIsLoadingDetectSapCode(is_loading) {
            this.case_is_loading.detect_sap_code = is_loading;
        },
        getCheckBoxRow(items, index) {
            this.case_data_temporary.item_selecteds = items;
        },
        getEventOrderLack() {
            // sử dụng inclues để kiểm tra xem item đã tồn tại trong mảng chưa
            this.case_data_temporary.order_lacks = this.case_data_temporary.order_lacks.filter(item => !this.case_data_temporary.item_selecteds.includes(item));
            this.case_data_temporary.order_lacks.push(...this.case_data_temporary.item_selecteds);
            this.refeshCheckBox();
        },
        refeshCheckBox() {
            this.$refs.parentOrderSuffice.refeshCheckBox();
            this.case_data_temporary.item_selecteds = [];
        },
        getEventOrderDelete() {
            this.case_data_temporary.item_selecteds.forEach(item_selected => {
                this.orders.splice(this.orders.indexOf(item_selected), 1);
            });
            this.refeshCheckBox();
        },
        getReplaceItem(item_materials) {
            this.case_data_temporary.item_selecteds.forEach((item_selected, index) => {
                item_materials.forEach(item_material => {
                    this.orders[this.orders.indexOf(item_selected)].barcode = item_material.bar_code;
                    this.orders[this.orders.indexOf(item_selected)].sku_sap_code = item_material.sap_code;
                    this.orders[this.orders.indexOf(item_selected)].sku_sap_name = item_material.name;
                    this.orders[this.orders.indexOf(item_selected)].sku_sap_unit = item_material.unit.unit_code;
                });
            });
            this.closeModalSearchOrderProcesses();
            this.refeshCheckBox();
        },
        getSaveOrderProcesses() {
            console.log('saveOrderProcesses');
            this.showDialogTitleOrderSo();
            // this.$refs.parentOrderSuffice.saveOrderProcesses();
        },
        showDialogTitleOrderSo() {
            this.$refs.dialogTitleOrderSo.showDialogTitleOrderSo();
        },
        getSaveOrderSO(item) {
            this.refeshOrders();
            this.case_save_so.id = item.id;
            this.case_save_so.title = item.title;
            this.case_save_so.serial_number = item.serial_number;
            this.case_save_so.customer_group_id = item.customer_group_id;
            this.$refs.headerOrderProcesses.setCustomerGroupId(item.customer_group_id);
            item.so_data_items.forEach(data_item => {
                this.orders.push({
                    id: data_item.id,
                    customer_sku_code: data_item.customer_sku_code,
                    customer_sku_name: data_item.customer_sku_name,
                    customer_sku_unit: data_item.customer_sku_unit,
                    quantity: data_item.quantity,
                    company_price: data_item.company_price,
                    customer_code: data_item.so_header.customer_code,
                    level2: data_item.so_header.level2,
                    level3: data_item.so_header.level3,
                    level4: data_item.so_header.level4,
                    note1: data_item.so_header.note,
                    note: data_item.note,
                    barcode: data_item.barcode,
                    sku_sap_code: data_item.sku_sap_code,
                    sku_sap_name: data_item.sku_sap_name,
                    sku_sap_unit: data_item.sku_sap_unit,
                    inventory_quantity: data_item.inventory_quantity,
                    amount_po: data_item.amount_po,
                    is_inventory: data_item.is_inventory,
                    is_promotive: data_item.is_promotive,
                    price_po: data_item.price_po,
                    promotive: data_item.promotive_name,
                    promotive_name: data_item.promotive_name,
                    quantity1_po: data_item.quantity1_po,
                    quantity2_po: data_item.quantity2_po,
                    customer_name: data_item.so_header.customer_name,

                });
            });
            this.refHeaderOrderProcesses();

        },
        refeshOrders() {
            this.orders = [];
        },
        getListOrderProcessSO() {
            this.$refs.dialogListOrderProcessSo.showModal();
        },
        getFetchOrderProcessSODetail(item) {
            this.case_data_temporary.order_lacks = [];
           this.getSaveOrderSO(item);
        },
        refHeaderOrderProcesses() {
            this.$refs.headerOrderProcesses.updateOrders(this.orders);
        },
        getCustomerGroupId(customer_group_id) {
            this.case_save_so.customer_group_id = customer_group_id;
            // this.$refs.headerOrderProcesses.getCustomerGroupId(customer_group_id);
        },
        getUrl() {
            const url = window.location.href;
            const id = url.split('#')[1];
            if (id) {
                this.fetchOrderProcessSODetail(id);
            }
            
        },
        async fetchOrderProcessSODetail(id) {
            try {
                // this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.get(this.api_order_process_so + '/' + id);
                this.getSaveOrderSO(data);
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                // this.case_is_loading.fetch_api = false;
            }
        },
        getCheckPromotion(){
            console.log('getCheckPromotion');
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