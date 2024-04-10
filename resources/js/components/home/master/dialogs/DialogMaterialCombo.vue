<template>
    <div>
        <div class="modal fade" id="modalMaterialCombo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="storeMaterialCombo">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold text-uppercase">Thêm mới hàng combo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-create">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 bg-light" id="basic-addon1">Mã SAP</span>
                                        </div>
                                        <input v-model="material_combo.sap_code" type="text"
                                            class="form-control border-bottom border-right-0 border-top-0 rounded-0"
                                            placeholder="Nhập mã SAP..."
                                            v-bind:class="hasError('sap_code') ? 'is-invalid' : ''">
                                        <span v-if="hasError('sap_code')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('sap_code') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 bg-light" id="basic-addon1">Sản phẩm</span>
                                        </div>
                                        <input v-model="material_combo.name" type="text"
                                            class="form-control border-bottom border-right-0 border-top-0 rounded-0"
                                            placeholder="Nhập sản phẩm..."
                                            v-bind:class="hasError('name') ? 'is-invalid' : ''">
                                        <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 bg-light" id="basic-addon1">Barcode</span>
                                        </div>
                                        <input v-model="material_combo.bar_code" type="text"
                                            class="form-control border-bottom border-right-0 border-top-0 rounded-0"
                                            placeholder="Nhập Barcode..."
                                            >
                                      >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-around">
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-light px-5 text-success"><i
                                        class="fas fa-save mr-2"></i>Lưu</button>
                                <button type="button" data-dismiss="modal"
                                    class="btn btn-sm btn-light px-5 text-secondary font-weight-bold"><i
                                        class="fas fa-clone mr-2"></i>Đóng</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';

export default {
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            errors: [],
            material_combo: {
                id: '',
                sap_code: '',
                name: '',
                bar_code:'',
            },
            api_material_combo_store: '/api/master/material-combos/store',

        }
    },
    methods: {
        async storeMaterialCombo() {
            try {
                let data = await this.api_handler
                    .post(this.api_material_combo_store, this.material_combo)
                    .finally(() => {
                        this.is_loading = false;
                    });
                this.$showMessage('success', 'Thêm thành công');
                this.$emit('storeMaterialCombo', data);
                this.hideModalMaterialCombo();
                this.refeshModel();
                this.refeshErrors();
            } catch (error) {
                this.errors = error.response.data.errors;
                this.$showMessage('error', 'Thêm mới không thành công', data.message);
            }
        },
        refeshModel() {
            this.material_combo = {
                id: '',
                sap_code: '',
                name: '',
                bar_code:'',
            }
        },
        showModalMaterialCombo() {
            this.refeshModel();
            this.refeshErrors();
            $('#modalMaterialCombo').modal('show');
        },
        hideModalMaterialCombo() {
            this.refeshModel();
            this.refeshErrors();
            $('#modalMaterialCombo').modal('hide');
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
    }

}
</script>
<style lang="scss" scoped>

</style>