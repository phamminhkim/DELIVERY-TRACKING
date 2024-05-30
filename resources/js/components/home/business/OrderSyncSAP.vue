<template>
    <div class="container-fluid bg-white p-2">
        <div v-if="!case_component.view_detail">
            <HeaderOrderSyncSAP></HeaderOrderSyncSAP>
            <div class="row mb-1">
                <div class="col-lg-6">
                    <div>
                        <button class="btn btn-sm btn-light text-info  btn-group__border">Đồng bộ
                            SAP</button>
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
            <TableOrderSync :fields="fields" :items="items" :query="case_filter.query"></TableOrderSync>
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
export default {
    components: {
        HeaderOrderSyncSAP,
        TableOrderSync,
        PaginationTable,
        TableOrderSyncDetail
    },
    data() {
        return {
            case_component: {
                view_detail: false,
            },
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
                    key: 'sap_so',
                    label: 'SAP SO num',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'so_key',
                    label: 'SO Key',
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
                    key: 'status',
                    label: 'Trạng thái',
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
                    key: 'status_sync',
                    label: 'TT Đồng bộ',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'noti_sync',
                    label: 'Thông báo',
                    sortable: true,
                    class: 'text-nowrap'
                },
            ],
            items: [
                {
                    id: 1,
                    index: 1,
                    sap_so: '123456',
                    so_key: 'SO123456',
                    customer_key: 'KH123456',
                    customer_name: 'Khách hàng 123456',
                    po_delivery_date: '2021-01-01',
                    status_sync: 'Đã đồng bộ',
                    noti_sync: 'Đã đồng bộ'
                },
                {
                    id: 2,
                    index: 2,
                    sap_so: '123456',
                    so_key: 'SO123456',
                    customer_key: 'KH123456',
                    customer_name: 'Khách hàng 123456',
                    po_delivery_date: '2021-01-01',
                    status_sync: 'Đã đồng bộ',
                    noti_sync: 'Đã đồng bộ'
                },
                {
                    id: 3,
                    index: 3,
                    sap_so: '123456',
                    so_key: 'SO123456',
                    customer_key: 'KH123456',
                    customer_name: 'Khách hàng 123456',
                    po_delivery_date: '2021-01-01',
                    status_sync: 'Đã đồng bộ',
                    noti_sync: 'Đã đồng bộ'
                },
            ],
            per_page: 100,
            page_options: [10, 20, 50, 100, 200, 300, 500],
            current_page: 1,
        }
    },
    created() {
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
        }
    },
    computed: {
        row_items() {
            return this.items.length;
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