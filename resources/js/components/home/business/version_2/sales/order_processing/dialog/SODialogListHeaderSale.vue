<template>
    <div class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-uppercase">Danh sách đơn hàng nhà sách</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <div class="row">
                        <div class="col-lg-6 ml-auto">
                            <div class="input-group input-group-sm mb-1 text-xs">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                                </div>
                                <input v-model="filter" type="text" class="form-control form-control-sm" placeholder="Tìm kiếm">
                            </div>
                        </div>
                    </div> -->
                    <div class="form-group">
                        <b-table :items="order_process_sales" :fields="fields" responsive hover small bordered
                            :current-page="pagination.current_page" :per-page="pagination.item_per_page" striped>
                            <template #cell(index)="data">
                                <span>{{ data.index + 1 }}</span>
                            </template>
                            <template #cell(status)="data">
                                <span v-if="data.item.status == 'sending'"
                                    class="badge badge-sm badge-info text-xs px-2">Đã gửi xử lý</span>
                                <span v-if="data.item.status == 'processing'"
                                    class="badge badge-sm badge-warning text-xs px-2">Đang xử lý</span>
                                <span v-if="data.item.status == 'completed'"
                                    class="badge badge-sm badge-success text-xs px-2">Hoàn thành</span>
                                <span v-if="data.item.status == 'canceled'"
                                    class="badge badge-sm badge-danger text-xs px-2">Hủy bỏ</span>

                            </template>
                            <template #cell(order_process_sale_by)="data">
                                <div v-if="data.item.order_process_sale_by == null">
                                    <button @click="applyOrderPOSales(data.item, 'processing')"
                                        class="btn btn-sm btn-info  text-xs px-2">Nhận đơn</button>
                                </div>
                                <div v-else>
                                    <span>{{ data.item.order_process_sale_by.user.name }}</span>
                                </div>
                            </template>
                            <template #cell(is_sap_sync)="data">
                                <div v-if="data.item.order_process_sale_by != null">
                                    <div
                                        v-if="current_user.id == data.item.order_process_sale_by.processing_by && data.item.status == 'processing'">
                                        <button @click="applyOrderPOSales(data.item, 'completed')"
                                            class="btn btn-sm btn-success text-xs px-2">Hoàn thành</button>
                                        <button @click="applyOrderPOSales(data.item, 'canceled')"
                                            class="btn btn-sm btn-danger text-xs px-2">Hủy bỏ</button>
                                    </div>
                                </div>

                            </template>
                            <template #cell(created_at)="data">
                                <span>{{ data.item.created_at | formatDate }}</span>
                            </template>
                            <template #cell(processing_at)="data">
                                <span v-if="data.item.order_process_sale_by != null">{{
                                    data.item.order_process_sale_by.processing_at | formatDate }}</span>
                            </template>
                            <template #cell(completed_at)="data">
                                <span v-if="data.item.order_process_sale_by != null">{{
                                    data.item.order_process_sale_by.completed_at | formatDate }}</span>
                            </template>
                        </b-table>
                        <div class="row">
                            <label class="col-form-label-sm col-md-2" style="text-align: left" for="per-page-select">
                                Số lượng mỗi trang:
                            </label>
                            <div class="col-md-2">
                                <b-form-select size="sm" v-model="pagination.item_per_page" :options="pagination.page_options.map((option) => option.toString())
                                    "></b-form-select>
                            </div>
                            <label class="col-form-label-sm col-md-1" style="text-align: left"></label>
                            <div class="col-md-3">
                                <b-pagination v-model="pagination.current_page" :total-rows="rows"
                                    :per-page="pagination.item_per_page" :limit="3"
                                    :size="pagination.page_options.length.toString()" class="ml-1"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div> -->
            </div>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../../../../ApiHandler';
import * as XLSX from 'xlsx';

