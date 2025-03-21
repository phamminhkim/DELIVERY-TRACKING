<template>
    <div class="modal fade" id="SODialogCreateColumn" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group d-flex text-xs">
                        <div class="flex-shrink-0 text-xs">
                            <label for="">Nhập Tiêu đề</label>
                        </div>
                        <div class="flex-fill text-xs">
                            <textarea name="" id="" size="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    methods: {

    },

}
</script>
<style lang="scss" scoped>

</style>
