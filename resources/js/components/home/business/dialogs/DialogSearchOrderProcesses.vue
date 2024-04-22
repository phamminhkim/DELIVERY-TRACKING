<template>
    <div>
        <div class="modal fade" id="form_search_order_processes" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-lg">
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
                                                <span @click="fetchSapMaterial()" type="button"
                                                    class="input-group-text font-weight-bold bg-light border-light"
                                                    id="basic-addon1">
                                                    <i v-if="!case_is_loading.fetch_api" class="fas fa-search mr-2"></i>
                                                    <span v-else><i
                                                            class="fas fa-spinner fa-spin fa-xs mr-2"></i></span>
                                                    Tìm kiếm</span>

                                            </div>
                                            <input v-model="case_filter.search" @keyup.enter="fetchSapMaterial"
                                                type="text" class="form-control input__focus"
                                                placeholder="Nhập ký tự cần tìm">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="btn-group-sm" role="group" aria-label="Basic example">
                                        <button @click="refeshFilter()" type="button"
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
                            <b-table small striped hover sticky-header head-variant="light"
                                :items="case_data.sap_materials" :fields="field_products" responsive="sm">
                                <template #cell(index)="data">
                                    {{ data.index + 1 }}
                                </template>
                            </b-table>
                            <div class="form-group">
                                <PaginationRequest @pageChange="getPageChange" @perPageChange="getPerPageChange"
                                    :per_page="case_pagination.per_page" :current_page="case_pagination.page"
                                    :rows="case_pagination.total">
                                </PaginationRequest>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-block text-center">
                        <button type="button" class="btn btn-sm px-4 btn-light shadow-btn">Replace</button>
                        <!-- <button type="button" class="btn btn-sm px-4 btn-light shadow-btn">Replace All</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import PaginationRequest from '../paginations/PaginationRequest.vue';
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
    components: {
        PaginationRequest
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            field_products: [
                {
                    key: 'index',
                    label: 'Stt',
                    sortable: true,
                    class: 'text-nowarp',
                },
                {
                    key: 'bar_code',
                    label: 'BarCode',
                    sortable: true,
                    class: 'text-nowarp',

                },
                {
                    key: 'sap_code',
                    label: 'Mã SAP',
                    sortable: true,
                    class: 'text-nowarp',

                },
                {
                    key: 'name',
                    label: 'Sản phẩm',
                    sortable: true,
                    class: 'text-nowarp',

                },
                {
                    key: 'unit.unit_code',
                    label: 'Đơn vị tính',
                    sortable: true,
                    class: 'text-nowarp',

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
                this.fetchSapMaterial();
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
        // this.fetchSapMaterial();
    },
    methods: {
        async fetchSapMaterial() {
            try {
                this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.get(this.case_api.api_sap_materials, {
                    search: this.case_filter.search,
                    page: this.case_pagination.page,
                    per_page: this.case_pagination.per_page,
                    sap_codes: this.mapSapCodes(),
                });
                if (Array.isArray(data.data)) {
                    this.case_data.sap_materials = data.data;
                    this.case_pagination.total = data.paginate.total;
                    this.case_pagination.page = data.paginate.current_page;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        mapSapCodes() {
            return this.case_data.item_selecteds.map(item => item.sku_sap_code);
        },
        closeModalSearchOrderProcesses() {
            this.$emit('closeModalSearchOrderProcesses');
        },
        removeItemSelect(index) {
            this.case_data.item_selecteds.splice(index, 1);
        },
        getPageChange(page) {
            this.case_pagination.page = page;
            this.fetchSapMaterial();
        },
        getPerPageChange(per_page) {
            this.case_pagination.per_page = per_page;
            this.fetchSapMaterial();
        },
        refeshFilter() {
            this.case_filter = {
                search: '',
            }
            this.fetchSapMaterial();
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