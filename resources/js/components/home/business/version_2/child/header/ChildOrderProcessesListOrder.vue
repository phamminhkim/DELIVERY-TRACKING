<template>
    <div class="mb-1">
        <button @click="emitModalListOrderBookStore()" class="btn btn-sm btn-info text-xs px-2 btn-book-store"><i
                class="fas fa-store mr-1"></i>
            Danh sách đơn hàng nhà sách
            <span class="badge badge-sm badge-danger text-xs rounded-pill span-badge">{{ count_order_pending }}</span>
        </button>
        <button @click="emitModalListOrder()" class="btn btn-sm btn-primary text-xs px-2">Danh sách xử lý đơn
            hàng</button>

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
    props: {
        count_order_pending: {
            type: Number,
            default: 0
        },
    },
    data() {
        return {

        }
    },
    methods: {
        emitModalListOrder() {
            this.$emit('modalListOrder');
        },
        emitModalListOrderBookStore() {
            this.$emit('modalListOrderBookStore');
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

.btn-book-store {
    position: relative;
}

.span-badge {
    position: absolute;
    top: -13px;
    left: 0;
}
</style>
