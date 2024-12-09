<template>
    <div class="modal fade" id="SODialogSaveHeader" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg">
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
                    <div class="form-group text-xs">
                        <div class="row text-xs">
                            <div class="col-lg-2">
                                <label for="inputSaveName" class="col-form-label col-form-label-sm text-xs">Tiêu
                                    đề</label>
                                <small class="text-danger">(*)</small>
                            </div>
                            <div class="col-lg-10">
                                <textarea v-model="so_header_data.title" class="form-control form-control-sm text-xs"
                                    id="inputSaveName" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-xs">
                        <div class="row text-xs">
                            <div class="col-lg-2">
                                <label for="inputSaveMain" class="col-form-label col-form-label-sm text-xs">Trung
                                    Tâm</label>
                            </div>
                            <div class="col-lg-10">
                                <textarea v-model="so_header_data.central_branch"
                                    class="form-control form-control-sm text-xs" id="inputSaveMain" rows="3"></textarea>
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
                                    class="form-control form-control-sm text-xs" id="inputSaveNote" rows="3"></textarea>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button @click="closeModal()" type="button" class="btn btn-secondary btn-sm text-xs px-2"
                        data-dismiss="modal">Đóng</button>
                    <div v-if="so_header.id == -1">
                        <button @click="saveChanges('pending')" type="button"
                            class="btn btn-info btn-sm text-xs px-4">Lưu</button>
                        <button @click="saveChanges('sending')" type="button"
                            class="btn btn-success btn-sm text-xs px-4"><i class="fas fa-paper-plane mr-1"></i>Lưu &
                            gửi</button>
                    </div>
                    <div v-else>
                        <button @click="editSaveHeader('pending')" type="button"
                            class="btn btn-info btn-sm text-xs px-4">Cập nhật</button>
                        <button @click="editSaveHeader('sending')" type="button"
                            class="btn btn-success btn-sm text-xs px-4"><i class="fas fa-paper-plane mr-1"></i>Cập nhật &
                            gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
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
                }
            }
        }
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
            so_header_data: {
                id: -1,
                title: '',
                central_branch: '',
                description: '',
                status: 'pending',
            }
        }
    },
    methods: {
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
