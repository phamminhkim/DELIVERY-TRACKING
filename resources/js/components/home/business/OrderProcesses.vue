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
            @listOrderProcessSO="getListOrderProcessSO" @getCustomerGroupId="getCustomerGroupId"
            @exportExcel="getExportExcel" @importExcel="getImportExcel"
            :item_selecteds="case_data_temporary.item_selecteds" @changeEventCompliance="getChangeEventCompliance"
            @changeEventOrderSyncSAP="showModalSyncSAP" @listCustomerGroup="getListCustomerGroup">
        </HeaderOrderProcesses>
        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes"
            @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses" @itemReplaceAll="getReplaceItemAll"
            @itemReplace="getReplaceItem" :item_selecteds="case_data_temporary.item_selecteds">
        </DialogSearchOrderProcesses>
        <DialogTitleOrderSO ref="dialogTitleOrderSo" :orders="orders"
            :customer_group_id="case_save_so.customer_group_id" @saveOrderSO="getSaveOrderSO"
            :order_lacks="case_data_temporary.order_lacks" :case_save_so="case_save_so">
        </DialogTitleOrderSO>
        <DialogListOrderProcessSO ref="dialogListOrderProcessSo"
            @fetchOrderProcessSODetail="getFetchOrderProcessSODetail"></DialogListOrderProcessSO>
        <TempExcelImport :is_show_hide="case_is_loading.show_hide_excel" :header_fields="header_fields"
            :title="title_excel" @convertFileExcel="getConvertFileExel"></TempExcelImport>
        <!-- Parent -->
        <ParentOrderSuffice ref="parentOrderSuffice" v-show="tab_value == 'order'" :row_orders="row_orders"
            :count_reset_filter="case_index.count_reset_filter" :orders="orders" :getDeleteRow="getDeleteRow"
            :material_donateds="material_donateds" :material_combos="material_combos"
            :order_lacks="case_data_temporary.order_lacks" :filterOrders="filterOrders"
            :getOnChangeCategoryType="getOnChangeCategoryType" :tab_value="tab_value" :case_save_so="case_save_so"
            :is_loading_detect_sap_code="case_is_loading.detect_sap_code" @checkBoxRow="getCheckBoxRow"
            @sortingChanged="getSortingChanged" @createRow="getCreateRow" @handleItem="getHandleItem"
            @btnDuplicateRow="getBtnDuplicateRow" @pasteItem="getPasteItem" @btnCopyDeleteRow="getBtnCopyDeleteRow"
            @btnParseCreateRow="getBtnParseCreateRow" @btnCopy="getBtnCopy" @filterItems="getFilterItems"
            @emitResetFilter="getResetFilter">
        </ParentOrderSuffice>
        <ParentOrderLack :tab_value="tab_value" :order_lacks="case_data_temporary.order_lacks"
            @convertOrderLack="getConvertOrderLack" @countOrderLack="getCountOrderLack"></ParentOrderLack>
        <ParentOrderSynchronized :showModalSyncSAP="showModalSyncSAP" :case_save_so="case_save_so"
            :customer_group_id="case_save_so.customer_group_id" :customer_groups="case_data_temporary.customer_groups"
            :order_syncs="case_data_temporary.order_syncs" @processOrderSync="getProcessOrderSync">
        </ParentOrderSynchronized>


    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import HeaderOrderProcesses from './headers/HeaderOrderProcesses.vue';
