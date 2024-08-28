<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesLoadingSAP" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog">
                <div  class="modal-content text-center modal-dialog-centered">
                    <div class="modal-body p-0 mt-3">
                        <div class="form-group text-center text-xs mb-0">
                            <p class="text-warning  p-0"><i class="fas fa-spinner fa-spin mr-2"></i>{{ title }}</p>
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
        is_loading: { type: Boolean, default: false },
        title: { type: String, default: 'Đang xử lý' },
    },
  
    data() {
        return {

        }
    },
    methods: {
        hideModal() {
            $('#DialogOrderProcessesLoadingSAP').modal('hide');
        },
        showModal() {
            $('#DialogOrderProcessesLoadingSAP').modal('show');
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>
