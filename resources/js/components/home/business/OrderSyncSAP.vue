<template>
    <div class="container-fluid bg-white p-2">
        <div v-if="!case_component.view_detail">
            <HeaderOrderSyncSAP @emitFormFilterOrderSync="getFormFilterOrderSync"></HeaderOrderSyncSAP>
            <div class="row mb-1 align-items-end">
                <div class="col-lg-6">
                    <div style="position: relative;">
                        <!-- <button @click="showModalOptionOrderSync()" type="button"
                            class="btn btn-sm btn-primary  btn-group__border">
                            <i class="fas fa-project-diagram mr-2"></i>Tùy chọn</button>
                        <span class="badge badge-danger badge-sm mr-2" style="position: absolute;left: 90px;top: -7px;">{{ case_data_temporary.order_syncs_selected.length }}</span> -->
                        <button @click="checkProcessOrderSync()" type="button"
                            class="btn btn-sm btn-light text-info  btn-group__border">
                            <span class="badge badge-info badge-sm mr-2">{{
            this.case_data_temporary.order_syncs_selected.length }}</span>Đồng bộ
                            SAP</button>
                        <button @click="viewDetailOrderSyncs()" type="button"
                            class="btn btn-sm btn-light text-primary btn-group__border">
                            <span class="badge badge-primary badge-sm mr-2">{{
            this.case_data_temporary.order_syncs_selected.length }}</span>Xem chi tiết</button>
                        <button @click="exportExcelOrderSyncs()" type="button"
                            class="btn btn-sm btn-success btn-group__border">
                            <span class="badge badge-light badge-sm mr-2">{{
            this.case_data_temporary.order_syncs_selected.length }}</span>Xuất Excel</button>
                        <div class="row">
                            <div class="col-lg-6">
                                <treeselect placeholder="Chọn kho.." :multiple="false" :disable-branch-nodes="true"
                                    :show-count="true" @input="changeInputSetWarehouse()"
                                    v-model="case_model.warehouse_id" :options="case_data_temporary.warehouses" />
                            </div>
                            <div class="col-lg-6">
                                <select v-model="case_model.shipping_id" class="form-control" aria-placeholder="Shipping"
                                @change="changeInputSetShippingID()" >
                                    <option  value="">Chọn Shipping</option>
                                    <option v-for="item in case_data.shipping_datas" :value="item.id">
                                        {{ item.code }}
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input v-model="case_filter.query" type="text" class="form-control form-control-sm"
                            placeholder="Tìm kiếm...">
                    </div>
                </div>
            </div>
            <TableOrderSync :fields="fields" :items="case_data.order_syncs" :query="case_filter.query"
                :use_component="'OrderSyncSAP'" :current_page="current_page" :per_page="per_page"
                :shipping_datas="case_data.shipping_datas" :warehouses="case_data_temporary.warehouses"
                @emitSelectedOrderSync="getSelectedOrderSync">
            </TableOrderSync>
            <PaginationTable :rows="row_items" :per_page="per_page" :page_options="page_options"
                :current_page="current_page" @pageChange="getPageChange" @perPageChange="getPerPageChange">
            </PaginationTable>
        </div>
        <div v-else>
            <TableOrderSyncDetail @rollBackUrl="getRollBackUrl"></TableOrderSyncDetail>
        </div>
        <DialogOptionOrderSync :viewDetailOrderSyncs="viewDetailOrderSyncs"
            :length_item="case_data_temporary.order_syncs_selected.length" @emitSetWarehouse="getEmitSetWarehouse"
            @emitOrderSyncsOption="getEmitOrderSyncsOption">
        </DialogOptionOrderSync>
        <DialogOptionSetWarehouse :length_item="case_data_temporary.order_syncs_selected.length"
            :is_sap_sync="case_is_loading.sap_sync" :item_selecteds="case_data_temporary.order_syncs_selected"
            :use_component_syncs_sap="case_component.order_sync_sap" @emitSetWarehouse="getSetWarehouse"
            @emitOrderSyncs="getEmitOrderSyncs"></DialogOptionSetWarehouse>
    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import HeaderOrderSyncSAP from './headers/HeaderOrderSyncSAP.vue';
