<template>
    <div>
        <DialogOrderSync :showModalSyncSAP="showModalSyncSAP" :customer_group_id="customer_group_id"
            :customer_groups="customer_groups" :order_syncs="order_syncs" :case_save_so="case_save_so"
            @emitSelectedOrderSync="emitSelectedOrderSync" @processOrderSync="processOrderSync"
            :is_sap_sync="is_sap_sync" 
            @viewDetailOrderSyncs="viewDetailOrderSyncs">
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
        is_sap_sync: Boolean,
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
        },
        emitSelectedOrderSync(selected) {
            this.$emit('emitSelectedOrderSync', selected);
        },
        viewDetailOrderSyncs(){
            this.$emit('viewDetailOrderSyncs');
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>