<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesLoadingConvertFile" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Convert File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <small>Đang xử lý file: {{ processing_index }}/{{ file_length }}</small>
                            <small class="ml-1"><i class="fas fa-spinner fa-spin"></i></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        file_length: { type: Number, default: 0 },
        file: { type: Object, default: () => { } },
        processing_index: { type: Number, default: 0 },
    },
    data() {
        return {

        }
    },
    watch: {
        // file_length() {
        //     this.showModal();
        // },
        processing_index() {
            if (this.processing_index == this.file_length) {
                this.hideModal();
            }
        }
    },
    methods: {
        emitConvertFile(file) {
            this.$emit('convertFile', file);
        },
        hideModal() {
            $('#DialogOrderProcessesLoadingConvertFile').modal('hide');
        },
        showModal() {
            $('#DialogOrderProcessesLoadingConvertFile').modal('show');
        }
    }
}
</script>
<style lang="scss" scoped></style>