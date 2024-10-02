<template>
    <div>
        <div>
            <DialogOrderProcessesSaveSO :order="order" @saveOrderSo="handleSaveOrderSo"
                @updateOrderSo="handleUpdateOrderSo" />
            <DialogOrderProcessesCheckInventory :is_loading="is_loading" :warehouses="warehouses"
                @checkInventory="handleCheckInventorySubmit" />
            <DialogOrderProcessesCheckPrice :is_loading="is_loading" @checkPrice="handleCheckPriceSubmit" />
            <DialogOrderProcessesSync :order_headers="order_headers" :api_handler="api_handler"
                @orderSyncSap="handleOrderSyncSapSubmit" @changeInputSetWarehouse="handleChangeInputSetWarehouse"
                :mapping_ships="mapping_ships" :case_check="case_check" @warehouseDefault="handeleWarehouseDefault"
                @changeInputSetShippingID="handleChangeInputSetShippingID" @emitWarehouseId="handleWarehouseId" />
            <DialogOrderProcessesLayout :columns="columns" />
        </div>
        <PROrderProcesses :columns="columns" :material_category_types="material_category_types"
            :filteredOrders="filteredOrders" :customer_groups="customer_groups" :order="order"
            :CountGrpSoNumber="CountGrpSoNumber" :CountOrderSoNumber="CountOrderSoNumber"
            :item_change_checked="item_change_checked" @convertFile="handleEmittedConvertFile" :file="file"
            :range="range" :update_column="update_status_function.column"
            :update_status_function="update_status_function" :position_order="position" :range_items="range.items"
            :hidden_columns="filterHiddenColumns" :processing_success="update_status_function.processing_file"
            :item_filters="item_filters" @inputCustomerGroupId="handleEmittedInputCustomerGroupId"
            :item_filter_backgrounds="item_filter_backgrounds" :item_filter_texts="item_filter_texts"
            :selected_indexs="selected_indexs" @inputExtractConfigID="handleEmittedInputExtractConfigID"
            @inputSearch="handleEmittedInputSearch" @emitRangeChanged="handleEmittedRangeChanged"
            @inputBackgroundColor="handleInputBackgroundColor" @inputTextColor="handleInputTextColor"
            @saveUpdateOrder="handleSaveUpdateOrder" @extractFilePDF="handleExtractFilePDF"
            @detectSapCodeOrder="handleDetectSapCodeOrder" @updateOrder="handleUpdateOrder"
            @emitDetectCustomerKey="handleDetectCustomerKey" @checkPromotion="handleCheckPromotion"
            @checkInventory="handleCheckInventory" @checkCompliance="handleCheckCompliance"
            @checkPrice="handleCheckPrice" @filterOrder="handleFilterOrder" @orderSyncSap="handleOrderSyncSap"
            @addRow="handleAddRow" @duplicateRow="handleDuplicateRow" @copyRow="handleCopyRow"
            @pasteRow="handlePasteRow" @deleteRow="handleDeleteRow" @rowSelectionChanged="handleRowSelectionChanged"
            @changeMaterial="handleChangeMaterial" @modalListOrder="handleModalListOrder" @cellEdited="handleCellEdited"
            @clipboardPasted="handleClipboardPasted" @exportExcel="handleExportExcel" @toggleColumn="handleToggleColumn"
            @hiddenColumns="handleHiddenColumns" @listLayout="handleListLayout"
            @toggleColumnShow="handleToggleColumnShow" @columnResized="handleColumnResized"
            @columnMoved="handleColumnMoved" @saveUpdateLayout="handleSaveUpdateLayout"
            @emitRangeRemoved="handleRangeRemoved" @headerClick="handleHeaderClick"
            @deleteRowSuccess="handleDeleteRowSuccess" @emitGetRangesData="handleGetRangesData"
            @popupOpened="handlePopupOpened" @deleteOrders="handleDeleteOrders"
            @itemChangeChecked="handleItemChangeChecked" @searchItem="handleSearchItem" @resetItem="handleResetItem"
            @deleteOrdersHistory="handleDeleteOrdersHistory" @rowDblClickMoveRow="handleRowDblClickMoveRow" />

        <DialogOrderProcessesLoadingConvertFile :file_length="processing_file.length"
            :processing_index="processing_file.index" :api_data_orders="api_data_orders" :orders="orders"
            :processing_files="processing_file.files" @processingSuccess="handleProcessingSuccess"
            @createDataOrders="handleCreateDataOrders" @moreDataOrders="handleMoreDataOrders" />

        <DialogSearchOrderProcesses @itemReplaceAll="getReplaceItemAll" @itemReplace="getReplaceItem"
            :item_selecteds="range.full_items" :update_status_function="update_status_function"
            :is_open_modal_search_order_processes="is_open_modal_search_order_processes" :orders="filteredOrders"
            @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses" />
        <DialogListOrderProcessSO @fetchOrderProcessSODetail="handleFetchOrderProcessSODetail"
            :update_status_function="update_status_function" />
        <DialogOrderProcessesLoadingSAP />
        <DialogOrderProcessesLoadingCustomerKey />
        <!-- <DialogGetDataConvertFile :csv_data="case_data_temporary.error_csv_data"></DialogGetDataConvertFile> -->
        <DialogOrderProcessesNotiCheckSkuSapCode :item_sku_sap_code_nulls="item_sku_sap_code_nulls"
            @submitOrderSyncSap="handleOrderSyncSapConfirm" />

        <DialogOrderProcessesHistoryDelete :orders_delete="orders_delete" :columns="columns"
            @sortOrdersHistory="handleSortOrdersHistory" :update_status_function="update_status_function"
            @restoreOrder="handleRestoreOrder" @restoreOrderAll="handleRestoreOrderAll" />
    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import PROrderProcesses from './parent/PROrderProcesses.vue';
import ApiHandler, { APIRequest } from '../../ApiHandler';
import DialogOrderProcessesSaveSO from './dialog/DialogOrderProcessesSaveSO.vue';
import DialogOrderProcessesLoadingConvertFile from './dialog/DialogOrderProcessesLoadingConvertFile.vue';
import DialogOrderProcessesCheckInventory from './dialog/DialogOrderProcessesCheckInventory.vue';
import DialogOrderProcessesCheckPrice from './dialog/DialogOrderProcessesCheckPrice.vue';
import DialogOrderProcessesSync from './dialog/DialogOrderProcessesSync.vue';
import DialogSearchOrderProcesses from '../../business/dialogs/DialogSearchOrderProcesses.vue';
import DialogListOrderProcessSO from '../../business/dialogs/DialogListOrderProcessSO.vue';
import DialogGetDataConvertFile from '../../business/dialogs/DialogGetDataConvertFile.vue';
import DialogOrderProcessesLayout from './dialog/DialogOrderProcessesLayout.vue';
import DialogOrderProcessesLoadingSAP from './dialog/DialogOrderProcessesLoadingSAP.vue';
import DialogOrderProcessesLoadingCustomerKey from './dialog/DialogOrderProcessesLoadingCustomerKey.vue';
import DialogOrderProcessesNotiCheckSkuSapCode from './dialog/DialogOrderProcessesNotiCheckSkuSapCode.vue';
import DialogOrderProcessesHistoryDelete from './dialog/DialogOrderProcessesHistoryDelete.vue';