import TableOrderSync from './tables/TableOrderSync.vue';
import PaginationTable from './paginations/PaginationTable.vue';
import TableOrderSyncDetail from './tables/TableOrderSyncDetail.vue';
import ApiHandler, { APIRequest } from '../ApiHandler';
import DialogOptionOrderSync from './dialogs/DialogOptionOrderSync.vue';
import DialogOptionSetWarehouse from './dialogs/DialogOptionSetWarehouse.vue';
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
export default {
    components: {
        HeaderOrderSyncSAP,
        TableOrderSync,
        PaginationTable,
        TableOrderSyncDetail,
        DialogOptionOrderSync,
        DialogOptionSetWarehouse,
        Treeselect
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            case_is_loading: {
                sap_sync: false,
            },
            case_component: {
                view_detail: false,
                order_sync_sap: 'OrderSyncSAP',
            },
            case_filter: {
                query: '',
                from_date: '',
                to_date: '',
                so_uid: '',
                po_number: '',
                customer_name: '',
                customer_code: '',
                customer_group_ids: [],
            },
            case_data: {
                order_syncs: [],
                wareshouses_default: [],
                shipping_datas: [
                    {
                        id: '01',
                        code: 'HCM_Company Deliver',
                    },
                    {
                        id: '02',
                        code: 'HCM_Customer Pick Up',
                    },
                    {
                        id: '03',
                        code: 'HN_Company Deliver',
                    },
                    {
                        id: '04',
                        code: 'HN_Customer Pick Up',
                    },
                    {
                        id: '05',
                        code: 'DN_Company Deliver',
                    },
                    {
                        id: '06',
                        code: 'DN_Customer Pick Up',
                    },
                ],
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
            },
            case_data_temporary: {
                order_syncs_selected: [],
                warehouses: [],
            },
            case_api: {
                get_order_sync: '/api/so-header',
                api_order_sync: '/api/so-header/sync-sale-order',
                warehouse: 'api/master/warehouses/company-3000',
            },
            case_model: {
                warehouse_id: null,
                shipping_id: '',
            },
            fields: [
                {
                    key: 'select',
                    label: '',
                    class: 'text-nowrap'
                },
                {
                    key: 'index',
                    label: 'Stt',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'so_uid',
                    label: 'SAP SO num',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'sync_sap_status',
                    label: 'TT Đồng bộ',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'noti_sync',
                    label: 'Thông báo',
                    sortable: true,
                    class: 'text-nowrap text-danger',
                    tdClass: 'text-danger'
                },
                {
                    key: 'po_number',
                    label: 'PO num',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'po_delivery_date',
                    label: 'Ngày YC giao',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'sap_so_number',
                    label: 'SO Key',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'warehouse_code',
                    label: 'Kho',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'shipping_id',
                    label: 'Shipping',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'customer_code',
                    label: 'Mã KH',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_name',
                    label: 'Tên KH',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'order_process.created_by.name',
                    label: 'Người tạo',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'po_delivery_date',
                    label: 'Ngày giao',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'create_at',
                    label: 'Ngày tạo',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'update_at',
                    label: 'Ngày cập nhật',
                    sortable: true,
                    class: 'text-nowrap'
                },

            ],
            per_page: 15,
            page_options: [15, 30, 50, 100, 200, 300, 500],
            current_page: 1,

        }
    },
    created() {
        this.getProcessOrderSync();
        this.getUrl();
        this.fetchWarehouses();
    },
    methods: {
        getSetMappingShipping(warehouse_id) {
            let find_warehouse = this.case_data.wareshouses_default.find(warehouse => warehouse.id == warehouse_id);
            let warehouse_code = find_warehouse ? find_warehouse.code : '';
            this.case_data.mapping_ships.forEach(item => {
                if (item.warehouse_code == warehouse_code) {
                    this.case_model.shipping_id = item.shipping_id;
                } 
            });
        },
        changeInputSetWarehouse() {
            this.getSetWarehouse(this.case_model.warehouse_id, this.case_data_temporary.order_syncs_selected);
            this.getSetMappingShipping(this.case_model.warehouse_id);
        },
        changeInputSetShippingID(){
            this.getSetShipping(this.case_model.shipping_id, this.case_data_temporary.order_syncs_selected);
        },
        findWarehouse(warehouse_id) {
            if (!warehouse_id) {
                return '';
            } else {
                if (this.case_data.wareshouses_default.length !== 0) {
                    let warehouse = this.case_data.wareshouses_default.find(warehouse => warehouse.id == warehouse_id);
                    return warehouse ? warehouse.name : '';

                } else {
                    return '';
                }
            }
        },
        async fetchWarehouses() {
            let { data, success } = await this.api_handler.get(this.case_api.warehouse);
            if (success) {
                this.case_data.wareshouses_default = data;
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
                this.case_data_temporary.warehouses = options;
            }
        },
        getEmitOrderSyncsOption() {
            this.checkProcessOrderSync();
        },
        getEmitOrderSyncs(item_selecteds) {
            // this.case_data_temporary.order_syncs_selected = selecteds;
            this.checkProcessOrderSyncSetWarehouse(item_selecteds);
        },
        getEmitSetWarehouse() {
            $('#modalOptionOrderSync').modal('hide');
            $('#modalOptionSetWarehouse').modal('show');
        },
        getPerPageChange(per_page) {
            this.per_page = per_page;
        },
        getPageChange(page) {
            this.current_page = page;
        },
        getUrl() {
            const url = window.location.href;
            const id = url.split('#')[1];
            if (id) {
                // this.fetchOrderProcessSODetail(id);
                this.case_component.view_detail = true;
            }

        },
        getRollBackUrl(is_roolback) {
            this.case_component.view_detail = is_roolback;
        },
        async getProcessOrderSync() {
            try {
                this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.get(this.case_api.get_order_sync, {
                    'from_date': this.case_filter.from_date,
                    'to_date': this.case_filter.to_date,
                    'so_uid': this.case_filter.so_uid,
                    'po_number': this.case_filter.po_number,
                    'customer_name': this.case_filter.customer_name,
                    'customer_code': this.case_filter.customer_code,
                    'customer_group_ids': this.case_filter.customer_group_ids,
                });
                if (Array.isArray(data)) {
                    this.case_data.order_syncs = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        getSelectedOrderSync(selected) {
            // Chức năng đồng bộ SAP
            this.case_data_temporary.order_syncs_selected = selected;
        },
        viewDetailOrderSyncs() {
            let ids = '';
            let url = '';

            ids = this.case_data_temporary.order_syncs_selected.map(item => item.id).join(',');
            url = window.location.origin + '/sap-syncs-detail' + '#' + ids + '?xem_chi_tiet';
            window.open(url, '_blank');
        },
        showModalOptionOrderSync() {
            $('#modalOptionOrderSync').modal('show');
        },
        async checkProcessOrderSync() {
            try {
                this.case_is_loading.sap_sync = true;
                let body = {
                    // 'order_process_id': 107,
                    'data': this.case_data_temporary.order_syncs_selected.map(item => {
                        return {
                            'id': item.id,
                            'warehouse_code': item.warehouse_id,
                            'so_sap_note': item.so_sap_note,
                            'Ship_cond': item.shipping_id,
                        }
                    })
                };
                const { data, success, errors } = await this.api_handler.post(this.case_api.api_order_sync, {}, body);
                if (success) {
                    data.forEach(item => {
                        this.case_data.order_syncs.forEach(order_sync => {
                            if (item.id == order_sync.id) {
                                // order_sync.id = item.id;
                                order_sync.so_uid = item.so_number;
                                order_sync.sync_sap_status = item.sync_sap_status;
                                order_sync.noti_sync = item.message;
                            }
                        });
                    });
                    this.$showMessage('success', 'Thành công', 'Đồng bộ đơn hàng thành công');
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
        async checkProcessOrderSyncSetWarehouse(item_selecteds) {
            try {
                this.case_is_loading.sap_sync = true;
                let body = {
                    // 'order_process_id': 107,
                    'data': item_selecteds.map(item => {
                        return {
                            'id': item.id,
                            'warehouse_code': item.warehouse_id,
                            'so_sap_note': item.so_sap_note
                        }
                    })
                };
                const { data, success, errors } = await this.api_handler.post(this.case_api.api_order_sync, {}, body);
                if (success) {
                    data.forEach(item => {
                        this.case_data.order_syncs.forEach(order_sync => {
                            if (item.id == order_sync.id) {
                                // order_sync.id = item.id;
                                order_sync.so_uid = item.so_number;
                                order_sync.sync_sap_status = item.sync_sap_status;
                                order_sync.noti_sync = item.message;
                            }
                        });
                    });
                    this.$showMessage('success', 'Thành công', 'Đồng bộ đơn hàng thành công');
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
        getFormFilterOrderSync(data) {
            this.case_filter.from_date = data.from_date;
            this.case_filter.to_date = data.to_date;
            this.case_filter.so_uid = data.so_uid;
            this.case_filter.po_number = data.po_number;
            this.case_filter.customer_name = data.customer_name;
            this.case_filter.customer_code = data.customer_code;
            this.case_filter.customer_group_ids = data.customer_group_ids;
            this.getProcessOrderSync();
        },
        getSetWarehouse(warehouse_code, order_syncs_selected) {
            // tìm cách set warehouse_code cho order_syncs_selected
            order_syncs_selected.forEach(item => {
                this.case_data.order_syncs.forEach(order_sync => {
                    if (item.id == order_sync.id) {
                        order_sync.warehouse_id = warehouse_code;
                    }
                });
            });
        },
        getSetShipping(shipping_id, order_syncs_selected) {
            order_syncs_selected.forEach(item => {
                this.case_data.order_syncs.forEach(order_sync => {
                    if (item.id == order_sync.id) {
                        order_sync.shipping_id = shipping_id;
                    }
                });
            });
        },
        exportExcelOrderSyncs() {
            let data = this.case_data_temporary.order_syncs_selected;
            let data_news = data.map((item, index) => {
                return {
                    'STT': index + 1,
                    'SAP SO num': item.so_uid,
                    'TT Đồng bộ': item.sync_sap_status == 0 ? 'Chưa đồng bộ' : 'Đã đồng bộ',
                    // 'Thông báo': 'chưa có',
                    'PO num': item.po_number,
                    'Ngày YC giao': item.po_delivery_date,
                    'SO Key': item.sap_so_number,
                    'Kho': this.findWarehouse(item.warehouse_id),
                    'Makh': item.customer_code,
                    'Tên KH': item.customer_name,
                    'Người tạo': item.order_process.created_by.name,
                    'Ngày tạo': this.$formatDateStyleApartYMD(item.create_at),
                    'Ngày cập nhật': this.$formatDateStyleApartYMD(item.update_at),
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
            link.download = 'Danh_sách_đơn_hàng.xlsx';
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
    },
    computed: {
        row_items() {
            return this.case_data.order_syncs.length;
        }
    },
}
</script>
<style lang="scss" scoped>
.btn-group {
    &__border {
        font-weight: 500;
        border: 1px solid #f0f0f0;
    }
}
</style>
