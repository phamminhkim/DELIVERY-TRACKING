<template>
    <div>
        <div class="modal fade" id="dialogTitleOrderSo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Nội dung</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title" class="font-weigh-bold">Tiêu đề</label>
                            <textarea v-model="case_data.title" type="text" class="form-control" id="title"
                                aria-describedby="titleHelp" placeholder="Nhập tiêu đề....">

                            </textarea>
                            <input>

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
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            errors: [],
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
                this.case_data.order_data = this.orders;
                this.case_data.customer_group_id = this.case_save_so.customer_group_id;
                if (this.case_save_so.id !== "") {
                    let { data } = await this.api_handler.put(this.api_order_update_so + '/' + this.case_save_so.id, {}, this.case_data)
                        .finally(() => {
                            this.is_loading = false;
                        });
                    this.$showMessage('success', 'Cập nhật thành công');
                    this.$emit('saveOrderSO', data);
                    this.hideDialogTitleOrderSo();
                } else {
                    let { data } = await this.api_handler.post(this.api_order_save_so, {}, this.case_data)
                        .finally(() => {
                            this.is_loading = false;
                        });
                    this.$showMessage('success', 'Thêm thành công');
                    this.$emit('saveOrderSO', data);
                    this.hideDialogTitleOrderSo();
                }
               
            } catch (error) {
                // handle error
            }
        },
    }
}
</script>
<style lang="scss" scoped></style>