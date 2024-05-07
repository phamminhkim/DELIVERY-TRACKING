<template>
    <div>
        <HeaderMaterialDonated @openModalMaterialDonated="openModalMaterialDonated" :tab_value="tab_value"
            :count_order_lack="count_order_lack"></HeaderMaterialDonated>
        <TableMaterialDonated @updateMaterialDonated="getUpdateMaterialDonated"
            @deleteMaterialDonated="getDeleteMaterialDonated" :material_donateds="material_donateds"
            :tab_value="tab_value" :count_order_lack="count_order_lack"></TableMaterialDonated>
        <DialogMaterialDonated ref="dialogMaterialDonated" @storeMaterialDonated="getStoreMaterialDonated">
        </DialogMaterialDonated>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import DialogMaterialDonated from '../../master/dialogs/DialogMaterialDonated.vue';
import TableMaterialDonated from '../tables/TableMaterialDonated.vue';
import HeaderMaterialDonated from '../headers/HeaderMaterialDonated.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order_donated'
        },
        count_order_lack: {
            type: Number,
            default: 0
        }
    },
    components: {
        DialogMaterialDonated,
        TableMaterialDonated,
        HeaderMaterialDonated
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            material_donateds: [],
            api_material_donateds: '/api/master/material-donateds/get-all',
        }
    },
    created() {
        this.fetchMaterialDonated();
    },
    methods: {
        async fetchMaterialDonated() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_material_donateds);
                if (Array.isArray(data)) {
                    this.material_donateds = data;
                    this.$emit('listMaterialDonated', data);
                }   
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        getStoreMaterialDonated(data) {
            this.material_donateds.unshift({ ...data.data });
        },
        openModalMaterialDonated() {
            this.$refs.dialogMaterialDonated.showModalMaterialDonated();
        },
        getUpdateMaterialDonated(data) {
            this.material_donateds.splice(data.index, 1, { ...data.data.data });
        },
        getDeleteMaterialDonated(index) {
            this.material_donateds.splice(index, 1);
        },
       
    }
}
</script>
<style lang="scss" scoped></style>