<template>
    <div>
        <div class="modal fade" id="modalCheckInventory" tabindex="-1">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-warning">
                        <h5 class="modal-title font-weight-bold text-uppercase">Form check tồn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-light">
                        <div>
                            <label>Mã kho</label>
                            <div class="input-group mb-3">
                                <!-- <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i
                                            class="fas fa-warehouse"></i></span>
                                </div> -->
                                <treeselect placeholder="Chọn kho.." :multiple="false" :disable-branch-nodes="true"
                                :show-count="true"
                                    v-model="case_model.warehouse_id" :options="case_data_temporary.warehouses" />
                                <!-- <input v-model="case_model.warehouse_id" type="text"
                                    class="form-control input-warehouse" placeholder="Nhập mã kho..." :class="{
                                    'border-danger': case_model.warehouse_id == '',
                                    'border-success': case_model.warehouse_id != ''
                                }"> -->
                            </div>
                        </div>
                        <div class="text-center">
                            <button @click="emitModelWarehouseId()" type="button" class="btn btn-warning btn-sm">Check
                                tồn</button>
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
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    components: {
        Treeselect
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            case_model: {
                warehouse_id: null,
            },
            case_data_temporary: {
                warehouses: [],

            },
            case_api: {
                warehouse: 'api/master/warehouses/company-3000',
            },
        }
    },
    created() {
        this.fetchWarehouses();
    },
    methods: {
        async fetchWarehouses() {
            let { data, success } = await this.api_handler.get(this.case_api.warehouse);
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
                this.case_data_temporary.warehouses = options;
            }
        },
        emitModelWarehouseId() {
            if (this.case_model.warehouse_id == '') {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng nhập mã kho');

            } else {
                this.$emit('emitModelWarehouseId', this.case_model.warehouse_id);
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.input-warehouse:focus {
    outline: none;
    box-shadow: none;
}
</style>