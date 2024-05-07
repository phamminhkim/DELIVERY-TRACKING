<template>
    <div>
        <HeaderMaterialCombo @openModalMaterialCombo="openModalMaterialCombo" :tab_value="tab_value"
            :count_order_lack="count_order_lack"></HeaderMaterialCombo>
        <TableMaterialCombo @updateMaterialCombo="getUpdateMaterialCombo"
            @deleteMaterialCombo="getDeleteMaterialCombo" :material_combos="material_combos"
            :tab_value="tab_value" :count_order_lack="count_order_lack"></TableMaterialCombo>
            <DialogMaterialCombo ref="dialogMaterialCombo" @storeMaterialCombo="getStoreMaterialCombo">
        </DialogMaterialCombo>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import HeaderMaterialCombo from '../headers/HeaderMaterialCombo.vue';
import TableMaterialCombo from '../tables/TableMaterialCombo.vue';
import DialogMaterialCombo from '../../master/dialogs/DialogMaterialCombo.vue';
export default {
    components: {
        HeaderMaterialCombo,
        TableMaterialCombo,
        DialogMaterialCombo
    },
    props: {
        tab_value: {
            type: String,
            default: 'order_combo'
        },
        count_order_lack: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            material_combos: [],
            api_material_combos: '/api/master/material-combos/get-all',
        }
    },
    created() {
        this.fetchMaterialCombo();
    },
    methods: {
        async fetchMaterialCombo() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_material_combos);
                if (Array.isArray(data)) {
                    this.material_combos = data;
                    this.$emit('listMaterialCombo', data);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        getStoreMaterialCombo(data) {
            this.material_combos.unshift({ ...data.data });
        },
        openModalMaterialCombo() {
            this.$refs.dialogMaterialCombo.showModalMaterialCombo();
        },
        getUpdateMaterialCombo(data) {
            this.material_combos.splice(data.index, 1, { ...data.data.data });
        },
        getDeleteMaterialCombo(index) {
            this.material_combos.splice(index, 1);
        },

    }
}
</script>
<style lang="scss" scoped></style>