<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesSync" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable" :class="{
                'modal-xl': !is_loading,
            }">
                <div class="modal-content" v-show="!is_loading">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Đồng bộ đơn hàng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group custom-text">
                            <button @click="checkProcessOrderSync()" type="button"
                                class="btn btn-light text-primary btn-sm text-xs">
                                <span class="badge badge-sm badge-danger px-2 mr-2">{{ order_syncs_selected.length
                                    }}</span>Đồng bộ đơn hàng</button>
                            <button @click="viewDetailOrderSyncs()" type="button"
                                class="btn btn-sm text-xs btn-light text-primary btn-group__border">
                                <span class="badge badge-primary badge-sm mr-2">{{
                                order_syncs_selected.length }}</span>Xem chi tiết</button>
                              <button class="btn btn-sm btn-light px-2 text-xs text-info" size="sm" @click="selectAllRows()" >
                                <span class="badge badge-info badge-sm mr-2">{{
                                selected.length }}</span>Chọn tất cả</button>
                              <button class="btn btn-sm btn-light px-2 text-xs text-secondary" size="sm" @click="clearSelected()" >Hủy chọn</button>
                        </div>
                        <div class="row custom-text">
                            <div class="col-lg-4">
                                <treeselect placeholder="Chọn kho.." :multiple="false" :disable-branch-nodes="true"
                                    :show-count="true" @input="changeInputSetWarehouse()"
                                    v-model="case_check.warehouse_id" :options="warehouses" />
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control form-control-sm" v-model="case_check.shipping_id"
                                    aria-placeholder="Shipping" @change="changeInputSetShippingID()">
                                    <option value="">Chọn Shipping</option>
                                    <option v-for="item in shipping_datas" :value="item.id">
                                        {{ item.code }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-5">
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input v-model="case_filter.query" type="text" class="form-control form-control-sm"
                                        placeholder="Tìm kiếm...">
                                </div>
                            </div>
                        </div>
                        <div class="form-group custom-text ">
                            <TableOrderSync :fields="fields" :items="order_headers" :shipping_datas="shipping_datas"
                                :warehouses="warehouses" @emitSelectedOrderSync="getSelectedOrderSync"
                                @emitWarehouseId="emitWarehouseId"
                                :query="case_filter.query" :use_component="'OrderSyncSAP'"
                                :onRowSelected="onRowSelected" ref="tableOrderSync"
                                :class_modal_v2="'V2OrderProcesses'" />
                        </div>
                    </div>
                </div>
                <div v-show="is_loading" class="modal-content text-center modal-dialog-centered">
                    <div class="modal-body">
                        <div class="form-group text-center">
                            <p class="text-primary"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tiến hành đồng bộ
                                Sap</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import TableOrderSync from '../../tables/TableOrderSync.vue';
export default {
    props: {
        order_headers: { type: Array, default: () => [] },
        api_handler: { type: Object, default: () => { } },
        mapping_ships: { type: Array, default: () => [] },
        case_check: { type: Object, default: () => { } },
        // viết cho tôi
    },
    components: {
        Treeselect,
        TableOrderSync
    },
    data() {
        return {
            is_loading: false,
            case_filter: {
                query: '',
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
                    class: 'text-nowrap',
                    thClass: 'th-class-so_uid',
                    tdClass: 'th-class-so_uid bg-white'
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
                    key: 'so_sap_note',
                    label: 'SAP note',
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
                    key: 'selected',
                    label: '',
                    class: 'text-nowrap',
                    stickyColumn: true,
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
            ref: 'selectableTable',
            wareshouses_default: [],
            warehouses: [],
            order_syncs_selected: [],
            selected_order_syncs: [],
            selected: [],
            warehouse: 'api/master/warehouses/company-3000',
            api_order_sync: '/api/so-header/sync-sale-order',

        }
    },
    async created() {
        await this.fetchWarehouses();
    },

    methods: {
        async checkProcessOrderSync() {
            try {
                this.is_loading = true;
                let body = {
                    // 'order_process_id': 107,
                    'data': this.order_syncs_selected.map(item => {
                        return {
                            'id': item.id,
                            'warehouse_code': this.isUndefined(item.warehouse_id),
                            'so_sap_note': item.so_sap_note,
                            'Ship_cond': item.shipping_id,
                            'promotive_name': item.promotive_name,
                        }
                    })
                };
                const { data, success, errors, sync_count } = await this.api_handler.post(this.api_order_sync, {}, body);
                if (success) {
                    console.log(data);
                    this.$emit('orderSyncSap', data);
                    // data.forEach(item => {
                    //     this.case_data.order_syncs.forEach(order_sync => {
                    //         if (item.id == order_sync.id) {
                    //             // order_sync.id = item.id;
                    //             order_sync.so_uid = item.so_number;
                    //             order_sync.sync_sap_status = item.sync_sap_status;
                    //             order_sync.noti_sync = item.message;
                    //         }
                    //     });
                    // });
                    this.$showMessage('success', 'Thành công', 'Đồng bộ ' + sync_count + 'đơn hàng thành công');
                    let calculator_order_sync = this.order_syncs_selected.length - sync_count;
                    if (calculator_order_sync > 0) {
                        this.$showMessage('warning', 'Cảnh báo', 'Đồng bộ ' + calculator_order_sync + 'đơn hàng thất bại');
                    }
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
                this.is_loading = false;
            }
        },
        async fetchWarehouses() {
            let { data, success } = await this.api_handler.get(this.warehouse);
            if (success) {
                this.wareshouses_default = data;
                this.$emit('warehouseDefault', data);
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
        getSelectedOrderSync(selected) {
            // Chức năng đồng bộ SAP
            this.order_syncs_selected = selected;
        },
        viewDetailOrderSyncs() {
            let ids = '';
            let url = '';

            ids = this.order_syncs_selected.map(item => item.id).join(',');
            url = window.location.origin + '/sap-syncs-detail' + '#' + ids + '?xem_chi_tiet';
            window.open(url, '_blank');
        },
        changeInputSetWarehouse() {
            this.$emit('changeInputSetWarehouse', this.case_check.warehouse_id, this.order_syncs_selected, this.selected)
        },
        changeInputSetShippingID() {
            this.$emit('changeInputSetShippingID', this.case_check.shipping_id, this.order_syncs_selected, this.selected)
        },
        isUndefined(value) {
            if (value === undefined) {
                return null;
            } else {
                return value;
            }
        },
        emitWarehouseId(warehouse_id, id) {
            this.$emit('emitWarehouseId', warehouse_id, id);

        },
        // emitSelectedFilter(selected) {
        //     this.selected_order_syncs = selected;
        // },
        onRowSelected(items) {
            this.selected = items;
        },
        selectAllRows() {
            this.$refs.tableOrderSync.selectAllRows();
        },
        clearSelected() {
            this.$refs.tableOrderSync.clearSelected();
        },


    },

}
</script>
<style lang="scss" scoped>
.custom-text {
    font-size: 0.8em !important;
    /* Kích thước văn bản nhỏ hơn */
}
.overflow{
    overflow: auto;
    height: 38em;
}
::v-deep .th-class-so_uid {
    position: sticky !important;
    /* position: fixed; */
    left: 0 !important;
    z-index: 3 !important;
}
</style>
