<template>
    <div>
        <div class="modal fade" id="form_search_order_processes" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-lg" @mousedown="handleMouseDownModal($event)">
                <!-- @mousemove="handleMouseMoveModal($event)" -->
                <!-- @mouseup="handleMouseUpModal($event)" -->
                <div ref="form_search_order_processes" class="modal-content modal-content-cursor">
                    <div class="modal-header bg-gradient-blue">
                        <h5 class="modal-title font-weight-bold text-uppercase">tìm kiếm tên hàng</h5>
                        <button type="button" class="close" @click="closeModalSearchOrderProcesses()"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-light">
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
                                            <input ref="input_search" v-model="case_filter.search"
                                                @keyup.enter="fetchSapMaterial" type="text"
                                                class="form-control input__focus" placeholder="Nhập ký tự cần tìm">
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
                        <div class="d-flex form-group border-bottom p-1">
                            <div class="flex-fill">
                                <!-- <span class="text-success">Items đã chọn ({{ case_data.item_selecteds.length }})</span> -->
                            </div>
                            <div class="">
                                <!-- <div v-for="(select, index) in case_data.item_selecteds" :key="index" type="button"
                                    class="badge badge-sm mr-2 p-2 badge-primary">
                                    {{ case_data.item_selecteds[0].barcode }}
                                </div> -->
                                <div type="button" v-if="case_data.item_selecteds.length !== 0"
                                    class="badge badge-sm mr-2 p-2 badge-primary">
                                    {{ case_data.item_selecteds[0].barcode }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <b-table small striped hover head-variant="light" table-class="box-max-height-500"
                                :items="case_data.sap_materials" :fields="field_products" responsive
                                :busy="case_is_loading.fetch_api" :per_page="case_pagination.per_page"
                                sticky-header="400px" :current_page="case_pagination.page">
                                <template #table-busy>
                                    <div class="text-center text-secondary my-2">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                    </div>
                                </template>
                                <template #cell(index)="data">
                                    {{ (data.index + 1) + (case_pagination.page * case_pagination.per_page) -
                case_pagination.per_page }}
                                </template>
                                <template #cell(action)="data">
                                    <div class="form-group">
                                        <input type="checkbox"
                                            :disabled="case_check_box.selected_item && case_check_box.selected_item !== data.item.id"
                                            v-model="case_check_box.item_materials" :value="data.item"
                                            @input="selectItem(data.item.id)" />
                                    </div>
                                    <!-- <div class="relative-action">
                                        <button @click="showDropdown(data.index)" class="btn btn-light btn-sm">
                                            <i class="fas fa-th fa-rotate-90  text-secondary"></i>
                                        </button>
                                        <div :id="'dropdown_' + data.index" @click.stop
                                            class="form-group card-absolute text-nowrap px-3 bg-white shadow border rounded">
                                            <b-dropdown id="dropdown-dropright" dropright text="Replace" variant="link" toggle-class="text-decoration-none" no-caret>
                                                <b-dropdown-item href="#"  v-for="(select, index) in case_data.item_selecteds" :key="index"
                                                    @click="itemReplace(data.index, data.item, select)">
                                                    {{ select.sku_sap_code }}
                                                </b-dropdown-item>
                                                
                                            </b-dropdown>
                                        </div>
                                    </div> -->


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
                        <button @click="emitReplaceItemAll()" type="button"
                            class="btn btn-sm px-4 btn-light shadow-btn">Replace
                            All</button>
                        <button @click="emitReplaceItem()" type="button"
                            class="btn btn-sm px-4 btn-light shadow-btn">Replace</button>
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
                    key: 'action',
                    label: '',
                    class: 'text-nowarp',
                },
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
            case_check_box: {
                item_materials: [],
                selected_item: null,
            },
            case_filter: {
                bar_codes: '',
                search: '',
            },
            case_data: {
                sap_materials: [],
                item_selecteds: this.item_selecteds,
                barcode: '',
            },
            case_pagination: {
                page: 1,
                per_page: 10,
                total: 0,
            },
            case_api: {
                api_sap_materials: 'api/master/sap-materials',
            },
            case_mouse: {
                is_mouse_down: false,
                initial_mouse_position: { x: 0, y: 0 },
                modal_position: { x: 0, y: 0 },
                offset: { x: 0, y: 0 },
            },
            debounce_timeout: null,



        }
    },
    watch: {
        is_open_modal_search_order_processes: function (val_new) {
            if (val_new) {
                this.fetchSapMaterial();
                this.createMapping();
                this.refeshCase();
                this.resetCaseMouse();
                $('#form_search_order_processes').modal('show');
                this.$refs.input_search.focus();
                if (this.item_selecteds.length == 0) {
                    this.refeshFilter();
                }

            } else {
                $('#form_search_order_processes').modal('hide');
            }
        },
        item_selecteds: function (val_new) {
            this.case_data.item_selecteds = val_new;
        },
        'case_filter.search': function (val_new) {
            clearTimeout(this.debounce_timeout);
            this.debounce_timeout = setTimeout(() => {
                this.fetchSapMaterial();
            }, 500);
        },

    },
    mounted() {
        window.addEventListener('mousemove', this.handleMouseMoveModal);
        window.addEventListener('mouseup', this.handleMouseUpModal);
    },
    beforeDestroy() {
        window.removeEventListener('mousemove', this.handleMouseMoveModal);
        window.removeEventListener('mouseup', this.handleMouseUpModal);
    },
    created() {
        // this.fetchSapMaterial();
    },
    methods: {
        showModalSearchOrderProcesses() {
            $('#form_search_order_processes').modal('show');
        },
        closeModalSearchOrderProcesses() {
            $('#form_search_order_processes').modal('hide');
        },
        handleMouseDownModal(e) {
            // e.preventDefault();
            if (e.target.tagName.toLowerCase() === 'input') {
                e.stopPropagation();
            } else {
                e.preventDefault();
                this.case_mouse.is_mouse_down = true;
                this.case_mouse.initial_mouse_position = { x: e.clientX, y: e.clientY };
            }

        },
        handleMouseMoveModal(e) {
            if (this.case_mouse.is_mouse_down) {
                e.preventDefault();
                const dx = e.clientX - this.case_mouse.initial_mouse_position.x;
                const dy = e.clientY - this.case_mouse.initial_mouse_position.y;
                this.case_mouse.modal_position = { x: this.case_mouse.modal_position.x + dx, y: this.case_mouse.modal_position.y + dy };
                const form_search_order_processes = this.$refs.form_search_order_processes;
                form_search_order_processes.style.transform = `translate(${this.case_mouse.modal_position.x}px, ${this.case_mouse.modal_position.y}px)`;
                this.case_mouse.initial_mouse_position = { x: e.clientX, y: e.clientY };
                if (e.target !== this.$refs.form_search_order_processes) {
                    // ngăn sự kiện lan truyền vào input
                    e.stopPropagation();

                }
            }
        },
        handleMouseUpModal(e) {
            e.preventDefault();
            this.case_mouse.is_mouse_down = false;
        },
        async fetchSapMaterial() {
            try {
                this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.get(this.case_api.api_sap_materials, {
                    search: this.case_filter.search,
                    page: this.case_pagination.page,
                    per_page: this.case_pagination.per_page,
                    // sap_codes: this.mapSapCodes(),
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
            // this.fetchSapMaterial();
        },
        showDropdown(index) {
            const dropdown = document.getElementById('dropdown_' + index);
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
            }
        },
        selectItem(id) {
            if (this.case_check_box.selected_item === id) {
                this.case_check_box.selected_item = null;
            } else {
                this.case_check_box.selected_item = id;
            }
        },
        emitReplaceItemAll() {
            if (this.case_data.item_selecteds.length === 0) {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng chọn ít nhất 1 dòng dữ liệu');
                return;
            }
            if (this.case_check_box.item_materials.length === 0) {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng chọn ít nhất 1 dòng dữ liệu mã SAP cần thay thế');
                return;
            }
            this.case_data.barcode = this.case_data.item_selecteds[0].barcode;
            this.$emit('itemReplaceAll', this.case_check_box.item_materials, this.case_data.barcode);
            this.refeshCase();
        },
        emitReplaceItem() {
            if (this.case_data.item_selecteds.length === 0) {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng chọn ít nhất 1 dòng dữ liệu');
                return;
            }
            if (this.case_check_box.item_materials.length === 0) {
                this.$showMessage('warning', 'Cảnh báo', 'Vui lòng chọn ít nhất 1 dòng dữ liệu mã SAP cần thay thế');
                return;
            }
            this.$emit('itemReplace', this.case_check_box.item_materials, this.case_data.item_selecteds[0].order);
            this.refeshCase();
        },
        createMapping() {
            if (this.item_selecteds.length !== 0) {
                this.case_filter.search = this.item_selecteds[0].barcode;

            }

        },
        refeshCase() {
            this.case_check_box.selected_item = null;
            this.case_check_box.item_materials = [];
        },
        resetCaseMouse() {
            this.case_mouse = {
                is_mouse_down: false,
                initial_mouse_position: { x: 0, y: 0 },
                modal_position: { x: 0, y: 0 },
                offset: { x: 0, y: 0 },
            };
            const form_search_order_processes = this.$refs.form_search_order_processes;
            form_search_order_processes.style.transform = `translate(0px, 0px)`;
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

.relative-action {
    position: relative;
}

.card-absolute {
    position: absolute;
    top: 15px;
    left: 20px;
    z-index: 1000;
    display: none;
}

.modal-content-cursor {
    cursor: move;
}
</style>