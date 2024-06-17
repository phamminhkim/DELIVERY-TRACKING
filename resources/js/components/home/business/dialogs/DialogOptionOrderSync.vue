<template>
    <div>
        <div class="modal fade" id="modalOptionOrderSync" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Options</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gradient-light">
                        <div class="form-group d-flex justify-content-center">
                            <!-- <label>Dialogs</label> -->
                            <button @click="emitOrderSyncsOption()" type="button"
                                class="btn btn-sm btn-light text-info font-weight-bold mr-2">
                                <span class="badge badge-sm badge-info mr-2">{{ length_item }}</span>Đồng bộ
                                ngay</button>
                            <button @click="emitViewDetailOrderSyncs()" type="button" class="btn btn-sm btn-light text-primary btn-group__border">
                                <span class="badge badge-primary badge-sm mr-2">{{ length_item }}</span>Xem chi
                                tiết</button>
                            <!-- <button @click="emitSetWarehouse()" type="button"
                                class="btn btn-sm btn-warning font-weight-bold mr-2">
                                <span class="badge badge-sm badge-light text-black mr-2">{{ length_item }}</span>Set
                                kho</button> -->
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-4 ml-auto mr-auto">
                                    <div class="card border-0 shadow rounded card-warehouse"
                                        @click="emitSetWarehouse()">
                                        <div class="card-body">
                                            <div class="text-center" style="position:relative; width: 100%;">
                                                <i class="fas fa-warehouse " style="font-size:50px"></i>
                                                <span class="badge badge-sm badge-danger mr-2"
                                                    style="position: absolute; right: -2px">{{
                                            length_item }}</span>
                                            </div>
                                        </div>
                                        <div class="card-footer p-0 text-center bg-white border-0">
                                            <div class="form-group mb-0">
                                                <label>Set kho</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group d-flex justify-content-center">
                            <button @click="emitOrderSyncsOption()" type="button"
                                class="btn btn-sm btn-light text-info font-weight-bold mr-2">
                                <span class="badge badge-sm badge-info mr-2">{{ length_item }}</span>Đồng bộ
                                ngay</button>
                            <button type="button" class="btn btn-sm btn-light text-primary btn-group__border">
                                <span class="badge badge-primary badge-sm mr-2">{{ length_item }}</span>Xem chi
                                tiết</button>
                            <button @click="emitSetWarehouse()" type="button"
                                class="btn btn-sm btn-warning font-weight-bold mr-2">
                                <span class="badge badge-sm badge-light text-black mr-2">{{ length_item }}</span>Set
                                kho</button> 
                            <button type="button" class="btn btn-sm btn-light font-weight-bold"
                                data-dismiss="modal">Đóng</button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        length_item: Number,
        viewDetailOrderSyncs: Function,
    },
    data() {
        return {

        }
    },
    methods: {
        emitViewDetailOrderSyncs() {
            if (this.length_item === 0) {
                alert('Vui lòng chọn ít nhất 1 đơn hàng');
                return;
            } else {
                this.viewDetailOrderSyncs();
            }
        },
        emitSetWarehouse() {
            if (this.length_item === 0) {
                alert('Vui lòng chọn ít nhất 1 đơn hàng');
                return;
            } else {
                this.$emit('emitSetWarehouse');
            }
        },
        emitOrderSyncsOption() {
            if (this.length_item === 0) {
                alert('Vui lòng chọn ít nhất 1 đơn hàng');
                return;
            } else {
                this.$emit('emitOrderSyncsOption');
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.card-warehouse {
    &:hover {
        cursor: pointer;
        background: #17a2b8 !important;
        color: white !important;
    }
}
</style>