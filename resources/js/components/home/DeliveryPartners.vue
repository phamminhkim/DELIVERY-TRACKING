<template>
    <div>
        <!-- container -->
        <div class="container" style="background: rgb(225 225 225 / 30%);">
            <div class="row">
                <div class="col-md-12">

                    <div class="form-group d-flex justify-content-between mt-2">
                        <h4 class="text-uppercase font-weight-bold">Danh sách Nhà vận chuyển </h4>

                        <!-- tạo mới -->

                        <div class="text-right">
                            <button @click="showModal()" class="btn btn-sm btn-info" style="height: 35px;width: 90px;">
                                + Tạo mới
                            </button>
                        </div>
                        <!-- end tạo mới -->
                    </div>

                </div>
            </div>



            <div class="container-header">


                <!-- end tạo mới -->


                <!-- tìm kiếm -->
                <div class="row" style="background-color:#F4F6F9">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm mt-1 mb-1">
                            <input type="search" class="form-control -control-navbar" v-model="filter"
                                :placeholder="placeholderText" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-default" style="background: #1b1a1a;color: white;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end tìm kiếm -->
                <!-- tạo nút edit và delete -->
                <div>
                    <b-table responsive hover striped :bordered="true" :current-page="current_page" :per-page="per_page"
                        :filter="filter" :fields="fields" :items="delivery_partners" :tbody-tr-class="rowClass">
                        <template #cell(index)="data">
                            {{ data.index + (current_page - 1) * per_page + 1 }}
                        </template>
                        <template #cell(action)="data">
                            <div class="margin">
                                <button class="btn btn-xs" style="margin-right: 10px;" @click="editPartner(data.item)"><i
                                        class="fas fa-edit text-green" style="color: green;" title="Edit"></i></button>

                                <button class="btn btn-xs" @click="deletePartner(data.item.id)"><i
                                        class="fas fa-trash text-red" style="color: red;" title="Delete"></i></button>
                            </div>
                        </template>
                    </b-table>
                </div>
                <!-- end tạo nút -->
                <!-- phân trang -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-form-label-sm col-md-1" style="text-align: left" for="">Per
                                page:</label>
                            <div class="col-md-3">
                                <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                                </b-form-select>
                            </div>
                            <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
                            <div class="col-md-3">
                                <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page" size="sm"
                                    class="ml-1"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end phân trang -->
                <!-- tạo form -->
                <div class="modal fade" id="delivery_partner" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form @submit.prevent=addPartner>
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
                                            placeholder="Nhập mã (code) ..."
                                            v-bind:class="hasError('code') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('code')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('code') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Tên nhà vận chuyển</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="partner.name" class="form-control" id="name" name="name"
                                            placeholder="Nhập tên nhà vận chuyển ..."
                                            v-bind:class="hasError('name') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('name') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>API_URL</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="partner.api_url" class="form-control" id="api_url" name="api_url"
                                            placeholder="Nhập api_url ..."
                                            v-bind:class="hasError('api_url') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('api_url')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('api_url') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>API_Key</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="partner.api_key" class="form-control" id="api_key" name="api_key"
                                            placeholder="Nhập api_key ..."
                                            v-bind:class="hasError('api_key') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('api_key')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('api_key') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>API_Secret</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="partner.api_secret" class="form-control" id="api_secret"
                                            name="api_secret" placeholder="Nhập API Secret ..."
                                            v-bind:class="hasError('api_secret') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('api_secret')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('api_secret') }}</strong>
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
            </div>
        </div>
        <!-- end container -->
    </div>
</template>

