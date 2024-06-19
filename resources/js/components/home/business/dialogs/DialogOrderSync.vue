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
                        <div class="row align-items-end">
                            <div class="col-lg-6">
                                <!-- <div v-if="!is_sap_sync" style="position: relative;">
                                    <button @click="showModalOptionOrderSync()" type="button"
                                        class="btn btn-sm btn-primary  btn-group__border">
                                        <i class="fas fa-project-diagram mr-2"></i>Tùy chọn</button>
                                    <span class="badge badge-danger badge-sm mr-2"
                                        style="position: absolute;left: 90px;top: -7px;">{{
                order_syncs_selected.length }}</span>
                                </div> -->
                                <div v-if="!is_sap_sync">
                                    <button @click="emitProcessOrderSync()" type="button"
                                        class="btn btn-sm btn-light text-info  btn-group__border shadow-btn">
                                        <span class="badge badge-info badge-sm mr-2">
                                            {{ order_syncs_selected.length }}</span>Đồng bộ SAP</button>
                                    <button @click="emitViewDetailOrderSyncs()" type="button"
                                        class="btn btn-sm btn-light text-primary  btn-group__border shadow-btn">
                                        <span class="badge badge-primary badge-sm mr-2">
                                            {{ order_syncs_selected.length }}</span>Xem chi tiết</button>
                                    <treeselect placeholder="Chọn kho.." :multiple="false" :disable-branch-nodes="true"
                                        :show-count="true" @input="emitDataWarehouse()" :searchable="true"
                                        v-model="case_model.warehouse_id" :options="case_data_temporary.warehouses" />
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
                                :warehouses="warehouses" @emitSelectedOrderSync="emitSelectedOrderSync">
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
                <div v-show="is_sap_sync" class="modal-content text-center modal-dialog-centered">
                    <div class="modal-body">
                        <div class="form-group text-center">
                            <p class="text-primary"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tiến hành đồng bộ
                                Sap</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <DialogOptionOrderSync :viewDetailOrderSyncs="emitViewDetailOrderSyncs"
            :length_item="order_syncs_selected.length" @emitSetWarehouse="getEmitSetWarehouse"
            @emitOrderSyncsOption="getEmitOrderSyncsOption">
        </DialogOptionOrderSync>
        <DialogOptionSetWarehouse :length_item="order_syncs_selected.length" :is_sap_sync="is_sap_sync"
            :item_selecteds="order_syncs_selected" :use_component_syncs_sap="use_component_syncs_sap"
            @emitSetWarehouse="getSetWarehouse" @emitOrderSyncs="getEmitOrderSyncs"></DialogOptionSetWarehouse>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import TableOrderSync from '../tables/TableOrderSync.vue';
import PaginationTable from '../paginations/PaginationTable.vue';
import DialogOptionOrderSync from './DialogOptionOrderSync.vue';
import DialogOptionSetWarehouse from './DialogOptionSetWarehouse.vue';
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
export default {
    props: {
        showModalSyncSAP: Function,
        customer_group_id: Number,
        customer_groups: Array,
        order_syncs: Array,
        case_save_so: Object,
        is_sap_sync: Boolean,
        order_syncs_selected: Array,
        use_component_syncs_sap: String,
        warehouses: Array

    },
    components: {
        TableOrderSync,
        PaginationTable,
        DialogOptionOrderSync,
        DialogOptionSetWarehouse,
        Treeselect
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            case_data_temporary: {
                order_syncs_selected: [],
                warehouses: [],

            },
            case_model: {
                warehouse_id: null,
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
                    label: 'Kho',
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
            case_api: {
                warehouse: 'api/master/warehouses/company-3000',
            },
            per_page: 100,
            page_options: [10, 20, 50, 100, 200, 300, 500],
            current_page: 1,
        }
    },
    created() {
        this.fetchWarehouses();
    },
    methods: {
        emitDataWarehouse() {
            this.$emit('emitDataWarehouse', this.case_model.warehouse_id);
        },
        async fetchWarehouses() {
            let { data, success } = await this.api_handler.get(this.case_api.warehouse);
            if (success) {
                this.$emit('emitDataFetchWarehouse', data);
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
            // console.log('emit order syncs option')
            // this.$emit('getEmitOrderSyncsOption');
            this.emitProcessOrderSync();
        },
        getEmitOrderSyncs(item_selecteds) {
            // this.case_data_temporary.order_syncs_selected = selecteds;
            // this.checkProcessOrderSyncSetWarehouse(item_selecteds);
            this.$emit('emitOrderSyncs', item_selecteds)
        },
        getEmitSetWarehouse() {
            $('#modalOptionOrderSync').modal('hide');
            $('#modalOptionSetWarehouse').modal('show');
        },
        showModalOptionOrderSync() {
            $('#modalOptionOrderSync').modal('show');
        },
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
        getSetWarehouse(warehouse_code, order_syncs_selected) {
            // order_syncs_selected.forEach(item => {
            //     this.case_data.order_syncs.forEach(order_sync => {
            //         if (item.id == order_sync.id) {
            //             order_sync.warehouse_id = warehouse_code;
            //         }
            //     });
            // });
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