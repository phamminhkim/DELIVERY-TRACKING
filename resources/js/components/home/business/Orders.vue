<template>
    <b-overlay :show="is_loading" rounded="sm">
        <!-- container -->
        <div class="container-fluid">
            <div>
                <div class="row mb-1">
                    <div class="col-md-9">
                        <div class="form-group row">
                            <button
                                type="button"
                                class="btn btn-success btn-sm ml-1 mt-1"
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
                            <span class="badge bg-primary">{{
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
                <!-- end tạo form -->
            </div>
        </div>
        <!-- end container -->
    </b-overlay>
</template>

<script>
import ApiHandler from "../ApiHandler";
export default {
    components: {},
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            form_title: "Quản lý đơn hàng",
            form_icon: "fas fa-truck",

            search_placeholder: "Tìm kiếm..",
            search_pattern: "",

            is_select_all: false,
            selected_ids: [],

            is_editing: false,
            is_loading: false,
            editing_item: {},

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
                    this.showMessage("error", "Lỗi", result.message);
                }
            } catch (error) {
                this.showMessage("error", "Lỗi", error.message);
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
        showMessage(type, title, message) {
            if (!title) title = "Information";
            toastr.options = {
                positionClass: "toast-bottom-right",
                toastClass: this.getToastClassByType(type),
            };
            toastr[type](message, title);
        },
        getToastClassByType(type) {
            switch (type) {
                case "success":
                    return "toastr-bg-green";
                case "error":
                    return "toastr-bg-red";
                case "warning":
                    return "toastr-bg-yellow";
                default:
                    return "";
            }
        },
    },
    computed: {
        rows() {
            return this.orders.length;
        },
    },
};
</script>
