<template>
    <div>
        <div class="modal fade" id="dialogTitleOrderSo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-green">
                        <h5 class="modal-title font-weight-bold text-uppercase">Lưu đơn hàng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-light">
                        <div class="font-italic border-bottom border-warning bg-note-save-so p-1">
                            <div v-if="!is_show_modal_sync_sap">
                                <p class="mb-0">
                                    <small class="text-danger">(*)</small>
                                    <small class="text-danger">
                                        Đơn hàng sẽ được lưu vào hệ thống sau khi lưu đơn hàng thành công.
                                    </small>
                                </p>

                            </div>
                            <div v-else class="mb-0">
                                <p class="mb-0">
                                    <small class="text-danger">(*)</small>
                                    <small class="text-danger">
                                        Đơn hàng sẽ được lưu vào hệ thống sau khi lưu đơn hàng thành công.
                                    </small>
                                </p>
                                <p class="mb-0">
                                    <small class="text-danger">(*)</small>
                                    <small class="text-danger">
                                        <b>"Bắt buộc"</b> lưu đơn hàng trước khi thực hiện chức năng <b>"đồng bộ
                                            SAP"</b>.
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title" class="font-weigh-bold mb-0 mt-3">Tiêu đề<small class="text-danger">(*)</small></label>
                            <textarea @keyup.enter="saveOrderSO()" v-model="case_data.title" type="text"
                                class="form-control" id="title" aria-describedby="titleHelp"
                                placeholder="Nhập tiêu đề...." rows="6">
                            </textarea>
                            <!-- <input> -->
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button @click="saveOrderSO()" type="button" class="btn btn-success btn-sm px-4">Lưu</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    props: {
        orders: {
            type: Array,
            default: () => []
        },
        case_save_so: {
            type: Object,
            default: () => { }
        },
        customer_group_id: {
            type: Number,
            default: -1
        },
        order_lacks: {
            type: Array,
            default: () => []
        },
        is_show_modal_sync_sap: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            errors: [],
            case_loading: {
                save: false,
            },
            case_data: {
                title: '',
                customer_group_id: -1,
                order_data: [],
            },
            api_order_save_so: '/api/sales-order/save-so',
            api_order_update_so: '/api/sales-order',
        }
    },
    methods: {
        showDialogTitleOrderSo() {
            this.case_data.title = this.case_save_so.title;
            this.case_data.customer_group_id = this.customer_group_id;
            $('#dialogTitleOrderSo').modal('show');
        },
        hideDialogTitleOrderSo() {
            $('#dialogTitleOrderSo').modal('hide');
        },
        async saveOrderSO() {
            try {
                this.is_loading = true;
                this.case_data.order_data = this.orders.concat(this.order_lacks);
                this.case_data.customer_group_id = this.case_save_so.customer_group_id;
                if (!this.checkCustomerGroup()) {
                    this.is_loading = false;
                    return;
                }
                if (this.case_save_so.id !== "") {
                    try {
                        let { data, message } = await this.api_handler.put(this.api_order_update_so + '/' + this.case_save_so.id, {}, this.case_data)
                        this.$showMessage('success', 'Cập nhật thành công', 'Tổng số đơn hàng: ' + message.so_count + '<br>'
                            + 'Số đơn hàng lưu thành công: ' + message.not_sync_so_count + '<br>'
                            + 'Số đơn hàng lưu thất bại: ' + message.sync_so_count + '<br>');
                        this.$emit('saveOrderSO', data);
                        this.hideDialogTitleOrderSo();
                        this.showModalSyncSap();
                    } catch (error) {
                        let errors = error.response.data.errors;
                        if (errors) {
                            this.$showMessage('error', 'Cập nhật thất bại', errors.sync_all_data);
                        }
                        console.log(error.response);
                    } finally {
                        this.is_loading = false;
                    }
                } else {
                    try {
                        let { data, success } = await this.api_handler.post(this.api_order_save_so, {}, this.case_data)
                        if (success) {
                            this.$showMessage('success', 'Thêm thành công', 'Tổng số đơn hàng: ' + data.so_count + '<br>'
                                + 'Số đơn hàng lưu thành công: ' + data.not_sync_so_count + '<br>'
                                + 'Số đơn hàng lưu thất bại: ' + data.sync_so_count + '<br>');
                            this.$emit('saveOrderSO', data);
                            this.hideDialogTitleOrderSo();
                            //     this.showModalSyncSap();
                            //     this.$showMessage('success', 'Thêm thành công');
                            // this.$emit('saveOrderSO', data);
                            // this.hideDialogTitleOrderSo();
                        }
                    } catch (error) {
                        let errors = error.response.data.errors;
                        if (errors) {
                            this.$showMessage('error', 'Thêm thất bại', errors.sync_all_data);
                        }
                        console.log(error.response);
                    } finally {
                        this.is_loading = false;
                    }
                }

            } catch (error) {
                console.log(error);
            }
        },
        checkCustomerGroup() {
            if (this.case_data.customer_group_id === -1) {
                this.$showMessage('error', 'Vui lòng chọn nhóm khách hàng');
                return false;
            }
            return true;
        },
        showModalSyncSap() {
            if (this.is_show_modal_sync_sap) {
                $('#modalOrderSync').modal('show');
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.bg-note-save-so {
    background: rgb(244, 244, 244);
}
</style>