export default {
    components: {
        PROrderProcesses,
        DialogOrderProcessesSaveSO,
        DialogOrderProcessesLoadingConvertFile,
        DialogOrderProcessesCheckInventory,
        DialogOrderProcessesCheckPrice,
        DialogOrderProcessesSync,
        DialogSearchOrderProcesses,
        DialogListOrderProcessSO,
        DialogGetDataConvertFile,
        DialogOrderProcessesLayout,
        DialogOrderProcessesLoadingSAP,
        DialogOrderProcessesLoadingCustomerKey,
        DialogOrderProcessesNotiCheckSkuSapCode,
        DialogOrderProcessesHistoryDelete,
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            is_open_modal_search_order_processes: false,
            is_modal_sync_sap: false,
            filter: {
                search: '',
                field: '',
                value: '',
                color: '',
            },
            case_data_temporary: {
                detect_materials: [],
            },
            case_check: {
                warehouse_id: null,
                shipping_id: '',
            },
            update_status_function: {
                set_data: 0,
                delete: 0,
                add_row: 0,
                color: 0,
                update_data: 0,
                processing_file: 0,
                column: 0,
                replace: 0,
                replace_all: 0,
                replace_data: 0,
                fetch_api_list_orders: 0,
                update_or_add: 0,
                delete_row: 0,
                order_history_delete: 0,
                update_move_row: 0,

            },
            position: {
                order: -1,
                order_end: -1,
                orders: [],
                orders_end: [],
            },
            error_csv_data: {},
            item_selecteds: [],
            item_change_checked: [],
            copy: {},
            api_data_orders: [],
            warehouses: [],
            material_saps: [],
            sap_codes: [],
            sap_materials: [],
            bar_codes: [],
            orders: [],
            customer_groups: [],
            material_inventories: [],
            theme_color_background: {},
            theme_color_text: {},
            material_prices: [],
            material_category_types: [],
            order_headers: [],
            warehouse_defaults: [],
            hidden_columns: [],
            columns: [],
            item_filters: [],
            item_filter_backgrounds: [],
            item_filter_texts: [],
            search_items: [],
            item_sku_sap_code_nulls: [],
            url_api: {
                order_process_so: '/api/sales-order',
                customer_groups: 'api/master/customer-groups',
                warehouses_company: '/api/master/warehouses/company-3000',
                sales_order: '/api/sales-order',
                convert_order: '/api/sales-order/convert-orders',
                sap_materials: 'api/master/sap-materials',
                detect_sap_code: '/api/check-data/check-material-sap',
                save_order_so: '/api/sales-order/save-so',
                material_category: '/api/master/material-category',
                check_customer_key: '/api/check-data/check-customer-partner',
                check_promotion: '/api/check-data/check-promotion',
                check_inventory: '/api/check-data/check-inventory',
                check_compliance: '/api/check-data/check-compliance',
                check_price: '/api/check-data/check-price',
                order_sync: '/api/so-header',
                user_field_table_version_2: '/api/master/user-field-table/v_2',
                check_sap_code: '/api/check-data/check-sap-code',

            },
            order: {
                id: -1,
                customer_group_id: -1,
                extract_config_id: -1,
                title: '',
                serial_number: '',
            },
            file: {
                id: 1,
                name: '',
                icon: '',
                color: '',
                note: '',
                type: ''
            },
            range: {
                indexs: [],
                items: [],
                full_items: [],
            },
            range_v2: {
                indexs: [],
                items: [],
                full_items: [],
            },
            processing_file: {
                length: 0,
                index: 0,
                files: [],
            },
            mapping_ships: [
                {
                    warehouse_code: '3114',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3101',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3001',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3002',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3003',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3005',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3008',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3108',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3010',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3113',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3011',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3117',
                    shipping_id: '01',
                },
                {
                    warehouse_code: '3004',
                    shipping_id: '03',
                },
                {
                    warehouse_code: '3003',
                    shipping_id: '03',
                },
                {
                    warehouse_code: '3009',
                    shipping_id: '03',
                },
                {
                    warehouse_code: '3007',
                    shipping_id: '03',
                },
                {
                    warehouse_code: '3012',
                    shipping_id: '03',
                },
                {
                    warehouse_code: '3017',
                    shipping_id: '03',
                },
            ],
            selected_indexs: [],
            orders_delete: [],
            histories_delete: [],
        }
    },
    async created() {
        // await this.fetchOrderProcessSODetail(258);
        await this.fetchUserFieldTable();
        await this.fetchCustomerGroup();
        await this.fetchWarehouse();
        await this.fetchMaterialCategoryType();
        // await this.fetchOrderHeader();
        await this.getUrl();
    },
    methods: {
        handleRestoreOrderAll() {
            if (this.histories_delete.length > 0) {

                this.histories_delete.forEach((item_history, index) => {
                    this.orders_delete.forEach((item_order, position) => {
                        if (item_order.order == item_history.order) {
                            this.orders.splice(item_order.order, 0, item_order);
                        } else {
                            this.orders.splice(item_order.order - 1, 0, item_order);
                        }
                    });
                });
            } else {
                this.orders_delete.forEach((item_order, position) => {
                    this.orders.splice(item_order.order - 1, 0, item_order);
                });
            }
            this.orders_delete = [];
            this.histories_delete = [];
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
            this.update_status_function.add_row++;
            this.update_status_function.order_history_delete++;
        },
        handleRestoreOrder(item_order, position) {
            if (this.histories_delete.length > 0) {
                let is_exist = false;
                for (let index = this.histories_delete.length - 1; index >= 0; index--) {
                    const item_history = this.histories_delete[index];
                    if (_.isEqual(item_order, item_history)) {
                        this.histories_delete.splice(index, 1);
                        is_exist = true;
                    }
                }
                if (!is_exist) {
                    this.orders.splice(item_order.order - 1, 0, item_order);
                } else {
                    this.orders.splice(item_order.order, 0, item_order);
                }

            } else {
                this.orders.splice(item_order.order - 1, 0, item_order);
            }
            this.orders_delete.splice(position - 1, 1);
            this.orders.forEach((item, index) => {
                item.order = index + 1;
            });
            this.update_status_function.add_row++;
            // this.update_status_function.set_data++;
            this.update_status_function.order_history_delete++;
        },
        handleSortOrdersHistory(sorters) {
            if (sorters.length > 0) {
                this.orders_delete = this.orders_delete.sort((a, b) => {
                    if (sorters[0].dir === 'asc') {
                        return a[sorters[0].field] > b[sorters[0].field] ? 1 : -1;
                    } else {
                        return a[sorters[0].field] < b[sorters[0].field] ? 1 : -1;
                    }
                });
            }
        },
        async fetchUserFieldTable(fields) {
            try {
                if (fields === undefined) {
                    fields = this.columns;
                }
                let body = {
                    user_id: window.Laravel.current_user.id,
                    tables: [
                        fields
                    ]
                };
                const { data, success, errors } = await this.api_handler.get(this.url_api.user_field_table_version_2, body);
                if (success) {
                    this.columns = data.field_table;
                    this.update_status_function.column++;
                }
                else {
                    this.$showMessage('error', 'Lỗi', errors.sap_error);
                }
            } catch (error) {

            }
        },
        async apiCheckPrice(so_numbers, is_promotion) {
            try {
                this.is_loading = true;
                let body = {
                    'data': [
                        {
                            'so_numbers': so_numbers,
                            'is_promotion': is_promotion
                        }
                    ]
                };
                const { data, success, errors } = await this.api_handler.post(this.url_api.check_price, {}, body);
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
                this.is_loading = false;
            }
        },
        async apiCheckInventory() {
            try {
                this.is_loading = true;
                let body = {
                    'data': this.filteredOrders.map(item => {
                        return {
                            'materials': item.sku_sap_code,
                            'warehouse_id': this.case_check.warehouse_id,
                        }
                    })
                };
                const { data, success, errors } = await this.api_handler.post(this.url_api.check_inventory, {}, body);
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
                this.is_loading = false;
            }
        },
        async apiCheckComplianceFromOrder() {
            try {
                this.is_loading = true;
                const { data, message, success } = await this.api_handler.post(this.url_api.check_compliance, {},
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
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.is_loading = false;
            }
        },
        uniqueOrderProcessId() {
            return [...new Set(this.filteredOrders.map(item => item.order_process_id))];
        },
        async fetchOrderHeader() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.url_api.order_sync, {
                    'order_process_id': this.uniqueOrderProcessId(),
                });
                if (Array.isArray(data)) {
                    this.order_headers = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchMaterialCategoryType() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.url_api.material_category);
                if (data.success) {
                    this.material_category_types = data.items;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchWarehouse() {
            let { data, success } = await this.api_handler.get(this.url_api.warehouses_company);
            if (success) {
                var options = [];
                let group_company_code = Object.groupBy(data, ({ company_code }) => company_code);
                for (const [key, value] of Object.entries(group_company_code)) {
                    var children = [];
                    for (let i = 0; i < value.length; i++) {
                        children.push({
                            id: value[i].id,
                            label: value[i].code + ' - ' + value[i].name,
                        });
                    }
                    options.push({
                        id: key,
                        label: key,
                        children: children,
                    });
                }
                this.warehouses = options;
            }
        },
        async fetchCustomerGroup() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.url_api.customer_groups);
                if (Array.isArray(data)) {
                    this.customer_groups = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchOrderProcessSODetail(id) {
            try {
                // this.case_is_loading.fetch_api = true;
                const { data, success } = await this.api_handler.get(this.url_api.order_process_so + '/' + id);
                if (success) {
                    console.log(data, 'data');
                    await this.getSaveOrderSO(data);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                // this.case_is_loading.fetch_api = false;
            }
        },
        async fetchSapMaterial() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.url_api.sap_materials, {
                    bar_codes: this.bar_codes,
                });
                if (Array.isArray(data)) {
                    this.sap_materials = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchSapCodeFromSkuCustomer() {
            try {
                this.is_loading = true;
                const { data, message } = await this.api_handler.post(this.url_api.detect_sap_code, {},
                    {
                        customer_group_id: this.order.customer_group_id,
                        items: this.orders,
                    }
                );
                //this.sap_codes =  data.original.mappingData;
                if (data) {
                    return data.items;
                    // this.$emit('getListMaterialDetect', data.items);
                    this.$showMessage('success', 'Thành công', 'Dò mã SAP thành công');
                } else {
                    return [];
                    this.$showMessage('error', 'Lỗi', message);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async getHeaderSaveOrderSo(item) {
            if (!_.isEmpty(item)) {
                this.order.id = item.id;
                this.order.title = item.title;
                this.order.customer_group_id = item.customer_group_id;
            }
        },
        async getSaveOrderSO(item) {
            // this.orders = [];
            this.getSaveOrderSOHeader(item);
            if (!_.isEmpty(item)) {
                this.refeshOrder();
                item.so_data_items.forEach(data_item => {
                    var variant_quantity = this.convertToNumber(data_item.inventory_quantity) - this.convertToNumber(data_item.quantity1_po) * this.convertToNumber(data_item.quantity2_po);
                    let convert_file_uid = data_item.so_header.convert_po_uid;

                    this.orders.push({
                        order: data_item.order,
                        id: data_item.id,
                        customer_sku_code: data_item.customer_sku_code,
                        customer_sku_name: data_item.customer_sku_name,
                        customer_sku_unit: data_item.customer_sku_unit,
                        // quantity: data_item.quantity,
                        quantity: '',
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
                        po: '',
                        po_delivery_date: data_item.so_header.po_delivery_date,
                        po_number: data_item.so_header.po_number,
                        sap_so_number: data_item.so_header.sap_so_number,
                        compliance: data_item.compliance,
                        is_compliant: data_item.is_compliant,
                        quantity3_sap: data_item.quantity3_sap,
                        so_header_id: data_item.so_header_id,
                        so_sap_note: data_item.so_header.so_sap_note,
                        difference: (data_item.company_price == null || data_item.company_price == '') ? 'price_difference' : (data_item.company_price == data_item.price_po ? 'price_equal' : 'price_difference'),
                        // difference: 'price_difference',
                        theme_color: this.setDataThemeColor(data_item.theme_color),
                        order_process_id: data_item.order_process_id,
                        so_header: this.setDataSoHeader(data_item.so_header),
                        convert_file_uid: convert_file_uid,

                    });
                });
            }
        },
        async apiConvertPDF(formData) {
            try {
                let file_response = await this.api_handler
                    .setHeaders({
                        'Content-Type': 'multipart/form-data',
                    })
                    .post(
                        this.url_api.convert_order,
                        {},
                        formData,
                    );
                return file_response;
            } catch (error) {
                if (!error.response.data.success) {
                    this.error_csv_data = error.response;
                    return error.response.data;
                }
                // ? this.$emit('emitErrorConvertFile', error.response.data.errors) : '';
                throw error;
            }

        },
        async UpdateSaleOrder(id) {
            try {
                this.is_loading = true;
                let body = {
                    customer_group_id: this.order.customer_group_id,
                    title: this.order.title,
                    order_data: this.orders,
                }
                const { data, success, errors } = await this.api_handler.put(this.url_api.sales_order + '/' + id, {}, body);
                if (success) {
                    // this.orders = data;
                    // await this.getSaveOrderSO(data);
                    await this.fetchOrderProcessSODetail(id);
                    await this.fetchOrderHeader();
                    $('#DialogOrderProcessesSaveSO').modal('hide');
                    this.$showMessage('success', 'Thành công', 'Cập nhật đơn hàng thành công');
                    if (this.is_modal_sync_sap) {
                        $('#DialogOrderProcessesSync').modal('show');
                    }
                }
            } catch (error) {
                // this.$showMessage('error', 'Lỗi', error);
                let errors = error.response.data.errors;
                console.log(errors);
                this.$showMessage('error', 'Lỗi', errors.sync_all_data);
                this.$showMessage('error', 'Lỗi', errors.map(item => item).join('<br>'));
            } finally {
                this.is_loading = false;
            }
        },
        async saveSaleOrderSO() {
            try {
                let body = {
                    title: this.order.title,
                    customer_group_id: this.order.customer_group_id,
                    order_data: this.orders,
                };
                let { data, success, message } = await this.api_handler.post(this.url_api.save_order_so, {}, body)
                if (success) {
                    // this.getHeaderSaveOrderSo(data);
                    await this.getSaveOrderSO(data);
                    await this.fetchOrderProcessSODetail(data.id);
                    await this.fetchOrderHeader();
                    this.$showMessage('success', 'Lưu đơn hàng thành công');
                    // this.$emit('saveOrderSO', data, this.is_show_modal_sync_sap);
                    // this.hideDialogTitleOrderSo();
                    $('#DialogOrderProcessesSaveSO').modal('hide');
                    if (this.is_modal_sync_sap) {
                        $('#DialogOrderProcessesSync').modal('show');
                    }

                }
            } catch (error) {
                let errors = error.response.data.errors;
                if (errors) {
                    this.$showMessage('error', 'Thêm thất bại', errors.sync_all_data);
                }
            } finally {
                this.is_loading = false;
            }
        },
        async apiCheckPromotion() {
            const filter = {
                customer_group_id: this.order.customer_group_id,
                items: this.getCheckPromotion(this.filteredOrders)
            }
            try {
                const { data, success, message } = await this.api_handler.post(this.url_api.check_promotion, {}, filter);
                if (data) {
                    await this.getValuePromotionCategory(data.items);
                    this.$showMessage('success', 'Thành công', 'Check khuyến mãi thành công');
                } else {
                    this.$showMessage('error', 'Lỗi', message);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                // this.case_is_loading.fetch_api = false;
            }
        },
        async handleCheckPromotion() {
            let check = await this.apiCheckPromotion();
            this.update_status_function.update_data++;
        },
        async handleDetectCustomerKey() {
            let unique_customer_name = [...new Set(this.filteredOrders.map(item => item.customer_name))];
            await this.checkCustomerKey(unique_customer_name);
            this.update_status_function.update_data++;


        },
        async checkCustomerKey(unique_customer_name) {
            try {

                let customer_keys = [];
                let is_customer_code_null = false;
                let body = {
                    'customer_group_id': this.order.customer_group_id,
                    'items': unique_customer_name.map(key => ({ customer_key: key }))
                };
                const { data, success, errors, message } = await this.api_handler.post(this.url_api.check_customer_key, {}, body);
                if (success) {
                    this.filteredOrders.map(item => {
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
                            let separators = data.so_sap_note_syntax.separators;
                            let format_data = data.so_sap_note_syntax.format_data;
                            item.so_sap_note = this.getSoSapNoteFromSyntax(item, key_array, separators, format_data);
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
                    this.is_loading = false;
                } else {
                    if (errors) {
                        errors.items.length == body.items.length ? this.$showMessage('error', 'Lỗi', errors.message + '<br>'
                            + errors.items.map(item => item.customer_key).join('<br>')) : this.$showMessage('warning', 'Cảnh báo', errors.message + '<br>'
                                + errors.items.map(item => item.customer_key).join('<br>'));
                    }
                    if (message != '') {
                        this.$showMessage('error', 'Lỗi', message);
                    }
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            }
        },
        async handleExtractFilePDF(files) {
            $('#DialogOrderProcessesLoadingConvertFile').modal('show');
            this.refeshProcessingFile();
            this.processing_file.length = files.length;
            this.api_data_orders = [];
            for (let index = 0; index < files.length; index++) {
                const file = files[index];
                this.api_data_orders.push(await this.apiConvertPDF(this.appendFormData(file, this.order.extract_config_id)));
                this.api_data_orders[index].name = file.name;
                this.processing_file.index = index + 1;

            }
            // this.refeshOrder();
            // this.refeshOrderHeader();
            // for (let index = 0; index < this.api_data_orders.length; index++) {
            //     const data_order = this.api_data_orders[index];
            //     if (data_order.success) {
            //         await this.getConvertFilePDF(data_order);
            //     }
            // }
            // this.update_status_function.set_data++;
            this.$showMessage('info', 'Hoàn tất', 'Xử lý file hoàn tất');
            // await this.fetchSapMaterial(); 
            $('#DialogOrderProcessesConvertFile').modal('hide');

        },
        async handleDetectSapCodeOrder() {
            await $('#DialogOrderProcessesLoadingSAP').modal('show');
            this.sap_codes = [];
            setTimeout(async () => {
                this.filteredOrders.forEach(element => {
                    this.sap_codes.push({
                        customer_sku_code: element.customer_sku_code,
                        customer_sku_unit: element.customer_sku_unit,
                        quantity2_po: element.quantity2_po,
                        promotion: element.promotive_name,
                        sap_so_number: element.sap_so_number,
                    });
                });
                await this.getListMaterialDetect(await this.fetchSapCodeFromSkuCustomer());
                await this.apiCheckComplianceFromOrder();
                await $('#DialogOrderProcessesLoadingSAP').modal('hide');
                // await this.updateFuncSetData();
                await this.updateFuncSetData();
                // this.update_status_function.update_data++;
            }, 10);

        },
        async updateFuncSetData() {
            this.update_status_function.set_data++;
        },
        async updateFuncUpdateOrAdd() {
            this.update_status_function.update_or_add++;
        },
        isUndefined(value) {
            if (value === undefined) {
                return '';
            } else {
                return value;
            }
        },
        async getConvertFilePDF(file_response) {
            // let index_item = 1;
            for (let index = 0; index < file_response.data.length; index++) {
                let files = file_response.data[index].items;
                let convert_file_uid = file_response.data[index].convert_po_uid;
                for (let index_item = 0; index_item < files.length; index_item++) {
                    let item = files[index_item];
                    this.orders.push({
                        order: index_item,
                        id: '',
                        barcode: '',
                        sku_sap_code: this.isUndefined(item.SkuSapCode),
                        sku_sap_name: this.isUndefined(item.SkuSapName),
                        sku_sap_unit: this.isUndefined(item.SkuSapUnit),
                        promotive: '',
                        promotive_name: '',
                        promotion_category: '',
                        extra_offer: '',
                        customer_name: this.isUndefined(file_response.data[index].headers.CustomerKey),
                        note: file_response.data[index].headers.CustomerKey === undefined ? '' : file_response.data[index].headers.CustomerKey,
                        note1: file_response.data[index].headers.CustomerNote,
                        customer_sku_code: item.ProductID,
                        customer_sku_name: item.ProductName,
                        customer_sku_unit: item.OrdUnit,
                        quantity1_po: this.convertStringToNumber(item.Quantity1),
                        quantity2_po: this.convertStringToNumber(item.Quantity2),
                        price_po: this.convertStringToNumber(item.ProductPrice),
                        amount_po: this.convertStringToNumber(item.ProductAmount),
                        // amount_po: item.ProductAmount,
                        // this.calculatorAmount(item.ProductAmount),
                        customer_code: this.isUndefined(file_response.data[index].headers.CustomerCode),
                        company_price: '',
                        level2: this.isUndefined(file_response.data[index].headers.CustomerLevel2),
                        level3: this.isUndefined(file_response.data[index].headers.CustomerLevel3),
                        level4: this.isUndefined(file_response.data[index].headers.CustomerLevel4),
                        is_promotive: false,
                        is_inventory: false,
                        inventory_quantity: '',
                        sap_so_number: this.isUndefined(file_response.data[index].headers.SapSoNumber),
                        so_sap_note: this.isUndefined(file_response.data[index].headers.SoSapNote),
                        po_number: this.isUndefined(file_response.data[index].headers.PoNumber),
                        po_delivery_date: this.isUndefined(file_response.data[index].headers.PoDeliveryDate),
                        compliance: '',
                        is_compliant: null,
                        quantity3_sap: this.convertStringToNumber(item.SapQuantity),
                        difference: 'price_difference',
                        po: '',
                        so_header: this.setDataSoHeader(item.so_header),
                        theme_color: this.setDataThemeColor(null),
                        order_process_id: '',
                        variant_quantity: '',
                        convert_file_uid: convert_file_uid,

                    });
                    // index_item++;
                    this.bar_codes.push(item.ProductID);
                }
            }

        },
        async getValuePromotionCategory(items) {
            await items.forEach(item => {
                this.filteredOrders.forEach(order => {
                    if (order.sku_sap_code == item.sap_code) {
                        order.promotion_category = item.promotion_category;
                        order.is_promotive = true;
                        order.extra_offer = item.extra_offer;
                    }
                });
            });

        },
        async handleCheckInventorySubmit(warehouse_id) {
            this.case_check.warehouse_id = warehouse_id;
            await this.apiCheckInventory();
            $('#DialogOrderProcessesCheckInventory').modal('hide');
            this.update_status_function.update_data++;
        },
        async handleCheckPriceSubmit(so_numbers, is_promotion) {
            let promotion = is_promotion ? 'X' : '';
            await this.apiCheckPrice(so_numbers, promotion);
            this.update_status_function.update_data++;
            $('#DialogOrderProcessesCheckPrice').modal('hide');
        },
        getInventory(data) {
            this.material_inventories = [...data];
            var orders = [...this.filteredOrders];
            this.material_inventories.forEach(tmp => {
                for (var i = 0; i < this.filteredOrders.length; i++) {
                    if (tmp['MATERIAL'] == this.filteredOrders[i]['sku_sap_code']) {
                        orders[i]['inventory_quantity'] = tmp['ATP_QUANTITY'];
                        orders[i]['variant_quantity'] = orders[i]['inventory_quantity'] - orders[i]['quantity1_po'] * orders[i]['quantity2_po'];
                        // orders[i]['is_inventory'] = orders[i]['quantity2_po'] < orders[i]['inventory_quantity'] ? true : false; // Đánh trạng thái hàng thiếu
                        this.filteredOrders[i].theme_color.text.inventory_quantity = (orders[i]['variant_quantity'] <= 0 || orders[i]['inventory_quantity'] < orders[i]['quantity2_po']) ? '#FF0000' : '';
                    }
                }
            });
            this.filteredOrders = [...orders];
        },
        handleCheckInventory() {
            $('#DialogOrderProcessesCheckInventory').modal('show');
        },
        handleCheckPrice() {
            $('#DialogOrderProcessesCheckPrice').modal('show');
        },
        handleSaveOrderSo(data) {
            this.saveSaleOrderSO();
        },
        handleUpdateOrderSo() {
            this.UpdateSaleOrder(this.order.id);
        },
        handleEmittedConvertFile(file) {
            this.file = file;
        },
        handleEmittedInputCustomerGroupId(customer_group_id) {
            this.order.customer_group_id = customer_group_id;
        },
        handleEmittedInputExtractConfigID(extract_config_id) {
            this.order.extract_config_id = extract_config_id;
        },
        handleEmittedInputSearch(value) {
            this.filter.search = value;
        },
        handleInputBackgroundColor(data) {
            this.theme_color_background = data;
            let keys = [];
            if (this.range_v2.items.length == 1) {
                this.range_v2.items.forEach((item_ranges, index_item) => {
                    item_ranges.forEach((item, index) => {
                        keys = Object.keys(item);
                        keys.forEach(key => {
                            let index_order = this.range_v2.indexs[index_item][index] - 1;
                            this.filteredOrders[index_order].theme_color.background[key] = this.theme_color_background.color;
                        });
                    });
                });
            } else {
                this.range_v2.items.forEach((item_ranges, index_item) => {
                    item_ranges.forEach((item, index) => {
                        keys = Object.keys(item);
                        let index_order = this.range_v2.indexs[index_item][index] - 1;
                        keys.forEach(key => {
                            this.filteredOrders[index_order].theme_color.background[key] = this.theme_color_background.color;
                        });
                    });
                });
            }

            this.update_status_function.color++;
        },
        handleInputTextColor(data) {
            this.theme_color_text = data;
            let keys = [];
            if (this.range_v2.items.length == 1) {
                this.range_v2.items.forEach((item_ranges, index_item) => {
                    item_ranges.forEach((item, index) => {
                        keys = Object.keys(item);
                        keys.forEach(key => {
                            let index_order = this.range_v2.indexs[index_item][index] - 1;
                            this.filteredOrders[index_order].theme_color.text[key] = this.theme_color_text.color;
                        });
                    });
                });
            } else {
                this.range_v2.items.forEach((item_ranges, index_item) => {
                    item_ranges.forEach((item, index) => {
                        keys = Object.keys(item);
                        let index_order = this.range_v2.indexs[index_item][index] - 1;
                        keys.forEach(key => {
                            this.filteredOrders[index_order].theme_color.text[key] = this.theme_color_text.color;
                        });
                    });
                });
            }
            this.update_status_function.color++;
        },
        handleEmittedRangeChanged(range) {
            this.range.indexs = range.getRows().map(row => row.getPosition());
            this.range.full_items = range.getRows().map(row => row.getData());
            this.range.items = range.getData();
        },
        handleSaveUpdateOrder() {
            this.is_modal_sync_sap = false;
            $('#DialogOrderProcessesSaveSO').modal('show');
            // this.UpdateSaleOrder(202);
        },
        convertToString(value) {
            if (value == null || value == undefined) {
                return '';
            } else {
                return value.toString();
            }
        },
        handleFilterOrder(value, field, event) {
            this.filter.field = '';
            this.filter.value = '';
            this.filter.event = '';
            this.filter.event = this.convertToString(event);
            switch (this.filter.event) {
                case 'reset':
                    this.item_change_checked = [];
                    this.filter.field = '';
                    this.filter.value = '';
                    this.filter.search = '';
                    this.range_v2.items = [];
                    this.range_v2.indexs = [];
                    this.range_v2.full_items = [];

                    break;
                default:
                    this.filter.field = field;
                    break;
            }
            this.filter.value = value;
            // this.update_status_function.add_row++;
            // this.update_status_function.set_data++;
            // this.update_status_function.update_data++;

        },
        async apiCheckSkuSapCodeIsNull() {
            try {
                this.is_loading = true;
                let body = {
                    'items': this.orders
                };
                const { data, success, errors, message } = await this.api_handler.post(this.url_api.check_sap_code, {}, body);
                if (data.success) {
                    await this.traverseArraySkuSapCodeIsNull(data.items);
                    this.$showMessage('success', 'Thành công', 'Kiểm tra mã SAP trống thành công');
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async traverseArraySkuSapCodeIsNull(items) {
            let item_sku_sap_code_nulls = [];
            let sku_items = [...items];
            await sku_items.forEach((sku_item, index) => {
                if (sku_item.is_sap_code_valid == false) {
                    this.orders[index].theme_color.background.customer_sku_code = '#FFFF00';
                    this.orders[index].theme_color.text.customer_sku_code = '#FF0000';
                    item_sku_sap_code_nulls.push(this.orders[index]);
                }
            });
            this.item_sku_sap_code_nulls = item_sku_sap_code_nulls;
            if (this.item_sku_sap_code_nulls.length > 0) {
                $('#DialogOrderProcessesNotiCheckSkuSapCode').modal('show');
                await this.update_status_function.update_data++;
            } else {
                await this.handleOrderSyncSapConfirm();
            }
        },
        async handleOrderSyncSap() {
            await this.apiCheckSkuSapCodeIsNull();

            // this.is_modal_sync_sap = true;
            // if (this.order.id == -1) {
            //     $('#DialogOrderProcessesSaveSO').modal('show');
            // } else {
            //     await this.UpdateSaleOrder(this.order.id);
            //     $('#DialogOrderProcessesSync').modal('show');
            // }
        },
        async handleOrderSyncSapConfirm() {
            this.is_modal_sync_sap = true;
            if (this.order.id == -1) {
                $('#DialogOrderProcessesSaveSO').modal('show');
            } else {
                await this.UpdateSaleOrder(this.order.id);
                $('#DialogOrderProcessesSync').modal('show');
            }
        },
        appendFormData(pdf_files, config_id) {
            let formData = new FormData();
            formData.append('file[]', pdf_files);
            formData.append('config_id', config_id);
            return formData;
        },
        setDataThemeColor(theme_color) {
            if (theme_color) {
                return theme_color;
            } else {
                let set_theme_color = {
                    background: {
                        order: '',
                        id: '',
                        customer_sku_name: '',
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
                        is_inventory: '',
                        is_promotive: '',
                        price_po: '',
                        promotive: '',
                        promotive_name: '',
                        quantity1_po: '',
                        quantity2_po: '',
                        customer_name: '',
                        variant_quantity: '',
                        extra_offer: '',
                        promotion_category: '',
                        po_delivery_date: '',
                        po_number: '',
                        sap_so_number: '',
                        compliance: '',
                        is_compliant: '',
                        quantity3_sap: '',
                        so_header_id: '',
                        so_sap_note: '',
                    },
                    text: {
                        order: '',
                        id: '',
                        customer_sku_name: '',
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
                        is_inventory: '',
                        is_promotive: '',
                        price_po: '',
                        promotive: '',
                        promotive_name: '',
                        quantity1_po: '',
                        quantity2_po: '',
                        customer_name: '',
                        variant_quantity: '',
                        extra_offer: '',
                        promotion_category: '',
                        po_delivery_date: '',
                        po_number: '',
                        sap_so_number: '',
                        compliance: '',
                        is_compliant: '',
                        quantity3_sap: '',
                        so_header_id: '',
                        so_sap_note: '',
                    },
                }
                return set_theme_color;
            }

        },
        setDataSoHeader(so_header) {
            if (so_header) {
                return {
                    sync_sap_status: so_header.sync_sap_status,
                }
            } else {
                return null;
            }
        },
        async getListMaterialDetect(data) {
            this.orders = [...data];
            await this.$showMessage('success', 'Thành công', 'Dò mã SAP thành công');
            await this.updateOrder();
            // this.orders.forEach((order, index) => {
            //     order.order = index + 1;
            // });
        },
        moveIndexOrder(array, fromIndex, toIndex) {
            if (fromIndex < 0 || fromIndex >= array.length || toIndex < 0 || toIndex >= array.length) {
                console.error("Index không hợp lệ");
                return;
            }
            const element = array.splice(fromIndex, 1)[0];
            array.splice(toIndex, 0, element);
        },
        convertStringToNumber(string) {
            if (string === undefined || string === null) {
                return '';
            }
            return parseFloat(string);
        },
        handleUpdateOrder() {
            // this.UpdateSaleOrder(202); 
            // this.handleSaveUpdateOrder();
            this.is_modal_sync_sap = false;
            $('#DialogOrderProcessesSaveSO').modal('show');
        },
        handleProcessingSuccess() {
            this.update_status_function.processing_file++;
        },
        async handleCheckCompliance() {
            await this.apiCheckComplianceFromOrder();
            this.update_status_function.update_data++;
        },
        async handleSaveUpdateLayout() {
            await this.fetchUserFieldTable(this.columns);
            this.$showMessage('success', 'Thành công', 'Cập nhật layout thành công');
            // this.update_status_function.set_data++;
        },
        convertToNumber(value) {
            return Number(value);
        },
        refeshOrder() {
            this.orders = [];
        },
        refeshOrderHeader() {
            this.order.id = -1;
            this.order.title = '';
            // this.order.customer_group_id = '';
            this.order.serial_number = '';
        },
        refeshProcessingFile() {
            this.processing_file.length = 0;
            this.processing_file.index = 0;
        },
        getSoSapNoteFromSyntax(data_item, key_array, separators, format_data) {
            let so_sap_note = "";
            let so_sap_note_array = [];
            let verify_separators = [];
            key_array.forEach((key, index) => {
                let data_key = this.getDataKeyFromSyntaxKey(key);
                let data_value = data_item[data_key];
                // Duyệt format_data
                format_data.forEach((format_item) => {
                    if (format_item.key == key) {
                        if (format_item.type == 'date') {
                            data_value = data_value ? this.convertFormatDate(data_item[data_key], format_item.format) : data_value;
                            return;
                        }
                    }
                });
                if (data_value) {
                    so_sap_note_array.push(data_value);
                    if (index > 0) {
                        verify_separators.push(separators[index - 1]);
                    } else {
                        verify_separators.push('');
                    }
                }
            });
            // Duyệt so_sap_note và merge theo verify_separators
            for (let index = 0; index < so_sap_note_array.length; index++) {
                if (index == 0) {
                    so_sap_note += so_sap_note_array[index];
                } else {
                    so_sap_note += verify_separators[index] + so_sap_note_array[index];
                }
            }
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
        convertFormatDate(date, format) {
            // 'd.m.Y' sang '20.01.2024'
            // 'd-m-Y' sang '20-01-2024'
            // 'd/m/Y' sang '20/01/2024'
            date = new Date(date);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            let year = date.getFullYear();
            return format.replace('d', day).replace('m', month).replace('Y', year);
        },
        getCheckPromotion(orders) {
            const data_check_promotion =
                orders.map((item) => {
                    return {
                        sap_code: item.sku_sap_code,
                        bar_code: item.barcode,
                    }
                });
            return data_check_promotion;
        },
        paramsSearchCompliance() {
            let items = [];
            items = this.filteredOrders.reduce((arr, item) => {
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
        resetCompliance() {
            this.filteredOrders.forEach(order => {
                order.compliance = '';
                order.is_compliant = null;
            });
        },
        mappingCompliance(items) {
            if (items.length > 0) {
                items.forEach(item => {
                    this.filteredOrders.forEach(order => {
                        if (order.sku_sap_code == item.sap_code && item.quantity2_po == order.quantity2_po) {
                            order.compliance = item.compliance;
                            order.is_compliant = item.is_compliant;
                        }
                    });
                });
            }
        },
        getSaveOrderSOHeader(item) {
            this.order.id = item.id;
            this.order.title = item.title;
            this.order.customer_group_id = item.customer_group_id;
            this.order.serial_number = item.serial_number;
            this.update_status_function.update_data++;
        },
        getCheckPrice(data) {
            this.material_prices = [...data];
            var orders = [...this.filteredOrders];
            this.material_prices.forEach(tmp => {
                for (var i = 0; i < this.filteredOrders.length; i++) {
                    if (tmp['MATERIAL'] !== "" && tmp['MATERIAL'] == this.filteredOrders[i]['sku_sap_code']) {
                        orders[i]['company_price'] = tmp['PRICE'];
                        var value_check_difference = (orders[i]['company_price'] == null || orders[i]['company_price'] == '') ? 'price_difference' : (orders[i]['company_price'] == orders[i]['price_po']) ? 'price_equal' : 'price_difference';
                        orders[i]['difference'] = value_check_difference;
                        orders[i]['theme_color'].text.company_price = orders[i]['price_po'] == orders[i]['company_price'] ? '' : '#FF0000';
                        orders[i]['theme_color'].text.price_po = orders[i]['price_po'] == orders[i]['company_price'] ? '' : '#FF0000';

                    }
                }
            });
            this.filteredOrders = [...orders];
            this.update_status_function.update_data++;
        },
        // handleEditPromotion(value, postion){
        //     this.filteredOrders[postion - 1].promotion_category = value;
        //     this.filteredOrders[postion - 1].note1 = value;
        // },
        handleOrderSyncSapSubmit(items) {
            if (Array.isArray(items)) {
                items.forEach(item => {
                    this.order_headers.forEach(order_sync => {
                        if (item.id == order_sync.id) {
                            // order_sync.id = item.id;
                            order_sync.so_uid = item.so_number;
                            order_sync.sync_sap_status = item.sync_sap_status;
                            order_sync.noti_sync = item.message;
                        }
                    });
                });
                this.update_status_function.update_data++;
            }
        },
        handleChangeInputSetWarehouse(warehouse_id, selecteds) {
            this.getSetWarehouse(warehouse_id, selecteds);
            this.getSetMappingShipping(warehouse_id, selecteds);
            this.update_status_function.update_data++;
        },
        getSetWarehouse(warehouse_code, order_syncs_selected) {
            order_syncs_selected.forEach(item => {
                this.order_headers.forEach(order_sync => {
                    if (item.id == order_sync.id) {
                        order_sync.warehouse_id = warehouse_code;
                    }
                });
            });
            this.update_status_function.update_data++;
        },
        getSetMappingShipping(warehouse_id, order_syncs_selected) {
            let find_warehouse = this.wareshouses_defaults.find(warehouse => warehouse.id == warehouse_id);
            let warehouse_code = find_warehouse ? find_warehouse.code : '';
            this.mapping_ships.forEach(item => {
                if (item.warehouse_code == warehouse_code) {
                    this.case_check.shipping_id = item.shipping_id;
                }
            });
            order_syncs_selected.forEach(item => {
                this.order_headers.forEach(order_sync => {
                    if (item.id == order_sync.id) {
                        order_sync.shipping_id = this.case_check.shipping_id;
                    }
                });
            });

        },
        handeleWarehouseDefault(warehouse_defaults) {
            this.wareshouses_defaults = warehouse_defaults;
        },
        handleChangeInputSetShippingID(shipping_id, selecteds) {
            this.getSetShipping(shipping_id, selecteds)
        },
        handleWarehouseId(warehouse_id, id) {
            let find_warehouse = this.wareshouses_defaults.find(warehouse => warehouse.id == warehouse_id);
            let warehouse_code = find_warehouse ? find_warehouse.code : '';
            let shipping_id = '';
            this.mapping_ships.forEach(item => {
                if (item.warehouse_code == warehouse_code) {
                    shipping_id = item.shipping_id;
                }
            });
            this.order_headers.find(order_sync => order_sync.id == id).shipping_id = shipping_id;
        },
        getSetShipping(shipping_id, order_syncs_selected) {
            order_syncs_selected.forEach(item => {
                this.order_headers.forEach(order_sync => {
                    if (item.id == order_sync.id) {
                        order_sync.shipping_id = shipping_id;
                    }
                });
            });
        },
        handleAddRow(position, positions) {
            this.position.order = position;
            let indexs = [...positions];
            let uniques = [...new Set(indexs.flat())];
            uniques.sort((a, b) => b - a);
            uniques.forEach(index => {
                this.filteredOrders.splice(index - 1, 0, {
                    order: this.orders.length,
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
                    po_delivery_date: '',
                    po_number: '',
                    sap_so_number: '',
                    compliance: '',
                    is_compliant: '',
                    quantity3_sap: '',
                    so_header_id: '',
                    so_sap_note: '',
                    difference: '',
                    theme_color: this.setDataThemeColor(null),
                });
            });

            // this.range.indexs.forEach(index_range => {
            //     this.filteredOrders.splice(index_range - 1, 0, {
            //         order: this.orders.length,
            //         id: '',
            //         customer_sku_code: '',
            //         customer_sku_name: '',
            //         customer_sku_unit: '',
            //         quantity: '',
            //         company_price: '',
            //         customer_code: '',
            //         level2: '',
            //         level3: '',
            //         level4: '',
            //         note1: '',
            //         note: '',
            //         barcode: '',
            //         sku_sap_code: '',
            //         sku_sap_name: '',
            //         sku_sap_unit: '',
            //         inventory_quantity: '',
            //         amount_po: '',
            //         is_inventory: false,
            //         is_promotive: false,
            //         price_po: '',
            //         promotive: '',
            //         promotive_name: '',
            //         quantity1_po: '',
            //         quantity2_po: '',
            //         customer_name: '',
            //         variant_quantity: '',
            //         extra_offer: '',
            //         promotion_category: '',
            //         po_delivery_date: '',
            //         po_number: '',
            //         sap_so_number: '',
            //         compliance: '',
            //         is_compliant: '',
            //         quantity3_sap: '',
            //         so_header_id: '',
            //         so_sap_note: '',
            //         difference: '',
            //         theme_color: this.setDataThemeColor(null),
            //     });
            // });
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            this.update_status_function.add_row++;
            // this.update_status_function.set_data++;
        },
        handleDuplicateRow(position, data, positions) {
            this.position.order = position;
            let indexs = [...positions];
            let uniques = [...new Set(indexs.flat())];
            uniques.sort((a, b) => b - a);
            uniques.forEach(index => {
                let item_double = this.filteredOrders[index - 1];
                this.filteredOrders.splice(index - 1, 0, {
                    // order: item_double.order++,
                    order: this.orders.length,
                    id: '',
                    customer_sku_code: item_double.customer_sku_code,
                    customer_sku_name: item_double.customer_sku_name,
                    customer_sku_unit: item_double.customer_sku_unit,
                    quantity: item_double.quantity,
                    company_price: item_double.company_price,
                    customer_code: item_double.customer_code,
                    level2: item_double.level2,
                    level3: item_double.level3,
                    level4: item_double.level4,
                    note1: item_double.note1,
                    note: item_double.note,
                    barcode: item_double.barcode,
                    sku_sap_code: item_double.sku_sap_code,
                    sku_sap_name: item_double.sku_sap_name,
                    sku_sap_unit: item_double.sku_sap_unit,
                    inventory_quantity: item_double.inventory_quantity,
                    amount_po: item_double.amount_po,
                    is_inventory: item_double.is_inventory,
                    is_promotive: item_double.is_promotive,
                    price_po: item_double.price_po,
                    promotive: item_double.promotive,
                    promotive_name: item_double.promotive_name,
                    quantity1_po: item_double.quantity1_po,
                    quantity2_po: item_double.quantity2_po,
                    customer_name: item_double.customer_name,
                    variant_quantity: item_double.variant_quantity,
                    extra_offer: item_double.extra_offer,
                    promotion_category: item_double.promotion_category,
                    po_delivery_date: item_double.po_delivery_date,
                    po_number: item_double.po_number,
                    sap_so_number: item_double.sap_so_number,
                    compliance: item_double.compliance,
                    is_compliant: item_double.is_compliant,
                    quantity3_sap: item_double.quantity3_sap,
                    so_header_id: item_double.so_header_id,
                    so_sap_note: item_double.so_sap_note,
                    difference: item_double.difference,
                    // theme_color: item_double.theme_color,
                    theme_color: this.setDataThemeColor(null),

                });
            });
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            uniques.forEach(index => {
                let newItem = this.filteredOrders[index - 1];
                let is_exist = this.histories_delete.some(item => _.isEqual(item, newItem));
                if (is_exist) {
                    return;  
                } else {
                    this.histories_delete.push({
                        // 'event': 'duplicate',
                        ...newItem
                    });
                }


            });
            this.update_status_function.add_row++;
        },
        handleCopyRow(position, data) {
            // data.order = position + 1;
            let data_copy = { ...data };
            // data_copy.order = position + 1;
            this.copy = data_copy;
            // this.update_status_function.set_data++;
        },
        handlePasteRow(position) {
            this.filteredOrders.splice(position - 1, 0, { ...this.copy });
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            // this.update_status_function.set_data++;
            this.update_status_function.add_row++;
        },
        handleDeleteRow(positions, data) {
            // let indexs = [...this.range_v2.indexs];

            let indexs = [...positions];
            let uniques = [...new Set(indexs.flat())];
            uniques.sort((a, b) => b - a);
            uniques.forEach(index => {
                // this.orders_delete.push(this.filteredOrders[index - 1]);
                let item_dlt = this.filteredOrders.splice(index - 1, 1);
                this.orders_delete.push({
                    // 'event': 'delete',
                    ...item_dlt[0]
                });
                this.orders = this.orders.filter(order => order.order != item_dlt[0].order);
            });
            // Hiển thị mảng đã cập nhật
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            // this.update_status_function.set_data++;
            // this.update_status_function.update_data++;
            this.update_status_function.delete++;
            this.update_status_function.order_history_delete++;

        },
        handleChangeMaterial() {
            this.is_open_modal_search_order_processes = true;
            $('#form_search_order_processes').modal('show');
        },
        handleRowSelectionChanged(selected, is_check_or_uncheck, positions) {
            let indexs = [...positions];
            let uniques = [...new Set(indexs)];
            let filter_uniques = uniques.filter(item => item !== false);
            filter_uniques.sort((a, b) => b - a);
            this.selected_indexs = filter_uniques;
        },
        getReplaceItemAll(item_materials, barcode) {
            item_materials.forEach(item_material => {
                this.filteredOrders.forEach((order) => {
                    if (order.barcode == barcode) {
                        order.sku_sap_code = item_material.sap_code;
                        order.sku_sap_name = item_material.name;
                        order.sku_sap_unit = item_material.unit.unit_code;
                        order.barcode = item_material.bar_code;
                    }
                })
            });
            this.closeModalSearchOrderProcesses();
            this.item_selecteds = [];
            this.update_status_function.replace_all++;
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;
        },
        getReplaceItem(item_materials, order_index) {
            item_materials.forEach(item_material => {
                this.filteredOrders.forEach((order) => {
                    if (order.order == order_index) {
                        order.sku_sap_code = item_material.sap_code;
                        order.sku_sap_name = item_material.name;
                        order.sku_sap_unit = item_material.unit.unit_code;
                        order.barcode = item_material.bar_code;
                    }
                })
            });
            this.closeModalSearchOrderProcesses();
            this.item_selecteds = [];
            this.update_status_function.replace++;
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;

        },
        closeModalSearchOrderProcesses() {
            this.is_open_modal_search_order_processes = false;
            $('#form_search_order_processes').modal('hide');
        },
        handleModalListOrder() {
            this.update_status_function.fetch_api_list_orders++;
            $('#listOrderProcessSO').modal('show');
        },
        async getUrl() {
            const url = window.location.href;
            const id = url.split('#')[1];
            if (id) {
                await this.fetchOrderProcessSODetail(id);
                await this.fetchOrderHeader();
                this.update_status_function.set_data++;
            }
        },
        handleFetchOrderProcessSODetail(data) {
            this.getSaveOrderSO(data);
            this.update_status_function.set_data++;
        },
        handleCellEdited(cell) {
            // cell.getRow().getData(), cell.getRow().getPosition()
            const position = cell.getRow().getPosition();
            const data = cell.getRow().getData();
            const value_check = data.price_po == data.company_price ? 'price_equal' : 'price_difference';
            data.promotive_name = data.promotive;
            // data.note1 = data.promotive;
            data.is_promotive = true;
            // data.promotion_category = data.promotive;
            // data.difference = (data.company_price == null || data.company_price == '' || data.price_po == null || data.price_po == '') ? 'price_difference' : (data.company_price == data.price_po) ? 'price_equal' : 'price_difference';
            data.difference = value_check;
            data.theme_color.text.company_price = data.price_po == data.company_price ? '' : '#FF0000';
            data.theme_color.text.price_po = data.price_po == data.company_price ? '' : '#FF0000';
            this.filteredOrders[position - 1] = data;
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;
        },
        handleClipboardPasted(rows) {
            let positions = rows.map(row => row.getPosition());
            let data = rows.map(row => row.getData());
            positions.forEach((position, index) => {
                // check price
                const value_check = data[index].price_po == data[index].company_price ? 'price_equal' : 'price_difference';
                data[index].difference = value_check;
                data[index].theme_color.text.company_price = data[index].price_po == data[index].company_price ? '' : '#FF0000';
                data[index].theme_color.text.price_po = data[index].price_po == data[index].company_price ? '' : '#FF0000';
                this.filteredOrders[position - 1] = data[index];
            });
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;
        },
        handleExportExcel() {
            // let data = this.orders.concat(this.case_data_temporary.order_lacks);
            // cột là this.columns
            let headers = this.columns.map(col => {
                if (col.visible !== false) {
                    return col.title;
                }
            });
            // xóa headers có giá trị là undefined
            headers = headers.filter(header => header !== undefined);
            let header_datas = this.columns.map(col => {
                if (col.visible !== false) {
                    return col;
                }
            });
            header_datas = header_datas.filter(header => header !== undefined);
            // Xây dựng dữ liệu với các cột được sắp xếp theo thứ tự của this.columns
            let data_news = this.filteredOrders.map((item, index) => {
                return header_datas.reduce((acc, col) => {
                    acc[col.title] = item[col.field] || (col.field === 'STT' ? index + 1 : '');
                    return acc;
                }, {});
            });

            // Tạo sheet với headers
            var ws = XLSX.utils.json_to_sheet(data_news, { header: headers });
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
        s2ab(s) {
            const buf = new ArrayBuffer(s.length);
            const view = new Uint8Array(buf);
            for (let i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        },
        handleToggleColumn(column) {
            Object.keys(this.range.items[0]).forEach(key => {
                this.columns.forEach(column => {
                    if (key == column.field) {
                        column.visible = false;
                    }
                });
            });
        },
        handleHiddenColumns(hidden_columns) {
            // console.log('hiddenColumns:', hidden_columns.map(column => column.getField()));
            this.hidden_columns = hidden_columns;
        },
        handleListLayout() {
            $('#DialogOrderProcessesLayout').modal('show');
        },
        handleToggleColumnShow(column, field) {
            this.columns.forEach(column => {
                if (column.field == field) {
                    column.visible = true;
                }
            });
        },
        handleColumnResized(item) {
            this.columns.forEach(column => {
                if (column.field == item.getField()) {
                    column.width = item.getWidth();
                }
            });
        },
        handleColumnMoved(items) {
            items.forEach((item, index_item) => {
                if (item !== undefined) {
                    this.columns.forEach((column, index) => {
                        if (item == column.field) {
                            this.moveIndexOrder(this.columns, index, index_item);
                        }
                    });
                }
            });
        },
        async handleCreateDataOrders() {
            this.refeshOrder();
            this.refeshOrderHeader();
            this.refeshUpdateFunctionReplace();

            for (let index = 0; index < this.api_data_orders.length; index++) {
                const data_order = this.api_data_orders[index];
                if (data_order.success) {
                    await this.getConvertFilePDF(data_order);
                }
            }
            await this.updateOrder();
            this.update_status_function.set_data++;
            // this.$showMessage('success', 'Thành công', 'Tạo mới dữ liệu thành công');
        },
        async handleMoreDataOrders() {
            for (let index = 0; index < this.api_data_orders.length; index++) {
                const data_order = this.api_data_orders[index];
                if (data_order.success) {
                    await this.getConvertFilePDF(data_order);
                }
            }
            await this.updateOrder();
            this.update_status_function.set_data++;
            // this.$showMessage('success', 'Thành công', 'Thêm dữ liệu thành công');
        },
        async updateOrder() {
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
        },

        refeshUpdateFunctionReplace() {
            this.update_status_function.replace = 0;
            this.update_status_function.replace_all = 0;
        },
        refeshRange() {
            this.range.indexs = [];
            this.range.items = [];
            this.range.full_items = [];
        },
        getItemsFirstRange() {
            let first_items = this.filteredOrders.length - (this.filteredOrders.length - 1);
            this.range.indexs = [1];
            this.range.items = this.filteredOrders.map((item, index) => {
                if (index == (first_items - 1)) {
                    return item;
                }
            });
            this.range.full_items = this.filteredOrders.map((item, index) => {
                if (index == (first_items - 1)) {
                    return item;
                }
            })
        },
        handleRangeRemoved(range) {
            if (this.filteredOrders.length == 0) {
                this.refeshRange();
            } else {
                this.getItemsFirstRange();
            }
        },
        handleHeaderClick(column, getRanges) {
            let field = column.getField();
            this.range.indexs = column.getTable().getRows().map(row => row.getPosition());
            this.range.items = column.getTable().getRows().map(row => row.getData());
            this.range.full_items = column.getTable().getRows().map(row => row.getData());

            this.range_v2.indexs = getRanges.map(range => range.getRows().map(row => row.getPosition()));

            switch (field) {
                case 'rownum':
                    // this.range.indexs.sort((a, b) => b - a);
                    // this.range.indexs.forEach(index => {
                    //     // xóa dữ liệu
                    //     this.filteredOrders.splice(index - 1, 1);
                    // });
                    // xóa tất cả dữ liệu trong filteredOrders
                    console.log(this.range_v2.indexs)
                    // this.filteredOrders = [];

                    break;

                default:
                    break;
            }
        },
        handleGetRangesData(table, positiones) {
            let items = table.map(range => range);
            this.range_v2.indexs = positiones;
            this.range_v2.items = items.map(item => item);

        },
        toStringInNumber(value) {
            if (value == null) {
                return '';
            } else {
                return value.toString();
            }
        },
        handlePopupOpened(field) {

            // let uniques = [...new Set(this.orders.map(order => {
            //     return {
            //         name: order[field],
            //         promotive_name: order.promotive_name == null ? '' : order.promotive_name
            //     }
            // }))];
            // this.item_filters = uniques;
            let uniques = [];
            let unique_backgrounds = [];
            let unique_texts = [];
            let map = new Map();
            let map_bg = new Map();
            let map_txt = new Map();


            this.orders.forEach(order => {
                let key = `${order[field]}_${order.promotive || ''}`;
                if (!map.has(key)) {
                    map.set(key, true); // Set any value to the map
                    uniques.push({
                        name: order[field] === undefined ? '' : this.toStringInNumber(order[field]),
                        promotive_name: order.promotive == null ? '' : order.promotive
                    });
                }
                let key_background = `${order.theme_color.background[field]}`;
                if (!map_bg.has(key_background)) {
                    map_bg.set(key_background, true); // Set any value to the map
                    unique_backgrounds.push({
                        color: order.theme_color.background[field]
                    });
                }
                let key_text = `${order.theme_color.text[field]}`;
                if (!map_txt.has(key_text)) {
                    map_txt.set(key_text, true); // Set any value to the map
                    unique_texts.push({
                        color: order.theme_color.text[field]
                    });
                }
            });
            this.item_filters = uniques;
            this.item_filter_backgrounds = unique_backgrounds;
            this.item_filter_texts = unique_texts;
        },
        handleItemChangeChecked(items, is_checked) {
            this.item_change_checked = items;
        },
        groupFiltersByField() {
            if (this.item_change_checked.length > 0) {
                const groupedFilters = this.item_change_checked.reduce((acc, item) => {
                    if (!acc[item.field]) {
                        acc[item.field] = [];
                    }
                    acc[item.field].push(item.name);
                    return acc;
                }, {});

                return groupedFilters;
            } else {
                return [];
            }

        },
        handleSearchItem(column, event) {
            this.filter.event = event;
            this.filter.field = column;
            // this.filter.value = event;
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;
        },
        handleResetItem() {
            this.item_change_checked = [];
            this.filter.field = '';
            this.update_status_function.set_data++;
        },
        toLowerCase(value) {
            if (value == null) {
                return value;
            } else {
                return value.toLowerCase();
            }
        },
        isNull(value) {
            return value == null ? '' : value;
        },
        handleDeleteOrders() {
            this.selected_indexs.forEach(index => {
                // this.orders_delete.push(this.filteredOrders[index - 1]);
                let item_dlt = this.filteredOrders.splice(index - 1, 1);
                this.orders_delete.push({
                    // 'event': 'delete',
                    ...item_dlt[0]
                });
                this.orders = this.orders.filter(order => order.order != item_dlt[0].order);
            });
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            this.update_status_function.delete_row++;
            this.update_status_function.order_history_delete++;

        },
        handleDeleteRowSuccess() {
            this.selected_indexs = [];
            this.update_status_function.update_data++;
        },
        handleDeleteOrdersHistory() {
            $('#DialogOrderProcessesHistoryDelete').modal('show');
        },
        handleRowDblClickMoveRow(position, input) {
            // di chuyển vị trí mảng
            let item = this.filteredOrders.splice(position - 1, 1);
            item[0].order = input;
            this.filteredOrders.splice(input - 1, 0, item[0]);
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });

            // this.update_status_function.set_data++;
            this.update_status_function.update_move_row++;
            // this.update_status_function.update_data++;

        },
    },
    computed: {
        filteredOrders() {
            // Nếu không có giá trị tìm kiếm thì trả về toàn bộ dữ liệu
            if (this.filter.field == '') {
                return this.orders;
            } else {
                switch (this.filter.event) {
                    case 'reset':
                        return this.orders;
                    case 'theme_color_bg':
                        // filter màu background
                        return this.orders.filter(order => {
                            let text_value = order.theme_color?.background?.[this.filter.field] || '';
                            let filter_value = this.filter.value || '';

                            if (text_value === null || text_value === '') {
                                text_value = '';
                            }
                            if (filter_value === null || filter_value === '') {
                                return text_value === "" || text_value === null;
                            }

                            return text_value.toLowerCase().includes(filter_value.toLowerCase());
                        });
                    case 'theme_color_txt':
                        // filter màu  text
                        return this.orders.filter(order => {
                            let text_value = order.theme_color?.text?.[this.filter.field] || '';
                            let filter_value = this.filter.value || '';

                            if (text_value === null || text_value === '') {
                                text_value = '';
                            }
                            if (filter_value === null || filter_value === '') {
                                return text_value === "" || text_value === null;
                            }
                            return text_value.toLowerCase().includes(filter_value.toLowerCase());
                        });
                    case 'extra_offer':
                        // filter extra_offer
                        return this.orders.filter(order => {
                            let field_value = order.extra_offer || ''; // Giữ nguyên giá trị gốc của field_value
                            return field_value.toLowerCase().includes(this.filter.value.toLowerCase());
                        });
                    case 'promotion_category':
                        // filter promotion_category,
                        return this.orders.filter(order => {
                            let field_value = order.promotion_category || ''; // Giữ nguyên giá trị gốc của field_value
                            return field_value.toLowerCase().includes(this.filter.value.toLowerCase());
                        });
                    case 'difference':
                        // filter difference
                        return this.orders.filter(order => {
                            let field_value = order.difference || ''; // Giữ nguyên giá trị gốc của field_value
                            return field_value.toLowerCase().includes(this.filter.value.toLowerCase());
                        });
                    case 'search_item':
                        const groupedFilters = this.groupFiltersByField();
                        return this.orders.filter(order => {
                            return Object.keys(groupedFilters).every(field => {
                                return groupedFilters[field].some(name => {
                                    let field_value = order[field]; // Giữ nguyên giá trị gốc của field_value
                                    if (field_value === null || field_value === '') {
                                        field_value = ""; // Đặt giá trị mặc định là chuỗi rỗng nếu field_value là null
                                    }

                                    if (typeof field_value === 'number') {
                                        name = Number(name);
                                    } else if (typeof field_value === 'string') {
                                        name = String(name);
                                    }
                                    if (typeof field_value === 'string' && typeof name === 'string') {
                                        if (name === "") {
                                            return field_value === "";
                                        }
                                        return field_value.toLowerCase().includes(name.toLowerCase());
                                    }
                                    return field_value === name;
                                });
                            });
                        });
                    default:
                        return this.orders.filter(order => {
                            return order[this.filter.field].includes(this.filter.value.toLowerCase());
                        });
                        break;
                }

            }

        },
        CountGrpSoNumber() {
            const group_by_so_num = Object.groupBy(this.orders, ({ sap_so_number, promotive_name }) => sap_so_number + (promotive_name == null ? '' : promotive_name));
            return Object.keys(group_by_so_num).length
        },
        CountOrderSoNumber() {
            const group_by_so_num = Object.groupBy(this.orders, ({ sap_so_number }) => sap_so_number);
            return Object.keys(group_by_so_num).length
        },
        filterHiddenColumns() {
            return this.columns.filter(column => column.visible == false);
        },

    }
}
</script>
<style lang="scss" scoped></style>