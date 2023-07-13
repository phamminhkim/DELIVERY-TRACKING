<template>
    <div>
        <!-- container -->
        <div class="container">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark">
                                <i :class="form_icon" /> {{ form_title }}
                            </h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <button
                                    class="btn btn-info btn-sm"
                                    @click="showModal()"
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
                                    v-model="filter"
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
                            :filter="filter"
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
                            <template #cell(action)="data">
                                <div class="margin">
                                    <button
                                        class="btn btn-xs mr-1"
                                        @click="editPartner(data.item)"
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
                        <div class="col-md-12">
                            <div class="form-group row">
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
                        </div>
                    </div>
                    <!-- end phân trang -->
                    <!-- tạo form -->
                    <div
                        class="modal fade"
                        id="delivery_partner"
                        tabindex="-1"
                        role="dialog"
                    >
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form @submit.prevent="addPartner">
                                    <div class="modal-header">
                                        <h4 class="modal-title">
                                            <span v-if="!edit"
                                                >Thêm mới nhà vận chuyển</span
                                            >
                                            <span v-if="edit"
                                                >Cập nhật nhà vận chuyển</span
                                            >
                                        </h4>
                                        <button
                                            type="button"
                                            class="close"
                                            data-dismiss="modal"
                                            aria-label="Close"
                                        >
                                            <span aria-hidden="true"
                                                >&times;</span
                                            >
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Mã (code)</label>
                                            <small class="text-danger">*</small>
                                            <input
                                                v-model="partner.code"
                                                class="form-control"
                                                id="code"
                                                name="code"
                                                placeholder="Nhập mã (code) ..."
                                                v-bind:class="
                                                    hasError('code')
                                                        ? 'is-invalid'
                                                        : ''
                                                "
                                            />
                                            <span
                                                v-if="hasError('code')"
                                                class="invalid-feedback"
                                                role="alert"
                                            >
                                                <!-- <strong>{{ getError('code') }}</strong> -->
                                                <div
                                                    v-for="(
                                                        error, index
                                                    ) in getError('code')"
                                                    :key="index"
                                                >
                                                    <strong>{{ error }}</strong>
                                                    <br />
                                                </div>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Tên nhà vận chuyển</label>
                                            <small class="text-danger">*</small>
                                            <input
                                                v-model="partner.name"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                placeholder="Nhập tên nhà vận chuyển ..."
                                                v-bind:class="
                                                    hasError('name')
                                                        ? 'is-invalid'
                                                        : ''
                                                "
                                            /> 
                                            <span
                                                v-if="hasError('name')"
                                                class="invalid-feedback"
                                                role="alert"
                                            >
                                                <!-- <strong>{{ getError('name') }}</strong> -->
                                                <div
                                                    v-for="(
                                                        error, index
                                                    ) in getError('name')"
                                                    :key="index"
                                                >
                                                    <strong>{{ error }}</strong>
                                                    <br />
                                                </div>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>API_URL</label>
                                            <small class="text-danger">*</small>
                                            <input
                                                v-model="partner.api_url"
                                                class="form-control"
                                                id="api_url"
                                                name="api_url"
                                                placeholder="Nhập api_url ..."
                                                v-bind:class="
                                                    hasError('api_url')
                                                        ? 'is-invalid'
                                                        : ''
                                                "
                                            />
                                            <span
                                                v-if="hasError('api_url')"
                                                class="invalid-feedback"
                                                role="alert"
                                            >
                                                <div
                                                    v-for="(
                                                        error, index
                                                    ) in getError('api_url')"
                                                    :key="index"
                                                >
                                                    <strong>{{ error }}</strong>
                                                    <br />
                                                </div>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>API_Key</label>
                                            <small class="text-danger">*</small>
                                            <input
                                                v-model="partner.api_key"
                                                class="form-control"
                                                id="api_key"
                                                name="api_key"
                                                placeholder="Nhập api_key ..."
                                                v-bind:class="
                                                    hasError('api_key')
                                                        ? 'is-invalid'
                                                        : ''
                                                "
                                            />
                                            <span
                                                v-if="hasError('api_key')"
                                                class="invalid-feedback"
                                                role="alert"
                                            >
                                                <!-- <strong>{{ getError('api_key') }}</strong> -->
                                                <div
                                                    v-for="(
                                                        error, index
                                                    ) in getError('api_key')"
                                                    :key="index"
                                                >
                                                    <strong>{{ error }}</strong>
                                                    <br />
                                                </div>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>API_Secret</label>
                                            <small class="text-danger">*</small>
                                            <input
                                                v-model="partner.api_secret"
                                                class="form-control"
                                                id="api_secret"
                                                name="api_secret"
                                                placeholder="Nhập API Secret ..."
                                                v-bind:class="
                                                    hasError('api_secret')
                                                        ? 'is-invalid'
                                                        : ''
                                                "
                                            />
                                            <span
                                                v-if="hasError('api_secret')"
                                                class="invalid-feedback"
                                                role="alert"
                                            >
                                                <!-- <strong>{{ getError('api_secret') }}</strong> -->
                                                <div
                                                    v-for="(
                                                        error, index
                                                    ) in getError('api_secret')"
                                                    :key="index"
                                                >
                                                    <strong>{{ error }}</strong>
                                                    <br />
                                                </div>
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="modal-footer justify-content-between"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-default"
                                            data-dismiss="modal"
                                        >
                                            Đóng
                                        </button>
                                        <button
                                            type="submit"
                                            title="Submit"
                                            class="btn btn-primary"
                                        >
                                            Lưu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end tạo form -->
                </div>
            </div>
        </div>
        <!-- end container -->
    </div>
