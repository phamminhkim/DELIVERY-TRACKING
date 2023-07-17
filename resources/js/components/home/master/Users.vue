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
                                    <i class="fas fa-users"></i>
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
                            :items="users"
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

                            <template #cell(active)="data">
                                <span
                                    class="badge bg-success"
                                    v-if="data.item.active == 1"
                                    >Đang hoạt động</span
                                >
                                <span
                                    class="badge bg-warning"
                                    v-if="data.item.active == 0"
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
                                        @click="deleteUser(data.item.id)"
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
                    <DialogAddUpdateUsers
                        ref="AddUpdateDialog"
                        :is_editing="is_editing"
                        :editing_item="editing_item"
                        :refetchData="fetchData"
                    ></DialogAddUpdateUsers>

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
import DialogAddUpdateUsers from "./dialogs/DialogAddUpdateUsers.vue";
export default {
    components: {
        Vue,
        DialogAddUpdateUsers,
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            form_title: window.Laravel.form_title,
            form_icon: " fas fa-users",

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
                    key: "name",
                    label: "Tên người dùng",
                    sortable: true,
                    class: "text-nowrap",
                },
                {
                    key: "email",
                    label: "Email",
                    sortable: true,
                    class: "text-nowrap",
                },
                {
                    key: "phone_number",
                    label: "Số điện thoại",
                    sortable: true,
                    class: "text-nowrap",
                },

                {
                    key: "active",
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

            users: [],
            page_url_user: "/api/master/users",
            page_url_destroy_user: "/api/master/users",
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                let result = await this.api_handler.get(this.page_url_user);
                if (result.success) {
                    this.users = result.data;
                } else {
                    this.showMessage("error", "Lỗi", result.message);
                }
            } catch (error) {
                this.showMessage("error", "Lỗi", error.message);
            }
        },
        async deleteUser(id) {
            if (confirm("Bạn muốn xoá?")) {
                let result = await this.api_handler.delete(
                    `${this.page_url_destroy_user}/${id}`
                );
                if (result.success) {
                    this.users = result.data;
                    this.fetchData();
                    this.showMessage('success', 'Xóa thành công');
                } else {
                    this.showMessage("error", "Lỗi", result.message);
                }
            }
        },
        showCreateDialog() {
            this.is_editing = false;
            this.editing_item = {};
            $("#DialogAddUpdateUser").modal("show");
        },
        showEditDialog(item) {
            this.is_editing = true;
            this.editing_item = item;
            $("#DialogAddUpdateUser").modal("show");
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
            return this.users.length;
        },
    },
};
</script>
