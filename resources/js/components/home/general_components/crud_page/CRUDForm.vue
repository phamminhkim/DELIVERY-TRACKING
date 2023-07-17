<template>
    <!-- tạo form -->
    <div class="modal fade" id="DialogAddUpdateCRUDPage" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form @submit.prevent="addItem">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <span>
                                {{
                                    is_editing
                                    ? `Cập nhật ${formStructure.form_name}`
                                    : `Thêm mới ${formStructure.form_name}`
                                }}</span>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group" v-for="(form_field, index) in formStructure.form_fields" :key="index">
                            <label>{{ form_field.label }}</label>
                            <small v-if="form_field.required" class="text-danger">(*)</small>
                            <input v-model="item[form_field.key]" class="form-control" :id="form_field.key"
                                :name="form_field.key" :placeholder="form_field.placeholder" v-bind:class="hasError(form_field.key) ? 'is-invalid' : ''
                                    " :type="form_field.type" :required="form_field.required" />
                            <span v-if="hasError(form_field.key)" class="invalid-feedback" role="alert">
                                <strong>{{ getError(form_field.key) }}</strong>

                            </span>
                        </div>
                        <!-- <div class="form-group">
                            <label>Mã của nhà vận chuyển</label>
                            <small class="text-danger">(*)</small>
                            <input v-model="partner.code" class="form-control" id="code" name="code"
                                placeholder="Nhập mã nhà vận chuyển.." v-bind:class="hasError('code') ? 'is-invalid' : ''
                                    " type="text" required />
                            <span v-if="hasError('code')" class="invalid-feedback" role="alert">
                                <div v-for="(error, index) in getError('code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Tên nhà vận chuyển</label>
                            <small class="text-danger">(*)</small>
                            <input v-model="partner.name" class="form-control" id="name" name="name"
                                placeholder="Nhập tên nhà vận chuyển.." v-bind:class="hasError('name') ? 'is-invalid' : ''
                                    " type="text" required />
                            <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                <div v-for="(error, index) in getError('name')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Api Url</label>
                            <small class="text-danger"></small>
                            <input v-model="partner.api_url" class="form-control" id="api_url" name="api_url"
                                placeholder="Chỉ nhập nếu có tích hợp API nhà vận chuyển" v-bind:class="hasError('api_url') ? 'is-invalid' : ''
                                    " type="url" />
                            <span v-if="hasError('api_url')" class="invalid-feedback" role="alert">
                                <div v-for="(error, index) in getError(
                                    'api_url'
                                )" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Api Key</label>
                            <input v-model="partner.api_key" class="form-control" id="api_key" name="api_key"
                                placeholder="Chỉ nhập nếu có tích hợp API nhà vận chuyển" v-bind:class="hasError('api_key') ? 'is-invalid' : ''
                                    " type="text" />
                            <span v-if="hasError('api_key')" class="invalid-feedback" role="alert">
                                <div v-for="(error, index) in getError(
                                    'api_key'
                                )" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Api Secret</label>
                            <input v-model="partner.api_secret" class="form-control" id="api_secret" name="api_secret"
                                placeholder="Chỉ nhập nếu có tích hợp API nhà vận chuyển" v-bind:class="hasError('api_secret') ? 'is-invalid' : ''
                                    " type="password" />
                            <span v-if="hasError('api_secret')" class="invalid-feedback" role="alert">
                                <div v-for="(error, index) in getError(
                                    'api_secret'
                                )" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div> -->
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="submit" title="Submit" class="btn btn-primary">
                            {{ is_editing ? "Cập nhật" : "Tạo mới" }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Đóng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end tạo form -->
</template>

<script>
import APIHandler from "../../ApiHandler";
import toastr from 'toastr';
import 'toastr/toastr.scss';
export default {
    name: "DialogAddUpdateDeliveryItems",
    props: {
        is_editing: Boolean,
        editing_item: Object,
        refetchData: Function,
        formStructure: Object,
        page_url_create: String,
        page_url_update: String,
    },
    data() {
        return {
            api_handler: new APIHandler(window.Laravel.access_token),

            is_loading: false,
            errors: {},
            item: {},

        };
    },
    created() {
        this.formStructure.form_fields.forEach(field => {
            this.item[field.key] = "".f;
        })
    },
    methods: {
        async addItem() {
            if (this.is_loading) return;
            this.is_loading = true;
            
            if (this.is_editing === false) {
                this.createItem();
            } else {
                this.updateItem();
            }
        },
        async updateItem() {
            try {
                console.log(this.item);
                let data = await this.api_handler
                .put(`${this.page_url_update}/${this.item.id}`, this.item)
                    .finally(() => {
                        this.is_loading = false;
                    });
                    
                    this.showMessage("success", "Cập nhật thành công");
                    this.closeDialog();
                    
                    this.refetchData();
                } catch (error) {
                    this.showMessage("error", "Lỗi", error.message);
                    this.errors = data.errors;
                    this.showMessage(
                        "error",
                        "Cập nhật không thành công",
                        data.message
                        );
                        this.resetForm();
                    }
                },
                async createItem() {
                    try {
                console.log(this.item);
                let data = await this.api_handler
                    .post(this.page_url_create, this.item)
                    .finally(() => {
                        this.is_loading = false;
                    });
                    this.showMessage("success", "Thêm thành công");
                    this.refetchData();
                    this.closeDialog();
                    
                } catch (error) {
                    this.showMessage("error", "Lỗi", error.message);
                    console.log(error);
                    this.errors = error.response.data.errors;
                    // this.showMessage(
                        //     "error",
                        //     "Thêm mới không thành công",
                        //     data.message
                        // );
                        this.resetForm();
                    }
                },
                closeDialog() {
                    this.clearFormErrors();
                    this.resetForm();
                    $("#DialogAddUpdateCRUDPage").modal("hide");
                },
                showMessage(type, title, message) {
                    if (!title) title = "Information";
                    toastr.options = {
                        positionClass: "toast-bottom-right",
                        toastClass: this.getToastClassByType(type),
            };
            toastr[type](message, title);
        },
        hasError(fieldName) {
            return fieldName in this.errors;
            
        },
        getError(fieldName) {
            return this.errors[fieldName];
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
        resetForm() {
            this.item = {};
        },
        clearFormErrors() {
            this.errors = {}
        }
    },
    watch: {
        editing_item: function (item) {
            this.errors = {};
            this.item = { ...item };
        },
    },

};
</script>

<style lang="scss" scoped></style>
