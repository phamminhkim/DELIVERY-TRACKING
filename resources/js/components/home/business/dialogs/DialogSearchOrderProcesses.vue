<template>
    <div>
        <div class="modal fade" id="form_search_order_processes" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">tìm kiếm tên hàng</h5>
                        <button type="button" class="close" @click="closeModalSearchOrderProcesses()"
                            aria-label="Close">
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
                                        <button type="button"
                                            class="btn w-100 btn-sm btn-danger px-4 mb-2 shadow-btn">Xóa kí
                                            tự</button>
                                        <button type="button" @click="closeModalSearchOrderProcesses()"
                                            class="btn w-100 btn-sm btn-light px-4 font-weight-bold shadow-btn">Thoát</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div v-for="(select, index) in case_data.item_selecteds" :key="index" type="button"
                                @click="removeItemSelect(index)" class="badge badge-sm mr-2 p-2 badge-secondary">
                                {{ select.sku_sap_code }}
                                <span @click="removeItemSelect(index)" class="badge badge-light">x</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <b-table small striped hover sticky-header head-variant="light" :items="products"
                                :fields="field_products" responsive="sm"></b-table>
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
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    props: {
        is_open_modal_search_order_processes: {
            type: Boolean,
            default: false
        },
        item_selecteds: {
            type: Array
        }

    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            products: [
                {
                    bar_code: '123456',
                    sap_code: '33333',
                    name: 'Sản phẩm 1',
                    unit_id: 'Hộp',
                },
               
            ],
            field_products: [
                {
                    key: 'bar_code',
                    label: 'BarCode',
                    required: false,
                },
                {
                    key: 'sap_code',
                    label: 'Mã SAP',
                    required: false,
                },
                {
                    key: 'name',
                    label: 'Sản phẩm',
                    required: false,
                },
                {
                    key: 'unit_id',
                    label: 'Đơn vị tính',
                    required: false,
                },
                
            ],
            case_is_loading: {
                fetch_api: false,
            },
            case_filter: {
                bar_codes: '',
                search: '',
            },
            case_data: {
                sap_materials: [],
                item_selecteds: this.item_selecteds,
            },
            case_pagination: {
                page: 1,
                per_page: 10,
                total: 0,
            },
            case_api: {
                api_sap_materials: 'api/master/sap-materials',
            }

        }
    },
    watch: {
        is_open_modal_search_order_processes: function (val_new) {
            if (val_new) {
                $('#form_search_order_processes').modal('show');
            } else {
                $('#form_search_order_processes').modal('hide');
            }
        },
        item_selecteds: function (val_new) {
            this.case_data.item_selecteds = val_new;
        }

    },
    created() {
        this.fetchSapMaterial();
    },
    methods: {
        async fetchSapMaterial() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.case_api.api_sap_materials, {
                    search: this.case_filter.search,
                    page: this.case_pagination.page,
                });
                if (Array.isArray(data)) {
                    this.case_data.sap_materials = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        closeModalSearchOrderProcesses() {
            this.$emit('closeModalSearchOrderProcesses');
        },
        removeItemSelect(index) {
            this.case_data.item_selecteds.splice(index, 1);
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

.shadow-btn {
    box-shadow: 0px 8px 10px -6px rgba(0, 0, 0, 0.4);
}
</style>