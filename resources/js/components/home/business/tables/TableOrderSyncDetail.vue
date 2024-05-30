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
        <div class="form-group">
            <div class="row mb-1">
                <div class="col-lg-6 ml-auto">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input v-model="case_filter.query" type="text" class="form-control form-control-sm"
                            placeholder="Tìm kiếm...">
                    </div>
                </div>
            </div>
            <TableOrderSync :fields="fields" :items="items" :query="case_filter.query"></TableOrderSync>
        </div>
    </div>
</template>
<script>
import TableOrderSync from './TableOrderSync.vue';
export default {
    components: {
        TableOrderSync
    },
    data() {
        return {
            case_filter: {
                query: '',
            },
            fields: [
                {
                    key: 'select',
                    label: '',
                    class: 'text-nowrap'
                },
                {
                    key: 'index',
                    label: 'Stt',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'sap_so',
                    label: 'SAP SO num',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'so_key',
                    label: 'SO Key',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_key',
                    label: 'Mã KH',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_name',
                    label: 'Tên KH',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'status',
                    label: 'Trạng thái',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'po_delivery_date',
                    label: 'Ngày giao',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'status_sync',
                    label: 'TT Đồng bộ',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'noti_sync',
                    label: 'Thông báo',
                    sortable: true,
                    class: 'text-nowrap'
                },
            ],
            items: [
                {
                    id: 1,
                    index: 1,
                    sap_so: '123456',
                    so_key: 'SO123456',
                    customer_key: 'KH123456',
                    customer_name: 'Khách hàng 123456',
                    po_delivery_date: '2021-01-01',
                    status_sync: 'Đã đồng bộ',
                    noti_sync: 'Đã đồng bộ'
                },
                {
                    id: 2,
                    index: 2,
                    sap_so: '123456',
                    so_key: 'SO123456',
                    customer_key: 'KH123456',
                    customer_name: 'Khách hàng 123456',
                    po_delivery_date: '2021-01-01',
                    status_sync: 'Đã đồng bộ',
                    noti_sync: 'Đã đồng bộ'
                },
                {
                    id: 3,
                    index: 3,
                    sap_so: '123456',
                    so_key: 'SO123456',
                    customer_key: 'KH123456',
                    customer_name: 'Khách hàng 123456',
                    po_delivery_date: '2021-01-01',
                    status_sync: 'Đã đồng bộ',
                    noti_sync: 'Đã đồng bộ'
                },
            ],
        }
    },
    methods: {
        rollBackUrl() {
            this.$router.push('/sap-syncs');
            this.$emit('rollBackUrl', false);

        }
    }
}
</script>
<style lang="scss" scoped></style>