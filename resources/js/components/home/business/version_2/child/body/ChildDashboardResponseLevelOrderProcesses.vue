<template>
    <div>
        <b-table :items="items" :fields="fields" responsive small :bordered="true">
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
    components: {
        PaginationTable
    },
    data() {
        return {
            currentPage: 1,
            perPage: 10,
            items: [
                {
                    id: 1, created_at: '2021-09-01', customer_code: 'KH001', customer_name: 'Nguyễn Văn A', group_customer: 'Emart', po_number: '123', po_unit_barcode: 'E/A',
                    po_unit_barcode_description: 'E/A', po_code_sap: '123', po_quantity_sap: '100', po_unit: 'EA', so_number: '123', so_code_sku: '123', so_name_product: 'Sản phẩm 1',
                    so_quantity_cf: '100', so_unit: 'EA', response_rate: '100%', create_by: 'Admin'
                },
                {
                    id: 2, created_at: '2021-09-01', customer_code: 'KH002', customer_name: 'Nguyễn Văn B', group_customer: 'Emart', po_number: '123', po_unit_barcode: 'E/A',
                    po_unit_barcode_description: 'E/A', po_code_sap: '123', po_quantity_sap: '100', po_unit: 'EA', so_number: '123', so_code_sku: '123', so_name_product: 'Sản phẩm 2',
                    so_quantity_cf: '100', so_unit: 'EA', response_rate: '100%', create_by: 'Admin'
                },
                {
                    id: 3, created_at: '2021-09-01', customer_code: 'KH003', customer_name: 'Nguyễn Văn C', group_customer: 'Emart', po_number: '123', po_unit_barcode: 'E/A',
                    po_unit_barcode_description: 'E/A', po_code_sap: '123', po_quantity_sap: '100', po_unit: 'EA', so_number: '123', so_code_sku: '123', so_name_product: 'Sản phẩm 3',
                    so_quantity_cf: '100', so_unit: 'EA', response_rate: '100%', create_by: 'Admin'
                },
                {
                    id: 4, created_at: '2021-09-01', customer_code: 'KH004', customer_name: 'Nguyễn Văn D', group_customer: 'Emart', po_number: '123', po_unit_barcode: 'E/A',
                    po_unit_barcode_description: 'E/A', po_code_sap: '123', po_quantity_sap: '100', po_unit: 'EA', so_number: '123', so_code_sku: '123', so_name_product: 'Sản phẩm 4',
                    so_quantity_cf: '100', so_unit: 'EA', response_rate: '100%', create_by: 'Admin'
                },
            ],
            fields: [
                { key: 'id', label: 'STT' },
                { key: 'created_at', label: 'Ngày tạo', class: 'text-nowrap' },
                { key: 'customer_code', label: 'Mã KH', class: 'text-nowrap' },
                { key: 'customer_name', label: 'Tên KH', class: 'text-nowrap' },
                { key: 'group_customer', label: 'Nhóm KH', class: 'text-nowrap' },
                { key: 'po_number', label: 'PO Number', class: 'text-nowrap' },
                { key: 'po_unit_barcode', label: 'PO Unit Barcode', class: 'text-nowrap' },
                { key: 'po_unit_barcode_description', label: 'PO Unit Barcode Description', class: 'text-nowrap' },
                { key: 'po_code_sap', label: 'PO Mã SAP', class: 'text-nowrap' },
                { key: 'po_quantity_sap', label: 'PO Số Lượng SAP', class: 'text-nowrap' },
                { key: 'po_unit', label: 'PO ĐVT', class: 'text-nowrap' },
                { key: 'so_number', label: 'SO Number', class: 'text-nowrap' },
                { key: 'so_code_sku', label: 'SO Mã SKU', class: 'text-nowrap' },
                { key: 'so_name_product', label: 'SO Tên SP', class: 'text-nowrap' },
                { key: 'so_quantity_cf', label: 'SO Số Lượng CF', class: 'text-nowrap' },
                { key: 'so_unit', label: 'SO ĐVT', class: 'text-nowrap' },
                { key: 'response_rate', label: 'Tỉ lệ đáp ứng', class: 'text-nowrap' },
                { key: 'create_by', label: 'Người tạo', class: 'text-nowrap' },
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