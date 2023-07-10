<template>
    <div>
        <!-- container -->
        <div class="container-fluid " style="background: rgb(225 225 225 / 30%);">
            <div class="row">
                <div class="col-md-12">

                    <div class="form-group d-flex justify-content-between mt-2">
                        <h4 class="text-uppercase font-weight-bold">Danh sách người dùng </h4>

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
                        :filter="filter" :fields="fields" :items="users" :tbody-tr-class="rowClass">
                        <template #cell(index)="data">
                            {{ data.index + (current_page - 1) * per_page + 1 }}
                        </template>
                        <template #cell(action)="data">
                            <div class="margin">
                                <button class="btn btn-xs" style="margin-right: 10px;" @click="editUser(data.item)"><i
                                        class="fas fa-edit text-green" style="color: green;" title="Edit"></i></button>

                                <button class="btn btn-xs" @click="deleteUser(data.item.id)"><i
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
                <div class="modal fade" id="delivery_user" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form @submit.prevent=addUser>
                                <div class="modal-header">
                                    <h4 class="modal-title">
                                        <span v-if="!edit">Thêm mới User</span>
                                        <span v-if="edit">Cập nhật User</span>
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="form-group">
                                        <label>Tên User</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="user.name" class="form-control" id="name" name="name"
                                            placeholder="Nhập tên User ..."
                                            v-bind:class="hasError('name') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('name') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <!-- <small class="text-danger">*</small> -->
                                        <input v-model="user.email" class="form-control" id="email" name="email"
                                            placeholder="Nhập email ..."
                                            v-bind:class="hasError('email') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('email')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('email') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Số điện thoại</label>
                                        <!-- <small class="text-danger">*</small> -->
                                        <input v-model="user.phone_number" class="form-control" id="phone_number" name="phone_number"
                                            placeholder="Nhập số điện thoại ..."
                                            v-bind:class="hasError('phone_number') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('phone_number')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('phone_number') }}</strong>
                                        </span>
                                    </div>


                                </div>

                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        Đóng
                                    </button>
                                    <button type="submit" class="btn btn-primary">
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
            user: {
                id: '',
                name: '',
                email:'',
                phone_number:'',
                active:'',
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
                    key: 'name',
                    label: 'Tên User',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'email',
                    label: 'Email',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'phone_number',
                    label: 'Số điện thoại',
                    sortable: true,
                    class: 'text-nowrap',

                },


                {
                    key: 'active',
                    label: 'Hoạt động',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'action',
                    label: 'Trạng thái',
                    sortable: true,
                    class: 'text-nowrap',

                },


            ],

            users: [],
            page_url_user: "/api/master/users",
            page_url_create_user: '/api/master/users',
            page_url_update_user: '/api/master/users',
            page_url_destroy_user: '/api/master/users',
        }
    },
    created() {
        this.errors={};
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchUser();
    },
    methods: {
        fetchUser() {
            var page_url = this.page_url_user;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {

                    this.users = data.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        addUser() {
            var page_url = this.page_url_create_user;
            var page_url_update = this.page_url_update_user;
            if (this.edit === false) {
                fetch(page_url, {
                    method: "POST",
                    body: JSON.stringify(this.user),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })

                    .then(res => res.json())
                    .then(data => {
                            // alert('abc');
                            // console.log(data);
                        if (data.success == true) {

                            this.reset();
                            this.showMessage('success', 'Thêm thành công');
                            this.fetchUser();
                            $('#delivery_user').modal('hide');
                        } else {
                            this.errors = data.errors;
                            this.showMessage('error', 'Thêm mới không thành công');
                            this.fetchUser();
                            //this.reset();
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                    });
            } else {
                //update
                fetch(page_url_update + "/" + this.user.id, {
                    method: "PUT",
                    body: JSON.stringify(this.user),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success == true)  {
                            this.showMessage('success', 'Cập nhật thành công');
                            this.fetchUser();
                            $('#delivery_user').modal('hide');
                            //this.clearError();
                        } else {
                            this.errors = data.errors;
                            this.showMessage('error', 'Cập nhật không thành công');
                            this.fetchUser();
                            //this.reset();

                        }
                    })
                    .catch(err => console.log(err));
            }
        },
        deleteUser(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_destroy_user}/${id}`, {
                    method: 'delete',
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.showMessage('success', 'Xoá thành công');
                        this.fetchUser();
                        this.reset();
                    });
            }
        },

        editUser(item) {
            this.edit = true;
            this.errors = {};
            this.user.id = item.id;
            this.user.name = item.name;
            this.user.email = item.email;
            this.user.phone_number = item.phone_number;
            this.user.active = item.active;

            $('#delivery_user').modal('show');
            //this.clearError();
        },
        reset() {
            this.user.id = '';
            this.user.name = '';
            this.user.email = null;
            this.user.phone_number = null;
            this.user.active= '';
        },
        showModal() {
            this.edit = false;
            //console.log('thu');
            this.errors = {};
            $('#delivery_user').modal('show');
            this.reset();
        },
        placeholder() {
            return this.placeholderText;
        },
        rowClass(item, type) {
            if (!item || type !== 'row') return
            if (item.status === 'awesome') return 'table-success'
        },
        // showViewProgram(program) {
        //     this.$refs.viewSubmission.viewSubmission(program);
        // },
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
            if (typeof this.errors === "object" && this.errors !== null) {
                return this.errors.hasOwnProperty(fieldName);
            }
            return false;
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
            return this.user.length;
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
