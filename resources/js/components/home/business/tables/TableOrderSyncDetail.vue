<template>
    <div>
        <HeaderOrderSyncSAPDetail :rollBackUrl="rollBackUrl" ></HeaderOrderSyncSAPDetail>
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
        </div>
        <TableOrderSync :fields="fields" :items="items" :query="case_filter.query"></TableOrderSync>
    </div>
</template>
<script>
import HeaderOrderSyncSAPDetail from '../headers/HeaderOrderSyncSAPDetail.vue';
import TableOrderSync from './TableOrderSync.vue';
export default {
    components: {
        TableOrderSync,
        HeaderOrderSyncSAPDetail
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