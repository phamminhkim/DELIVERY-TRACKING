<template>
    <div>
        <div class="mb-1 row">
            <div class="col-lg-6">
                <h5 class="text-black font-weight-bold mb-0">Chi tiết đơn hàng nhóm CoopMax</h5>
            </div>
            <div class="col-lg-6">
                <div class="text-right">
                    <button class="btn btn-light text-info px-2">Đồng bộ SAP</button>
                    <button @click="rollBackUrl()" type="button" class="btn btn-light text-black px-2">Đóng</button>
                </div>
            </div>
        </div>
        <div class="form-group border border-info p-3 bg-white">
            <div class="row align-items-baseline">
                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Mã KH
                    SAP</label>
                <div class="col-lg-3 mt-1 mb-1">
                    <b>123</b>
                </div>
                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Tên
                    KH</label>
                <div class="col-lg-3 mt-1 mb-1">
                    <b>Siêu thị Bình CHán</b>
                </div>
            </div>
            <div class="row align-items-baseline">
                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Số SO
                    SAP</label>
                <div class="col-lg-3 mt-1 mb-1">
                    <b>5000</b>
                </div>
                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Ngày
                    tạo</label>
                <div class="col-lg-3 mt-1 mb-1">
                    <b>20/11/2024</b>
                </div>
            </div>
            <div class="row align-items-baseline">
                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Số
                    PO</label>
                <div class="col-lg-3 mt-1 mb-1">
                    <b>PO12345</b>
                </div>
                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1" for="">Ngày
                    giao</label>
                <div class="col-lg-3 mt-1 mb-1">
                    <b>30/05/2024</b>
                </div>
            </div>
        </div>
       
    </div>
</template>
<script>
export default {
    props: {
        rollBackUrl: Function,
        query: String
    },
    data() {
        return {

        }
    }
}
</script>