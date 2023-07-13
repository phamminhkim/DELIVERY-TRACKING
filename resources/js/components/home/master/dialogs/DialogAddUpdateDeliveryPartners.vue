<template>
    <!-- tạo form -->
    <div class="modal fade" id="delivery_partner" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form @submit.prevent="addPartner">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <span v-if="!edit">Thêm mới nhà vận chuyển</span>
                            <span v-if="edit">Cập nhật nhà vận chuyển</span>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Mã (code)</label>
                            <small class="text-danger">*</small>
                            <input v-model="partner.code" class="form-control" id="code" name="code"
                                placeholder="Nhập mã (code) ..." v-bind:class="hasError('code')
                                    ? 'is-invalid'
                                    : ''
                                    " type="text" required />
                            <span v-if="hasError('code')" class="invalid-feedback" role="alert">
                                <!-- <strong>{{ getError('code') }}</strong> -->
                                <div v-for="(
                                    error, index
                                ) in getError('code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Tên nhà vận chuyển</label>
                            <small class="text-danger">*</small>
                            <input v-model="partner.name" class="form-control" id="name" name="name"
                                placeholder="Nhập tên nhà vận chuyển ..." v-bind:class="hasError('name')
                                    ? 'is-invalid'
                                    : ''
                                    " type="text" required />
                            <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                <!-- <strong>{{ getError('name') }}</strong> -->
                                <div v-for="(
                                    error, index
                                ) in getError('name')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>API_URL</label>
                            <small class="text-danger">*</small>
                            <input v-model="partner.api_url" class="form-control" id="api_url" name="api_url"
                                placeholder="Nhập api_url ..." v-bind:class="hasError('api_url')
                                    ? 'is-invalid'
                                    : ''
                                    " type="url" required />
                            <span v-if="hasError('api_url')" class="invalid-feedback" role="alert">
                                <div v-for="(
                                    error, index
                                ) in getError('api_url')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>API_Key</label>
                            <small class="text-danger">*</small>
                            <input v-model="partner.api_key" class="form-control" id="api_key" name="api_key"
                                placeholder="Nhập api_key ..." v-bind:class="hasError('api_key')
                                    ? 'is-invalid'
                                    : ''
                                    " type="text" required />
                            <span v-if="hasError('api_key')" class="invalid-feedback" role="alert">
                                <!-- <strong>{{ getError('api_key') }}</strong> -->
                                <div v-for="(
                                    error, index
                                ) in getError('api_key')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>API_Secret</label>
                            <small class="text-danger">*</small>
                            <input v-model="partner.api_secret" class="form-control" id="api_secret" name="api_secret"
                                placeholder="Nhập API Secret ..." v-bind:class="hasError('api_secret')
                                    ? 'is-invalid'
                                    : ''
                                    " type="text" required />
                            <span v-if="hasError('api_secret')" class="invalid-feedback" role="alert">
                                <!-- <strong>{{ getError('api_secret') }}</strong> -->
                                <div v-for="(
                                    error, index
                                ) in getError('api_secret')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div>
                            </span>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Đóng
                        </button>
                        <button type="submit" title="Submit" class="btn btn-primary">
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end tạo form -->
</template>

<script>
import Vue from "vue";
import APIHandler from '../../ApiHandler';
import toastr from "toastr";
import "toastr/toastr.scss";
export default {
    name: 'DialogAddUpdateDeliveryPartners',
    components: {
        Vue
    },
    props: {
        isEdit: Boolean,
        partnerItem: Object,
        fetchData: Function
    },
    data() {
        return {
            api_handler: new APIHandler(window.Laravel.access_token),

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

            page_url_partner: "/api/master/delivery-partners",
            page_url_create_partner: "/api/master/delivery-partners",
            page_url_update_partner: "/api/master/delivery-partners",
        }
    },
    methods: {
        async addPartner() {
            if (this.is_loading) return;
            // if (!this.formValidate()) return;
            var page_url = this.page_url_create_partner;
            var page_url_update = this.page_url_update_partner;
            if (this.edit === false) {
                try {
                    this.is_loading = true;
                    let data = await this.api_handler.post(page_url, this.partner)
                        .finally(() => {
                            this.is_loading = false;
                        })
                    if (data.success) {
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
                }
                catch (error) {
                    this.showMessage("error", "Lỗi", error.message);
                }

            } else {
                //update
                try {
                    if(this.is_loading) return;
                    this.is_loading = true;
                    let data = await this.api_handler.put(page_url_update, this.partner)
                        .finally(() => {
                            this.is_loading = false;
                        })

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
                } catch (error) {
                    this.showMessage("error", "Lỗi", error.message);
                }
            }
        },
        reset() {
            this.partner.id = "";
            this.partner.code = "";
            this.partner.name = "";
            this.partner.api_url = "";
            this.partner.api_key = "";
            this.partner.api_secret = "";
            this.partner.is_external = "";
            this.partner.is_active = "";
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
        showModal(){
            $("#delivery_partner").modal("show");
        }
    },
    watch: {
        partnerItem: function(item){
            this.errors = {};
            this.partner.id = item.id;
            this.partner.code = item.code;
            this.partner.name = item.name;
            this.partner.api_url = item.api_url;
            this.partner.api_key = item.api_key;
            this.partner.api_secret = item.api_secret;
        },

    }

}
</script>

<style lang="scss" scoped></style>
