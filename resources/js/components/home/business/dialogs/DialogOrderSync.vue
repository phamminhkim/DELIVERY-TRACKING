<template>
    <div>
        <div class="modal fade" id="modalOrderSync" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Đồng bộ đơn hàng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title" class="font-weigh-bold">Nhóm khách hàng: <b class="text-danger">{{
                                findIdName() }}</b> </label>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    <button class="btn btn-sm btn-info  btn-group__border shadow-btn">Đồng bộ
                                        SAP</button>
                                    <button class="btn btn-sm btn-success btn-group__border shadow-btn">Đồng bộ SAP & Lưu</button>
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
                        <div class="form-group">
                            <TableOrderSync :fields="fields" :items="orders" :query="case_filter.query">
                            </TableOrderSync>
                            <PaginationTable :rows="row_items" :per_page="per_page" :page_options="page_options"
                                :current_page="current_page" @pageChange="getPageChange"
                                @perPageChange="getPerPageChange">
                            </PaginationTable>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TableOrderSync from '../tables/TableOrderSync.vue';
import PaginationTable from '../paginations/PaginationTable.vue';
export default {
    props: {
        showModalSyncSAP: Function,
        customer_group_id: Number,
        customer_groups: Array,
        orders: Array

    },
    components: {
        TableOrderSync,
        PaginationTable
    },
    data() {
        return {
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
                    key: 'sloc_code',
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
            per_page: 100,
            page_options: [10, 20, 50, 100, 200, 300, 500],
            current_page: 1,
        }
    },
    methods: {
        getPerPageChange(per_page) {
            this.per_page = per_page;
        },
        getPageChange(page) {
            this.current_page = page;
        },
        findIdName() {
            let customer = this.customer_groups.find(item => item.id === this.customer_group_id);
            return customer ? customer.name : '';
        }
    },
    computed: {
        row_items() {
            return this.orders.length;
        }
    }
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