</template>

<script>
import Vue from "vue";
import toastr from "toastr";
import "toastr/toastr.scss";

import ApiHandler from "../ApiHandler";
export default {
    components: {
        Vue,
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            form_title: "Nhà vận chuyển",
            form_icon: "fas fa-truck",

            token: "Bearer " + window.Laravel.access_token,

            search_placeholder: "Tìm kiếm..",
            is_loading: false,
            edit: false,
            errors: {},
            partner: {
                id: "",
                code: "",
                name: "",
                api_url: "",
                api_key: "",
                api_secret: "",
                is_external: "",
                is_active: "",
            },
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
            filter: "",

            fields: [
                {
                    key: "index",
                    label: "STT",
                    sortable: true,
                    class: "text-nowrap",
                },

                {
                    key: "code",
                    label: "(Code)",
                    sortable: true,
                    class: "text-nowrap",
                },

                {
                    key: "name",
                    label: "Tên nhà vận chuyển",
                    sortable: true,
                    class: "text-nowrap",
                },

                {
                    key: "api_url",
                    label: "API URL",
                    sortable: true,
                    class: "text-nowrap",
                },

                {
                    key: "api_key",
                    label: "API key",
                    sortable: true,
                    class: "text-nowrap",
                },

                {
                    key: "api_secret",
                    label: "API Secret",
                    sortable: true,
                    class: "text-nowrap",
                },

                // {
                //     key: 'is_external',
                //     label: 'External',
                //     sortable: true,
                //     class: 'text-nowrap',

                // },

                // {
                //     key: 'is_active',
                //     label: 'Hoạt động',
                //     sortable: true,
                //     class: 'text-nowrap',

                // },
                {
                    key: "action",
                    label: "Trạng thái",
                    sortable: true,
                    class: "text-nowrap",
                },
            ],

            delivery_partners: [],
            page_url_partner: "/api/master/delivery-partners",
            page_url_create_partner: "/api/master/delivery-partners",
            page_url_update_partner: "/api/master/delivery-partners",
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
        addPartner() {
            if (!this.formValidate()) return;
            var page_url = this.page_url_create_partner;
            var page_url_update = this.page_url_update_partner;
            if (this.edit === false) {
                fetch(page_url, {
                    method: "POST",
                    body: JSON.stringify(this.partner),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json",
                    },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.success == true) {
                            this.reset();
                            this.showMessage("success", "Thêm thành công");
                            this.fetchData();
                            $("#delivery_partner").modal("hide");
                        } else {
                            this.errors = data.errors;
                            this.showMessage(
                                "error",
                                "Thêm mới không thành công"
                            );
                            this.fetchData();
                            this.reset();
                        }
                    })
                    .catch((err) => {})
                    .finally(() => {
                        this.is_loading = false;
                    });
            } else {
                //update
                fetch(page_url_update + "/" + this.partner.id, {
                    method: "PUT",
                    body: JSON.stringify(this.partner),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json",
                    },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.success == true) {
                            this.showMessage("success", "Cập nhật thành công");
                            $("#delivery_partner").modal("hide");
                        } else {
                            this.errors = data.errors;
                            this.showMessage(
                                "error",
                                "Cập nhật không thành công"
                            );
                        }
                        this.fetchData();
                    })
                    .catch((err) => console.log(err));
            }
        },
        deletePartner(id) {
            if (confirm("Bạn muốn xoá?")) {
                fetch(`${this.page_url_destroy_partner}/${id}`, {
                    method: "delete",
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json",
                    },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        this.showMessage("success", "Xoá thành công");
                        this.fetchData();
                        this.reset();
                    });
            }
        },

        editPartner(item) {
            this.edit = true;
            this.errors = {};
            this.partner.id = item.id;
            this.partner.code = item.code;
            this.partner.name = item.name;
            this.partner.api_url = item.api_url;
            this.partner.api_key = item.api_key;
            this.partner.api_secret = item.api_secret;
            $("#delivery_partner").modal("show");
        },
        reset() {
            this.partner.id = "";
            (this.partner.code = ""), (this.partner.name = "");
            this.partner.api_url = "";
            this.partner.api_key = "";
            this.partner.api_secret = "";
            this.partner.is_external = "";
            this.partner.is_active = "";
        },
        showModal() {
            this.edit = false;
            this.errors = {};
            $("#delivery_partner").modal("show");
            this.reset();
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
            //this.reset()
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
        hasError(fieldName) {
            return fieldName in this.errors;
        },
        getError(fieldName) {
            return this.errors[fieldName];
        },
        clearError(event) {
            Vue.delete(this.errors, event.target.name);
        },
        formValidate() {
            const errors = {};
            const pushError = (field_name, error) => {
                if (field_name in errors) {
                    errors[field_name].push(error);
                } else {
                    errors[field_name] = [error];
                }
            };
            const validator = {
                code: () => {
                    if (this.partner.code.length === 0) {
                        pushError("code", "Yêu cầu nhập mã kho.");
                    }
                },
                name: () => {
                    if (this.partner.name.length === 0) {
                        pushError("name", "Yêu cầu nhập mã nhà vận chuyển.");
                    }
                },
                api_url: () => {
                    const url_regex =
                        /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g;
                    if (this.partner.api_url.length === 0) {
                        pushError("api_url", "Yêu cầu nhập api_url.");
                    }
                    if (this.partner.api_url.match(url_regex) === null) {
                        pushError("api_url", "api_url phải là một URL");
                    }
                },
                api_key: () => {
                    if (this.partner.api_key.length === 0) {
                        pushError("api_key", "Yêu cầu nhập api_key.");
                    }
                },
                api_secret: () => {
                    if (this.partner.api_secret.length === 0) {
                        pushError("api_secret", "Yêu cầu nhập api_secret.");
                    }
                },
            };
            Object.keys(validator).forEach((key) => {
                validator[key]();
            });
            this.errors = errors;
            return Object.keys(errors).length === 0;
        },
    },
    computed: {
        rows() {
            return this.delivery_partners.length;
        },
    },
};
</script>

<style lang="scss" scoped>
.table {
    margin-bottom: 0px;
}
</style>
