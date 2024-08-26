<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesCheckPrice" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" v-show="!is_loading">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Check Giá</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-light">
                        <div>
                            <label>Mã đơn hàng</label>
                            <textarea v-model="so_numbers" class="form-control" rows="5"
                            placeholder="Nhập đơn hàng..." :class="{
                                'border-danger': so_numbers == '',
                                'border-success': so_numbers != ''
                            }">></textarea>
                        </div>
                        <div class="form-group text-right">
                            <label>Khuyến mãi <input class="ml-2" type="checkbox"
                                    v-model="is_promotion" /></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm text-xs" data-dismiss="modal">Close</button>
                        <button @click="emitCheckPrice()" type="button" class="btn btn-warning btn-sm text-xs">Check
                            giá</button>
                    </div>
                </div>
                <div v-show="is_loading" class="modal-content text-center modal-dialog-centered">
                    <div class="modal-header p-0 mt-3">
                        <div class="text-center text-xs">
                            <p class="text-warning p-0 "><i class="fas fa-spinner fa-spin mr-2"></i>Đang Check Giá</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        is_loading: { type: Boolean, default: false },
    },
    data() {
        return {
            so_numbers: '',
            is_promotion: false,
        }
    },
    methods: {
        emitCheckPrice() {
            if (this.so_numbers == '') {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng nhập mã đơn hàng');

            } else {
                this.$emit('checkPrice', this.so_numbers, this.is_promotion);
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