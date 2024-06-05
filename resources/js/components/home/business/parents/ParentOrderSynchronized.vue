<template>
    <div>
        <DialogOrderSync :showModalSyncSAP="showModalSyncSAP" :customer_group_id="customer_group_id"
            :customer_groups="customer_groups" :order_syncs="order_syncs" :case_save_so="case_save_so"
             @processOrderSync="processOrderSync">
        </DialogOrderSync>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import DialogOrderSync from '../dialogs/DialogOrderSync.vue';
export default {
    props: {
        showModalSyncSAP: Function,
        customer_group_id: Number,
        customer_groups: Array,
        order_syncs: Array,
        case_save_so: Object,
    },
    components: {
        DialogOrderSync
    },
    data() {
        return {
           
            
        }
    },
    methods: {
        processOrderSync() {
           this.$emit('processOrderSync');
        }
    },
    computed: {
       
    }
}
</script>
<style lang="scss" scoped></style>