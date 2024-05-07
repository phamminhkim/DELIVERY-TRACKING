<template>
    <div>
        <TableOrderLack :tab_value="tab_value"
        @convertOrderLack="getConvertOrderLack"
            :order_lacks="order_lacks"></TableOrderLack>
    </div>
</template>
<script>
import TableOrderLack from '../tables/TableOrderLack.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order_lack'
        },
        count_order_lack: {
            type: Number,
            default: 0
        },
        order_lacks: {
            type: Array,
            default: () => []
        }
    },
    components: {
        TableOrderLack
    },
    data() {
        return {
        }
    },
    methods: {
        getConvertOrderLack(index, data) {
            this.$emit('convertOrderLack', index, data);
        }
    }
}
</script>
<style lang="scss" scoped></style>