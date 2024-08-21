<template>
    <div class="mb-1">
        <button @click="emitModalListOrder()" class="btn btn-sm btn-primary text-xs px-2">Danh sách xử lý đơn hàng</button>
        <!-- <div class="card border-0 mr-2 shadow-sm card-cursor">
            <div class="card-body p-2 border-0 text-center text-xs" @click="emitModalListOrder(file)">
                <i class="fas fa-list-ul text-info icon-font-size"></i>
                <p class="mb-0">Đơn hàng</p>
                <p class="mb-0 font-italic text-black-50 note-font-size">Danh sách xử lý đơn hàng</p>
            </div>
        </div> -->
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    methods: {
        emitModalListOrder() {
            this.$emit('modalListOrder');
        }
    },

}
</script>
<style lang="scss" scoped>
.icon-font-size {
    font-size: 1.5em;
}
.note-font-size {
    font-size: 0.7em;
}
.card-cursor {
    cursor: pointer;
    &:hover {
        background-color: #f8f9fa;
    }
}
</style>