<template>
    <div class="mb-1">
        <button @click="emitModalListOrder()" class="btn btn-sm btn-primary px-2 text-xs">Danh sách xử lý đơn hàng</button>
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
<style lang="scss" scoped>  </style>