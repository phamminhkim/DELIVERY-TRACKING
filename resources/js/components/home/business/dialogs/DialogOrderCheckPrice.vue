<template>
    <div>
        <div class="modal fade" id="modalCheckPrice" tabindex="-1">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Form check giá</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label>Mã đơn hàng</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i
                                            class="fas fa-warehouse"></i></span>
                                </div>
                                <input v-model="case_model.so_numbers" type="text" class="form-control input-warehouse"
                                    placeholder="Nhập đơn hàng..." :class="{
                                    'border-danger': case_model.so_numbers == '',
                                    'border-success': case_model.so_numbers != ''
                                }">
                            </div>
                            <div class="form-group text-right">
                                <label>Khuyến mãi <input class="ml-2" type="checkbox" v-model="case_model.is_promotion" /></label>
                            </div>
                        </div>
                        <div class="text-center">
                            <button @click="emitModelSoNumbers()" type="button" class="btn btn-warning btn-sm">Check
                                giá</button>
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
                so_numbers: '',
                is_promotion: false,
            }
        }
    },
    methods: {
        emitModelSoNumbers() {
            if (this.case_model.so_numbers == '') {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng nhập mã đơn hàng');

            } else {
                this.$emit('emitModelSoNumbers', this.case_model.so_numbers, this.case_model.is_promotion);
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