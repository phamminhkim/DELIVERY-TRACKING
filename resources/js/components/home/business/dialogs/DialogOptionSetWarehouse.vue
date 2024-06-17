<template>
    <div>
        <div class="modal fade" id="modalOptionSetWarehouse" tabindex="-1">
            <div class="modal-dialog  modal-dialog-scrollable" style="margin-top: 0 !important" :class="{
                'modal-xl': !is_sap_sync,
            }">
                <div class="modal-content" v-show="!is_sap_sync">
                    <div class="modal-header bg-cyan">
                        <h5 class="modal-title font-weight-bold">Options Set Warehouse</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-0">
                        <div class="form-group set-body">
                            <div class="row">
                                <div class="col-lg-3 section-left shadow text-info">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="text-body">Số đơn hàng chọn set kho <span
                                                    class="text-info">({{
                case_data_temporary.order_syncs_selected.length }})</span>:</label>
                                            <div class="form-group border-bottom border-primary">
                                                <div class="input-group input-group-sm mb-3">
                                                    <input @keyup.enter="getSetWarehouse()"
                                                        v-model="case_input.warehouse_code" type="text"
                                                        class="form-control" placeholder="Nhập set mã kho...." />
                                                    <div class="input-group-append" @click="getSetWarehouse()"
                                                        style="cursor: pointer;">
                                                        <span class="input-group-text bg-info border-0"
                                                            id="basic-addon2">Enter</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <b-table striped responsive hover small
                                                :items="case_data_temporary.order_syncs_selected"
                                                :fields="field_item_selecteds" :filter="case_filter.warehouse_code">
                                                <template #cell(index)="data">
                                                    {{ data.index + 1 }}
                                                </template>
                                                <template #cell(warehouse_id)="data">
                                                    <span class="badge badge-sm badge-info px-2">{{
                data.item.warehouse_id }}</span>
                                                </template>
                                                <template #cell(select)="data">
                                                    <input type="checkbox" v-model="case_data_temporary.selecteds"
                                                        :value="data.item" value="Xóa">
                                                </template>
                                                <template #head(select)="data">
                                                    <input type="checkbox" v-model="case_data_temporary.select_all"
                                                        @change="changeSelectAll()" />
                                                </template>
                                            </b-table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 section-right">
                                    <div class="form-group mt-1">
                                        <div class="border-bottom border-info">
                                            <div class="row align-items-center mb-2">
                                                <div class="col-lg-6">
                                                    <div class="text-sm">
                                                        <label class="mb-0 ">Items <span class="text-success">({{
                length_item
            }})</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="text-right">
                                                        <button @click="emitOrderSyncs()" type="button"
                                                            class="btn btn-sm btn-light text-info font-weight-bold px-4">
                                                            <span class="badge badge-sm badge-success px-2 mr-2">{{
                case_data_temporary.order_syncs_selected.length
            }}</span>Đồng
                                                            bộ
                                                            SAP</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <TableOrderSync :items="item_selecteds" :fields="fields"
                                            :use_component="'OrderSyncSAP'" :query="case_filter.query"
                                            @emitSelectedOrderSync="getSelectedOrderSync" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="is_sap_sync" class="modal-content text-center">
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
import TableOrderSync from '../tables/TableOrderSync.vue';
export default {
    props: {
        length_item: Number,
        item_selecteds: Array,
        is_sap_sync: {
            type: Boolean,
            default: false
        }
    },
    components: {
        TableOrderSync
    },
    watch: {
        'case_data_temporary.selecteds': function (val) {
            if (val.length === this.case_data_temporary.order_syncs_selected.length) {
                this.case_data_temporary.select_all = true;
            } else {
                this.case_data_temporary.select_all = false;
            }
        },
    },
    data() {
        return {
            case_filter: {
                query: '',
                warehouse_code: ''
            },
            case_input: {
                warehouse_code: ''
            },
            case_data_temporary: {
                order_syncs_selected: [],
                selecteds: [],
                select_all: false
            },
            field_item_selecteds: [
                {
                    key: 'index',
                    label: 'Stt',
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'sap_so_number',
                    label: 'Số PO num',
                    class: 'text-nowrap'
                },
                {
                    key: 'warehouse_id',
                    label: 'Mã kho',
                    class: 'text-nowrap'
                },
                {
                    key: 'select',
                    label: 'Chọn',
                    class: 'text-nowrap text-center'
                },
            ],
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
                    label: 'Mã Kho',
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
        }
    },
    methods: {
        changeSelectAll() {
            if (this.case_data_temporary.select_all) {
                this.case_data_temporary.selecteds = this.case_data_temporary.order_syncs_selected;
            } else {
                this.case_data_temporary.selecteds = [];
            }
        },
        setWarehouse() {
            if (this.case_data_temporary.order_syncs_selected.length == 0) {
                alert('Vui lòng chọn ít nhất 1 đơn hàng để thực hiện thao tác này');
                return;
            } else {
                this.$showMessage('success', 'Set kho thành công');
                this.case_input.warehouse_code = '';
            }
        },
        getSelectedOrderSync(selected) {
            this.case_data_temporary.order_syncs_selected = selected;
            this.case_data_temporary.selecteds = selected;
        },
        emitOrderSyncs() {
            this.$emit('emitOrderSyncs', this.case_data_temporary.order_syncs_selected);
        },
        getSetWarehouse() {
            // tìm cách set warehouse_code cho order_syncs_selected
            if (this.case_data_temporary.selecteds.length == 0) {
                alert('Vui lòng chọn ít nhất 1 đơn hàng để thực hiện thao tác này');
                return;
            } else {
                this.case_data_temporary.order_syncs_selected.forEach(item => {
                    this.case_data_temporary.selecteds.forEach(order_sync => {
                        if (item.id == order_sync.id) {
                            order_sync.warehouse_id = this.case_input.warehouse_code;
                        }
                    });
                });
                this.$showMessage('success', 'Set kho thành công');
                this.reset();
            }

        },
        reset() {
            this.case_input.warehouse_code = '';
            this.case_data_temporary.selecteds = [];
        }

    },
}
</script>
<style lang="scss" scoped>
.set-body {
    height: 100vh;

}

.section-left {
    border-right: 1px solid #ccc;
    background: rgb(242 252 255);
}

input:focus {
    box-shadow: none;
    outline: none;
}
</style>