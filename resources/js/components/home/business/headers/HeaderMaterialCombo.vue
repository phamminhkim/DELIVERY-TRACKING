<template>
    <div>
        <div class="row">
            <div class="col-lg-12 text-left">
                <button @click="showModalMaterialCombo()" type="button" class="btn px-4 btn-light btn-sm py-1 text-info"><i
                        class="fas fa-plus mr-2"></i>Thêm mới</button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order_combo'
        },
        count_order_lack: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {

        }
    },
    methods: {
        showModalMaterialCombo() {
            this.$emit('openModalMaterialCombo');
        }
    }
}
</script>
<style lang="scss" scoped></style>