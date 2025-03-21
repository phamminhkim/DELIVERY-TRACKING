<template>
    <div class="modal fade" id="SODialogSaveHeader" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: rgb(224 224 224 / 50%);">
                    <h5 class="modal-title font-weight-bold text-uppercase text-success">
                        <span v-if="so_header.id !== -1">Cập nhật đơn hàng</span>
                        <span v-else>Thêm mới đơn hàng</span>
                    </h5>
                    <button @click="closeModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group text-xs">
                                <div class="row text-xs">
                                    <div class="col-lg-2">
                                        <label for="inputSaveName" class="col-form-label col-form-label-sm text-xs">Tiêu
                                            đề</label>
                                        <small class="text-danger">(*)</small>
                                    </div>
                                    <div class="col-lg-10">
                                        <textarea v-model="so_header_data.title"
                                            class="form-control form-control-sm text-xs" id="inputSaveName"
                                            rows="3"></textarea>
                                        <small class="text-xs font-italic text-danger">(*) không được bỏ trống</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-xs">
                                <div class="row text-xs">
                                    <div class="col-lg-2">
                                        <label for="inputSaveMain"
                                            class="col-form-label col-form-label-sm text-xs">Trung
                                            Tâm</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <textarea v-model="so_header_data.central_branch"
                                            class="form-control form-control-sm text-xs" id="inputSaveMain"
                                            rows="3"></textarea>
                                        <small class="text-xs font-italic text-black-50">(có thể bỏ trống)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-xs">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label for="inputSaveNote"
                                            class="col-form-label col-form-label-sm text-xs font-italic font-weight-normal">Ghi
                                            chú</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <textarea v-model="so_header_data.description"
                                            class="form-control form-control-sm text-xs" id="inputSaveNote"
                                            rows="3"></textarea>
                                        <small class="text-xs font-italic text-black-50">(có thể bỏ trống)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-xs">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label for="inputSaveNote"
                                            class="col-form-label col-form-label-sm text-xs font-italic font-weight-normal">Người
                                            nhận mail</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <!-- <textarea v-model="so_header_data.description" -->
                                        <!-- class="form-control form-control-sm text-xs" id="inputSaveNote" rows="3"></textarea> -->
                                        <!-- <select class="form-control form-control-sm text-xs" id="inputSaveNote">
                                    <option>Khanh</option>
                                    <option>Giáp</option>
                                </select> -->
                                        <select v-model="so_header_data.user_receiver_maile_id"
                                            class="form-control form-control-sm text-xs" @change="demoSelect()">
                                            <option v-for="(user, index) in users" :key="index" :value="user.id">
                                                {{ user.name }} - ({{ user.email }})
                                            </option>
                                        </select>

                                        <!-- <treeselect v-model="so_header_data.user_receiver_mailes" :multiple="true"
                                            :options="data_trees" placeholder="Chọn người nhận mail" /> -->
                                        <small class="text-danger text-xs font-italic">(user thuộc bộ phận điều
                                            phối)</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-4 shadow">
                            <div class="text-xs">
                                <label for="inputSaveStatus"
                                    class="col-form-label col-form-label-sm text-xs font-italic font-weight-normal text-danger">
                                    <u>Review thông tin Mail:</u>
                                </label>
                            </div>
                            <div class="text-xs">
                                <span class="text-gray">Subject: </span>
                                <label for="">
                                    ✨ (Mới) Đơn hàng #... - {{ current_time }}
                                    <!-- currentTime -->
                                </label></br>
                                <span class="text-gray">Form: Example 'hello@example.com'<span></span></span></br>
                                <span class="text-gray">To: '{{ user.email }}'<span></span></span>
                            </div>
                            <div class="form-group text-xs h-75 border p-2 rounded">
                                <SOReviewMail :data="so_header_data" :api_handler="api_handler" :current_time="current_time" />
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button @click="closeModal()" type="button" class="btn btn-secondary btn-sm text-xs px-2"
                        data-dismiss="modal">Đóng</button>
                    <div v-if="so_header.id == -1">
                        <button @click="saveChanges('pending')" type="button"
                            class="btn btn-success btn-sm text-xs px-4">Lưu</button>
                        <button @click="saveChanges('sending')" type="button"
                            class="btn btn-info btn-sm text-xs px-4"><i class="fas fa-paper-plane mr-1"></i>Lưu &
                            gửi</button>
                    </div>
                    <div v-else>
                        <button @click="editSaveHeader('pending')" type="button"
                            class="btn btn-info btn-sm text-xs px-4">Cập nhật</button>
                        <button @click="editSaveHeader('sending')" type="button"
                            class="btn btn-success btn-sm text-xs px-4"><i class="fas fa-paper-plane mr-1"></i>Cập nhật
                            &
                            gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import SOReviewMail from '../review/SOReviewMail.vue';
import { type } from 'jquery';
import { find } from 'lodash';
export default {
    props: {
        is_show: {
            type: Boolean,
            default: false
        },
        so_header: {
            type: Object,
            default: () => {
                return {
                    id: -1,
                    title: '',
                    central_branch: '',
                    description: '',
                    status: 'pending',
                    user_receiver_mailes: [],
                    user_receiver_maile_id: -1,
                }
            }
        },
        api_handler: { type: Object, default: () => { return {} } },
    },
    components: {
        Treeselect,
        SOReviewMail
    },
    watch: {
        is_show: function (val) {
            if (val) {
                $('#SODialogSaveHeader').modal('show');
            } else {
                $('#SODialogSaveHeader').modal('hide');
            }
        },
        so_header: {
            handler: function (val) {
                this.so_header_data = val;
            },
            deep: true
        }
    },
    data() {
        return {
            is_loading: false,
            so_header_data: {
                id: -1,
                title: '',
                central_branch: '',
                description: '',
                status: 'pending',
                user_receiver_maile_id: -1,
            },
            users: [],
            user: {},
            current_time: this.getCurrentTime(),
            // data_trees: [
            //     {
            //         id: 1,
            //         label: 'Phạm Quốc Khanh',
            //     },
            //     {
            //         id: 2,
            //         label: 'Quách Tuyền',

            //     },
            // ],
            api: {
                get_users_processing: '/api/master/users/processing'
            }
        }
    },
    created() {
        this.fetchUsers();
    },
    mounted() {
        this.timer = setInterval(() => {
            this.current_time = this.getCurrentTime(); // Cập nhật thời gian mỗi giây
        }, 1000);
    },
    beforeDestroy() {
        clearInterval(this.timer); // Dọn dẹp interval khi component bị hủy
    },
    methods: {
        getCurrentTime() {
            // let date = new Date();
            // let current_time = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
            // return current_time;
            const now = new Date();
            return now.toISOString().slice(0, 19).replace('T', ' ');
        },
        demoSelect() {
            let user = this.findUserById(this.so_header_data.user_receiver_maile_id);
            this.user = user;
            console.log(user);
        },
        findUserById(id) {
            return find(this.users, { id: id });
        },
        async fetchUsers() {
            try {
                this.is_loading = true;
                const { data, success, errors, message } = await this.api_handler.get(this.api.get_users_processing, {}, {});
                if (success) {
                    this.users = data;
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        closeModal() {
            this.$emit('close-modal', false);
        },
        saveChanges(status) {
            this.so_header_data.status = status;
            this.$emit('save-changes', false, this.so_header_data);
        },
        editSaveHeader(status) {
            this.so_header_data.status = status;
            this.$emit('edit-save-header', false, this.so_header_data);
        }

    },
}
</script>
<style lang="scss" scoped></style>
