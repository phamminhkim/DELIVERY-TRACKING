<template>
    <div>
        <div class="modal fade" id="form_search_order_processes" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">tìm kiếm tên hàng</h5>
                        <button type="button" class="close" @click="closeModalSearchOrderProcesses()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text font-weight-bold" id="basic-addon1">Nhập
                                                    ký tự
                                                    cần tìm</span>
                                            </div>
                                            <input type="text" class="form-control input__focus"
                                                placeholder="Nhập ký tự cần tìm">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="btn-group-sm" role="group" aria-label="Basic example">
                                        <button type="button" class="btn w-100 btn-sm btn-danger px-4 mb-2 shadow-btn">Xóa kí
                                            tự</button>
                                        <button type="button" @click="closeModalSearchOrderProcesses()"
                                            class="btn w-100 btn-sm btn-light px-4 font-weight-bold shadow-btn" >Thoát</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <p>Bảng sap_materials</p>
                            <b-table small striped hover sticky-header head-variant="light" :items="products" :fields="field_products"
                                responsive="sm"></b-table>
                        </div>
                    </div>
                    <div class="modal-footer d-block text-center">
                        <button type="button" class="btn btn-sm px-4 btn-light shadow-btn">Replace</button>
                        <button type="button" class="btn btn-sm px-4 btn-light shadow-btn">Replace All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        is_open_modal_search_order_processes: {
            type: Boolean,
            default: false
        },
       
    },
    data() {
        return {
            products: [
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 1',
                    unit: 'Hộp',
                },
                {
                    barcode: '123456',
                    code_sap: '123456',
                    name: 'Sản phẩm 2',
                    unit: 'Hộp',
                },
            ],
            field_products: [
                {
                    key: 'barcode',
                    label: 'BARCODE',
                    required: false,
                },
                {
                    key: 'code_sap',
                    label: 'MÃ SAP',
                    required: false,
                },
                {
                    key: 'name',
                    label: 'TÊN SẢN PHẨM',
                    required: false,
                },
                {
                    key: 'unit',
                    label: 'ĐƠN VỊ TÍNH',
                    required: false,
                },
            ]
        }
    },
    watch: {
        is_open_modal_search_order_processes: function (val_new) {
            if (val_new) {
                $('#form_search_order_processes').modal('show');
            } else {
                $('#form_search_order_processes').modal('hide');
            }
        }
    },
    methods: {
        closeModalSearchOrderProcesses() {
            this.$emit('closeModalSearchOrderProcesses');
        }
    }
}
</script>
<style lang="scss" scoped>
.input__focus:focus {
    outline: none !important;
    box-shadow: none !important;
    border: 1px solid gainsboro !important;

}
.shadow-btn{
    box-shadow: 0px 8px 10px -6px rgba(0, 0, 0, 0.4);
}
</style>