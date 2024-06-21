<template>
    <div>
        <div class="px-5">
            <div class="mb-1 row">
                <div class="col-lg-6">
                    <p class="text-black font-weight-bold mb-0 w-100 py-2"><b class="mr-2"><u>{{ index }}.</u></b>Chi tiết đơn hàng nhóm <b v-if="order_sync.order_process.customer_group_id !== -1">{{
                        order_sync.order_process.customer_group.name }}</b></p>
                </div>
                <div class="col-lg-6">
                    <div class="text-right py-2">
                        <!-- <button class="btn btn-light text-success btn-sm px-2"><i class="fas fa-file-excel mr-2"></i>Xuất phiếu</button> -->
                        <!-- <button @click="rollBackUrl()" type="button" class="btn btn-light text-black px-2">Đóng</button> -->
                    </div>
                </div>
            </div>
            <div class="form-group border rounded p-3 bg-white text-left">
                <div class="row align-items-baseline">
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Mã KH
                        SAP</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b> {{ order_sync.customer_code }}</b>
                    </div>
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Tên
                        KH</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b>{{ order_sync.customer_name }}</b>
                    </div>
                </div>
                <div class="row align-items-baseline">
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Số SO
                        SAP</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b> {{ order_sync.so_uid }}</b>
                    </div>
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Ngày
                        tạo</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b> {{ order_sync.created_at | formatDateStyleApart }}</b>
                    </div>
                </div>
                <div class="row align-items-baseline">
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Số
                        PO</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b> {{ order_sync.po_number }}</b>
                    </div>
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Ngày
                        giao</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b>{{ order_sync.po_delivery_date   }}</b>
                    </div>
                </div>
                <div class="row align-items-baseline">
                    <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Ghi
                        chú</label>
                    <div class="col-lg-3 mt-1 mb-1">
                        <b> {{ order_sync.so_sap_note }}</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        rollBackUrl: Function,
        query: String,
        order_sync: Object,
        index: Number,
    },
    data() {
        return {

        }
    }
}
</script>