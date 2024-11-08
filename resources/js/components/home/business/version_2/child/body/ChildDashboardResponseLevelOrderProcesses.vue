<template>
    <div>
        <b-table :items="reportes" :fields="fields" responsive small :bordered="true" class="text-xs"
            :per-page="perPage">
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
        <PaginationTable :rows="total" :per_page="perPage" :current_page="currentPage" @pageChange="handlePageChange"
            @perPageChange="handlePerPageChange" />
    </div>
</template>
<script>
import PaginationTable from '../../../paginations/PaginationRequest.vue';
import ApiHandler, { APIRequest } from '../../../../ApiHandler';

export default {
    props: {
        search_change: { type: String, default: '' },
        change_search: { type: Number, default: 0 },
        start_date: { type: Date, default: '' },
        end_date: { type: Date, default: '' },    
    },
    components: {
        PaginationTable
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            currentPage: 1,
            perPage: 10,
            total: 0,
            reportes: [],
            customer_partners: [],
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
            url_api: {
                dashboard_report: 'api/dashboard/MT/report',
                customer_partners: 'api/master/customer-partners',
            }
        }
    },
    watch: {
        search_change: {
            handler: function (val) {
                this.search = val;
                this.fetchCustomerPartner();
            },
            deep: true
        },
        change_search: {
            handler: function (val) {
                this.fetchDashboardReport();
            },
            deep: true
        },

    },
    async created() {
        await this.fetchDashboardReport();
        await this.fetchCustomerPartner();

    },
    methods: {
        async fetchDashboardReport() {
            try {
                this.is_loading = true;
                const body = {
                    from_date: this.start_date,
                    to_date: this.end_date,
                    // customer_group_ids: this.order.customer_group_ids,
                    // user_ids: this.order.user_ids,
                    // sync_sap_status: this.order.sync_sap_status
                    page: this.currentPage,
                    per_page: this.perPage,
                }
                const { data, success } = await this.api_handler.get(this.url_api.dashboard_report, body);
                console.log(data)
                if (success) {
                    this.reportes = data.data;
                    this.currentPage = data.current_page;
                    this.perPage = Number(data.per_page);
                    this.total = data.total
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchCustomerPartner() {
            try {
                this.is_loading = true;
                const body = {
                    // from_date: this.order.start_date,
                    // to_date: this.order.end_date,
                    // customer_group_ids: this.order.customer_group_ids,
                    // user_ids: this.order.user_ids,
                    search: this.search,
                    per_page: 100,
                }
                const { data, success } = await this.api_handler.get(this.url_api.customer_partners, body);
                if (success) {
                    this.customer_partners = this.mapTreeSelect(data.data);

                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async handlePageChange(page) {
            this.currentPage = page;
            await this.fetchDashboardReport();
        },
        async handlePerPageChange(perPage) {
            this.perPage = perPage;
            await this.fetchDashboardReport();
        },
        mapTreeSelect(data) {
            return data.map(item => {
                return {
                    id: item.id,
                    label: item.name + ' (' + item.code + ')',
                }
            });
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>