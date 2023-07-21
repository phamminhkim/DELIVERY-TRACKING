<template>
    <b-overlay :show="is_loading" rounded="sm">
        <!-- container -->
        <div class="container-fluid">
            <div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group row">
                            <!-- <button type="button" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>Tạo hợp đồng</button> -->
                            <div class="btn-group">
                                <button
                                    type="button"
                                    class="btn btn-warning btn-xs"
                                    @click="is_show_search = !is_show_search"
                                >
                                    <span v-if="!is_show_search"
                                        >Hiện tìm kiếm</span
                                    >
                                    <span v-if="is_show_search"
                                        >Ẩn tìm kiếm</span
                                    >
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-warning btn-xs"
                                    @click="is_show_search = !is_show_search"
                                >
                                    <i
                                        v-if="is_show_search"
                                        class="fas fa-angle-up"
                                    ></i>
                                    <i v-else class="fas fa-angle-down"></i>
                                </button>
                            </div>
                            <!-- <button type="button" :title="$t('form.filter')" onclick="location.reload(true)" class="btn btn-secondary  btn-xs ml-1" ><i class="fas fa-redo-alt" title="Refresh"></i></button> -->
                            <button
                                @click="filter_data()"
                                class="btn btn-secondary btn-xs ml-1"
                            >
                                <i class="fas fa-sync-alt" title="Tải lại"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row"></div>
                    </div>
                </div>
                <div class="row" v-if="is_show_search">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label
                                        for="start_date"
                                        class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
                                        >Từ ngày</label
                                    >
                                    <div class="col-sm-2">
                                        <input
                                            type="date"
                                            v-model="form_filter.start_date"
                                            class="form-control form-control-sm mt-1"
                                        />
                                    </div>
                                    <label
                                        class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
                                        for=""
                                        >Đến ngày</label
                                    >
                                    <div class="col-sm-2">
                                        <input
                                            type="date"
                                            v-model="form_filter.end_date"
                                            class="form-control form-control-sm mt-1"
                                        />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label
                                        class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
                                        for=""
                                        >Trạng thái</label
                                    >
                                    <div class="col-sm-6 mt-1 mb-1">
                                        <treeselect
                                            placeholder="All"
                                            :multiple="true"
                                            :disable-branch-nodes="false"
                                            v-model="form_filter.status"
                                            :options="order_statuses"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="col-md-12"
                                    style="text-align: center"
                                >
                                    <button
                                        type="submit"
                                        class="btn btn-warning btn-sm mt-1 mb-1"
                                        @click="filter_data()"
                                    >
                                        <i class="fa fa-search"></i>
                                        Tìm
                                    </button>
                                    <button
                                        type="reset"
                                        class="btn btn-secondary btn-sm mt-1 mb-1"
                                        @click="clearFilter()"
                                    >
                                        <i class="fa fa-reset"></i>
                                        Xóa bộ lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-md-9">
                        <div class="form-group row">
                            <button
                                type="button"
                                class="btn btn-success btn-sm ml-1 mt-1"
                                @click="showCreateDialog"
                            >
                                <strong>
                                    <i
                                        class="fas fa-truck-loading mr-1 text-bold"
                                    />Tạo vận đơn</strong
                                >
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm mt-1 mb-1">
                            <input
                                type="search"
                                class="form-control -control-navbar"
                                v-model="search_pattern"
                                :placeholder="search_placeholder"
                                aria-label="Search"
                            />
                            <div class="input-group-append">
                                <button
                                    class="btn btn-default"
                                    style="background: #1b1a1a; color: white"
                                >
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- tạo nút edit và delete -->

                <div class="row">
                    <b-table
                        responsive
                        hover
                        striped
                        :bordered="true"
                        :current-page="pagination.current_page"
                        :per-page="pagination.item_per_page"
                        :filter="search_pattern"
                        :fields="fields"
                        :items="orders"
                        :tbody-tr-class="rowClass"
                    >
                        <template #head(selection)>
                            <b-form-checkbox
                                class="ml-1"
                                v-model="is_select_all"
                                @change="selectAll"
                            ></b-form-checkbox>
                        </template>
                        <template v-slot:cell(selection)="data">
                            <b-form-checkbox
                                class="ml-1"
                                :value="data.item.id"
                                v-model="selected_ids"
                            >
                            </b-form-checkbox>
                        </template>
                        <template #cell(index)="data">
                            {{
                                data.index +
                                (pagination.current_page - 1) *
                                    pagination.item_per_page +
                                1
                            }}
                        </template>
                        <template #cell(warehouse)="data">
                            <span>{{ data.value.name }}</span>
                        </template>
                        <template #cell(status)="data">
                            <span :class="data.value.badge_class">{{
                                data.value.name
                            }}</span>
                        </template>
                        <template #cell(receiver)="data">
                            <span>{{ data.value.receiver_name }}</span>
                        </template>
                        <template #cell(action)="data">
                            <div class="margin">
                                <button
                                    class="btn btn-xs mr-1 text-info"
                                    @click="showInfoDialog(data.item)"
                                >
                                    <i
                                        class="fas fa-info-circle mr-1"
                                        title="Xem thông tin chi tiết"
                                    />Xem thông tin chi tiết
                                </button>
                            </div>
                        </template>
                    </b-table>
                </div>
                <!-- end tạo nút -->
                <!-- phân trang -->
                <div class="row">
                    <label
                        class="col-form-label-sm col-md-2"
                        style="text-align: left"
                        for=""
                        >Số lượng mỗi trang:</label
                    >
                    <div class="col-md-2">
                        <b-form-select
                            size="sm"
                            v-model="pagination.item_per_page"
                            :options="pagination.page_options"
                        >
                        </b-form-select>
                    </div>
                    <label
                        class="col-form-label-sm col-md-1"
                        style="text-align: left"
                        for=""
                    ></label>
                    <div class="col-md-3">
                        <b-pagination
                            v-model="pagination.current_page"
                            :total-rows="rows"
                            :per-page="pagination.item_per_page"
                            size="sm"
                            class="ml-1"
                        ></b-pagination>
                    </div>
                </div>
                <!-- end phân trang -->

                <!-- tạo form -->
                <DialogCreateDelivery
                :order_ids="creating_delivery_order_ids"
                />
                <!-- end tạo form -->
            </div>
        </div>
        <!-- end container -->
    </b-overlay>