export default {
    props: {
        order_process_sales: { type: Array, default: [] },
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            current_user: window.Laravel.current_user,
            is_loading: false,
            filter: '',
            fields: [
                { key: 'index', label: 'STT', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'status', label: 'Trạng thái', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'order_process_sale_by', label: 'Người xử lý', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'is_sap_sync', label: 'Đồng bộ Sap', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'title', label: 'Tiêu đề', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'central_branch', label: 'Trung tâm', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'description', label: 'Ghi chú', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'user.name', label: 'Người tạo đơn', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'created_at', label: 'Ngày tạo đơn', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'processing_at', label: 'Ngày xử lí', sortable: true, class: 'text-center text-nowrap text-xs' },
                { key: 'completed_at', label: 'Ngày hoàn thành', sortable: true, class: 'text-center text-nowrap text-xs' },
            ],
            pagination: {
                current_page: 1,
                item_per_page: 10,
                total_items: 0,
                last_page: 0,
                page_options: [10, 20, 50, 100, 500],
            },
            api: {
                apply_order_po_sales: '/api/sales-order/apply-order-po-sales',
            },
            data_excel: {},
        }
    },
    methods: {
        async applyOrderPOSales(item, status) {
            try {
                this.is_loading = true;
                let item_apply = { ...item };
                item_apply.status = status;
                const { data, success, errors, message } = await this.api_handler.post(this.api.apply_order_po_sales, {}, {
                    item: item,
                    status: status,
                });
                if (success) {
                    // formart giá trị trong biến status thành chuỗi
                    let toString = (status) => {
                        return status.toString();
                    }
                    switch (toString(status)) {
                        case 'completed':
                            this.$showMessage('success', 'Thành công', 'Hoàn thành đơn hàng thành công');
                            break;
                        case 'canceled':
                            this.$showMessage('success', 'Thành công', 'Hủy đơn hàng thành công');
                            break;
                        case 'processing':
                            this.$showMessage('success', 'Thành công', 'Nhận đơn hàng thành công');
                            this.exportExcel(data);
                            break;
                        default:
                            break;
                    }
                    // this.$showMessage('success', 'Thành công', 'Nhận đơn hàng thành công');
                    this.$emit('applyOrderPOSales', data);
                    // this.data_excel = data;

                }
                else {
                    this.$showMessage('error', 'Lỗi', message);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error.response.data.message);
            } finally {
                this.is_loading = false;
            }
        },
        exportExcel(data) {
            const headerMapping = {
                customer_key: "Tên NS",
                type: "Loại Phiếu",
                customer_name: "Nhóm",
                barcode: "Mã NS",
                sap_name: "Tên Sản Phẩm",
                price: "Giá bán",
                quantity: "Số Lượng",
                specifications: "Qui cách",
                description: "Ghi Chú",
                order_process_sale_id: "Số phiếu",
                barcode_cty: "Barcode_cty",
                sap_code: "Mã SAP",
                sap_name: "Tên SP",
                unit: "DVT",
            };
            // Chuyển đổi dữ liệu
            const formattedData = data.order_process_sale_items.map(item => ({
                "Tên NS": item.customer_key,
                "Loại Phiếu": item.type,
                "Nhóm": item.customer_name,
                "Mã NS": item.barcode,
                "Tên Sản Phẩm": item.sap_name,
                "Giá bán": item.price,
                "Số Lượng": item.quantity,
                "Qui cách": item.specifications,
                "Ghi Chú": item.description,
                "Số phiếu": item.count_order,
                "Barcode_cty": item.barcode_cty,
                "Mã SAP": item.sap_code,
                "Tên SP": item.sap_name,
                "DVT": item.unit,

            }));

            const ws = XLSX.utils.json_to_sheet(formattedData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            XLSX.writeFile(wb, 'Dữ_liệu_sales.xlsx');
        }
    },
    computed: {
        rows() {
            return this.order_process_sales.length;
        },
    },
}
</script>
<style lang="scss" scoped></style>
