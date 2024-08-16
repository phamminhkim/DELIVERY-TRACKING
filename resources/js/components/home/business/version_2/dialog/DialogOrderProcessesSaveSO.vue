<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesSaveSO" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Lưu đơn hàng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tiêu đề</label><small class="text-danger">(*)</small>
                            <textarea v-model="order.title"
                             class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-if="order.id == -1" @click="emitSaveOrderSo()" type="button" class="btn btn-success btn-sm px-2">Lưu đơn hàng</button>
                        <button v-else @click="emitUpdateOrderSo()" type="button" class="btn btn-success btn-sm px-2">Cập nhật đơn hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        order: { type: Object, default: () => {} },
    },
    data() {
        return {
            demo: ''
        }
    },
    methods: {
        emitConvertFile(file) {
            this.$emit('convertFile', file);
        },
        emitSaveOrderSo() {
            this.$emit('saveOrderSo');
        },
        emitUpdateOrderSo() {
            this.$emit('updateOrderSo');
        }
    }
}
</script>
<style lang="scss" scoped></style>