import DialogSearchOrderProcesses from './dialogs/DialogSearchOrderProcesses.vue';
import DialogTitleOrderSO from './dialogs/DialogTitleOrderSO.vue';
import DialogListOrderProcessSO from './dialogs/DialogListOrderProcessSO.vue';
import HeaderTabOrderProcesses from './headers/HeaderTabOrderProcesses.vue';
import TableOrderLack from './tables/TableOrderLack.vue';
import ParentOrderSuffice from './parents/ParentOrderSuffice.vue';
import ParentOrderLack from './parents/ParentOrderLack.vue';
import ParentOrderSynchronized from './parents/ParentOrderSynchronized.vue';
import TempExcelImport from '../../Templates/Excels/TempExcelImport.vue';
import ApiHandler, { APIRequest } from '../ApiHandler';
import { isUndefined } from 'axios/lib/utils';
export default {
    components: {
        HeaderOrderProcesses,
        DialogSearchOrderProcesses,
        HeaderTabOrderProcesses,
        TableOrderLack,
        ParentOrderSuffice,
        ParentOrderLack,
        DialogTitleOrderSO,
        DialogListOrderProcessSO,
        ParentOrderSynchronized,
        TempExcelImport
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_open_modal_search_order_processes: false,
            tab_value: 'order',
            title_excel: 'Template Import dữ liệu đơn hàng',
            count_order_lack: 0,
            orders: [],
            material_donateds: [],
            material_combos: [],
            material_saps: [],
            material_inventories: [],
            material_prices: [],
            case_index: {
                check_box: [],
                count_reset_filter: 0,
            },
            case_save_so: {
                id: '',
                title: '',
                serial_number: '',
                customer_group_id: -1
            },
            case_is_loading: {
                detect_sap_code: false,
                delete_row: false,
                is_inventory: false,
                fetch_api: false,
                created_conponent: false,
                reeset_filter_header: false,
                show_hide_excel: false,
            },
            case_data_temporary: {
                item_selecteds: [],
                order_lacks: [],
                copy: {},
                items: [],
                field: '',
                customer_groups: [],
                order_syncs: [],
            },
            header_fields: ['Vị trí', 'Makh Key', 'Mã Sap So', 'Barcode_cty', 'Tensp', 'Tên SKU', 'SL_sap', 'Dvt',
                'Km', 'Ghi_chu', 'Makh', 'Unit_barcode_description', 'Dvt_po', 'Po', 'Qty', 'Combo', 'Check tồn', 'Po_qty',
                'Pur_price', 'Amount', 'QC', 'Đúng_QC', 'Ghi chú 1', 'Gia_cty', 'Level 2', 'Level 3', 'Level 4',
                'po_number', 'po_delivery_date'],
            api_order_process_so: '/api/sales-order',
            api_order_process_check_compliance: '/api/check-data/check-compliance',
            api_order_sync: '/api/so-header/sync-sale-order',


        }
    },
    created() {
        this.getUrl();
        this.case_is_loading.created_conponent = true;
    },
    methods: {
        async getProcessOrderSync() {
            try {
                this.case_is_loading.fetch_api = true;
                let body = {
                    'order_process_id': this.case_save_so.id,
                    'data': this.case_data_temporary.order_syncs.map(item => {
                        return {
                            'id': item.so_header_id,
                            'warehouse_code': item.warehouse_code,
                        }
                    })
                };
                console.log(body);
                const { data, success } = await this.api_handler.post(this.api_order_sync, {}, body);
                if (success) {
                    data.forEach(item => {
                        this.case_data_temporary.order_syncs.forEach(order_sync => {
                            if (item.id == order_sync.so_header_id) {
                                order_sync.sap_so_number = item.so_number;
                                order_sync.is_sync_sap = item.is_sync_sap;
                                order_sync.noti_sync = item.message;
                            }
                        });
                    });
                    this.$showMessage('success', 'Thành công', 'Đồng bộ đơn hàng thành công');
                } else {
                    this.$showMessage('error', 'Lỗi', 'Đồng bộ đơn hàng thất bại');
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        isCheckUndefined(value) {
            if (isUndefined(value)) {
                return ''
            } else {
                return value;
            }
        },
        convertExcelSuccess() {
            this.$showMessage('success', 'Thành công', 'Import dữ liệu thành công');
            $('#template_excel').modal('hide');
            $("#data_excel").modal("hide");
        },
        sliceConvertHeader(data) {
            if (data.length > 0) {
                const index_header_fields = this.header_fields;
                const header_fields = data[0];
                const result = data.slice(1).map(item => {
                    let obj = {};
                    index_header_fields.forEach((field, index) => {
                        obj[field] = item[header_fields.indexOf(field)];
                    });
                    return obj;
                });
                console.log(result);
                return result;
            }
        },
        getConvertFileExel(data) {
            const result = this.sliceConvertHeader(data);
            this.orders = result.map((item, index) => {
                return {
                    order: this.isCheckUndefined(item['Vị trí']),
                    id: '',
                    customer_sku_code: this.isCheckUndefined(item['Unit_barcode']),
                    customer_sku_name: this.isCheckUndefined(item['Unit_barcode_description']),
                    customer_sku_unit: this.isCheckUndefined(item['Dvt_po']),
                    company_price: this.isCheckUndefined(item['Gia_cty']),
                    customer_code: this.isCheckUndefined(item['Makh']),
                    level2: this.isCheckUndefined(item['Level_2']),
                    level3: this.isCheckUndefined(item['Level_3']),
                    level4: this.isCheckUndefined(item['Level_4']),
                    note1: this.isCheckUndefined(item['Ghi chú 1']),
                    note: this.isCheckUndefined(item['Ghi_chu']),
                    barcode: this.isCheckUndefined(item['Barcode_cty']),
                    sap_so_number: this.isCheckUndefined(item['Mã Sap So']),
                    sku_sap_code: this.isCheckUndefined(item['Masap']),
                    sku_sap_name: this.isCheckUndefined(item['Tensp']),
                    sku_sap_unit: this.isCheckUndefined(item['Dvt']),
                    inventory_quantity: this.isCheckUndefined(item['Check tồn']),
                    amount_po: this.isCheckUndefined(item['Amount']),
                    is_inventory: false,
                    is_promotive: false,
                    price_po: this.isCheckUndefined(item['Pur_price']),
                    promotive: this.isCheckUndefined(item['Km']),
                    promotive_name: this.isCheckUndefined(item['Combo']),
                    quantity1_po: this.isCheckUndefined(item['Qty']),
                    quantity2_po: this.isCheckUndefined(item['Po_qty']),
                    customer_name: this.isCheckUndefined(item['Makh Key']),
                    // variant_quantity: '',
                    // extra_offer: '',
                    // promotion_category: this.isCheckUndefined(item['Combo']),
                    compliance: this.isCheckUndefined(item['QC']),
                    is_compliant: this.isCheckUndefined(item['Đúng_QC']) === '' ? null : this.isCheckUndefined(item['Đúng_QC']),
                    quantity3_sap: this.isCheckUndefined(item['SL_sap']),
                    po_number: this.isCheckUndefined(item['po_number']),
                    po_delivery_date: this.isCheckUndefined(item['po_delivery_date']),
                }
            });
            this.refHeaderOrderProcesses();
            this.convertExcelSuccess();
        },
        getListCustomerGroup(data) {
            this.case_data_temporary.customer_groups = data;
        },
        showModalSyncSAP() {
            if (this.case_save_so.id == '') {
                $('#dialogTitleOrderSo').modal('show');
            } else {
                $('#modalOrderSync').modal('show');
                const result = this.orders.map(order => {
                    return {
                        sap_so_number: '',
                        so_key: order.sap_so_number + (order.promotive_name == null ? '' : order.promotive_name),
                        customer_key: order.customer_code,
                        customer_name: order.customer_name,
                        po_delivery_date: order.po_delivery_date,
                        is_sync_sap: false,
                        noti_sync: '',
                        warehouse_code: '',
                        so_header_id: order.so_header_id
                    }
                });
                this.case_data_temporary.order_syncs = [...new Set(result.map(item => JSON.stringify(item)))].map(item => JSON.parse(item));
            }

        },
        mappingOrderSync() {
            const result = this.orders.map(order => {
                return {
                    sap_so_number: '',
                    so_key: order.sap_so_number + (order.promotive_name == null ? '' : order.promotive_name),
                    customer_key: order.customer_code,
                    customer_name: order.customer_name,
                    po_delivery_date: order.po_delivery_date,
                    status_sync: false,
                    noti_sync: '',
                    warehouse_code: '',

                }
            });
            this.case_data_temporary.order_syncs = [...new Set(result.map(item => JSON.stringify(item)))].map(item => JSON.parse(item));
        },
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
            this.case_data_temporary.order_lacks = [];
            this.case_index.count_reset_filter++;
            this.getResetFilter();
        },
        getOnChangeCategoryType(index, item, order) {
            this.$refs.headerOrderProcesses.updateMaterialCategoryTypeInOrder(index, item, order);
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
                    if ((tmp.customer_sku_code === this.orders[i].customer_sku_code &&
                        tmp.customer_sku_unit === this.orders[i].customer_sku_unit) ||
                        (tmp.bar_code == this.orders[i].customer_sku_code)) {
                        this.orders[i]['sku_sap_code'] = tmp.sap_code;
                        this.orders[i]['sku_sap_name'] = tmp.name;
                        this.orders[i]['sku_sap_unit'] = tmp.unit_code;
                        this.orders[i]['barcode'] = tmp.bar_code;
                        this.orders[i]['quantity3_sap'] = tmp.quantity3_sap;
                    }
                }
            });
            this.refHeaderOrderProcesses();
        },
        getInventory(data) {
            this.material_inventories = [...data];
            var orders = [...this.orders];
            this.material_inventories.forEach(tmp => {
                for (var i = 0; i < this.orders.length; i++) {
                    if (tmp['Material'] == this.orders[i]['sku_sap_code']) {
                        orders[i]['inventory_quantity'] = tmp['ATP_Quantity'];
                        orders[i]['variant_quantity'] = orders[i]['inventory_quantity'] - orders[i]['quantity1_po'] * orders[i]['quantity2_po'];
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
        isCheckLack(item) {
            let result = this.convertToNumber(item.quantity1_po) * this.convertToNumber(item.quantity2_po);
            if (result > this.convertToNumber(item.inventory_quantity) && this.convertToNumber(item.inventory_quantity) > 0) {
                // item.is_inventory = true;
                return true;
            }
            // return item.is_inventory;
            return false;
        },
        convertToNumber(value) {
            return Number(value);
        },
        getEventOrderLack() {
            let exists = false;
            if (this.case_data_temporary.item_selecteds.length == 0) {
                this.orders.filter((item, index_order) => {
                    if (this.isCheckLack(item)) {
                        item.is_inventory = true;
                        this.case_data_temporary.order_lacks.forEach(order_lack => {
                            if (order_lack.customer_sku_code == item.customer_sku_code && order_lack.customer_sku_unit == item.customer_sku_unit) {
                                exists = true;
                            }
                        });
                        if (!exists) {
                            this.case_data_temporary.order_lacks.push(item);
                            this.orders.splice(index_order, 1);
                        }
                    }
                });
            } else {
                // sử dụng inclues để kiểm tra xem item đã tồn tại trong mảng chưa và set is_inventory = true
                this.case_data_temporary.item_selecteds.forEach(item_selected => {
                    item_selected.is_inventory = true;
                    this.case_data_temporary.order_lacks.forEach(order_lack => {
                        if (order_lack.customer_sku_code == item_selected.customer_sku_code && order_lack.customer_sku_unit == item_selected.customer_sku_unit) {
                            exists = true;
                        }
                    });
                    if (!exists) {
                        this.case_data_temporary.order_lacks.push(item_selected);
                        this.orders = this.orders.filter(item => !this.case_data_temporary.item_selecteds.includes(item));
                    }
                });
                // this.case_data_temporary.order_lacks = this.case_data_temporary.order_lacks.filter(item => !this.case_data_temporary.item_selecteds.includes(item));
                // this.case_data_temporary.order_lacks.push(...this.case_data_temporary.item_selecteds);
                // this.orders = this.orders.filter(item => !this.case_data_temporary.item_selecteds.includes(item));

            }
            this.refeshCheckBox();
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
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
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
        },
        getReplaceItemAll(item_materials, barcode) {
            this.case_data_temporary.item_selecteds.forEach((item_selected, index) => {
                item_materials.forEach(item_material => {
                    this.orders.forEach((order) => {
                        if (order.barcode == barcode) {
                            order.sku_sap_code = item_material.sap_code;
                            order.sku_sap_name = item_material.name;
                            order.sku_sap_unit = item_material.unit.unit_code;
                            order.barcode = item_material.bar_code;
                        }
                    })
                });
            });
            this.closeModalSearchOrderProcesses();
            this.refeshCheckBox();
        },
        getReplaceItem(item_materials, order_index) {
            this.case_data_temporary.item_selecteds.forEach((item_selected, index) => {
                item_materials.forEach(item_material => {
                    this.orders.forEach((order) => {
                        if (order.order == order_index) {
                            order.sku_sap_code = item_material.sap_code;
                            order.sku_sap_name = item_material.name;
                            order.sku_sap_unit = item_material.unit.unit_code;
                            order.barcode = item_material.bar_code;
                        }
                    })
                });
                this.closeModalSearchOrderProcesses();
                this.refeshCheckBox();
            });
        },
        getSaveOrderProcesses() {
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
                var variant_quantity = this.convertToNumber(data_item.inventory_quantity) - this.convertToNumber(data_item.quantity1_po) * this.convertToNumber(data_item.quantity2_po);
                if (data_item.is_inventory == true) {
                    this.case_data_temporary.order_lacks.push({
                        order: data_item.order,
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
                        variant_quantity: variant_quantity,
                        extra_offer: '',
                        promotion_category: '',
                        po_delivery_date: data_item.so_header.po_delivery_date,
                        po_number: data_item.so_header.po_number,
                        sap_so_number: data_item.so_header.sap_so_number,
                        compliance: data_item.compliance,
                        is_compliant: data_item.is_compliant,
                        quantity3_sap: data_item.quantity3_sap,
                        so_header_id: data_item.so_header_id,
                    });
                } else {
                    this.orders.push({
                        order: data_item.order,
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
                        variant_quantity: variant_quantity,
                        extra_offer: '',
                        promotion_category: '',
                        po_delivery_date: data_item.so_header.po_delivery_date,
                        po_number: data_item.so_header.po_number,
                        sap_so_number: data_item.so_header.sap_so_number,
                        compliance: data_item.compliance,
                        is_compliant: data_item.is_compliant,
                        quantity3_sap: data_item.quantity3_sap,
                        so_header_id: data_item.so_header_id,
                    });
                }

            });
            this.refHeaderOrderProcesses();
            // this.mappingOrderSync();
        },
        refeshOrders() {
            this.orders = [];
            this.case_data_temporary.order_lacks = [];
            this.case_data_temporary.items = [];
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
        getConvertOrderLack(index, data) {
            data.is_inventory = false;
            this.orders.unshift(data);
            this.case_data_temporary.order_lacks.splice(index, 1);
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
        },
        getSortingChanged(sort) {
            this.orders = [...sort];
            console.log(this.orders);
            this.refHeaderOrderProcesses();
        },
        getExportExcel() {
            let data = this.orders.concat(this.case_data_temporary.order_lacks);
            let data_news = data.map(item => {
                return {
                    'Vị trí': item.order,
                    'Makh Key': item.customer_code,
                    'Mã Sap So': item.sap_so_number,
                    'Barcode_cty': item.barcode,
                    'Masap': item.sku_sap_code,
                    'Tensp': item.customer_sku_name,
                    'SL_sap': item.quantity3_sap,
                    'Dvt': item.customer_sku_unit,
                    'Km': item.promotive,
                    'Ghi_chu': item.note1,
                    'Makh': item.customer_code,
                    'Unit_barcode_description': item.sku_sap_name,
                    'Dvt_po': item.sku_sap_unit,
                    'Po': item.po_number,
                    'Qty': item.quantity,
                    'Combo': item.promotion_category,
                    'Check tồn': item.inventory_quantity,
                    'Po_qty': item.quantity1_po,
                    'Pur_price': item.price_po,
                    'Amount': item.amount_po,
                    'QC': item.compliance,
                    'Đúng_QC': item.is_compliant,
                    'Ghi chú 1': item.note,
                    'Gia_cty': item.company_price,
                    'Level 2': item.level2,
                    'Level 3': item.level3,
                    'Level 4': item.level4,
                    'po_number': item.po_number,
                    'po_delivery_date': item.po_delivery_date,
                }
            })
            var ws = XLSX.utils.json_to_sheet(data_news);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });
            const blob = new Blob([this.s2ab(wbout)], { type: 'application/octet-stream' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'xu_ly_don_hang.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        getImportExcel() {
            this.case_is_loading.show_hide_excel = !this.case_is_loading.show_hide_excel;
        },
        s2ab(s) {
            const buf = new ArrayBuffer(s.length);
            const view = new Uint8Array(buf);
            for (let i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        },
        getCreateRow() {
            this.orders.unshift({
                order: 1,
                id: '',
                customer_sku_code: '',
                customer_sku_name: '',
                customer_sku_unit: '',
                quantity: '',
                company_price: '',
                customer_code: '',
                level2: '',
                level3: '',
                level4: '',
                note1: '',
                note: '',
                barcode: '',
                sku_sap_code: '',
                sku_sap_name: '',
                sku_sap_unit: '',
                inventory_quantity: '',
                amount_po: '',
                is_inventory: false,
                is_promotive: false,
                price_po: '',
                promotive: '',
                promotive_name: '',
                quantity1_po: '',
                quantity2_po: '',
                customer_name: '',
                variant_quantity: '',
                extra_offer: '',
                promotion_category: '',
                sap_so_number: '',
                po_number: '',
                po_delivery_date: '',
                compliance: '',
                is_compliant: false,
                quantity3_sap: '',
            });
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
            this.refHeaderOrderProcesses();
        },

        getHandleItem(item, field, index, orders) {
            this.orders = [...orders];
            this.orders[index][field] = item;
            this.refHeaderOrderProcesses();
        },
        getBtnDuplicateRow(index, item) {
            // Thêm item vào sau vị trí index và order của item sau đó thì tăng index lên 1
            let new_order = this.convertNewOrder(item);
            this.orders.splice(index + 1, 0, JSON.parse(JSON.stringify(new_order)));
            let start_index = this.startIndex(new_order.order);
            this.changeIndexOrder(start_index);
            this.refHeaderOrderProcesses();
        },
        getPasteItem(items, indexs, field, e) {
            if (indexs.length !== 0) {
                e.preventDefault();
                indexs.forEach(index => {
                    items.forEach(item => {
                        this.orders[index - 1][field] = item.promotive;
                        this.orders[index - 1].promotive_name = item.promotive;
                    });
                });
            }
            this.refHeaderOrderProcesses();
        },
        convertNewOrder(item) {
            let new_order = {
                order: item.order + 1,
                id: item.id ? item.id : '',
                customer_sku_code: item.customer_sku_code,
                customer_sku_name: item.customer_sku_name,
                customer_sku_unit: item.customer_sku_unit,
                quantity: item.quantity,
                company_price: item.company_price,
                customer_code: item.customer_code,
                level2: item.level2,
                level3: item.level3,
                level4: item.level4,
                note1: item.note1,
                note: item.note,
                barcode: item.barcode,
                sap_so_number: item.sap_so_number,
                sku_sap_code: item.sku_sap_code,
                sku_sap_name: item.sku_sap_name,
                sku_sap_unit: item.sku_sap_unit,
                inventory_quantity: item.inventory_quantity,
                amount_po: item.amount_po,
                is_inventory: item.is_inventory,
                is_promotive: item.is_promotive,
                po_number: item.po_number,
                price_po: item.price_po,
                promotive: item.promotive,
                promotive_name: item.promotive_name,
                quantity1_po: item.quantity1_po,
                quantity2_po: item.quantity2_po,
                customer_name: item.customer_name,
                variant_quantity: item.variant_quantity,
                extra_offer: item.extra_offer,
                promotion_category: item.promotion_category,
                compliance: '',
                is_compliant: false,
                quantity3_sap: item.quantity3_sap,
            }
            return new_order;
        },
        startIndex(index) {
            let start = index;
            return start;
        },
        changeIndexOrder(start_index) {
            for (start_index; start_index < this.orders.length; start_index++) {
                const order_item = this.orders[start_index];
                order_item.order = start_index + 1;
            }
        },
        getBtnCopyDeleteRow(index, item) {
            this.case_data_temporary.copy = JSON.parse(JSON.stringify(item));
            this.case_is_loading.delete_row = true;
        },
        getBtnParseCreateRow(index) {
            if (this.case_is_loading.delete_row) {
                let index_item = this.orders.findIndex(item => item.order == this.case_data_temporary.copy.order);
                if (index_item !== -1) {
                    this.orders.splice(index_item, 1);
                }
            }
            let new_order = this.convertNewOrder(this.case_data_temporary.copy);
            this.orders.splice(index, 0, JSON.parse(JSON.stringify(new_order)));
            this.orders.forEach((item, index_order) => {
                item.order = index_order + 1;
            });
            this.refHeaderOrderProcesses();
        },
        getBtnCopy(index, item) {
            this.case_data_temporary.copy = JSON.parse(JSON.stringify(item));
            this.case_is_loading.delete_row = false;
        },
        getFilterItems(items, field, boolean) {
            this.case_data_temporary.items = items;
            this.case_data_temporary.field = field;
            this.case_is_loading.is_inventory = boolean;
            this.case_is_loading.created_conponent = false;
        },
        getResetFilter() {
            this.case_data_temporary.field = 'customer_sku_code';
            this.case_data_temporary.items = this.orders.map(item => item.customer_sku_code);
        },
        getChangeEventCompliance() {
            // console.log('check quy cách');
            this.CheckComplianceFromOrders();
        },
        async CheckComplianceFromOrders() {
            try {
                this.case_is_loading.fetch_api = true;
                const { data, message, success } = await this.api_handler.post(this.api_order_process_check_compliance, {},
                    {
                        items: this.paramsSearchCompliance(),
                    }
                );
                if (!success && success !== undefined) {
                    this.$showMessage('error', 'Lỗi', message);
                }
                if (success === undefined) {
                    if (data.success == true) {
                        this.resetCompliance();
                        this.mappingCompliance(data.items);
                        this.$showMessage('success', 'Thành công', 'Kiểm tra quy cách thành công');
                        this.refHeaderOrderProcesses();
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        paramsSearchCompliance() {
            let items = [];
            items = this.orders.reduce((arr, item) => {
                if (item.sku_sap_code) {
                    arr.push({
                        sap_code: item.sku_sap_code,
                        unit_code: item.sku_sap_unit,
                        quantity1_po: item.quantity1_po,
                        quantity2_po: item.quantity2_po,
                    });
                }
                return arr;
            }, []);
            return items;
        },
        mappingCompliance(items) {
            if (items.length > 0) {
                items.forEach(item => {
                    this.orders.forEach(order => {
                        if (order.sku_sap_code == item.sap_code && item.quantity2_po == order.quantity2_po) {
                            order.compliance = item.compliance;
                            order.is_compliant = item.is_compliant;
                        }
                    });
                });
            }
        },
        resetCompliance() {
            this.orders.forEach(order => {
                order.compliance = '';
                order.is_compliant = null;
            });
        },

    },
    computed: {
        row_orders() {
            return this.orders.length;
        },
        filterOrders() {
            var news = [];
            if (!this.case_is_loading.is_inventory) {
                this.case_data_temporary.items.forEach(item => {
                    news.push(...this.orders.filter(order => order[this.case_data_temporary.field] == item));
                });
                if (this.case_is_loading.created_conponent) {
                    news = this.orders;
                }
            } else {
                news = this.orders.filter(order => order.is_inventory == true);
            }
            news.sort((a, b) => a.order - b.order);
            return news;
        }
    },
}
</script>
<style lang="scss" scoped>
.input-group-text {
    font-size: smaller !important;
}
</style>