<template>
    <div>
        <!-- container -->
        <div class="container-fluid">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark">
                                <i :class="form_icon" />
                                {{ form_title }}
                            </h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <button
                                    class="btn btn-info btn-sm"
                                    @click="showCreateDialog()"
                                >
                                    <i class="fa fa-plus"></i>
                                    Tạo mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group row">
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm ml-1 mt-1"
                                >
                                    <strong>
                                        <i
                                            class="fas fa-check mr-1 text-bold"
                                        />Cập nhật chức năng</strong
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
                                        style="
                                            background: #1b1a1a;
                                            color: white;
                                        "
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
                            :items="delivery_partners"
                            :tbody-tr-class="rowClass"
                        >
                            <template #cell(index)="data">
                                {{
                                    data.index +
                                    (pagination.current_page - 1) *
                                        pagination.item_per_page +
                                    1
                                }}
                            </template>
                            <template #cell(is_external)="data">
                                <span
                                    v-if="data.item.is_external"
                                    class="badge bg-primary"
                                    >Đối tác ngoài</span
                                >
                                <span v-else class="badge bg-info">Nội bộ</span>
                            </template>
                            <template #cell(is_active)="data">
                                <span
                                    class="badge bg-success"
                                    v-if="data.item.is_active == 1"
                                    >Đang hoạt động</span
                                >
                                <span
                                    class="badge bg-warning"
                                    v-if="data.item.is_active == 0"
                                    >Ngưng hoạt động</span
                                >
                            </template>
                            <template #cell(action)="data">
                                <div class="margin">
                                    <button
                                        class="btn btn-xs mr-1"
                                        @click="showEditDialog(data.item)"
                                    >
                                        <i
                                            class="fas fa-edit text-green"
                                            title="Edit"
                                        ></i>
                                    </button>

                                    <button
                                        class="btn btn-xs mr-1"
                                        @click="deletePartner(data.item.id)"
                                    >
                                        <i
                                            class="fas fa-trash text-red bigger-120"
                                            title="Delete"
                                        ></i>
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
                    <DialogAddUpdateDeliveryPartners
                        ref="AddUpdateDialog"
                        :is_editing="is_editing"
                        :editing_item="editing_item"
                        :refetchData="fetchData"
                    ></DialogAddUpdateDeliveryPartners>

                    <!-- end tạo form -->
                </div>
            </div>
        </div>
        <!-- end container -->
    </div>
</template>

<script>
import Vue from "vue";
import ApiHandler from "../ApiHandler";
import DialogAddUpdateDeliveryPartners from "./dialogs/DialogAddUpdateDeliveryPartners.vue";
export default {
    components: {
        Vue,
        DialogAddUpdateDeliveryPartners,
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            form_title: window.Laravel.form_title,
            form_icon: "fas fa-truck",

            search_placeholder: "Tìm kiếm..",
            search_pattern: "",

            is_editing: false,
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
                    key: "index",
                    label: "STT",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "code",
                    label: "Mã",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "name",
                    label: "Tên nhà vận chuyển",
                    sortable: true,
                    class: "text-nowrap",
                },
                {
                    key: "api_url",
                    label: "API Url",
                    sortable: true,
                    class: "text-nowrap",
                },
                {
                    key: "api_key",
                    label: "API Key",
                    sortable: true,
                    class: "text-nowrap",
                },
                {
                    key: "api_secret",
                    label: "API Secret",
                    sortable: true,
                    class: "text-nowrap",
                },
                {
                    key: "is_external",
                    label: "Phạm vị",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "is_active",
                    label: "Khả dụng",
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

            delivery_partners: [],
            page_url_partner: "/api/master/delivery-partners",
            page_url_destroy_partner: "/api/master/delivery-partners",
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                let result = await this.api_handler.get(this.page_url_partner);
                if (result.success) {
                    this.delivery_partners = result.data;
                } else {
                    this.showMessage("error", "Lỗi", result.message);
                }
            } catch (error) {
                this.showMessage("error", "Lỗi", error.message);
            }
        },
        async deletePartner(id) {
            if (confirm("Bạn muốn xoá?")) {
                let result = await this.api_handler.delete(
                    `${this.page_url_destroy_partner}/${id}`
                );
                if (result.success) {
                    this.delivery_partners = result.data;
                    this.fetchData();
                    this.showMessage('success', 'Xóa thành công');
                } else {
                    this.showMessage("error", "Lỗi", result.message);
                }
            }
        },
        showCreateDialog() {
            this.is_editing = false;
            $("#DialogAddUpdateDeliveryPartner").modal("show");
        },
        showEditDialog(item) {
            this.is_editing = true;
            this.editing_item = item;
            $("#DialogAddUpdateDeliveryPartner").modal("show");
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
            return this.delivery_partners.length;
        },
    },
};
</script>
