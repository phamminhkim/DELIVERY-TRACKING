<template>
    <div>
        <b-tabs small>
            <b-tab v-for="(tab, index) in tabs" :key="index" :title="tab.name" @click="onChangeTab(tab)" :active="tab_value == tab.value" >
              <template #title>
                {{ tab.name }}
                <small v-if="tab.value == 'order_lack'" class="badge badge-warning" > {{ count_order_lack }}</small>
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
                { name: 'Đơn hàng', value: 'order' },
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