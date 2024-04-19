<template>
    <div>
        <b-table small responsive hover :items="material_combos" :fields="fields">
            <template #cell(index)="data">
                <div>
                    <label>{{ data.index + 1 }}</label>
                </div>
            </template>
            <template #cell(customer_group_id)="data">
                <div v-if="index_edit == data.index">
                    <input type="text" class="form-control form-control-edit"
                        v-model="edit_material_combo.customer_group" :placeholder="data.item.customer_group_id"
                        v-bind:class="hasError('customer_group_id') ? 'is-invalid' : ''" />
                    <span v-if="hasError('customer_group_id')" class="invalid-feedback" role="alert">
                        <strong>{{ getError('customer_group_id') }}</strong>
                    </span>
                </div>
                <div v-else>
                    <label>{{ data.item.customer_group_id }}</label>
                </div>
            </template>
            <template #cell(sap_code)="data">
                <div v-if="index_edit == data.index">
                    <input type="text" class="form-control form-control-edit" v-model="edit_material_combo.sap_code"
                        :placeholder="data.item.sap_code" v-bind:class="hasError('sap_code') ? 'is-invalid' : ''" />
                    <span v-if="hasError('sap_code')" class="invalid-feedback" role="alert">
                        <strong>{{ getError('sap_code') }}</strong>
                    </span>
                </div>
                <div v-else>
                    <label>{{ data.item.sap_code }}</label>
                </div>
            </template>
            <template #cell(name)="data">
                <div v-if="index_edit == data.index">
                    <input type="text" class="form-control form-control-edit" v-model="edit_material_combo.name"
                        :placeholder="data.item.name" v-bind:class="hasError('name') ? 'is-invalid' : ''" />
                    <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                        <strong>{{ getError('name') }}</strong>
                    </span>
                </div>
                <div v-else>
                    <label>{{ data.item.name }}</label>
                </div>
            </template>
            <template #cell(bar_code)="data">
                <div v-if="index_edit == data.index">
                    <input type="text" class="form-control form-control-edit" v-model="edit_material_combo.bar_code"
                        :placeholder="data.item.bar_code" />
                </div>
                <div v-else>
                    <label>{{ data.item.bar_code }}</label>
                </div>
            </template>
            <template #cell(action)="data">
                <div v-if="index_edit != data.index" class="text-center">
                    <button type="button" @click="onChangeEdit(data.index, data.item)"
                        class="btn btn-sm py-1 btn-light px-3 text-info">
                        <i class="fas fa-pen mr-2"></i>Sửa
                    </button>
                    <button type="button" @click="btnDelete(data.index, data.item.id)"
                        class="btn btn-sm py-1 btn-light px-3 text-danger">
                        <i class="fas fa-trash mr-2"></i>Xóa
                    </button>
                </div>
                <div v-else class="d-inline">
                    <button type="button" @click="btnUpdate(data.index, data.item.id, data.item)"
                        class="btn btn-sm py-1 btn-light px-3 text-success">
                        <i class="fas fa-save mr-2"></i>Cập nhật
                    </button>
                    <button @click="onChangeEditCancel()" type="button"
                        class="btn btn-sm py-1 btn-light px-3 text-secondary">
                        <i class="fas fa-door-closed mr-2"></i>Thoát
                    </button>
                </div>
            </template>
        </b-table>
        <div class="row">
            <div class="col-lg-12 text-left">
                <div class="btn-group">
                    <label class="col-form-label-sm text-nowrap mr-1">Per page: </label>
                    <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                    </b-form-select>
                    <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page" size="sm"
                        class="ml-1"></b-pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order_combo',
        },
        count_order_lack: {
            type: Number,
            default: 0,
        },
        material_combos: {
            type: Array,
            default: [],
        },
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            is_edit: false,
            errors: [],
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'customer_group.name',
                    label: 'Nhóm khách hàng',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'sap_code',
                    label: 'Mã SAP',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'name',
                    label: 'Sản phẩm',
                    sortable: true,
                },
                {
                    key: 'bar_code',
                    label: 'Barcode',
                    sortable: true,
                },
                {
                    key: 'action',
                    label: 'Action',
                    class: 'text-center',
                },
            ],
            per_page: 10,
            pageOptions: [10, 20, 50, 100],
            current_page: 1,
            index_edit: -1,
            edit_material_combo: {
                id: '',
                customer_group: '',
                sap_code: '',
                name: '',
                bar_code: '',
            },
            api_material_combo_update: '/api/master/material-combos/update',
            api_material_combo_delete: '/api/master/material-combos/delete',
        };
    },
    created() { },
    methods: {
        async btnUpdate(index, id, item) {
            try {
                let data = await this.api_handler
                    .put(this.api_material_combo_update + '/' + id, this.edit_material_combo)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.$showMessage('success', 'Cập nhật thành công');
                this.is_edit = false;
                this.index_edit = -1;
                this.$emit('updateMaterialCombo', { index, data });
            } catch (error) {
                this.errors = error.response.data.errors;
                this.$showMessage('error', 'Cập nhật không thành công', data.message);
            }
        },
        async btnDelete(index, id) {
            try {
                let data = await this.api_handler
                    .delete(this.api_material_combo_delete + '/' + id)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.$showMessage('success', 'Xóa thành công');
                this.$emit('deleteMaterialCombo', index);
            } catch (error) {
                this.$showMessage('error', 'Xóa không thành công', data.message);
            }
        },
        onChangeEdit(index, item) {
            this.index_edit = index;
            this.is_edit = true;
        },
        onChangeEditCancel() {
            this.index_edit = -1;
            this.is_edit = false;
        },
        hasError(fieldName) {
            return fieldName in this.errors;
        },
        getError(fieldName) {
            return this.errors[fieldName];
        },
    },
    computed: {
        rows() {
            return this.material_combos.length;
        },
    },
};
</script>
<style lang="scss" scoped>
.form-control-edit {
    color: orange !important;
    font-weight: bold !important;
    border: none !important;
    box-shadow: 3px 10px 10px 0 rgba(0, 0, 0, 0.1) !important;

    &::placeholder {
        color: lightgray !important;
        font-weight: 300 !important;
        font-style: italic !important;
    }

    &:focus {
        border: none !important;
        outline: none !important;
    }
}
</style>
