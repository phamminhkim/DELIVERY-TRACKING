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
            @convertFile="handleEmittedConvertFile" :file="file" :range="range"
            :update_column="update_status_function.column" :update_status_function="update_status_function"
            :position_order="position" :range_items="range.items" :hidden_columns="filterHiddenColumns"
            :processing_success="update_status_function.processing_file"
            @inputCustomerGroupId="handleEmittedInputCustomerGroupId"
            @inputExtractConfigID="handleEmittedInputExtractConfigID" @inputSearch="handleEmittedInputSearch"
            @emitRangeChanged="handleEmittedRangeChanged" @inputBackgroundColor="handleInputBackgroundColor"
            @inputTextColor="handleInputTextColor" @saveUpdateOrder="handleSaveUpdateOrder"
            @extractFilePDF="handleExtractFilePDF" @detectSapCodeOrder="handleDetectSapCodeOrder"
            @updateOrder="handleUpdateOrder" @emitDetectCustomerKey="handleDetectCustomerKey"
            @checkPromotion="handleCheckPromotion" @checkInventory="handleCheckInventory"
            @checkCompliance="handleCheckCompliance" @checkPrice="handleCheckPrice" @filterOrder="handleFilterOrder"
            @orderSyncSap="handleOrderSyncSap" @addRow="handleAddRow" @duplicateRow="handleDuplicateRow"
            @copyRow="handleCopyRow" @pasteRow="handlePasteRow" @deleteRow="handleDeleteRow"
            @rowSelectionChanged="handleRowSelectionChanged" @changeMaterial="handleChangeMaterial"
            @modalListOrder="handleModalListOrder" @cellEdited="handleCellEdited"
            @clipboardPasted="handleClipboardPasted" @exportExcel="handleExportExcel" @toggleColumn="handleToggleColumn"
            @hiddenColumns="handleHiddenColumns" @listLayout="handleListLayout"
            @toggleColumnShow="handleToggleColumnShow" @columnResized="handleColumnResized"
            @columnMoved="handleColumnMoved" @saveUpdateLayout="handleSaveUpdateLayout"
            @emitRangeRemoved="handleRangeRemoved" @headerClick="handleHeaderClick"
            @emitGetRangesData="handleGetRangesData" />

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
        <!-- <DialogGetDataConvertFile :csv_data="case_data_temporary.error_csv_data"></DialogGetDataConvertFile> -->
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
                fetch_api_list_orders: 0,

            },
            position: {
                order: -1,
                order_end: -1,
            },
            error_csv_data: {},
            item_selecteds: [],
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
                        items: this.sap_codes,
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
                        difference: (data_item.company_price == null || data_item.company_price == '') ? '' : (data_item.company_price == data_item.price_po ? 'price_equal' : 'price_difference'),
                        // difference: 'price_difference',
                        theme_color: this.setDataThemeColor(data_item.theme_color),
                        order_process_id: data_item.order_process_id,
                        so_header: this.setDataSoHeader(data_item.so_header),
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
            await this.apiCheckPromotion();
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
                    this.filteredOrders = this.filteredOrders.map(item => {
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
            this.sap_codes = [];
            this.filteredOrders.forEach(element => {
                this.sap_codes.push({
                    customer_sku_code: element.customer_sku_code,
                    customer_sku_unit: element.customer_sku_unit,
                    quantity2_po: element.quantity2_po,
                    promotion: element.promotive_name,
                    sap_so_number: element.sap_so_number,
                });
            });
            this.getListMaterialDetect(await this.fetchSapCodeFromSkuCustomer());
            this.$showMessage('success', 'Thành công', 'Dò mã SAP thành công');
            await this.apiCheckComplianceFromOrder();
            this.update_status_function.set_data++;
        },
        isUndefined(value) {
            if (value === undefined) {
                return '';
            } else {
                return value;
            }
        },
        async getConvertFilePDF(file_response) {
            let item_index = 1;
            for (let index = 0; index < file_response.data.length; index++) {
                let files = file_response.data[index].items;
                for (let index_item = 0; index_item < files.length; index_item++) {
                    let item = files[index_item];
                    this.orders.push({
                        order: item_index,
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
                        difference: '',
                        po: '',
                        so_header: this.setDataSoHeader(item.so_header),
                        theme_color: this.setDataThemeColor(null),
                        order_process_id: '',
                        variant_quantity: '',

                    });
                    item_index++;
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
                        this.filteredOrders[i].theme_color.background.inventory_quantity = (orders[i]['variant_quantity'] == '' || orders[i]['variant_quantity'] == null || orders[i]['variant_quantity'] <= 0 || orders[i]['inventory_quantity'] < orders[i]['quantity2_po']) ? '#FF0000' : '';
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
        handleFilterOrder(value, field, event) {
            if (event !== undefined) {
                this.filter.event = event;
            }
            this.filter.value = value;
            this.filter.field = field;
            // this.update_status_function.add_row++;
            this.update_status_function.set_data++;
        },
        async handleOrderSyncSap() {
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
                        first_group_entri.customer_sku_unit == this.orders[index_order_group]['customer_sku_unit'])) {
                        //     ||
                        // (first_group_entri.bar_code == this.orders[index_order_group]['customer_sku_code']
                        // )
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
                                tmp.customer_sku_unit == this.orders[i].customer_sku_unit)) {
                                //     ||
                                // (tmp.bar_code == this.orders[i].customer_sku_code)
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
                            let index_sap_code = this.orders.findIndex((order) => order.sku_sap_code == material.sap_code);
                            if (index_sap_code == -1) {
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
                                    theme_color: this.setDataThemeColor(null),
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
                        orders[i]['difference'] = (orders[i]['company_price'] == null || orders[i]['company_price'] == '') ? '' : (orders[i]['company_price'] == orders[i]['price_po']) ? 'price_difference' : 'price_different';
                        orders[i]['theme_color'].text.company_price = orders[i]['pur_price'] == orders[i]['company_price'] ? '' : '#FF0000';
                        orders[i]['theme_color'].text.pur_price = orders[i]['pur_price'] == orders[i]['company_price'] ? '' : '#FF0000';

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
                console.log(items);
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
        handleAddRow(position) {
            this.position.order = position;
            this.range.indexs.forEach(index_range => {
                this.filteredOrders.splice(index_range - 1, 0, {
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
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            this.update_status_function.add_row++;
            // this.update_status_function.set_data++;
        },
        handleDuplicateRow(position, data) {
            // let data_copy = { ...data };
            this.range.full_items.forEach(item => {
                let data_copy = { ...item };
                this.filteredOrders.splice(item.order - 1, 0, { ...data_copy });
            });
            this.orders.forEach((order, index) => {
                order.order = index + 1;
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
        handleDeleteRow(position, data) {
            let indexs = [...this.range_v2.indexs];
            let uniques = [...new Set(indexs.flat())];
            uniques.sort((a, b) => b - a);
            uniques.forEach(index => {
                let item_dlt = this.filteredOrders.splice(index - 1, 1);
                this.orders = this.orders.filter(order => order.order != item_dlt[0].order);
            });
            // Hiển thị mảng đã cập nhật
            this.orders.forEach((order, index) => {
                order.order = index + 1;
            });
            // this.update_status_function.set_data++;
            // this.update_status_function.update_data++;
            this.update_status_function.delete++;

        },
        handleChangeMaterial() {
            this.is_open_modal_search_order_processes = true;
            $('#form_search_order_processes').modal('show');
        },
        handleRowSelectionChanged(selected, is_check_or_uncheck) {
            if (is_check_or_uncheck) {
                this.item_selecteds = [];
                this.item_selecteds.push(selected);
            } else {
                this.item_selecteds = this.item_selecteds.filter(item => item != selected);
            }
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
            data.promotive_name = data.promotive;
            data.note1 = data.promotive;
            data.is_promotive = true;
            // data.promotion_category = data.promotive;
            this.filteredOrders[position - 1] = data;
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;
        },
        handleClipboardPasted(rows) {
            let positions = rows.map(row => row.getPosition());
            let data = rows.map(row => row.getData());
            positions.forEach((position, index) => {
                this.filteredOrders[position - 1] = data[index];
            });
            // this.update_status_function.set_data++;
            this.update_status_function.update_data++;
        },
        handleExportExcel() {
            // let data = this.orders.concat(this.case_data_temporary.order_lacks);
            let data_news = this.orders.map((item, index) => {
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
            this.update_status_function.set_data++;
            // this.$showMessage('success', 'Thành công', 'Thêm dữ liệu thành công');
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
        handleHeaderClick(column) {
            let field = column.getField();
            this.range.indexs = column.getTable().getRows().map(row => row.getPosition());
            this.range.items = column.getTable().getRows().map(row => row.getData());
            this.range.full_items = column.getTable().getRows().map(row => row.getData());
            switch (field) {
                case 'rownum':
                    this.range.indexs.sort((a, b) => b - a);
                    this.range.indexs.forEach(index => {
                        // xóa dữ liệu
                        this.filteredOrders.splice(index - 1, 1);
                    });
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

    },
    computed: {
        filteredOrders() {
            // Nếu không có giá trị tìm kiếm thì trả về toàn bộ dữ liệu
            if (this.filter.field == '') {
                return this.orders;
            } else {
                switch (this.filter.event) {
                    case 'theme_color_bg':
                        // filter màu background
                        return this.orders.filter(order => {
                            return Object.values(order.theme_color.background).some(value =>
                                String(value).toLowerCase().includes(this.filter.value.toLowerCase())
                            );
                        });
                    case 'theme_color_txt':
                        // filter màu  text
                        return this.orders.filter(order => {
                            return Object.values(order.theme_color.text).some(value =>
                                String(value).toLowerCase().includes(this.filter.value.toLowerCase())
                            );
                        });
                    default:
                        return this.orders.filter(order => {
                            return order[this.filter.field].includes(this.filter.value.toLowerCase());
                        });
                }

            }
            // // Nếu có giá trị tìm kiếm thì trả về dữ liệu đã lọc
            // const searchTerm = this.filter.search.toLowerCase();
            //     return this.orders.filter(order => {
            //         // filter tất cả các trường có chứa giá trị tìm kiếm
            //         return Object.values(order).some(value =>
            //             String(value).toLowerCase().includes(searchTerm)
            //         );
            //     });

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