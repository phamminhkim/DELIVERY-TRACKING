<template>
    <!-- tạo form -->
    <div class="modal fade" id="DialogAddUpdateCRUDPage" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form @submit.prevent="addPartner">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <span>
                                {{
                                    is_editing
                                    ? "Cập nhật nhà vận chuyển"
                                    : "Thêm mới nhà vận chuyển"
                                }}</span>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Mã của nhà vận chuyển</label>
                            <small class="text-danger">(*)</small>
                            <input v-model="partner.code" class="form-control" id="code" name="code"
                                placeholder="Nhập mã nhà vận chuyển.." v-bind:class="hasError('code') ? 'is-invalid' : ''
                                    " type="text" required />
                            <span v-if="hasError('code')" class="invalid-feedback" role="alert">
                                <!-- <strong>{{ getError('code') }}</strong> -->
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
                                <!-- <strong>{{ getError('name') }}</strong> -->
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
                                <!-- <strong>{{ getError('api_key') }}</strong> -->
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
                                <!-- <strong>{{ getError('api_secret') }}</strong> -->
                                <div v-for="(error, index) in getError(
                                    'api_secret'
                                )" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
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

export default {
    name: "DialogAddUpdateDeliveryPartners",
    props: {
        is_editing: Boolean,
        editing_item: Object,
        refetchData: Function,
    },
    data() {
        return {
            api_handler: new APIHandler(window.Laravel.access_token),

            is_loading: false,
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

            page_url_create_partner: "/api/master/delivery-partners",
            page_url_update_partner: "/api/master/delivery-partners",
        };
    },
    methods: {
        async addPartner() {
            if (this.is_loading) return;
            this.is_loading = true;

            if (this.is_editing === false) {
                this.createPartner();
            } else {
                this.updatePartner();
            }
        },
        async updatePartner() {
            try {
                let data = await this.api_handler
                    .put(`${this.page_url_update_partner}/${this.partner.id}`, this.partner)
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
            }
        },
        async createPartner() {
            try {
                let data = await this.api_handler
                    .post(this.page_url_create_partner, this.partner)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.showMessage("success", "Thêm thành công");
                this.refetchData();
                this.closeDialog();

            } catch (error) {
                this.showMessage("error", "Lỗi", error.message);
                this.errors = data.errors;
                this.showMessage(
                    "error",
                    "Thêm mới không thành công",
                    data.message
                );
            }
        },
        closeDialog() {
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
    },
    watch: {
        editing_item: function (item) {
            this.errors = {};
            this.partner.id = item.id;
            this.partner.code = item.code;
            this.partner.name = item.name;
            this.partner.api_url = item.api_url;
            this.partner.api_key = item.api_key;
            this.partner.api_secret = item.api_secret;
        },
    },
};
</script>

<style lang="scss" scoped></style>
