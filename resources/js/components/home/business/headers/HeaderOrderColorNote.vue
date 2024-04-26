<template>
    <div>
        <!-- <label class="font-weight-bold font-smaller">Ghi chú: </label> -->
        <div class="row mb-1 font-smaller">
            <div class="col-lg-12">
                <div class="p-1" style="background: rgba(0, 0, 0, 0.02);">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="d-flex mr-2 align-items-center" v-for="(note, index) in list_notes" :key="index">
                            <div class="mr-2 rounded"
                                :style="{ background: note.background, height: '20px', width: '3rem' }">
                            </div>
                            <div class="text-secondary font-smaller">{{ note.name }}</div>
                        </div>
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
                    background: '#ffc107'
                },
                {
                    name: 'Combo',
                    background: '#007bff'
                },

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