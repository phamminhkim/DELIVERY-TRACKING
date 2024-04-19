<template>
    <div>
        <div class="modal fade" :id="'modalMaterialCategory'" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Loại danh mục phiếu</h5>
                        <button type="button" class="close" @click="hideModalCategoryType(index)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="btn-group-sm text-left" role="group">
                            <button class="btn px-4 btn-light btn-sm py-1 text-info" v-b-toggle.accordion-1><i
                                    class="fas fa-plus mr-2"></i>Tạo phiếu</button>
                        </div>
                        <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                            <div class="form-create mt-3 shadow p-3 mb-2 ">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 bg-light" id="basic-addon1">Mã
                                                phiếu</span>
                                        </div>
                                        <input v-model="material_category_type.code" type="text"
                                            class="form-control border-bottom border-right-0 border-top-0 rounded-0"
                                            placeholder="Nhập mã phiếu...">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 bg-light" id="basic-addon1">Tên
                                                phiếu</span>
                                        </div>
                                        <input v-model="material_category_type.name" type="text"
                                            class="form-control border-bottom border-right-0 border-top-0 rounded-0"
                                            placeholder="Nhập tên phiếu...">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button @click="storeMaterialCategoryType()" type="button"
                                        class="btn btn-sm btn-light px-5 text-success"><i
                                            class="fas fa-save mr-2"></i>Lưu</button>
                                    <button type="button" v-b-toggle.accordion-1
                                        class="btn btn-sm btn-light px-5 text-secondary font-weight-bold"><i
                                            class="fas fa-clone mr-2"></i>Đóng</button>
                                </div>
                            </div>
                        </b-collapse>
                        <div class="list-material-category">
                            <div class="text-center bg-light text-uppercase p-2">
                                <label class="font-weight-bold">Danh sách loại danh mục phiếu</label>
                            </div>
                            <b-table :current-page="current_page" :per-page="per_page" small responsive
                                :items="material_category_types" :fields="fields" :hover="false"
                                :tbody-tr-class="rowClass">
                                <template #cell(index)="data">
                                    {{ data.index + 1 }}
                                </template>
                                <template #cell(code)="data">
                                    <div v-show="edit_index != data.index" class="">
                                        {{ data.item.code }}
                                    </div>
                                    <div v-show="is_editing && edit_index == data.index">
                                        <input class="form-control form-control-edit" v-model="edit_item.code"
                                            v-bind:class="hasError('code') ? 'is-invalid' : ''"
                                            :placeholder="data.item.code" />
                                        <span v-if="hasError('code')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('code') }}</strong>
                                        </span>
                                    </div>
                                </template>
                                <template #cell(name)="data">
                                    <div v-show="edit_index != data.index" class="">
                                        {{ data.item.name }}
                                    </div>
                                    <div v-show="is_editing && edit_index == data.index">
                                        <input class="form-control form-control-edit" v-model="edit_item.name"
                                            v-bind:class="hasError('name') ? 'is-invalid' : ''"
                                            :placeholder="data.item.name" />
                                        <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('name') }}</strong>
                                        </span>
                                    </div>
                                </template>
                                <template #cell(action)="data">
                                    <div v-if="edit_index != data.index" class="text-center d-inline">
                                        <button class="btn btn-sm py-1 btn-light px-3 text-secondary"
                                            @click="onChangeCategoryType(modal_index, data.item)"><i
                                                class="fas fa-check mr-2"></i>Chọn</button>
                                        <button class="btn btn-sm py-1 btn-light px-3 text-info"
                                            @click="onChangeEdit(data.item, data.index)"><i
                                                class="fas fa-pen mr-2"></i>Sửa</button>
                                        <button class="btn btn-sm py-1 btn-light px-3 text-danger"
                                            @click="onChangeDelete(data.index, data.item.id)"><i
                                                class="fas fa-trash mr-2"></i>Xóa</button>
                                    </div>
                                    <div v-if="edit_index == data.index" class="d-inline">
                                        <button @click="btnUpdate(data.index, data.item.id, data.item)"
                                            class="btn btn-sm py-1 btn-light px-3 text-success"><i
                                                class="fas fa-save mr-2"></i>Cập nhật</button>
                                        <button @click="onChangeEditCancel()" type="button"
                                            class="btn btn-sm py-1 btn-light px-3 text-secondary"><i
                                                class="fas fa-door-closed mr-2"></i>Thoát</button>
                                    </div>
                                </template>
                            </b-table>
                            <div class="row">
                                <div class="col-lg-12 text-left ">
                                    <div class="btn-group">
                                        <label class="col-form-label-sm text-nowrap mr-1">Per page: </label>
                                        <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                                        </b-form-select>
                                        <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page"
                                            size="sm" class="ml-1"></b-pagination>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-sm py-1 btn-light px-3 text-secondary font-weight-bold w-25"
                            @click="hideModalCategoryType(index)"><i class="fas fa-clone mr-2"></i>Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    props: {
        is_modal_material_category_type: {
            type: Boolean,
            default: false
        },
        index: {
            type: Number,
            default: 0
        },
        material_category_types: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            modal_index: 0,
            edit_index: -1,
            is_editing: false,
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap',
                    sortable: true
                },
                {
                    key: 'code',
                    label: 'Loại phiếu',
                    class: 'text-nowrap',
                    sortable: true,
                    tdClass: 'item-hover'
                },
                {
                    key: 'name',
                    label: 'Tên phiếu',
                    class: 'text-nowrap',
                    sortable: true,
                    tdClass: 'item-hover'
                },
                {
                    key: 'action',
                    label: 'Action',
                    class: 'text-nowrap',
                    sortable: true
                },
            ],
            errors: [],
            selectMode: 'single',
            selected: [],
            material_category_type: {
                id: '',
                code: '',
                name: ''
            },
            edit_item: {
                id: '',
                code: '',
                name: ''
            },
            total: 0,
            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],
            api_material_category_type_store: '/api/master/material-category',
            api_material_category_type_update: '/api/master/material-category',
            api_material_category_type_delete: '/api/master/material-category',

        }
    },


    methods: {
        showModalCategoryType(index) {
            this.modal_index = index;
            $('#modalMaterialCategory').modal('show');
        },
        hideModalCategoryType() {
            $('#modalMaterialCategory').modal('hide');
        },
        onChangeCategoryType(index, item) {
            this.$emit('onChangeCategoryType', index, item);
            this.hideModalCategoryType();
        },
        async storeMaterialCategoryType() {
            try {
                let data = await this.api_handler
                    .post(this.api_material_category_type_store, this.material_category_type)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.$showMessage('success', 'Thêm thành công');
                this.$emit('storeMaterialCategoryType', data);
                this.refeshModel();
            } catch (error) {
                this.errors = error.response.data.errors;
                this.$showMessage('error', 'Thêm mới không thành công', data.message);
            }
        },
        async updateMaterialCategoryType(id , index) {
            try {
                let data = await this.api_handler
                    .put(this.api_material_category_type_update + "/" + id, this.edit_item)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.$showMessage('success', 'Cập nhật thành công');
                this.$emit('updateMaterialCategoryType', index, data);
                this.onChangeEditCancel();
            } catch (error) {
                this.errors = error.response.data.errors;
                this.$showMessage('error', 'Cập nhật không thành công', data.message);
            }
        },
        async deleteMaterialCategoryType(id, index) {
            try {
                let data = await this.api_handler
                    .delete(this.api_material_category_type_delete + "/" + id)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.$showMessage('success', 'Xóa thành công');
                this.$emit('deleteMaterialCategoryType', index);
                this.refeshModel();
            } catch (error) {
                this.errors = error.response.data.errors;
                this.$showMessage('error', 'Xóa không thành công', data.message);
            }
        },
        refeshModel() {
            this.material_category_type = {
                id: '',
                code: '',
                name: ''
            }
        },
        onChangeEdit(item, index) {
            this.is_editing = true;
            this.edit_index = index;
            this.refeshEditModel();
            this.refeshErrors();
        },
        onChangeEditCancel() {
            this.is_editing = false;
            this.edit_index = -1;
            this.refeshEditModel();
            this.refeshErrors();
        },
        rowClass(item) {
            if (item == this.material_category_types[this.edit_index]) {
                return 'custom-edit-row';
            }
        },
        btnUpdate(index, id, item) {
            this.updateMaterialCategoryType(id, index);
        },
        refeshEditModel() {
            this.edit_item = {
                id: '',
                code: '',
                name: ''
            }
        },
        onChangeDelete(index, id) {
            this.deleteMaterialCategoryType(id, index);
        },
        hasError(fieldName) {
            return fieldName in this.errors;
        },
        getError(fieldName) {
            return this.errors[fieldName];
        },
        refeshErrors() {
            this.errors = [];
        }

    },
    computed: {
        rows() {
            return this.material_category_types.length;
        }
    }
}
</script>
<style lang="scss" scoped>
::v-deep .custom-edit-row {
    background: yellow !important;
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1) !important;
    font-weight: bold;
    border-radius: 5px;

    // item-hover
    &>.item-hover:hover {
        // border: 2px solid #5744ff !important;
        background: white !important;
        cursor: pointer;
    }
}

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