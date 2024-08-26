<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesCheckInventory" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog" >
                <div class="modal-content" v-show="!is_loading">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Check Tồn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-light">
                        <div>
                            <label>Mã kho</label>
                            <div class="input-group mb-3">
                                <treeselect placeholder="Chọn kho.." :multiple="false" :disable-branch-nodes="true"
                                    :show-count="true" v-model="warehouse_id" :options="warehouses" />

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm text-xs" data-dismiss="modal">Close</button>
                        <button @click="emitCheckInventory()" type="button" class="btn btn-warning btn-sm text-xs">Check
                            tồn</button>
                    </div>
                </div>
                <div v-show="is_loading" class="modal-content text-center modal-dialog-centered">
                    <div class="modal-header p-0 mt-3">
                        <div class="text-center text-xs">
                            <p class="text-warning p-0 "><i class="fas fa-spinner fa-spin mr-2"></i>Đang Check Tồn</p>
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
export default {
    props: {
        warehouses: { type: Array, default: () => [] },
        is_loading: { type: Boolean, default: false },
    },
    components: {
        Treeselect
    },
    data() {
        return {
            warehouse_id: null,
        }
    },
    methods: {
        emitCheckInventory() {
            this.$emit('checkInventory', this.warehouse_id);
        },
    }
}
</script>
<style lang="scss" scoped></style>