</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";
import ApiHandler from "../ApiHandler";
import DialogCreateDelivery from "./dialogs/DialogCreateDelivery.vue";

export default {
    components: {
        Treeselect,
        DialogCreateDelivery
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            search_placeholder: "Tìm kiếm..",
            search_pattern: "",

            is_select_all: false,
            selected_ids: [],
            creating_delivery_order_ids: [],

            is_editing: false,
            is_loading: false,
            editing_item: {},

            is_show_search: false,
            form_filter: {
                start_date: "",
                end_date: "",
                status: [],
            },
            order_statuses: [
                { id: 10, label: "Đang xử lí đơn hàng" },
                { id: 20, label: "Đã duyệt & đang soạn hàng" },
                { id: 30, label: "Đang vận chuyển" },
                { id: 40, label: "Đã giao một phần" },
                { id: 100, label: "Đã giao xong" },
            ],

            pagination: {
                item_per_page: 10,
                current_page: 1,
                page_options: [
                    10,
                    50,
                    100,
                    500,
                    { value: this.rows, text: "All" },
                ],
            },

            fields: [
                {
                    key: "selection",
                    label: "All",
                    stickyColumn: true,
                },
                {
                    key: "index",
                    label: "STT",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "customer.code",
                    label: "Mã KH",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "warehouse",
                    label: "Kho hàng",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "sap_so_number",
                    label: "SO",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "sap_do_number",
                    label: "DO",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "receiver",
                    label: "Bên nhận",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "status",
                    label: "Trạng thái",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "action",
                    label: "Hành động",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
            ],

            orders: [],
            api_url_orders: "/api/admin/orders",
        };
    },
    created() {
        this.fetchData();
    },
    watch: {
        "$route.query": {
            immediate: true,
            handler(new_query, old_query) {
                if (new_query !== old_query) {
                    this.fetchData(new_query);
                }
            },
        },
    },
    methods: {
        async fetchData(query) {
            try {
                if (this.is_loading) return;
                this.is_loading = true;
                let result = await this.api_handler.get(
                    this.api_url_orders,
                    query
                );
                if (result.success) {
                    this.orders = result.data;
                } else {
                    this.$showMessage("error", "Lỗi", result.message);
                }
            } catch (error) {
                this.$showMessage("error", "Lỗi", error.message);
            } finally {
                this.is_loading = false;
            }
        },
        selectAll() {
            this.selected_ids = [];
            if (this.is_select_all) {
                for (let i in this.orders) {
                    this.selected_ids.push(this.orders[i].id);
                }
            }
        },
        rowClass(item, type) {
            if (!item || type !== "row") return;
            if (item.status === "awesome") return "table-success";
        },
        showCreateDialog(){
            this.creating_delivery_order_ids = this.selected_ids;
            $('#DialogCreateDelivery').modal('show');
        }
    },
    computed: {
        rows() {
            return this.orders.length;
        },
    },
};
</script>
