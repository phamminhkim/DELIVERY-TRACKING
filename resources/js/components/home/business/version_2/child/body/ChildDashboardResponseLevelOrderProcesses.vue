<template>
    <div>
        <b-dropdown text="Ẩn/hiện cột" class="mb-2">
            <div style="max-height: 200px; overflow-y: auto; left: 0;">
                <b-dropdown-item-button v-for="field in fields" :key="field.key">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <b-form-checkbox 
                            v-model="field.visible" 
                            @click.stop="toggleColumn(field.key)" 
                            style="margin-right: 8px;">
                        </b-form-checkbox>
                        {{ field.label }}
                    </label>
                </b-dropdown-item-button>
            </div>
        </b-dropdown>
        <b-table :fields="filteredFields" show-empty :busy="is_loading" :items="reportes" responsive small :bordered="true"
            class="text-xs" :per-page="perPage">
            <template #empty>
                <h6 class="text-center">Không có dữ liệu nào để hiển thị</h6>
            </template>
            <template #table-busy>
                <div class="text-center text-primary my-2">
                    <b-spinner class="align-middle" type="grow"></b-spinner>
                    <strong>Đang tải dữ liệu...</strong>
                </div>
            </template>
            <template #thead-top="data">
                <b-tr>
                    <b-th class="text-center border-0" :colspan="webMtColspan" variant="info">Dữ liệu Web MT</b-th>
                    <b-th class="text-center border-0" :colspan="sapColspan" variant="success">Dữ liệu SAP</b-th>
                </b-tr>
            </template>
            <template #cell(index)="data">
                {{ (data.index + 1) + (currentPage * perPage) - perPage }}
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
            order: {
                customer_group_ids: [],
                customer_code: '',
                customer_name: '',
                created_bys: [],
                start_date: null,
                end_date: null,
                po_number: '',
                so_uid: '',
                sap_code: '',
                sap_codes: [],
                sap_user: '',
                created_by: -1,
            },
            reportes: [],
            // customer_partners: [],            
            fields: [
                { key: 'index', label: 'STT', visible: true, group: 'webMt' },
                { key: 'created_at', label: 'Ngày tạo', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'customer_code', label: 'Mã KH', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'customer_name', label: 'Tên KH', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'order_process.customer_group_name', label: 'Nhóm KH', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'po_number', label: 'PO Number', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'customer_sku_code', label: 'PO Unit Barcode', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'customer_sku_name', label: 'PO Unit Barcode Description', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'sku_sap_code', label: 'PO Mã SAP', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'quantity3_sap', label: 'PO Số Lượng SAP', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                { key: 'sku_sap_unit', label: 'PO ĐVT', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },                
                { key: 'so_uid', label: 'SO Number', class: 'text-nowrap text-xs', visible: true, group: 'webMt' },
                
                { key: 'sap_code', label: 'SO Mã SKU', class: 'text-nowrap text-xs', visible: true, group: 'sap' },
                { key: 'sap_name', label: 'SO Tên SP', class: 'text-nowrap text-xs', visible: true, group: 'sap' },
                { key: 'sap_quantity', label: 'SO Số Lượng CF', class: 'text-nowrap text-xs', visible: true, group: 'sap' },
                { key: 'sap_unit_code', label: 'SO ĐVT', class: 'text-nowrap text-xs', visible: true, group: 'sap' },
                { key: 'fulfillment_rate', label: 'Tỉ lệ đáp ứng', class: 'text-nowrap text-xs', visible: true, group: 'sap' },
                { key: 'sap_user', label: 'Người tạo', class: 'text-nowrap text-xs', visible: true, group: 'sap' },
            ],
            url_api: {
                dashboard_report: 'api/dashboard/MT/report',
                // customer_partners: 'api/master/customer-partners',
            }
        }
    },
    watch: {
        // search_change: {
        //     handler: function (val) {
        //         this.search = val;
        //         this.fetchCustomerPartner();
        //     },
        //     deep: true
        // },
        change_search: {
            handler: function (val) {
                this.fetchDashboardReport();
            },
            deep: true
        },

    },
    async created() {
        await this.fetchDashboardReport();
        // await this.fetchCustomerPartner();

    },
    methods: {
        toggleColumn(key) {
            const field = this.fields.find(f => f.key === key);
            if (field) field.visible = !field.visible;
        },
        async fetchDashboardReport() {
            try {
                this.is_loading = true;
                const body = {
                    from_date: this.start_date,
                    to_date: this.end_date,
                    page: this.currentPage,
                    per_page: this.perPage,
                    po_number: this.order.po_number,
                    sap_codes: this.order.sap_codes,
                    sap_code: this.order.sap_code,
                    sap_user: this.order.sap_user,
                    so_uid: this.order.so_uid,
                    customer_code: this.order.customer_code,
                    customer_name: this.order.customer_name,
                    customer_group_ids: this.order.customer_group_ids,
                }
                const { data, success,errors } = await this.api_handler.get(this.url_api.dashboard_report, body);
                console.log(data)
                if (success) {
                    this.reportes = data.data;
                    this.total = data.total;
                }else {
                    this.$showMessage('error', 'Lỗi', errors.sap_error);
                }
            } catch (error) {
                // this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        // async fetchCustomerPartner() {
        //     try {
        //         this.is_loading = true;
        //         const body = {
        //             // from_date: this.order.start_date,
        //             // to_date: this.order.end_date,
        //             // customer_group_ids: this.order.customer_group_ids,
        //             // user_ids: this.order.user_ids,
        //             search: this.search,
        //             per_page: 100,
        //         }
        //         const { data, success } = await this.api_handler.get(this.url_api.customer_partners, body);
        //         if (success) {
        //             this.customer_partners = this.mapTreeSelect(data.data);

        //         }
        //     } catch (error) {
        //         this.$showMessage('error', 'Lỗi', error);
        //     } finally {
        //         this.is_loading = false;
        //     }
        // },
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
        },
        setOrder(order) {
            this.order = order;
        }
    },
    computed: {
        filteredFields() {
            return this.fields.filter(field => field.visible);
        },
        webMtColspan() {
            // Đếm các trường hiển thị thuộc nhóm "Dữ liệu Web MT"
            return this.fields.filter(field => field.visible && field.group === 'webMt').length;
        },
        sapColspan() {
            // Đếm các trường hiển thị thuộc nhóm "Dữ liệu SAP"
            return this.fields.filter(field => field.visible && field.group === 'sap').length;
        }
    },
}
</script>
<style lang="scss" scoped></style>