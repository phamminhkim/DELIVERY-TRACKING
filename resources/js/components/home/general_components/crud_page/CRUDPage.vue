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
                                <button class="btn btn-info btn-sm" @click="showModal()">
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
                                <button type="button" class="btn btn-warning btn-sm ml-1 mt-1">
                                    <strong>
                                        <i class="fas fa-check mr-1 text-bold" />Cập nhật chức năng</strong>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm mt-1 mb-1">
                                <input type="search" class="form-control -control-navbar" v-model="filter"
                                    :placeholder="search_placeholder" aria-label="Search" />
                                <div class="input-group-append">
                                    <button class="btn btn-default" style="
                                            background: #1b1a1a;
                                            color: white;
                                        ">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- tạo nút edit và delete -->
                    <div class="row">
                        <b-table responsive hover striped :bordered="true" :current-page="pagination.current_page"
                            :per-page="pagination.item_per_page" :filter="filter" :fields="fields"
                            :items="items" :tbody-tr-class="rowClass">
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
                                    <button class="btn btn-xs mr-1" @click="editPartner(data.item)">
                                        <i class="fas fa-edit text-green" title="Edit"></i>
                                    </button>

                                    <button class="btn btn-xs mr-1" @click="deletePartner(data.item.id)">
                                        <i class="fas fa-trash text-red bigger-120" title="Delete"></i>
                                    </button>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <!-- end tạo nút -->
                    <!-- phân trang -->
                    <div class="form-group row">
                        <label class="col-form-label-sm col-md-2" style="text-align: left" for="">Số lượng mỗi
                            trang:</label>
                        <div class="col-md-2">
                            <b-form-select size="sm" v-model="pagination.item_per_page" :options="pagination.page_options">
                            </b-form-select>
                        </div>
                        <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
                        <div class="col-md-3">
                            <b-pagination v-model="pagination.current_page" :total-rows="rows"
                                :per-page="pagination.item_per_page" size="sm" class="ml-1"></b-pagination>
                        </div>
                    </div>
                    <!-- end phân trang -->

                    <!-- tạo form -->
                    <CRUDForm ref="dialog" :isEdit="is_edit_modal" :partnerItem="editing_item"
                        :fetchData="fetchData"></CRUDForm>

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
import ApiHandler from "../../ApiHandler";
import CRUDForm from "./CRUDForm.vue";
export default {
    components: {
        Vue,
        CRUDForm
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            form_title: "Đối tác vận chuyển",
            form_icon: "fas fa-truck",

            search_placeholder: "Tìm kiếm..",
            
           editing_item: {
                id: "",
                code: "",
                name: "",
                api_url: "",
                api_key: "",
                api_secret: "",
                is_external: "",
                is_active: "",
            },
            is_edit_modal: false,
            // ----------------------------
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

            items: [],
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
                    this.items = result.data;
                } else {
                    this.showMessage("error", "Lỗi", result.message);
                }
            } catch (error) {
                this.showMessage("error", "Lỗi", error.message);
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
            this.is_edit_modal = true;
            this.editing_item = item;
            this.$refs.dialog.showModal();
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
            this.is_edit_modal = false;
            this.$refs.dialog.showModal();
        },
        rowClass(item, type) {
            if (!item || type !== "row") return;
            if (item.status === "awesome") return "table-success";
        },

    },
    computed: {
        rows() {
            return this.items.length;
        },
    },
};
</script>

<style lang="scss" scoped>
.table {
    margin-bottom: 0px;
}
</style>
