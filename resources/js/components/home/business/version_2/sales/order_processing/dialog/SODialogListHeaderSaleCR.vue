<template>
    <div class="modal fade" id="SODialogListHeaderSaleCR" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-uppercase">Danh sách đơn hàng nhà sách</h5>
                    <button @click="closeModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-xs">
                        <b-table :items="items" :fields="fields" responsive hover small bordered head-variant="light"
                            :current-page="pagination.current_page" :per-page="pagination.item_per_page" striped>
                            <template #cell(index)="data">
                                <span>{{ data.index + 1 }}</span>
                            </template>
                            <template #cell(status)="data">
                                <span v-if="data.item.status == 'pending'"
                                    class="badge badge-sm badge-secondary text-xs px-2">Chưa gửi xử lý</span>
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
                                <div v-if="data.item.order_process_sale_by !== null">
                                    <span>{{ data.item.order_process_sale_by.user.name }}</span>
                                </div>
                            </template>
                            <template #cell(created_at)="data">
                                <span>{{ data.item.created_at | formatDate }}</span>
                            </template>
                            <template #cell(processing_at)="data">
                                <div v-if="data.item.order_process_sale_by !== null">
                                    <span>{{ data.item.order_process_sale_by.processing_at | formatDate }}</span>
                                </div>
                            </template>
                            <template #cell(completed_at)="data">
                                <div v-if="data.item.order_process_sale_by !== null">
                                    <span>{{ data.item.order_process_sale_by.completed_at | formatDate }}</span>
                                </div>
                            </template>
                            <template #cell(action)="data">
                                <!-- <button class="btn btn-sm btn-primary px-2 text-xs">
                                    <i class="fas fa-eye"></i>
                                </button> -->
                                <div v-if="data.item.status == 'pending'">
                                    <button @click="sendingOrderPOSale(data.item, data.item.id)" class="btn btn-sm btn-outline-primary px-2 text-xs">
                                        <i class="fas fa-paper-plane mr-1 text-xs"></i>Gửi xử lý
                                    </button>
                                    <button @click="editOrderPOSale(data.item)"
                                        class="btn btn-sm btn-outline-warning px-2 text-xs">
                                        <i class="fas fa-edit mr-1 text-xs"></i>Chỉnh sửa
                                    </button>
                                    <!-- <button class="btn btn-sm btn-danger px-2 text-xs">
                                        <i class="fas fa-trash"></i>
                                    </button> -->
                                </div>

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
                        <!-- end phân trang -->

                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="closeModal()" type="button" class="btn btn-secondary btn-sm px-2 text-xs"
                        data-dismiss="modal">Đóng</button>
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { edit } from 'vue-json-editor/assets/jsoneditor';
import ApiHandler, { APIRequest } from '../../../../../ApiHandler';
export default {
    props: {
        is_show: {
            type: Boolean,
            default: false
        },
        items: {
            type: Array,
            default: []
        },
    },
    watch: {
        is_show: function (val) {
            if (val) {
                $('#SODialogListHeaderSaleCR').modal('show');
            } else {
                $('#SODialogListHeaderSaleCR').modal('hide');
            }
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            current_user: window.Laravel.current_user,
            is_loading: false,
            order_process_sales: [],
            pagination: {
                current_page: 1,
                item_per_page: 10,
                total_items: 0,
                last_page: 0,
                page_options: [10, 20, 50, 100, 500],
            },
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true

                },
                {
                    key: 'status',
                    label: 'Trạng thái',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true
                },
                {
                    key: 'title',
                    label: 'Tiêu đề',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true
                },

                {
                    key: 'central_branch',
                    label: 'Trung tâm',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true

                },
                {
                    key: 'order_process_sale_by',
                    label: 'Người xử lý',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true

                },
                {
                    key: 'created_at',
                    label: 'Ngày tạo đơn',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true

                },
                {
                    key: 'processing_at',
                    label: 'Ngày xử lí',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true

                },
                {
                    key: 'completed_at',
                    label: 'Ngày hoàn thành',
                    class: 'text-center text-nowarp text-xs',
                    sortable: true

                },
                {
                    key: 'action',
                    label: 'Hành động',
                    class: 'text-center text-nowarp text-xs',
                },
            ],
            api: {
                get_all_po_sale_id: '/api/sales-order/get-all-po-sale',
                sending_po_sale: '/api/sales-order/sending-order-po-sales',
            }

        }
    },
    methods: {
        closeModal() {
            this.$emit('close-modal', false);
        },
        editOrderPOSale(item) {
            this.$emit('edit-order-po-sale', item);
        },
        async sendingOrderPOSale(item, id) {
            try {
                let {data, success} = await this.api_handler.put(this.api.sending_po_sale + '/' + id, {}, {
                item: item,
                status: 'sending',
            });
            if (success) {
                this.$emit('sending-order-po-sale', item);
                this.$showMessage('success', 'Thành công', 'Gửi xử lý đơn hàng thành công');
            }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error.response.data.message);
            }

        }
    },
    computed: {
        rows() {
            return this.items.length;
        },
    },

}
</script>
<style lang="scss" scoped></style>