<script>
import Vue from 'vue';
import toastr from 'toastr';
import 'toastr/toastr.scss';
export default {
    components: {
        Vue
    },

    data() {
        return {
            token: '',
            pagesNumber: [],
            placeholderText: "Tìm kiếm ",
            loading: false,
            edit: false,
            errors: {},
            partner: {
                id: '',
                code: '',
                name: '',
                api_url: '',
                api_key: '',
                api_secret: '',
                is_external: '',
                is_active: '',
            },
            //component per-page
            pagination: {
                total: 0,
                per_page: 2,
                from: 1,
                to: 0,
                current_page: 1,
            },
            per_page: 10,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],
            //search
            filter: "",

            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    sortable: true,
                    class: 'text-nowrap',
                },

                {
                    key: 'code',
                    label: '(Code)',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'name',
                    label: 'Tên nhà vận chuyển',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'api_url',
                    label: 'API URL',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'api_key',
                    label: 'API key',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'api_secret',
                    label: 'API Secret',
                    sortable: true,
                    class: 'text-nowrap',

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
                    key: 'action',
                    label: 'Trạng thái',
                    sortable: true,
                    class: 'text-nowrap',

                },


            ],

            delivery_partners: [],
            page_url_partner: "/api/master/delivery-partner",
            page_url_create_partner: '/api/master/delivery-partner',
            page_url_update_partner: '/api/master/delivery-partner',
            page_url_destroy_partner: '/api/master/delivery-partner',
        }
    },
    created() {
        this.errors = {};
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchPartner();
    },
    methods: {
        fetchPartner() {
            var page_url = this.page_url_partner;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {

                    this.delivery_partners = data.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        addPartner() {
            var page_url = this.page_url_create_partner;
            var page_url_update = this.page_url_update_partner;
            if (this.edit === false) {
                fetch(page_url, {
                    method: "POST",
                    body: JSON.stringify(this.partner),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })

                    .then(res => res.json())
                    .then(data => {

                        if (data.success == true) {

                            this.reset();
                            this.showMessage('success', 'Thêm thành công');
                            this.fetchPartner();
                            $('#delivery_partner').modal('hide');
                        } else {
                            this.errors = data.errors;
                            this.showMessage('error', 'Thêm mới không thành công');
                            this.fetchPartner();
                            this.reset();
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                    });
            } else {
                //update
                fetch(page_url_update + "/" + this.partner.id, {
                    method: "PUT",
                    body: JSON.stringify(this.partner),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success == true) {
                            this.showMessage('success', 'Cập nhật thành công');
                            this.fetchPartner();
                            $('#delivery_partner').modal('hide');
                            //this.clearError();
                        } else {
                            this.errors = data.errors;
                            this.showMessage('error', 'Cập nhật không thành công');
                            this.fetchPartner();
                            //this.reset();
                        }
                    })
                    .catch(err => console.log(err));
            }
        },
        deletePartner(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_destroy_partner}/${id}`, {
                    method: 'delete',
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.showMessage('success', 'Xoá thành công');
                        this.fetchPartner();
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
            $('#delivery_partner').modal('show');
            //this.clearError();
        },
        reset() {
            this.partner.id = '';
            this.partner.code = '',
            this.partner.name = '';
            this.partner.api_url = '';
            this.partner.api_key = '';
            this.partner.api_secret = '';
            this.partner.is_external = '';
            this.partner.is_active = '';
        },
        showModal() {
            this.edit = false;
            //console.log('thu');
            this.errors = {};
            $('#delivery_partner').modal('show');
            this.reset();
        },
        placeholder() {
            return this.placeholderText;
        },
        rowClass(item, type) {
            if (!item || type !== 'row') return
            if (item.status === 'awesome') return 'table-success'
        },
        showMessage(type, title, message) {
            if (!title)
                title = "Information";
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
            // if (typeof this.errors === "object" && this.errors !== null) {
            //     return this.errors.hasOwnProperty(fieldName);
            // }
            // return false;
            return fieldName in this.errors;
        },
        getError(fieldName) {
            //console.log(fieldName+"="+ this.errors[fieldName][0]);
            return this.errors[fieldName][0];
        },
        clearError(event) {
            Vue.delete(this.errors, event.target.name);
            //  console.log(event.target.name);
        },
    },
    computed: {
        rows() {
            return this.delivery_partners.length;
        },
    }
}
</script>

<style lang="scss" scoped
>
.table {
    margin-bottom: 0px;
}
</style>
