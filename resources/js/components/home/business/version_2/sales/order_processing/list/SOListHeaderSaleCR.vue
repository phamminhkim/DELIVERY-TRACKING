<template>
    <div>
        <!-- <div class="row">
            <div class="col-lg-12">
                <div class="form-group text-xs">
                    <div class="form-group-sm">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="input-group input-group-sm text-xs">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">
                                            <small for="" class="text-xs labes-m"><i
                                                    class="fas fa-search mr-1 font-weight-bold"></i>Tìm kiếm</small>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-xs"
                                        placeholder="Nhập tìm kiếm..." aria-label="Username"
                                        aria-describedby="basic-addon1">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <select class="form-control form-control-sm text-xs">
                                        <option value="-1">Chọn trạng thái</option>
                                        <option v-for="status in order_status" :value="status.id">
                                            {{ status.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="form-group small">
            <b-table :items="items" :fields="fields" responsive hover small bordered head-variant="light"
                :tbody-tr-class="rowHighLight"
                :current-page="pagination.current_page" :per-page="pagination.item_per_page" striped>
                <template #cell(index)="data">
                    <small>{{ data.index + 1 }}</small>
                </template>
                <template #cell(status)="data">
                    <small v-if="data.item.status == 'pending'" class="badge badge-sm badge-secondary px-2">Chưa
                        gửi xử lý</small>
                    <small v-if="data.item.status == 'sending'" class="badge badge-sm badge-info px-2">Đã gửi xử
                        lý</small>
                    <small v-if="data.item.status == 'processing'" class="badge badge-sm badge-warning px-2">Đang
                        xử lý</small>
                    <small v-if="data.item.status == 'completed'" class="badge badge-sm badge-success px-2">Hoàn
                        thành</small>
                    <small v-if="data.item.status == 'canceled'" class="badge badge-sm badge-danger px-2">Hủy
                        bỏ</small>

                </template>
                <template #cell(order_process_sale_by)="data">
                    <div v-if="data.item.order_process_sale_by !== null">
                        <small>
                            <i class="fas fa-user-cog mr-1 text-xs" :class="{
                                'text-warning': data.item.status == 'processing',
                                'text-success': data.item.status == 'completed',
                                'text-danger': data.item.status == 'canceled',

                            }"></i>
                            {{ data.item.order_process_sale_by.user.name }}
                        </small>
                    </div>
                    <div class="small" v-else>
                        <button v-show="getIsStatus(data.item.status, 'pending')" @click="sendingOrderPOSale(data.item, data.item.id)"
                            class="btn btn-sm btn-danger px-2 p-0 text-xs border-0">
                            <i class="fas fa-paper-plane mr-1 text-xs"></i>Gửi
                        </button>
                    </div>
                </template>
                <template #cell(created_at)="data">
                    <small>{{ data.item.created_at | formatDate }}</small>
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
                <template #cell(order_process_sale_receive)="data">
                    <div v-if="data.item.order_process_sale_receive !== null">
                        <small><i class="fas fa-user-check mr-1 text-xs" :class="{
                            'text-secondary': getIsStatus(data.item.status, 'pending'),
                            'text-info': getIsStatus(data.item.status, 'sending'),
                            'text-danger': getIsStatus(data.item.status, 'canceled'),
                            'text-success': getIsStatus(data.item.status, 'completed'),
                            'text-warning': getIsStatus(data.item.status, 'processing'),
                        }"></i>{{
                            data.item.order_process_sale_receive.received.name }}</small>
                    </div>
                    <div v-else>
                        <select v-model="data.item.order_process_sale_receive"
                            class="form-control form-control-sm text-xs">
                            <option :value="-1">Chọn người nhận</option>
                            <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>


                </template>
                <template #cell(received_at)="data">
                    <div v-if="data.item.order_process_sale_receive !== null">
                        <span>{{ data.item.order_process_sale_receive.received_at | formatDate }}</span>
                    </div>

                </template>received_at
                <template #cell(action)="data">
                    <!-- <button class="btn btn-sm btn-primary px-2 text-xs">
                                    <i class="fas fa-eye"></i>
                                </button> -->
                    <div class="d-flex small">

                        <div v-if="data.item.status == 'pending'" class="d-flex">
                            <!-- <button @click="sendingOrderPOSale(data.item, data.item.id)"
                                class="btn btn-sm btn-outline-primary px-2 text-xs">
                                <i class="fas fa-paper-plane mr-1 text-xs"></i>Gửi xử lý
                            </button> -->
                            <button @click="deleteOrderPOSale(data.item)"
                                class="btn btn-sm btn-outline-danger px-2 p-0 mr-1 text-xs">
                                <i class="fas fa-trash-alt mr-1 text-xs"></i>Xóa
                            </button>
                            <button @click="editOrderPOSale(data.item)"
                                class="btn btn-sm btn-outline-warning px-2 p-0 mr-1 text-xs">
                                <i class="fas fa-edit mr-1 text-xs"></i>Chỉnh sửa
                            </button>
                            <button @click="viewOrderPOSale(data.item)"
                                class="btn btn-sm btn-outline-info px-2 p-0 text-xs">
                                <i class="far fa-eye mr-1 text-xs"></i>Xem
                            </button>
                        </div>
                        <div v-else>
                            <button @click="viewOrderPOSale(data.item)"
                                class="btn btn-sm btn-outline-info px-2 p-0 text-xs">
                                <i class="far fa-eye mr-1 text-xs"></i>Xem
                            </button>
                        </div>
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

