<template>
    <div>
        <label class="font-weight-bold font-smaller">Ghi chú: </label>
        <div class="row mb-1 font-smaller" v-for="(note, index) in list_notes" :key="index">
            <div class="col-lg-12">
                <div class="row align-items-center">
                    <div class="col-lg-3" >
                        <div :style="{ background: note.background, height: '30px' }">
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="text-secondary font-smaller">{{ note.name }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            is_open_modal_search_order_processes: false,
            tab_value: 'order',
            list_notes: [
                {
                    name: 'Khuyến mãi',
                    background: '#FFFF00'
                },
                {
                    name: 'Combo',
                    background: '#FF0000'
                },
                // {
                //     name: 'Khác',
                //     background: '#0000FF'
                // },
            ]
        }
    }
}
</script>
<style lang="scss" scoped>
.font-smaller {
    font-size: smaller !important;
}
</style>