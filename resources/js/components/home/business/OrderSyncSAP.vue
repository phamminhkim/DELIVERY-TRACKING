<template>
    <div class="container-fluid bg-white p-2">
        <div v-if="!case_component.view_detail">
            <HeaderOrderSyncSAP></HeaderOrderSyncSAP>
            <div class="row mb-1">
                <div class="col-lg-6">
                    <div>
                        <button @click="checkProcessOrderSync()" type="button" class="btn btn-sm btn-light text-info  btn-group__border">
                            <span class="badge badge-info badge-sm mr-2">{{ this.case_data_temporary.order_syncs_selected.length }}</span>Đồng bộ
                            SAP</button>
                        <button @click="viewDetailOrderSyncs()" type="button"
                            class="btn btn-sm btn-light text-primary btn-group__border">
                            <span class="badge badge-primary badge-sm mr-2">{{ this.case_data_temporary.order_syncs_selected.length }}</span>Xem chi tiết</button>
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
            :use_component="'OrderSyncSAP'"
                :current_page="current_page" :per_page="per_page" @emitSelectedOrderSync="getSelectedOrderSync">
            </TableOrderSync>
            <PaginationTable :rows="row_items" :per_page="per_page" :page_options="page_options"
                :current_page="current_page" @pageChange="getPageChange" @perPageChange="getPerPageChange">
            </PaginationTable>
        </div>
        <div v-else>
            <TableOrderSyncDetail @rollBackUrl="getRollBackUrl"></TableOrderSyncDetail>
        </div>
    </div>
</template>
<script>
import HeaderOrderSyncSAP from './headers/HeaderOrderSyncSAP.vue';
import TableOrderSync from './tables/TableOrderSync.vue';
import PaginationTable from './paginations/PaginationTable.vue';
import TableOrderSyncDetail from './tables/TableOrderSyncDetail.vue';
import ApiHandler, { APIRequest } from '../ApiHandler';

export default {
    components: {
        HeaderOrderSyncSAP,
        TableOrderSync,
        PaginationTable,
        TableOrderSyncDetail
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            case_is_loading: {
                sap_sync: false,
            },
            case_component: {
                view_detail: false,
            },
            case_filter: {
                query: '',
            },
            case_data: {
                order_syncs: [],
            },
            case_data_temporary: {
                order_syncs_selected: []
            },
            case_api: {
                get_order_sync: '/api/so-header',
            api_order_sync: '/api/so-header/sync-sale-order',

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
                    key: 'sap_so_number',
                    label: 'SO Key',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'warehouse_code',
                    label: 'Mã Kho',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_key',
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
                    key: 'po_delivery_date',
                    label: 'Ngày giao',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'is_sync_sap',
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
            ],
            per_page: 100,
            page_options: [10, 20, 50, 100, 200, 300, 500],
            current_page: 1,

        }
    },
    created() {
        this.getProcessOrderSync();
        this.getUrl();
    },
    methods: {
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
                const { data } = await this.api_handler.get(this.case_api.get_order_sync, {});
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
            // this.case_data_temporary.order_syncs_selected.forEach((item, index) => {
            //     url = window.location.origin + '/sap-syncs-detail' + '#' + item.id + '?sap_so_number=' + item.sap_so_number;
            //     console.log({ url })
            // })
            ids = this.case_data_temporary.order_syncs_selected.map(item => item.id).join(',');
            url = window.location.origin + '/sap-syncs-detail' + '#' + ids + '?xem_chi_tiet';
            window.open(url, '_blank');
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
                            'so_sap_note': item.so_sap_note
                        }
                    })
                };
                const { data, success } = await this.api_handler.post(this.case_api.api_order_sync, {}, body);
                if (success) {
                    data.forEach(item => {
                        this.case_data.order_syncs.forEach(order_sync => {
                            if (item.id == order_sync.id) {
                                // order_sync.id = item.id;
                                order_sync.so_uid = item.so_number;
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
                this.case_is_loading.sap_sync = false;
            }
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