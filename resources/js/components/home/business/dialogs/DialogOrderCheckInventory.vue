<template>
    <div>
        <div class="modal fade" id="modalCheckInventory" tabindex="-1">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                            <h5 class="modal-title font-weight-bold text-uppercase">Form check tồn</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label>Mã kho</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i
                                            class="fas fa-warehouse"></i></span>
                                </div>
                                <input v-model="case_model.warehouse_id" type="text" class="form-control input-warehouse" placeholder="Nhập mã kho..."
                                :class="{
                                    'border-danger' : case_model.warehouse_id == '',
                                    'border-success' : case_model.warehouse_id != ''
                                }">
                            </div>
                        </div>
                        <div class="text-center">
                            <button @click="emitModelWarehouseId()" type="button" class="btn btn-warning btn-sm">Check tồn</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            case_model: { 
                warehouse_id: '',
            }
        }
    },
    methods: {
        emitModelWarehouseId(){
            if(this.case_model.warehouse_id == ''){
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng nhập mã kho');

            } else {
                this.$emit('emitModelWarehouseId', this.case_model.warehouse_id);
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.input-warehouse:focus{
    outline: none;
    box-shadow: none;
}
</style>