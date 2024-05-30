<template>
    <div>
        <DialogOrderSync :showModalSyncSAP="showModalSyncSAP" :customer_group_id="customer_group_id"
            :customer_groups="customer_groups" :orders="orders"></DialogOrderSync>
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
        orders: Array
    },
    components: {
        DialogOrderSync
    },
    data() {
        return {

        }
    },
    methods: {
        
    },
    computed: {
       
    }
}
</script>
<style lang="scss" scoped></style>