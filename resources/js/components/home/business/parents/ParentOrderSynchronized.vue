<template>
    <div>
        <DialogOrderSync :showModalSyncSAP="showModalSyncSAP" :customer_group_id="customer_group_id"
            :customer_groups="customer_groups" :order_syncs="order_syncs" :case_save_so="case_save_so"
            @emitSelectedOrderSync="emitSelectedOrderSync" @processOrderSync="processOrderSync"
            :is_sap_sync="is_sap_sync" 
            :order_syncs_selected="order_syncs_selected"
            :warehouses="warehouses"
            @emitOrderSyncs="getEmitOrderSyncs"
            :use_component_syncs_sap="use_component_syncs_sap"
            @emitDataFetchWarehouse="emitDataFetchWarehouse"
            @viewDetailOrderSyncs="viewDetailOrderSyncs"
            @emitDataWarehouse="emitDataWarehouse">
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
        order_syncs_selected: Array,
        use_component_syncs_sap: {
            type: String,
            default: 'OrderProcess'
        },
        warehouses: Array
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
        },
        getEmitOrderSyncs(item_selecteds){
            this.$emit('emitOrderSyncs', item_selecteds)

        },
        emitDataFetchWarehouse(warehouses){
            this.$emit('emitDataFetchWarehouse', warehouses);
        },
        emitDataWarehouse(warehouse_id){
            this.$emit('emitDataWarehouse', warehouse_id);
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>