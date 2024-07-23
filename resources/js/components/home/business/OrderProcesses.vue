<template>
    <div class="container-header bg-white p-3 shadow-sm rounded css-font-size">
        <HeaderTabOrderProcesses @changeTab="getTab" :count_order_lack="case_data_temporary.order_lacks.length">
        </HeaderTabOrderProcesses>
        <HeaderOrderProcesses ref="headerOrderProcesses" @listMaterialCombo="getListMaterialCombo"
            @listMaterialDonated="getListMaterialDonated" @listOrders="getOrders" @getInventory="getInventory"
            @checkPrice="getCheckPrice" @getListMaterialDetect="getListMaterialDetect" :tab_value="tab_value"
            @openModalSearchOrderProcesses="openModalSearchOrderProcesses"
            @isLoadingDetectSapCode="getIsLoadingDetectSapCode" @changeEventOrderLack="getEventOrderLack"
            :count_selected="case_data_temporary.item_selecteds.length" @saveOrderProcess="getSaveOrderProcesses"
            @changeEventOrderDelete="getEventOrderDelete" @listOrderProcessSO="getListOrderProcessSO"
            @getCustomerGroupId="getCustomerGroupId" @exportExcel="getExportExcel" @importExcel="getImportExcel"
            :item_selecteds="case_data_temporary.item_selecteds" @changeEventCompliance="getChangeEventCompliance"
            @changeEventOrderSyncSAP="showModalSyncSAP" @listCustomerGroup="getListCustomerGroup"
            @emitCheckInventory="getCheckInventory" @emitCheckPrice="getCheckPriceModal"
            @emitErrorConvertFile="getEmitErrorConvertFile" @emitDetectCustomerKey="getEmitDetectCustomerKey">
        </HeaderOrderProcesses>
        <DialogOrderCheckInventory @emitModelWarehouseId="getModelWarehouseId"></DialogOrderCheckInventory>
        <DialogOrderCheckPrice @emitModelSoNumbers="getModelSoNumbers"></DialogOrderCheckPrice>
        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes"
            @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses" @itemReplaceAll="getReplaceItemAll"
            @itemReplace="getReplaceItem" :item_selecteds="case_data_temporary.item_selecteds">
        </DialogSearchOrderProcesses>
        <DialogTitleOrderSO ref="dialogTitleOrderSo" :orders="orders"
            :customer_group_id="case_save_so.customer_group_id" @saveOrderSO="getSaveOrderSO"
            :order_lacks="case_data_temporary.order_lacks" :case_save_so="case_save_so"
            :is_show_modal_sync_sap="case_is_loading.show_modal_sync_sap">
        </DialogTitleOrderSO>
        <DialogListOrderProcessSO ref="dialogListOrderProcessSo"
            @fetchOrderProcessSODetail="getFetchOrderProcessSODetail"></DialogListOrderProcessSO>
        <DialogGetDataConvertFile :csv_data="case_data_temporary.error_csv_data"></DialogGetDataConvertFile>
        <TempExcelImport :is_show_hide="case_is_loading.show_hide_excel" :header_fields="header_fields"
            :title="title_excel" @convertFileExcel="getConvertFileExel"></TempExcelImport>
        <!-- Parent -->
        <ParentOrderSuffice ref="parentOrderSuffice" v-show="tab_value == 'order'" :row_orders="row_orders"
            :count_reset_filter="case_index.count_reset_filter" :orders="orders" :getDeleteRow="getDeleteRow"
            :material_donateds="material_donateds" :material_combos="material_combos"
            :api_handler="api_handler"
            :order_lacks="case_data_temporary.order_lacks" :filterOrders="filterOrders"
            :getOnChangeCategoryType="getOnChangeCategoryType" :tab_value="tab_value" :case_save_so="case_save_so"
            :is_loading_detect_sap_code="case_is_loading.detect_sap_code" @checkBoxRow="getCheckBoxRow"
            @sortingChanged="getSortingChanged" @createRow="getCreateRow" @handleItem="getHandleItem"
            @btnDuplicateRow="getBtnDuplicateRow" @pasteItem="getPasteItem" @btnCopyDeleteRow="getBtnCopyDeleteRow"
            @btnParseCreateRow="getBtnParseCreateRow" @btnCopy="getBtnCopy" @filterItems="getFilterItems"
            @emitResetFilter="getResetFilter" @editRow="getEditRow">
        </ParentOrderSuffice>
        <ParentOrderLack v-show="tab_value == 'order_lack'" :tab_value="tab_value"
            :order_lacks="case_data_temporary.order_lacks" @convertOrderLack="getConvertOrderLack"
            @countOrderLack="getCountOrderLack"></ParentOrderLack>
        <ParentOrderSynchronized :showModalSyncSAP="showModalSyncSAP" :case_save_so="case_save_so"
            :customer_group_id="case_save_so.customer_group_id" :customer_groups="case_data_temporary.customer_groups"
            :order_syncs="case_data_temporary.order_syncs" @processOrderSync="getProcessOrderSync"
            :order_syncs_selected="case_data_temporary.order_syncs_selected" @emitOrderSyncs="getEmitOrderSyncs"
            :warehouses="case_data_temporary.warehouses" @emitSelectedOrderSync="getSelectedOrderSync"
            :is_sap_sync="case_is_loading.sap_sync" @viewDetailOrderSyncs="getviewDetailOrderSyncs"
            @emitDataFetchWarehouse="getEmitDataFetchWarehouse" @emitDataWarehouse="getEmitDataWarehouse">
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
import DialogOrderCheckInventory from './dialogs/DialogOrderCheckInventory.vue';
import DialogOrderCheckPrice from './dialogs/DialogOrderCheckPrice.vue';
import DialogGetDataConvertFile from './dialogs/DialogGetDataConvertFile.vue';
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
        TempExcelImport,
        DialogOrderCheckInventory,
        DialogOrderCheckPrice,
        DialogGetDataConvertFile
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
                detect_material: 0,
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
                sap_sync: false,
                show_modal_sync_sap: false,
                edit_row: false,
                is_save_with_sync_sap: false,
            },
            case_data_temporary: {
                item_selecteds: [],
                order_lacks: [],
                copy: {},
                items: [],
                field: '',
                customer_groups: [],
                order_syncs: [],
                order_syncs_selected: [],
                warehouse_id: '',
                warehouses: [],
                filter_orders: [],
                error_csv_data: {},
                detect_materials: [],
            },
            header_fields: ['Makh Key', 'Mã Sap So', 'Barcode_cty', 'Masap', 'Tensp', 'Tên SKU', 'SL_sap', 'Dvt',
                'Km', 'Ghi_chu', 'Makh', 'Unit_barcode_description', 'Dvt_po', 'Po', 'Qty', 'Combo', 'Check tồn', 'Po_qty', 'SL chênh lệch',
                'Pur_price', 'Amount', 'QC', 'Đúng_QC', 'Ghi chú 1', 'Gia_cty', 'Level 2', 'Level 3', 'Level 4',
                'po_number', 'po_delivery_date'],
            api_order_process_so: '/api/sales-order',
            api_order_process_check_compliance: '/api/check-data/check-compliance',
            api_order_sync: '/api/so-header/sync-sale-order',
            api_check_inventory: '/api/check-data/check-inventory',
            api_check_price: '/api/check-data/check-price',
            api_check_customer_key: '/api/check-data/check-customer-partner',

        }
    },
    created() {
        this.getUrl();
        this.case_is_loading.created_conponent = true;
    },
    methods: {
       
        getSoSapNoteFromSyntax(data_item, key_array, separator) {
            let so_sap_note = "";
            key_array.forEach((key, index) => {
                let data_key = this.getDataKeyFromSyntaxKey(key);
                if (index == key_array.length - 1) {
                    so_sap_note += data_item[data_key];
                } else {
                    so_sap_note += data_item[data_key] + separator;
                }
            });
            return so_sap_note;
        },
        getDataKeyFromSyntaxKey(syntax_key) {
            let data_key = syntax_key;
            switch (syntax_key) {
                case 'CustomerNote':
                    data_key = 'note1';
                    break;
                case 'PoNumber':
                    data_key = 'po_number';
                    break;
                case 'CustomerKey':
                    data_key = 'customer_name';
                    break;
                case 'OrdUnit':
                    data_key = 'customer_sku_unit';
                    break;
                case 'ProductID':
                    data_key = 'customer_sku_code';
                    break;
                case 'Quantity1':
                    data_key = 'quantity1_po';
                    break;
                case 'Quantity2':
                    data_key = 'quantity2_po';
                    break;
                case 'ProductName':
                    data_key = 'customer_sku_name';
                    break;
                case 'ProductPrice':
                    data_key = 'price_po';
                    break;
                case 'ProductAmount':
                    data_key = 'amount_po';
                    break;
                case 'PoDeliveryDate':
                    data_key = 'po_delivery_date';
                    break;
                case 'SoSapNote':
                    data_key = 'so_sap_note';
                    break;
                case 'SapSoNumber':
                    data_key = 'sap_so_number';
                    break;

                default:
                    break;
            }
            return data_key;
        },
        async getEmitDetectCustomerKey() {
            let unique_customer_name = [...new Set(this.orders.map(item => item.customer_name))];
            await this.checkCustomerKey(unique_customer_name);
        },
        async checkCustomerKey(unique_customer_name) {
            try {
                let customer_keys = [];
                let is_customer_code_null = false;
                let body = {
                    'customer_group_id': this.case_save_so.customer_group_id,
                    'items': unique_customer_name.map(key => ({ customer_key: key }))

                };
                const { data, success, errors, message } = await this.api_handler.post(this.api_check_customer_key, {}, body);
                if (success) {
                    this.orders = this.orders.map(item => {
                        data.items.forEach(item_data => {
                            if (item.customer_name == item_data.customer_key) {
                                item.customer_code = item_data.customer_code;
                                item.level2 = item_data.customer_LV2;
                                item.level3 = item_data.customer_LV3;
                                item.level4 = item_data.customer_LV4;
                                item.note1 = item_data.customer_note;
                            }
                        });
                        if (data.so_sap_note_syntax) {
                            let key_array = data.so_sap_note_syntax.key_array;
                            let separator = data.so_sap_note_syntax.separator;
                            item.so_sap_note = this.getSoSapNoteFromSyntax(item, key_array, separator);
                            // console.log(item.so_sap_note);
                        }
                        return item;
                    });
                    data.items.map(item => {
                        if (item.customer_code == '' || item.customer_code == null) {
                            is_customer_code_null = true;
                            customer_keys.push(item.customer_key);
                        }
                    });
                    if (is_customer_code_null) {
                        this.$showMessage('warning', 'Cảnh báo', 'Không tìm thấy mã khách hàng cho khách hàng '
                         + customer_keys.map(item => item).join(', '));
                    } else {
                        this.$showMessage('success', 'Thành công', 'Kiểm tra mã khách hàng thành công');
                    }
                    this.refHeaderOrderProcesses();
                } else {
                    if (errors) {
                        errors.items.length == body.items.length ? this.$showMessage('error', 'Lỗi', errors.message + '<br>'
                            + errors.items.map(item => item.customer_key).join('<br>')) : this.$showMessage('warning', 'Cảnh báo', errors.message + '<br>'
                                + errors.items.map(item => item.customer_key).join('<br>'));
                    }
                    // this.$showMessage('error', 'Lỗi', errors.message + '<br>'
                    //     + errors.items.map(item => item.customer_key).join('<br>'));
                    if (message != '') {
                        this.$showMessage('error', 'Lỗi', message);
                    }
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            }
        },
        getEmitErrorConvertFile(errors) {
            this.case_data_temporary.error_csv_data = errors.csv_data;
            $('#modalGetConvertFile').modal('show');
        },
        getEditRow(is_edit_row) {
            this.case_is_loading.edit_row = is_edit_row;
        },
        getEmitDataWarehouse(warehouse_id) {
            this.case_data_temporary.order_syncs_selected.forEach(item => {
                item.warehouse_id = warehouse_id;
            });
        },
        getEmitDataFetchWarehouse(warehouses) {
            this.case_data_temporary.warehouses = warehouses;
        },
        async fetchCheckPrice(so_numbers, is_promotion) {
            try {
                this.case_is_loading.fetch_api = true;
                let body = {
                    'data': [
                        {
                            'so_numbers': so_numbers,
                            'is_promotion': is_promotion
                        }
                    ]
                };
                const { data, success, errors } = await this.api_handler.post(this.api_check_price, {}, body);
                if (success) {
                    this.getCheckPrice(data);
                    this.$showMessage('success', 'Thành công', 'Check giá thành công');
                }
                else {
                    this.$showMessage('error', 'Lỗi', errors.sap_error);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        async fetchCheckInventory() {
            try {
                this.case_is_loading.is_inventory = true;
                let body = {
                    'data': this.orders.map(item => {
                        return {
                            'materials': item.sku_sap_code,
                            'warehouse_id': this.case_data_temporary.warehouse_id,
                        }
                    })
                };
                const { data, success, errors } = await this.api_handler.post(this.api_check_inventory, {}, body);
                if (success) {
                    this.getInventory(data);
                    this.$showMessage('success', 'Thành công', 'Check tồn thành công');
                }
                else {
                    this.$showMessage('error', 'Lỗi', errors.sap_error);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.is_inventory = false;
            }
        },
        getModelSoNumbers(so_numbers, is_promotion) {
            let promotion = is_promotion ? 'X' : '';
            $('#modalCheckPrice').modal('hide');
            this.fetchCheckPrice(so_numbers, promotion);
            // this.getCheckPrice();

        },
        getModelWarehouseId(warehouse_id) {
            this.case_data_temporary.warehouse_id = warehouse_id;
            $('#modalCheckInventory').modal('hide');
            this.fetchCheckInventory();
        },
        getCheckInventory() {
            $('#modalCheckInventory').modal('show'); // component DialogOrderCheckInventory
        },
        getCheckPriceModal() {
            $('#modalCheckPrice').modal('show'); // component DialogOrderCheckPrice
        },
        getviewDetailOrderSyncs() {
            let ids = '';
            let url = '';
            ids = this.case_data_temporary.order_syncs_selected.map(item => item.so_header_id).join(',');
            url = window.location.origin + '/sap-syncs-detail' + '#' + ids + '?xem_chi_tiet';
            window.open(url, '_blank');
        },
        getSelectedOrderSync(selected) {
            this.case_data_temporary.order_syncs_selected = selected;
        },
        getEmitOrderSyncs(item_selecteds) {
            this.getProcessOrderSyncSelected(item_selecteds);
        },
        async getProcessOrderSync(shipping_id) {
            try {
                this.case_is_loading.sap_sync = true;
                // $('#modalOptionOrderSync').modal('hide'); // nghiệp vụ
                let body = {
                    'order_process_id': this.case_save_so.id,
                    'data': this.case_data_temporary.order_syncs_selected.map(item => {
                        return {
                            'id': item.so_header_id,
                            'warehouse_code': item.warehouse_id,
                            'so_sap_note': item.so_sap_note,
                            'Ship_cond': shipping_id
                        }
                    })
                };
                const { data, success } = await this.api_handler.post(this.api_order_sync, {}, body);
                if (success) {
                    let count_not_sync = 0;
                    let count_sync = 0;
                    data.forEach(item => {
                        this.case_data_temporary.order_syncs.forEach(order_sync => {
                            if (item.id == order_sync.so_header_id) {
                                order_sync.id = item.id;
                                order_sync.so_uid = item.so_number;
                                order_sync.sync_sap_status = item.sync_sap_status;
                                order_sync.noti_sync = item.message;
                            }
                        });
                        switch (item.sync_sap_status) {
                            case 0:
                                count_not_sync++;
                                break;
                            default:
                                count_sync++;
                                break;
                        }
                    });
                    // this.$showMessage('success', 'Thành công', 'Đồng bộ đơn hàng thành công');
                    if (count_not_sync > 0) {
                        this.$showMessage('warning', 'Cảnh báo', 'Có ' + count_not_sync + ' đơn hàng chưa đồng bộ');
                    }
                    if (count_sync > 0) {
                        this.$showMessage('success', 'Thành công', 'Có ' + count_sync + ' đơn hàng đã đồng bộ');
                    }
                    this.checkOrderSyncWithOrderProcess();
                } else {
                    this.$showMessage('error', 'Lỗi', 'Đồng bộ đơn hàng thất bại', errors.synchronized_error);
                }
            } catch (error) {
                if (error.response.data.errors.sap_error) {
                    this.$showMessage('error', 'Lỗi', error.response.data.errors.sap_error);
                }
                if (error.response.data.errors.not_config_user) {
                    this.$showMessage('error', 'Lỗi', error.response.data.errors.not_config_user);
                }
                if (error.response.data.errors.synchronized_error) {
                    this.$showMessage('warning', 'Cảnh báo', error.response.data.errors.synchronized_error);
                }
            } finally {
                this.case_is_loading.sap_sync = false;
                // $('#modalOptionOrderSync').modal('show');  // Nghiệp vụ
            }
        },
        async getProcessOrderSyncSelected(item_selecteds) {
            try {
                this.case_is_loading.sap_sync = true;
                let body = {
                    'order_process_id': this.case_save_so.id,
                    'data': item_selecteds.map(item => {
                        return {
                            'id': item.so_header_id,
                            'warehouse_code': item.warehouse_id,
                            'so_sap_note': item.so_sap_note
                        }
                    })
                };
                const { data, success } = await this.api_handler.post(this.api_order_sync, {}, body);
                if (success) {
                    data.forEach(item => {
                        this.case_data_temporary.order_syncs.forEach(order_sync => {
                            if (item.id == order_sync.so_header_id) {
                                order_sync.id = item.id;
                                order_sync.so_uid = item.so_number;
                                order_sync.sync_sap_status = item.sync_sap_status;
                                order_sync.noti_sync = item.message;
                            }
                        });
                    });
                    this.$showMessage('success', 'Thành công', 'Đồng bộ đơn hàng thành công');
                    this.checkOrderSyncWithOrderProcess();
                } else {
                    this.$showMessage('error', 'Lỗi', 'Đồng bộ đơn hàng thất bại', errors.synchronized_error);
                }
            } catch (error) {
                if (error.response.data.errors.sap_error) {
                    this.$showMessage('error', 'Lỗi', error.response.data.errors.sap_error);
                }
                if (error.response.data.errors.not_config_user) {
                    this.$showMessage('error', 'Lỗi', error.response.data.errors.not_config_user);
                }
                if (error.response.data.errors.synchronized_error) {
                    this.$showMessage('warning', 'Cảnh báo', error.response.data.errors.synchronized_error);
                }
            } finally {
                this.case_is_loading.sap_sync = false;
            }
        },
        checkOrderSyncWithOrderProcess() {
            let new_orders = [];

            this.orders.forEach((order_prcess) => {
                let should_keep = true;

                this.case_data_temporary.order_syncs.forEach((order_sync) => {
                    if (order_prcess.so_header_id == order_sync.id && order_sync.so_uid !== '') {
                        should_keep = false;
                    }
                });

                if (should_keep) {
                    new_orders.push(order_prcess);
                }
            });
            this.orders = new_orders;
            this.refHeaderOrderProcesses();
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
                return result;
            }
        },
        getConvertFileExel(data) {
            const result = this.sliceConvertHeader(data);
            this.orders = result.map((item, index) => {
                return {
                    order: index + 1,
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
        showModalSyncSAP(is_show_modal_sync_sap) {
            this.case_is_loading.show_modal_sync_sap = is_show_modal_sync_sap;
            this.showDialogTitleOrderSo();
            if (this.case_is_loading.is_save_with_sync_sap) {
                $('#modalOrderSync').modal('show');
                const unique = {};
                const result = this.filterOrders.filter(order => {
                    const key = order.sap_so_number + (order.promotive_name == null ? '' : order.promotive_name);
                    if (!unique[key]) {
                        unique[key] = true;
                        return true;
                    }
                    return false;
                }).map(order => {
                    return {
                        id: '',
                        so_uid: '',
                        sap_so_number: order.sap_so_number + (order.promotive_name == null ? '' : order.promotive_name),
                        customer_code: order.customer_code,
                        customer_name: order.customer_name,
                        po_delivery_date: order.po_delivery_date,
                        sync_sap_status: '',
                        noti_sync: '',
                        warehouse_id: '',
                        so_header_id: order.so_header_id,
                        so_sap_note: order.so_sap_note !== null ? order.so_sap_note + (order.promotive_name == null ? '' : order.promotive_name) : this.itemNote(order),
                    }
                });
                this.case_data_temporary.order_syncs = result;
            }

        },
        itemNote(item) {
            let note = (item.note1 == null ? '' : item.note1 + "_") + item.po_number + (item.promotive_name == null ? '' : item.promotive_name) + ((item.po_delivery_date == null || item.po_delivery_date == '' || item.po_delivery_date == undefined) ? '' : '_Ngày giao ' + this.$formatDate(item.po_delivery_date));
            return note;
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
            this.case_save_so.id = '';
            this.case_save_so.title = '';
            this.case_data_temporary.order_syncs_selected = [];
            this.case_data_temporary.order_lacks = [];
            this.orders = orders;
            this.case_index.count_reset_filter++;
            // this.getResetFilter();
        },
        getOnChangeCategoryType(index, item, order) {
            // this.$refs.headerOrderProcesses.updateMaterialCategoryTypeInOrder(index, item, order);
            if (item !== null) {
                if (this.filterOrders[index].promotive != item.name) {
                    this.filterOrders[index].promotive = item.name;
                    this.filterOrders[index].promotive_name = item.name;
                }
            } else {
                this.filterOrders[index].promotive = '';
                this.filterOrders[index].promotive_name = '';
            }
            requestAnimationFrame(() => {
                this.refHeaderOrderProcesses();
            }); // chờ render xong
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
        connectStringSapWithPromotion(sap_so_number, promotion) {
            return sap_so_number + promotion;
        },
        getListMaterialDetect(data) {
            this.material_saps = [...data];
            // group by theo sap_so_number và customer_sku_code
            let group = Object.groupBy(this.material_saps, ({ sap_so_number, customer_sku_code }) => sap_so_number + customer_sku_code);
            let group_entries = Object.entries(group);
            let exist = false;
            group_entries.forEach((group_entrie, index) => {
                if (group_entrie[1].length > 1) {
                    let first_group_entri = group_entrie[1][0];
                    const index_order_group = this.orders.findIndex((order) => order.customer_sku_code == first_group_entri.customer_sku_code &&
                                                                            order.sap_so_number == first_group_entri.sap_so_number);
                    if ((first_group_entri.customer_sku_code == this.orders[index_order_group]['customer_sku_code'] &&
                        first_group_entri.sap_so_number == this.orders[index_order_group]['sap_so_number'] &&
                        (this.orders[index_order_group]['sku_sap_code'] != '' || this.orders[index_order_group]['sku_sap_code'] != null) &&
                        first_group_entri.customer_sku_unit == this.orders[index_order_group]['customer_sku_unit']) ||
                        (first_group_entri.bar_code == this.orders[index_order_group]['customer_sku_code']
                        )) {
                        this.orders[index_order_group]['sku_sap_code'] = first_group_entri.sap_code;
                        this.orders[index_order_group]['sku_sap_name'] = first_group_entri.name;
                        this.orders[index_order_group]['sku_sap_unit'] = first_group_entri.unit_code;
                        this.orders[index_order_group]['barcode'] = first_group_entri.bar_code;
                        this.orders[index_order_group]['quantity3_sap'] = first_group_entri.quantity3_sap;
                    }
                    this.case_data_temporary.detect_materials.forEach(item => {
                        group_entrie[1].forEach(item_material => {
                            if (item.customer_sku_code == item_material.customer_sku_code &&
                                item.customer_sku_unit == item_material.customer_sku_unit &&
                                item.sap_so_number == item_material.sap_so_number &&
                                item.sap_code == item_material.sap_code &&
                                item.bar_code == item_material.bar_code) {
                                exist = true;
                            }
                        });
                    });
                    if (!exist) {
                        this.case_data_temporary.detect_materials.push(...group_entrie[1]);
                    }
                } else {
                    group_entrie[1].forEach(tmp => {
                        for (var i = 0; i < this.orders.length; i++) {
                            if ((tmp.customer_sku_code == this.orders[i].customer_sku_code &&
                                // this.orders[i]['sku_sap_code'] != '' &&
                                tmp.sap_so_number == this.orders[i].sap_so_number &&
                                tmp.customer_sku_unit == this.orders[i].customer_sku_unit) ||
                                (tmp.bar_code == this.orders[i].customer_sku_code)) {
                                this.orders[i]['sku_sap_code'] = tmp.sap_code;
                                this.orders[i]['sku_sap_name'] = tmp.name;
                                this.orders[i]['sku_sap_unit'] = tmp.unit_code;
                                this.orders[i]['barcode'] = tmp.bar_code;
                                this.orders[i]['quantity3_sap'] = tmp.quantity3_sap;
                            }
                        }
                    });
                }
            });
            for (let index = 0; index < this.case_data_temporary.detect_materials.length; index++) {
                const material = this.case_data_temporary.detect_materials[index];
                const index_order = this.orders.findIndex((order) => order.customer_sku_code == material.customer_sku_code && order.sap_so_number == material.sap_so_number);
                switch (index_order) {
                    default:
                            let exist = false;
                            this.orders.forEach((order, index_item) => {
                                if (order.customer_sku_code == material.customer_sku_code &&
                                    order.customer_sku_unit == material.customer_sku_unit &&
                                    order.sap_so_number == material.sap_so_number &&
                                    order.sku_sap_code == material.sap_code &&
                                    order.sku_sap_name == material.name &&
                                    order.barcode == material.bar_code) {
                                    exist = true;
                                }
                            })
                            if (!exist) {
                                let index_sap_code  = this.orders.findIndex((order) => order.sku_sap_code == material.sap_code );
                                if(index_sap_code == -1) {
                                    this.orders.push({
                                    order: this.orders.length + 1,
                                    id: '',
                                    customer_sku_code: material.customer_sku_code == undefined ? '' : material.customer_sku_code,
                                    customer_sku_name: this.orders[index_order]['customer_sku_name'],
                                    customer_sku_unit: material.customer_sku_unit == undefined ? '' : material.customer_sku_unit,
                                    company_price: this.orders[index_order]['company_price'],
                                    customer_code: this.orders[index_order]['customer_code'],
                                    level2: this.orders[index_order]['level2'],
                                    level3: this.orders[index_order]['level3'],
                                    level4: this.orders[index_order]['level4'],
                                    note1: this.orders[index_order]['note1'],
                                    note: this.orders[index_order]['note'],
                                    barcode: material.bar_code == undefined ? '' : material.bar_code,
                                    sap_so_number: this.orders[index_order]['sap_so_number'],
                                    sku_sap_code: material.sap_code == undefined ? '' : material.sap_code,
                                    sku_sap_name: material.name == undefined ? '' : material.name,
                                    sku_sap_unit: material.unit_code == undefined ? '' : material.unit_code,
                                    inventory_quantity: this.orders[index_order]['inventory_quantity'],
                                    amount_po: this.orders[index_order]['amount_po'],
                                    is_inventory: this.orders[index_order]['is_inventory'],
                                    is_promotive: this.orders[index_order]['is_promotive'],
                                    price_po: this.orders[index_order]['price_po'],
                                    promotive: this.orders[index_order]['promotive'],
                                    promotive_name: this.orders[index_order]['promotive_name'],
                                    quantity: this.orders[index_order]['quantity'],
                                    quantity1_po: this.orders[index_order]['quantity1_po'],
                                    quantity2_po: this.orders[index_order]['quantity2_po'],
                                    customer_name: this.orders[index_order]['customer_name'],
                                    variant_quantity: this.orders[index_order]['variant_quantity'],
                                    extra_offer: this.orders[index_order]['extra_offer'],
                                    promotion_category: this.orders[index_order]['promotion_category'],
                                    compliance: this.orders[index_order]['compliance'],
                                    is_compliant: this.orders[index_order]['is_compliant'],
                                    quantity3_sap: material.quantity3_sap == undefined ? '' : material.quantity3_sap,
                                    po_number: this.orders[index_order]['po_number'],
                                    po_delivery_date: this.orders[index_order]['po_delivery_date'],
                                    so_header_id: this.orders[index_order]['so_header_id'],
                                });
                                this.moveIndexOrder(this.orders, this.orders.length - 1, index_order + 1);
                                }

                            }
                        break;
                }
            }
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            this.refHeaderOrderProcesses();

        },
        moveIndexOrder(array, fromIndex, toIndex) {
            if (fromIndex < 0 || fromIndex >= array.length || toIndex < 0 || toIndex >= array.length) {
                console.error("Index không hợp lệ");
                return;
            }
            const element = array.splice(fromIndex, 1)[0];
            array.splice(toIndex, 0, element);
        },
        getInventory(data) {
            this.material_inventories = [...data];
            var orders = [...this.orders];
            this.material_inventories.forEach(tmp => {
                for (var i = 0; i < this.orders.length; i++) {
                    if (tmp['MATERIAL'] == this.orders[i]['sku_sap_code']) {
                        orders[i]['inventory_quantity'] = tmp['ATP_QUANTITY'];
                        orders[i]['variant_quantity'] = orders[i]['inventory_quantity'] - orders[i]['quantity1_po'] * orders[i]['quantity2_po'];
                        // orders[i]['is_inventory'] = orders[i]['quantity2_po'] < orders[i]['inventory_quantity'] ? true : false; // Đánh trạng thái hàng thiếu
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
                    if (tmp['MATERIAL'] !== "" && tmp['MATERIAL'] == this.orders[i]['sku_sap_code']) {
                        orders[i]['company_price'] = tmp['PRICE'];
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
                return true;
            }
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
                this.orders = this.orders.filter(item => item != item_selected);
            });
            this.refeshCheckBox();
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
            this.refHeaderOrderProcesses();

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
        getSaveOrderProcesses(is_show_modal_sync_sap) {
            this.case_is_loading.show_modal_sync_sap = is_show_modal_sync_sap;
            this.showDialogTitleOrderSo();
        },
        showDialogTitleOrderSo() {
            this.$refs.dialogTitleOrderSo.showDialogTitleOrderSo();
        },
        getSaveOrderSO(item, is_modal_sync_sap) {
            this.case_is_loading.is_save_with_sync_sap = is_modal_sync_sap;

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
                        so_sap_note: data_item.so_header.so_sap_note,
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
                        so_sap_note: data_item.so_header.so_sap_note,
                    });
                }

            });
            this.refHeaderOrderProcesses();
            if (this.case_is_loading.is_save_with_sync_sap) {
                this.showModalSyncSAP(this.case_is_loading.is_save_with_sync_sap);
                this.case_is_loading.is_save_with_sync_sap = false;
            }
        },
        refeshOrders() {
            this.orders = [];
            this.case_data_temporary.order_lacks = [];
            // this.case_data_temporary.items = [];
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
            this.filterOrders.splice(data.order - 1, 0, data);
            this.case_data_temporary.order_lacks.splice(index, 1);
            this.filterOrders.forEach((item, index) => {
                item.order = index + 1;
            });
            this.refHeaderOrderProcesses();
            console.log(data);
        },
        getSortingChanged(sort) {
            this.orders = [...sort];
            this.refHeaderOrderProcesses();
        },
        getExportExcel() {
            let data = this.orders.concat(this.case_data_temporary.order_lacks);
            let data_news = data.map((item, index) => {
                return {
                    'STT': index + 1,
                    'Makh Key': item.customer_name,
                    'Mã Sap So': item.sap_so_number,
                    'Barcode_cty': item.barcode,
                    'Masap': item.sku_sap_code,
                    'Tensp': item.customer_sku_name,
                    'SL_sap': item.quantity3_sap,
                    // 'Dvt': item.customer_sku_unit,
                    'Dvt': item.sku_sap_unit,
                    'Km': item.promotive,
                    'Ghi_chu': item.note1,
                    'Makh': item.customer_code,
                    'Unit_barcode': item.customer_sku_code,
                    'Unit_barcode_description': item.sku_sap_name,
                    // 'Dvt_po': item.sku_sap_unit,
                    'Dvt_po': item.customer_sku_unit,
                    'Po': item.po_number,
                    'Qty': item.quantity1_po,
                    'Combo': item.promotion_category,
                    'Check tồn': item.inventory_quantity,
                    'Po_qty': item.quantity2_po,
                    'SL chênh lệch': item.variant_quantity,
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
                so_header_id: '',
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
            new_order.so_header_id = ''; // reset so_header_id
            this.orders.splice(index + 1, 0, JSON.parse(JSON.stringify(new_order)));
            let start_index = this.startIndex(new_order.order);
            this.changeIndexOrder(start_index);
            this.refHeaderOrderProcesses();
        },
        getPasteItem(items, indexs, field, e) {
            if (indexs.length !== 0) {
                // e.preventDefault();
                indexs.forEach(index => {
                    items.forEach(item => {
                        // this.orders[index][field] = item.promotive;
                        // this.orders[index].promotive_name = item.promotive;filterOrders
                        this.filterOrders[index][field] = item.promotive;
                        this.filterOrders[index].promotive_name = item.promotive;
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
                so_sap_note: item.so_sap_note,
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
                so_header_id: item.so_header_id,
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
            item.so_header_id = '';
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
            new_order.so_header_id = ''; // reset so_header_id
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
            if (field == 'reset') {
                this.case_data_temporary.items = [];
            } else {
                this.case_data_temporary.items = items;
                this.case_data_temporary.field = field;
                this.case_is_loading.is_inventory = boolean;
                this.case_is_loading.created_conponent = false;
                let items_filter = this.orders.filter(order => {
                    return items.includes(order[field]);
                });
                this.case_data_temporary.filter_orders = items_filter;
            }

        },
        getResetFilter() {
            this.case_data_temporary.field = 'customer_sku_code';
            this.case_data_temporary.items = this.orders.map(item => item.customer_sku_code);
        },
        getChangeEventCompliance() {
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
            let items = [...this.orders];
            var news = [];
            if (this.case_is_loading.created_conponent) {
                news = this.orders;
            }
            if (this.case_data_temporary.items.length == 0) {
                news = this.orders;
            } else {
                if (!this.case_is_loading.edit_row) {
                    this.case_data_temporary.items.forEach(item => {
                        news.push(...items.filter(order => {
                            return order[this.case_data_temporary.field] == item;
                        }));
                    });
                    news.sort((a, b) => a.order - b.order);
                } else {
                    news = this.case_data_temporary.filter_orders;
                }
            }
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
