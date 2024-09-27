<template>
    <div class="form-group mb-1">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group mb-0">
                    <button @click="emitDetectSapCodeOrder()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fab fa-product-hunt text-danger mr-2"></i>Dò Mã <span
                            class="border-bottom border-danger">SAP</span></button>
                    <button @click="emitDetectCustomerKey()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fas fa-user-tag text-primary mr-2"></i>Dò Mã <span
                            class="border-bottom border-danger">KH</span> </button>
                    <button @click="emitCheckPromotion()"
                        class="btn btn-light btn-sm text-xs border-bottom text-warning"><i
                            class="fas fa-ad text-warning mr-2"></i>Check <span
                            class="border-bottom border-danger">Khuyến Mãi</span> </button>
                    <button @click="emitCheckInventory()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fas fa-boxes text-info mr-2"></i>Check <span
                            class="border-bottom border-danger">Tồn</span> </button>
                    <button @click="emitCheckPrice()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="far fa-money-bill-alt text-success mr-2"></i>Check <span
                            class="border-bottom border-danger">Giá</span> </button>
                    <button @click="emitCheckCompliance()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fab fa-trade-federation text-danger mr-2"></i>Check <span
                            class="border-bottom border-danger">Quy Cách</span> </button>
                    <button @click="emitDeleteOrders()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fas fa-trash-alt text-danger mr-2"></i>Xóa <span
                            class="border-bottom border-danger">Dữ liệu</span> </button>
                    <button @click="emitChangeMaterial()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fas fa-search text-primary mr-2"></i>Tìm <span class="border-bottom border-danger">Mã
                            SAP</span> </button>
                    <button @click="emitExportExcel()" class="btn btn-light btn-sm text-xs border-bottom"><i
                            class="fas fa-file-export text-info mr-2"></i>Xuất <span
                            class="border-bottom border-danger">Excel</span> </button>
                    <button @click="emitSaveUpdateLayout()"
                        class="btn btn-light text-primary btn-sm text-xs border-bottom">
                        <i class="fas fa-disease mr-2"></i>Lưu <span class="border-bottom border-danger">Layout</span>
                    </button>
                    <button  @click="emitDeleteOrdersHistory()"    
                        class="btn btn-light text-secondary btn-sm text-xs border-bottom">
                        <i class="fas fa-history mr-2"></i><span class="border-bottom border-danger">Đã Xóa </span>Gần Đây
                    </button>
                    <button @click="emitOrderSyncSap()" class="btn btn-info btn-sm text-xs border-bottom"><i
                            class="fas fa-cloud-upload-alt mr-2"></i>Đồng Bộ <span
                            class="border-bottom border-danger">SAP</span> </button>    
                    <button v-if="order.id == -1" @click="emitSaveUpdateOrder()" type="button"
                        class="btn btn-sm px-2 text-xs btn-success">Lưu đơn hàng</button>
                    <button v-else @click="emitUpdateOrder()" type="button"
                        class="btn btn-sm px-2 text-xs btn-warning">Cập nhật đơn hàng</button>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        order: { type: Object, default: () => { } },
    },
    data() {
        return {

        }
    },
    methods: {
        emitSaveUpdateOrder() {
            this.$emit('saveUpdateOrder');
        },
        emitDetectSapCodeOrder() {
            this.$emit('detectSapCodeOrder');
        },
        emitUpdateOrder() {
            this.$emit('updateOrder');
        },
        emitDetectCustomerKey() {
            this.$emit('emitDetectCustomerKey');
        },
        emitCheckPromotion() {
            this.$emit('checkPromotion');
        },
        emitCheckInventory() {
            this.$emit('checkInventory');
        },
        emitCheckCompliance() {
            this.$emit('checkCompliance');
        },
        emitCheckPrice() {
            this.$emit('checkPrice');
        },
        emitOrderSyncSap() {
            this.$emit('orderSyncSap');
        },
        emitChangeMaterial() {
            this.$emit('changeMaterial');
        },
        emitExportExcel() {
            this.$emit('exportExcel');
        },
        emitSaveUpdateLayout() {
            this.$emit('saveUpdateLayout');
        },
        emitDeleteOrders() {
            this.$emit('deleteOrders');
        },
        emitDeleteOrdersHistory() {
            this.$emit('deleteOrdersHistory');
        }
    }
}
</script>
<style lang="scss" scoped></style>