</template>
<script>
import { edit } from 'vue-json-editor/assets/jsoneditor';
import ApiHandler, { APIRequest } from '../../../../../ApiHandler';
export default {
    props: {

        items: {
            type: Array,
            default: []
        },
    },

    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            current_user: window.Laravel.current_user,
            is_loading: false,
            order_process_sales: [],
            users: [],
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
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'p-1',
                    sortable: true

                },
                {
                    key: 'status',
                    label: 'Trạng thái',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'bg-white p-0',
                    sortable: true
                },
                {
                    key: 'code',
                    label: 'Mã',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'text-primary bg-white p-0 px-1',
                    sortable: true
                },
                {
                    key: 'title',
                    label: 'Tiêu đề',
                    class: 'text-nowrap text-xs',
                    tdClass: 'p-1',
                    sortable: true
                },

                {
                    key: 'central_branch',
                    label: 'Trung tâm',
                    class: 'text-nowrap text-xs',
                    tdClass: 'p-1',
                    sortable: true

                },
                {
                    key: 'order_process_sale_receive',
                    label: 'Người nhận',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'p-0',
                    sortable: true

                },
                {
                    key: 'order_process_sale_by',
                    label: 'Người xử lý',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'p-1',
                    sortable: true

                },

                {
                    key: 'received_at',
                    label: 'Ngày gửi',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'bg-white p-0',
                    sortable: true

                },

                {
                    key: 'processing_at',
                    label: 'Ngày xử lí',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'bg-white p-0',
                    sortable: true

                },
                {
                    key: 'completed_at',
                    label: 'Ngày hoàn thành',
                    class: 'text-center text-nowrap text-xs',
                    tdClass: 'bg-white p-0',
                    sortable: true

                },
                {
                    key: 'created_at',
                    label: 'Ngày tạo',
                    class: 'text-center text-nowrap text-xs',
                    sortable: true,
                    tdClass: 'p-1',

                },
                {
                    key: 'user.name',
                    label: 'Người tạo',
                    class: 'text-center text-nowrap text-xs',
                    sortable: true,
                    tdClass: 'p-1',

                },
                {
                    key: 'action',
                    label: 'Hành động',
                    class: 'text-center text-nowrap text-xs ',
                    tdClass: 'p-1',
                },
            ],
            order_status: [],
            api: {
                get_all_po_sale_id: '/api/sales-order/get-all-po-sale',
                sending_po_sale: '/api/sales-order/sending-order-po-sales',
                get_users_processing: '/api/master/users/processing',
                get_order_status: '/api/sales-order/get-order-status',
            }

        }
    },
    created() {
        this.fetchUsers();
        this.fetchOrderStatus();
    },
    methods: {
        rowHighLight(row) {
            if (row.status == 'pending') {
                return 'bg-pending text-white';
            }
            // if (row.status == 'processing') {
            //     return 'bg-warning';
            // }
            // if (row.status == 'completed') {
            //     return 'bg-success';
            // }
            // if (row.status == 'canceled') {
            //     return 'bg-danger';
            // }
        },
        getIsStatus(status, default_status) {
            if (status != null) {
                return status == default_status;
            }
            return false;
        },
        async fetchOrderStatus() {
            try {
                this.is_loading = true;
                const { data, success, errors, message } = await this.api_handler.get(this.api.get_order_status, {}, {});
                if (success) {
                    console.log(data, 'data status');
                    this.order_status = data;
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchUsers() {
            try {
                this.is_loading = true;
                const { data, success, errors, message } = await this.api_handler.get(this.api.get_users_processing, {}, {});
                if (success) {
                    this.users = data;
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        closeModal() {
            this.$emit('close-modal', false);
        },
        editOrderPOSale(item) {
            this.$emit('edit-order-po-sale', item);
        },
        deleteOrderPOSale(item) {
            this.$emit('delete-order-po-sale', item);
        },
        viewOrderPOSale(item) {
            // viết giống hàm editOrderPOSale
            this.editOrderPOSale(item);
        },
        async sendingOrderPOSale(item, id) {
            try {
                let { data, success } = await this.api_handler.put(this.api.sending_po_sale + '/' + id, {}, {
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
<style lang="scss" scoped>
::v-deep .bg-pending{
    background: #a1a1a1a1 !important;
}
</style>
