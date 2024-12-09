<template>
    <div class="modal fade" id="SODialogCreateBookV2" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">
                            <span v-if="item.id == -1">Thêm nhà sách</span>
                            <span v-else>Sửa nhà sách</span>
                        </h5>
                        <button @click="closeDialog()" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <small for="" class="text-xs mr-1">Mã nhà sách</small><small class="text-danger">(*)</small>
                            <input v-model="item.code" required class="form-control form-control-sm" rows="3"
                                placeholder="Nhập mã nhà sách" />
                        </div>
                        <div class="form-group">
                            <small for="" class="text-xs mr-1">Tên nhà sách</small><small
                                class="text-danger">(*)</small>
                            <textarea v-model="item.name" required class="form-control form-control-sm" rows="3"
                                placeholder="Nhập tên nhà sách"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeDialog()" type="button" class="btn btn-secondary btn-sm px-4 text-xs"
                            data-dismiss="modal">Đóng</button>
                        <button v-if="item.id == -1" @click="createBookStore()" type="button" class="btn btn-success btn-sm px-4 text-xs">Lưu</button>
                        <button v-else @click="updateBookStore()" type="button" class="btn btn-warning btn-sm px-4 text-xs">Cập nhật</button>
                    </div>

            </div>
        </div>
    </div>

</template>
<script>
import ApiHandler, { APIRequest } from '../../../../../ApiHandler';

export default {
    components: {

    },
    props: {
        is_show_hide_dialog: {
            type: Boolean,
            default: false
        },
        items: {
            type: Array,
            default: () => []
        },
        item_parent: {
            type: Object,
            default: () => ({
                id: -1,
                code: '',
                name: '',
            })
        }
    },
    watch: {
        is_show_hide_dialog: function (val) {
            if (val) {
                $('#SODialogCreateBookV2').modal('show');
            } else {
                $('#SODialogCreateBookV2').modal('hide');
            }
        },
        item_parent: {
            handler: function (val) {
                this.item = val;
            },
            deep: true
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            current_user: window.Laravel.current_user,
            dialog: false,
            item: {
                id: -1,
                code: '',
                name: '',
            },
            api: {
                create_book_store: '/api/book-store/create-book-store',
                update_book_store: '/api/book-store/update-book-store'
            }
        }
    },
    methods: {
        async createBookStore() {
            try {
                const { data, success } = await this.api_handler.post(this.api.create_book_store, {}, { ...this.item });
                if (success) {
                    this.$showMessage('success', 'Thành công', 'Thêm thành công');
                    this.$emit('save-success');
                    this.resetItem();
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
                console.log(error);
            }
        },
        async updateBookStore(){
            try {
                const { data, success } = await this.api_handler.put(this.api.update_book_store + '/' + this.item.id, {}, { ...this.item });
                if (success) {
                    this.$showMessage('success', 'Thành công', 'Cập nhật thành công');
                    this.$emit('update-success');
                    this.resetItem();
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
                console.log(error);
            }
        },
        openDialog() {
            this.dialog = true;
        },
        closeDialog() {
            this.$emit('close-dialog', false);
        },
        resetItem() {
            this.item = {
                id: -1,
                code: '',
                name: '',
            }
        }
    },

}
</script>
<style scoped lang="scss"></style>
