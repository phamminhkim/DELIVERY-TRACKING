<template>
    <div>
        <b-table :items="reportes" :fields="fields" responsive small :bordered="true" class="text-xs">
            <template #thead-top="data">
                <b-tr>
                    <b-th class="text-center border-0" colspan="12" variant="info">Dữ liệu Web MT</b-th>
                    <b-th class="text-center border-0" variant="success" colspan="6">Dữ liệu SAP</b-th>
                </b-tr>
            </template>
            <template #cell(id)="data">
                {{ data.index + 1 }}
            </template>
        </b-table>
        <PaginationTable :per_page="perPage" :current_page="currentPage" @pageChange="handlePageChange"
            @perPageChange="handlePerPageChange" />
    </div>
</template>
<script>
import PaginationTable from '../../../paginations/PaginationRequest.vue';
export default {
    props: {
        reportes: { type: Array, default: () => [] },

    },
    components: {
        PaginationTable
    },
    data() {
        return {
            currentPage: 1,
            perPage: 10,
            fields: [
                { key: 'id', label: 'STT' },
                { key: 'created_at', label: 'Ngày tạo', class: 'text-nowrap text-xs' },
                { key: 'customer_code', label: 'Mã KH', class: 'text-nowrap text-xs' },
                { key: 'customer_name', label: 'Tên KH', class: 'text-nowrap text-xs' },
                { key: 'order_process.customer_group_name', label: 'Nhóm KH', class: 'text-nowrap text-xs' },
                { key: 'po_number', label: 'PO Number', class: 'text-nowrap text-xs' },
                { key: 'customer_sku_code', label: 'PO Unit Barcode', class: 'text-nowrap text-xs' },
                { key: 'customer_sku_name', label: 'PO Unit Barcode Description', class: 'text-nowrap text-xs' },
                { key: 'sku_sap_code', label: 'PO Mã SAP', class: 'text-nowrap text-xs' },
                { key: 'quantity3_sap', label: 'PO Số Lượng SAP', class: 'text-nowrap text-xs' },
                { key: 'sku_sap_unit', label: 'PO ĐVT', class: 'text-nowrap text-xs' },
                { key: 'so_uid', label: 'SO Number', class: 'text-nowrap text-xs' },
                { key: 'sap_code', label: 'SO Mã SKU', class: 'text-nowrap text-xs' },
                { key: 'sap_name', label: 'SO Tên SP', class: 'text-nowrap text-xs' },
                { key: 'sap_quantity', label: 'SO Số Lượng CF', class: 'text-nowrap text-xs' },
                { key: 'sap_unit_code', label: 'SO ĐVT', class: 'text-nowrap text-xs' },
                { key: 'fulfillment_rate', label: 'Tỉ lệ đáp ứng', class: 'text-nowrap text-xs' },
                { key: 'sap_user', label: 'Người tạo', class: 'text-nowrap text-xs' },
            ],
        }
    },
    methods: {
        handlePageChange(page) {
            this.currentPage = page;
        },
        handlePerPageChange(perPage) {
            this.perPage = perPage;
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>