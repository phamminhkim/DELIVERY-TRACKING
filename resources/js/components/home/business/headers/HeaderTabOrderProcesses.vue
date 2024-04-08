<template>
    <div>
        <b-tabs card>
            <b-tab v-for="(tab, index) in tabs" :key="index" :title="tab.name" @click="onChangeTab(tab)" :active="tab_value == tab.value" >
              <template #title>
                {{ tab.name }}
                <small v-if="tab.value == 'order_lack'" class="badge badge-danger" > {{ count_order_lack }}</small>
              </template>
            </b-tab>
        </b-tabs>
    </div>
</template>
<script>
export default {
    props: {
        tab_value: {
            type: String,
        },
        count_order_lack: {
            type: Number,
            default: 0
        }       
    },

    data() {
        return {
            tabs: [
                { name: 'Xử lý đơn hàng', value: 'order' },
                { name: 'Hàng tặng hàng', value: 'order_donated' },
                { name: 'Đơn hàng thiếu', value: 'order_lack' },

            ],
            change_value: 'order'
        }
    },
    methods: {
        onChangeTab(tab) {
            this.change_value = tab.value;
            this.$emit('changeTab', this.change_value);
        }
    }
}
</script>
<style lang="scss" scoped></style>