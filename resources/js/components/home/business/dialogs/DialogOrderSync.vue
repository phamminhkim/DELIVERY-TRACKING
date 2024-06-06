<template>
    <div>
        <div class="modal fade" id="modalOrderSync" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog " :class="{
                'modal-xl': !is_sap_sync,
            }">
                <div class="modal-content" v-show="!is_sap_sync">
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
                                <div v-if="!is_sap_sync">
                                    <button @click="emitProcessOrderSync()" type="button"
                                        class="btn btn-sm btn-info  btn-group__border shadow-btn">Đồng bộ
                                        SAP</button>
                                        <button @click="emitViewDetailOrderSyncs()" type="button"
                                        class="btn btn-sm btn-info  btn-group__border shadow-btn">Xem chi tiết</button>
                                    <!-- <button class="btn btn-sm btn-success btn-group__border shadow-btn">Đồng bộ SAP & Lưu</button> -->
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
                            <TableOrderSync :fields="fields" :items="order_syncs" :query="case_filter.query"
                                @emitSelectedOrderSync="emitSelectedOrderSync">
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
                <div v-show="is_sap_sync" class="modal-content text-center">
                    <div class="modal-body">
                        <div class="form-group text-center">
                            <p class="text-primary"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tiến hành đồng bộ Sap</p>
                        </div>
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
        order_syncs: Array,
        case_save_so: Object,
        is_sap_sync: Boolean,

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
                    key: 'so_sap_note',
                    label: 'Ghi chú',
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
        },
        emitProcessOrderSync() {
            this.$emit('processOrderSync');
        },
        emitSelectedOrderSync(selected) {
            this.$emit('emitSelectedOrderSync', selected);
        },
        emitViewDetailOrderSyncs() {
            this.$emit('viewDetailOrderSyncs');
        },
    },
    computed: {
        row_items() {
            return this.order_syncs.length;
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