<template>
    <div>
        <!-- container -->
        <div class="container-fluid">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark">
                                <i :class="page_structure.header.title_icon" />
                                {{ page_structure.header.title }}
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
                            :items="items"
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

                            <template
                                v-for="(table_cell, index) in this
                                    .page_structure.table.table_cells"
                                #[`cell(${table_cell.target_key})`]="data"
                            >
                                <slot
                                    v-if="table_cell.type === 'template'"
                                    :name="`cell(${table_cell.target_key})`"
                                    v-bind="data"
                                >
                                </slot>

                                <b-img
                                    v-if="table_cell.type === 'image'"
                                    :src="data.item[table_cell.target_key]"
                                    width="100"
                                    :key="index"
                                ></b-img>
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
                                        @click="deleteItem(data.item.id)"
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
                        <!-- end phân trang -->

                        <!-- tạo form -->

                        <CrudDialog
                            :id="dialog_name"
                            :is_editing="is_editing"
                            :editing_item="editing_item"
                            :refetchData="fetchData"
                            :form_structure="page_structure.form"
                            :page_url_create="page_structure.api_url"
                            :page_url_update="page_structure.api_url"
                            :dialog_name="dialog_name"
                        ></CrudDialog>

                        <!-- end tạo form -->
                    </div>
                </div>
            </div>
            <!-- end container -->
        </div>
    </div>
</template>

<script>
import Vue from "vue";
import ApiHandler from "../../ApiHandler";
import CrudDialog from "./CrudDialog.vue";
import CrudPageStructure from "./CrudPageStructure";
export default {
    name: "CrudPage",
    components: {
        Vue,
        CrudDialog,
    },
    props: {
        structure: Object,
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),

            page_structure: {},

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

            fields: [],

            items: [],

            api_item_data: "",
        };
    },
    created() {
        this.page_structure = new CrudPageStructure(this.structure);
        this.fields = this.page_structure.table.table_fields;
        this.api_item_data = this.page_structure.api_url;
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                let result = await this.api_handler.get(this.api_item_data);
                this.items = result.data;                
            } catch (error) {
                this.$showMessage("error", "Lỗi", error.message);
            }
        },
        async deleteItem(id) {
            if (confirm("Bạn muốn xoá?")) {
                try {
                    let result = await this.api_handler.delete(
                        `${this.api_item_data}/${id}`
                    );
                    if (result.success) {
                        this.fetchData();
                        this.$showMessage(
                            "success",
                            "Xóa thành công",
                            result.message
                        );
                    } else {
                        this.$showMessage("error", "Lỗi", result.message);
                    }
                } catch (error) {
                    this.$showMessage("error", "Lỗi", error);
                }
            }
        },
        showCreateDialog() {
            this.is_editing = false;
            this.editing_item = {};
            console.log("#" + this.dialog_name);
            $("#" + this.dialog_name).modal("show");
        },
        showEditDialog(item) {
            this.is_editing = true;
            this.editing_item = item;
            $("#" + this.dialog_name).modal("show");
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
        dialog_name() {
            return (
                "DialogCrudPage" +
                (this.structure ? this.structure.form.unique_name : "")
            );
        },
    },
};
</script>
