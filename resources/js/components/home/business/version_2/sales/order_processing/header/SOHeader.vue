<template>
    <div>
        <b-tabs small content-class="mt-1 bg-white p-1 rounded">
            <b-tab title="Danh sách đơn hàng" @click="btnStep(1)" active>
                <div class="row align-items-end mb-2">
                    <div class="col-lg-5">
                        <div class="mr-1 text-xs">
                            <div><small class="mb-0">Chọn File - Excel xử lý</small><small
                                    class="text-danger ml-1">(*)</small>
                            </div>
                            <div class="d-flex">
                                <input class="flex-shrink-0" type="file" ref="fileInput" @change="handleFileChangeCopy"
                                    accept=".xlsx, .xls, .csv" multiple>
                                <div class="flex-fill" v-show="is_loading">
                                    <i class="fas fa-spinner fa-pulse fa-xs" style="color: #c7c6c7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="input-group input-group-sm text-xs">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <small for="" class="text-xs labes-m"><i
                                            class="fas fa-search mr-1 font-weight-bold"></i>Tìm kiếm</small>
                                </span>
                            </div>
                            <input type="text" class="form-control form-control-sm text-xs"
                                placeholder="Nhập tìm kiếm..." aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>

                <SOListHeaderSaleCR :items="po_sale_create_bies" @edit-order-po-sale="handleOrderEdit"
                    @sending-order-po-sale="handleFetchOrderSale" @delete-order-po-sale="handleOrderDelete" />
            </b-tab>
            <b-tab title="Xử lý Mẫu (1)" @click="btnStep(2)" :active="step == 2">
                <div>
                    <div class="header text-xs ">
                        <!-- <div class="row">
                <div class="col-lg-2">
                    <div class="stepper">
                        <div class="step">
                            <div class="circle">1</div>
                            <div class="line"></div>
                            <div class="label">Xử lý đơn hàng</div>
                        </div>
                        <div class="step">
                            <div class="circle circle-two">2</div>
                            <div class="line"></div>
                            <div class="label">Gửi Điều phối</div>
                        </div>
                        <div class="step">
                            <div class="circle circle-three">3</div>
                            <div class="label">Hoàn tất</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="content">
                        <button class="btn btn-light text-primary btn-sm px-2 text-xs ">Tiếp theo</button>
                    </div>
                </div>
            </div> -->
                        <div class="header-title text-xs mt-2">

                            <div class="bg-white rounded p-2">
                                <div class="row text-xs mb-2">
                                    <!-- <div class="col-lg-3 text-xs">
                                        <div class="mr-1">
                                            <div><small class="mb-0">Nhóm khách hàng</small><small
                                                    class="text-danger ml-1">(*)</small>
                                            </div>
                                            <select v-model="customer_group_id" aria-placeholder="Chọn nhóm khách hàng"
                                                class="form-control form-control-sm text-xs">
                                                <option :value="-1">Chọn nhóm khách hàng</option>
                                                <option v-for="(customer_group, index) in customer_groups" :key="index"
                                                    :value="customer_group.id">
                                                    {{ customer_group.name }}</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-lg-5 text-xs">
                                        <div class="mr-1">
                                            <div><small class="mb-0">Chọn File</small><small
                                                    class="text-danger ml-1">(*)</small>
                                            </div>
                                            <div class="d-flex">
                                                <input class="flex-shrink-0" type="file" ref="fileInput"
                                                    @change="handleFileChange" accept=".xlsx, .xls, .csv" multiple>
                                                <div class="flex-fill" v-show="is_loading">
                                                    <i class="fas fa-spinner fa-pulse fa-xs"
                                                        style="color: #c7c6c7;"></i>
                                                </div>
                                            </div>

                                        </div>
                                    </div> -->
                                    <!-- <div class="col-lg-3 text-xs">
                                        <div class="mr-1">
                                            <button @click="uploadFiles()" class="btn btn-sm px-2 btn-primary text-xs">
                                                <i class="fa fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="row text-xs">
                                    <div class="col-lg-12">
                                        <div class="text-xs d-flex">
                                            <!-- <button @click="btnShowDialogBook()"
                                                class="btn btn-sm btn-outline-info px-2 text-xs mr-2 rounded">Thêm nhà
                                                sách nhanh</button> -->
                                            <div v-show="data_import.items.length > 0">
                                                <button @click="checkBookStore()"
                                                    class="btn btn-sm btn-outline-primary px-2 text-xs mr-2 rounded">Lấy
                                                    tên
                                                    nhà sách</button>
                                                <button @click="checkSapcode()"
                                                    class="btn btn-sm btn-outline-primary px-2 text-xs mr-2 rounded">
                                                    <small class="text-danger mr-1 text-xs"><i
                                                            class="fab fa-diaspora"></i></small>
                                                    Lấy
                                                    mã
                                                    SAP</button>
                                                <button @click="btnShowDialogSearchOrderProcesses()"
                                                    class="btn btn-sm btn-outline-primary px-2 text-xs mr-2 rounded">Tìm
                                                    và
                                                    thay thế</button>
                                                <button @click="checkSapCompliance()"
                                                    class="btn btn-sm btn-outline-primary px-2 text-xs mr-2 rounded">Check
                                                    QC</button>
                                                <button @click="btnStepTwo()"
                                                    class="btn btn-sm btn-success px-2 text-xs mr-2 rounded">Lưu
                                                    đơn hàng</button>
                                            </div>
                                            <!-- <div>
                                                <button @click="btnListOrderProcess()"
                                                    class="btn btn-sm btn-info px-2 text-xs mr-2 rounded">Danh sách đơn
                                                    hàng</button>
                                            </div> -->

                                        </div>
                                    </div>
                                </div>


                            </div>


                        </div>
                    </div>
                    <div class="body-content">
                        <div class="d-flex mb-1 text-xs p-1" style="background: #eaeaea;">
                            <div class="mr-1 ">
                                <i class="fas fa-filter"></i>
                                <small class="font-weight-bold">Filter</small>
                            </div>
                            <div class="flex-fill text-xs">
                                <button @click="filterCustomerIsNull()" class="btn btn-sm btn-light text-xs text-gray">
                                    Filter Khách Hàng trống
                                </button>
                                <button @click="filterSapCodeIsNull()" class="btn btn-sm btn-light text-xs text-gray">
                                    Filter Mã SAP trống
                                </button>
                                <button @click="filterWrongSpecifications()"
                                    class="btn btn-sm btn-light text-xs text-gray">
                                    Tìm kiếm sản phẩm sai quy cách
                                </button>
                                <!-- <button class="btn btn-sm btn-light text-xs text-gray">
                                    Filter Màu đỏ
                                </button> -->
                                <!-- Example single danger button -->
                                <!-- <div class="btn-group btn-group-sm">
                                    <b-dropdown id="dropdown-1" variant="light" text="Filter màu" class="btn-group-sm">
                                        <b-dropdown-item>
                                            <div @click="filterItemColorRed()" class="filter-red"></div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div @click="filterItemColorBlue()" class="filter-blue"></div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div @click="filterItemColorPurple()" class="filter-purple"></div>
                                        </b-dropdown-item>
                                        <b-dropdown-divider></b-dropdown-divider>
                                        <b-dropdown-item>
                                            <div @click="filterItemColorFill()" class="filter-all">No fill</div>
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div> -->
                                <!-- <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        Filter màu
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">Đỏ</a>
                                        <a class="dropdown-item" href="#">Vàng</a>
                                        <a class="dropdown-item" href="#">Xanh</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">All</a>
                                    </div>
                                </div> -->
                                <button @click="clearFilter()" class="btn btn-sm btn-light text-xs">
                                    <i class="fas fa-eraser mr-1 text-xs"></i>Clear Filter
                                </button>
                            </div>
                        </div>
                        <!-- <div class="p-1 text-xs">
                            <div class="row">
                                <div class="col-lg-3">
                                    <small class="font-weight-bold">
                                        Mã đơn: <span class="text-primary">{{ so_header.code }}</span>
                                    </small>
                                    <small class="text-xs">
                                        <span v-show="so_header.status == 'pending'"
                                            class="badge badge-sm badge-secondary">Chưa gửi xử lý</span>
                                        <span v-show="so_header.status == 'sending'"
                                            class="badge badge-sm badge-info">Đã gửi xử lý</span>
                                    </small>
                                </div>
                                <div class="col-lg-3">
                                    <small class="font-weight-bold">
                                        Trạng thái: <span class="">{{ so_header.status }}</span>
                                    </small>
                                </div>
                            </div>
                        </div> -->
                        <div class="d-flex mb-1 text-xs p-1" style="background: #eaeaea;">
                            <div class="mr-1">
                                <div v-if="so_header.code !== ''">
                                    <small class="font-weight-bold">
                                        Mã đơn: <span class="text-primary">{{ so_header.code }}</span>
                                    </small>
                                    <small class="font-weight-bold" @click="btnCopyCode()">
                                        <button class="border px-1 rounded shadow-sm btn-light" data-toggle="tooltip"
                                            data-placement="top" title="Sao chép">
                                            <i class="fas fa-copy "></i>
                                        </button>
                                    </small>
                                    <small class="text-xs">
                                        <span v-show="so_header.status == 'pending'"
                                            class="badge badge-sm badge-secondary">Chưa gửi xử lý</span>
                                        <span v-show="so_header.status == 'sending'"
                                            class="badge badge-sm badge-info">Đã
                                            gửi xử lý</span>
                                    </small>
                                    <small class="font-weight-bold">Thông báo:</small>

                                </div>

                            </div>
                            <div class="flex-fill text-xs">
                                <!-- <small for="" class="text-xs text-danger">{{ error.message }}</small> -->
                                <div>
                                    <small for="" class="text-xs text-danger">{{ error.message }}</small>
                                    <!-- <span v-for="(item_index, index) in error.indexs" :key="index"
                                        class="font-weight-bold text-xs text-danger"> {{ item_index }}, </span> -->
                                </div>
                            </div>
                        </div>

                        <hot-table ref="myHotTable" :data="data_import.items" :settings="settings">
                            <!-- <hot-column title="STT" :renderer="sttRenderer"></hot-column> -->
                            <hot-column title="QC" data="is_specifications">
                            </hot-column>
                            <hot-column title="Tên_ns" data="customer_key">
                            </hot-column>
                            <hot-column title="Loại phiếu" data="type" :settings="setting_types">
                            </hot-column>
                            <hot-column title="Tên nhà sách" data="customer_name">
                            </hot-column>
                            <hot-column title="Mã vạch" data="barcode">
                            </hot-column>
                            <!-- <hot-column title="" data="sap_code">
                            </hot-column> -->
                            <hot-column title="Tên sản phẩm" data="product_name">
                            </hot-column>
                            <hot-column title="Giá bán" data="price">
                            </hot-column>
                            <hot-column title="Số lượng" data="quantity">
                            </hot-column>
                            <hot-column title="Quy cách" data="specifications">
                            </hot-column>
                            <hot-column title="Ghi chú" data="description">
                            </hot-column>
                            <hot-column title="Số phiếu" data="count_order">
                            </hot-column>
                            <hot-column title="barcode_cty" data="barcode_cty">
                            </hot-column>
                            <hot-column title="Mã SAP" data="sap_code">
                            </hot-column>
                            <hot-column title="Tên SP" data="sap_name">
                            </hot-column>
                            <hot-column title="Dvt" data="unit">
                            </hot-column>
                        </hot-table>
                    </div>
                </div>
            </b-tab>
            <b-tab title="Tạo đơn hàng" @click="btnStep(3)" :active="step == 3">
                <SOHeaderSecond :prop_items="data_import.items" :step="step"
                @click-modal="handleClickModal" />
            </b-tab>
            <!-- <b-tab title="3. Review đơn hàng">
                <SOHeaderThird />
            </b-tab> -->

        </b-tabs>
        <SODialogCreateOrder id="SODialogCreateOrder" />

        <DialogSearchOrderProcesses :is_open_modal_search_order_processes="is_open_modal_search_order_processes"
            :orders="data_import.items" :item_selecteds="item_selecteds"
            @closeModalSearchOrderProcesses="closeModalSearchOrderProcesses" @itemReplaceAll="getReplaceItemAll"
            @itemReplace="getReplaceItem" />

        <SODialogCreateBook id="SODialogCreateBook" :items="prop_items" :is_show_hide_dialog="is_show_hide_dialog"
            @close-dialog="handleCloseDialog" />
        <SODialogSaveHeader id="SODialogSaveHeader" :is_show="is_show_hide_dialog_save" :so_header="so_header"
            @close-modal="handelCloseDialogSave" @save-changes="handleSaveChanges"
            @edit-save-header="handleEditSaveChange" :api_handler="api_handler" />
        <SODialogListHeaderSaleCR id="SODialogListHeaderSaleCR" :is_show="is_show_hide_modal_list_header_sale"
            @close-modal="handleCloseModalListSaleCR" :items="po_sale_create_bies" @edit-order-po-sale="handleOrderEdit"
            @sending-order-po-sale="handleFetchOrderSale" />
    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import ApiHandler from '../../../../../ApiHandler';
import { HotTable, HotColumn } from '@handsontable/vue';
import { ContextMenu } from 'handsontable/plugins/contextMenu';
import { registerAllModules } from 'handsontable/registry';
import 'handsontable/dist/handsontable.full.css';
import DialogSearchOrderProcesses from '../../../../../business/dialogs/DialogSearchOrderProcesses.vue';
import { toLower } from 'lodash';
import SODialogCreateBook from '../dialog/SODialogCreateBook.vue';
import SOHeaderSecond from './SOHeaderSecond.vue';
import SODialogSaveHeader from '../dialog/SODialogSaveHeader.vue';
import SODialogListHeaderSaleCR from '../dialog/SODialogListHeaderSaleCR.vue';
import SOHeaderThird from './SOHeaderThird.vue';
import SOListHeaderSaleCR from '../list/SOListHeaderSaleCR.vue';
import SODialogCreateOrder from '../dialog/SODialogCreateOrder.vue';
import { forEach } from 'lodash';
registerAllModules();


export default {
    components: {
        HotTable,
        HotColumn,
        DialogSearchOrderProcesses,
        SODialogCreateBook,
        SOHeaderSecond,
        SODialogSaveHeader,
        SODialogListHeaderSaleCR,
        SOHeaderThird,
        SOListHeaderSaleCR,
        SODialogCreateOrder
    },
    data() {
        return {
            is_loading: false,
            is_open_modal_search_order_processes: false,
            is_show_hide_dialog: false,
            is_show_hide_dialog_save: false,
            is_show_hide_modal_list_header_sale: false,
            item_selecteds: [],
            index_specifications: [],
            columns: [

                {
                    data: 'type', title: 'Loại phiếu',
                    type: 'dropdown', // Loại cột là dropdown
                    // source: ['Option 1', 'Option 2', 'Option 3'], // Các giá trị có thể chọn
                    // correctFormat: true,
                    // strict: true, // Chỉ cho phép chọn các giá trị trong danh sách (không nhập giá trị tùy ý)
                },
                {
                    data: 'customer_name', title: 'Tên NS', type: 'autocomplete',
                    source: ['<strong>foo</strong>', '<strong>bar</strong>'],
                    allowHtml: true,
                },
                { data: 'barcode', title: 'Mã vạch' },
                // { data: 'sap_code', title: '' },
                { data: 'product_name', title: 'Tên sản phẩm' },
                {
                    data: 'price', title: 'Giá tiền', type: 'numeric', numericFormat: {
                        pattern: '$ 0,0.00',
                        culture: 'en-US',
                    },
                },
                { data: 'quantity', title: 'Số lượng' },
                { data: 'specifications', title: 'Quy cách' },
                { data: 'description', title: 'Ghi chú' },
                { data: 'count_order', title: 'Số phiếu' },

                { data: 'barcode_cty', title: 'Barcode_cty' },
                { data: 'sap_code', title: 'Mã SAP' },
                { data: 'sap_name', title: 'Tên SP' },
                { data: 'unit', title: 'Dvt' },

            ],
            setting_types: {
                type: 'dropdown',
                source: ['_GK', '_Hộp'],
                strict: true,
            },
            settings: {
                height: '500',
                width: '100%',
                autoWrapRow: true,
                autoWrapCol: true,
                rowHeaders: true,
                colHeaders: [
                    'STT',
                    'Loại phiếu',
                    'Tên nhà sách',
                    'Mã vạch',
                    '',
                    'Tên sản phẩm',
                    'Số lượng',
                    'Quy cách',
                    'barcode_cty',
                    'Mã SAP',
                    'Tên SP',
                    'Mã SAP',
                    'Dvt',
                ],

                hiddenColumns: {
                    columns: [0, 9, 10], // Chỉ số cột `is_specifications`
                    indicators: false, // Không hiển thị chỉ báo ẩn
                },
                // dropdownMenu: true,
                // dropdownMenu: ['filter_by_value', 'filter_action_bar'],
                dropdownMenu: {
                    items: {
                        custom_group: {
                            name: 'Lọc theo màu',
                            callback: function (key, options) {
                                alert('Bạn vừa chọn Nhóm!', key, options);
                            },
                            submenu: {
                                items: this.uniqueColors(),
                            },
                            // submenu:
                            // {
                            //     items: [ // Đây phải là một mảng
                            //         {
                            //             key: 'custom_group:suboption1',
                            //             name: '<div style="width:100%;height:20px;background:red"></div>',
                            //             callback: function (key, options) {
                            //                 alert('Bạn chọn Hành động 1 từ Nhóm!', key, options);
                            //             },
                            //         },
                            //         {
                            //             key: 'custom_group:suboption2',
                            //             name: '<div style="width:100%;height:20px;background:#CC66FF"></div>',
                            //             callback: function () {
                            //                 alert('Hành động 2 được kích hoạt.');
                            //             },
                            //         },
                            //         {
                            //             key: 'custom_group:suboption3',
                            //             name: '<div style="width:100%;height:20px;background:#3366CC"></div>',
                            //             callback: function () {
                            //                 alert('Hành động 3 được kích hoạt.');
                            //             },
                            //         },
                            //     ],
                            // }
                        },
                        custom_action: {
                            name: 'Tùy chỉnh hành động',
                            callback: function (key, selection, clickEvent) {
                                // Xử lý logic khi click vào mục này
                                console.log('Custom action triggered!', selection);
                                alert('Bạn vừa chọn hàng tại vị trí: ' + JSON.stringify(selection));
                            },
                        },

                        filter_by_value: {
                            name: 'Tìm kiếm',
                        },
                        filter_action_bar: {},

                    },
                    className: 'custom-dropdown-menu',
                },
                afterDropdownMenuShow(instance) {
                    const menu = document.querySelector('.htDropdownMenu');
                    // từ menu đó tìm thêm thẻ table có class là 'htCore'
                    // console.log(menu);

                    if (menu) { // Tìm thẻ table có class là 'htCore' bên trong menu
                        const table = menu.querySelector('table.htCore');
                        console.log('table', table);
                        table.style.width = '300px'; // Đặt kích thước tùy chỉnh
                        menu.style.width = '300px'; // Đặt kích thước tùy chỉnh
                        if (table) {
                            const ht_master = menu.querySelector('div.ht_master');
                            ht_master.style.width = '300px'; // Đặt kích thước tùy chỉnh
                            // if (ht_master) {
                            //     const wt_holder = menu.querySelector('div.wtHolder');
                            //     const wt_hider = menu.querySelector('div.wtHider');
                            //     console.log('wt_holder', wt_holder);
                            //     wt_holder.style.width = '300px'; // Đặt kích thước tùy chỉnh
                            //     wt_hider.style.width = '300px'; // Đặt kích thước tùy chỉnh
                            // }
                        }
                    }
                },
                className: 'customFilterButtonExample1',

                filters: true,
                contextMenu: {
                    items: {
                        "export": {
                            name: 'Xuất Excel',
                            callback: function (key, options) {
                                this.getPlugin('exportFile').downloadFile('csv', {
                                    filename: 'Dữ liệu Khách Hàng'
                                });
                            },
                        },
                        row_above: {
                            name: 'Thêm dòng phía trên',
                        },
                        row_below: {
                            name: 'Thêm dòng phía dưới',
                        },

                        remove_row: {
                            name: 'Xóa dòng',
                        },
                        show_modal: {
                            name: 'Thay thế',
                            callback: (key, options) => {
                                const row = options[0].end.row; // Vị trí hàng
                                const col = options[0].end.col; // Vị trí cột
                                this.btnShowDialogSearchOrderProcesses();
                                const item_rows = [];
                                item_rows.push({
                                    ...this.data_import.items[row],
                                    row: row,
                                });
                                this.item_selecteds = item_rows;
                                console.log(this.item_selecteds);
                            }
                        },
                        separator: ContextMenu.SEPARATOR,
                        // clear_custom: {
                        //     name: 'Clear all cells (custom)',
                        //     callback() {
                        //         this.clear();
                        //         console.log('Clear all cells');
                        //     }
                        // }
                    },
                },
                // columns: this.columns,

                licenseKey: 'non-commercial-and-evaluation'
            },
            so_header: {
                id: -1,
                title: '',
                central_branch: '',
                description: '',
                code: '',
                status: '',
            },
            api_handler: new ApiHandler(window.Laravel.access_token),
            customer_group_id: '',
            customer_groups: [],
            po_sale_create_bies: [],
            files: [],
            step: 1,
            types: [
                {
                    id: 1,
                    value: '_GK'
                },
                {
                    id: 2,
                    value: '_Hộp'
                }
            ],
            data_import: {
                items: [],
                header: {
                    customer_name: '',
                }
            },

            api: {
                sales_import: '/api/sales-processing',
                customer_groups: 'api/master/customer-groups',
                check_sap_code: 'api/sales-order/check-sap-code',
                check_book_store: 'api/sales-order/check-book-store',
                check_sap_compliance: 'api/sales-order/check-sap-compliance',
                save: 'api/sales-order/save-sales',
                update: 'api/sales-order/update-sales',
                delete: 'api/sales-order/delete-order-po-sales',

                get_all_po_sale_create_by: '/api/sales-order/get-all-po-sale-create-by',
                get_all_po_sale_id: '/api/sales-order/get-all-po-sale-create-by',

            },
            prop_items: [],
            error: {
                indexs: [],
                message: '',
            }
        }
    },

    created() {

        this.fetchCustomerGroup();
        this.fetchOrderProcessSaleCreateBy();
    },
    mounted() {
        this.conditionalFormatting();
        // this.modifyColWidth();
    },

    methods: {
        // // tôi muốn viết thêm 1 hàm addHook modifyRowHeight cho hotTable dropdownMenu
        // modifyColWidth() {
        //     this.$refs.myHotTable.hotInstance.addHook('modifyColWidth', (width, col) => {
        //         console.log('modifyColWidth', width, col);

        //     });
        // },
        handleClickModal() {
            console.log('handleCLickMOdal');
            $('#SODialogCreateOrder').modal('show');

        },
        btnCopyCode() {
            const text = this.so_header.code;
            const textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);

            this.$showMessage('success', 'Thông báo', 'Sao chép mã đơn hàng thành công');
        },
        uniqueColors() {
            // Dữ liệu mẫu của bạn
            // Dữ liệu mẫu của bạn
            const data = [
                {
                    barcode: "8935283903316",
                    theme: {
                        background: { barcode_cty: "#ff0000", sap_code: "#00ff00" },
                        text: { barcode_cty: "#ff9900", sap_code: "#ff0000" },
                    },
                },
                {
                    barcode: "8935283903317",
                    theme: {
                        background: { barcode_cty: "#3366CC", sap_code: "#00ff00" },
                        text: { barcode_cty: "#CC66FF", sap_code: "#00ff00" },
                    },
                },
                {
                    barcode: "8935283903318",
                    theme: {
                        background: { barcode_cty: "#ff0000", sap_code: "#00ff00" },
                        text: { barcode_cty: "#00ff00", sap_code: "#3366CC" },
                    },
                },
            ];
            // Lấy danh sách màu từ cả background và text
            const colors = [];
            data.forEach(item => {
                Object.values(item.theme.background).forEach(color => colors.push(color));
                Object.values(item.theme.text).forEach(color => colors.push(color));
            });

            // Loại bỏ các màu trùng lặp
            const uniqueColors = [...new Set(colors)];

            // Tạo submenu items từ danh sách màu
            const submenuItems = uniqueColors.map(color => ({
                key: `color:${color}`,
                name: `<div style="width:100%;height:20px;background:${color}"></div>`,
                callback: function (key, options) {
                    alert(`Bạn chọn màu: ${color}`);
                },
            }));
            console.log('submenuItems', submenuItems);
            return submenuItems;
        },
        filterItemColorRed() {
            this.$refs.myHotTable.hotInstance.getPlugin('filters').addCondition(0, 'eq', ['red']);
        },
        async handleFetchOrderSale() {
            await this.fetchOrderProcessSaleCreateBy();
        },
        async updateOrderProcessSale() {
            try {
                this.is_loading = true;
                const { data, success, errors, message } = await this.api_handler.put(this.api.update + '/' + this.so_header.id, {}, {
                    items: this.data_import.items,
                    so_header: this.so_header,
                });
                if (success) {
                    this.data_import.items = data.order_process_sale_items;
                    this.so_header.id = data.id;
                    this.so_header.title = data.title;
                    this.so_header.central_branch = data.central_branch;
                    this.so_header.description = data.description;
                    this.so_header.code = data.code;
                    this.so_header.status = data.status;
                    this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);
                    this.$showMessage('success', 'Thông báo', 'Cập nhật đơn hàng thành công');
                    // this.step = 2;
                    this.error = {
                        indexs: [],
                        message: '',
                    }

                }
            } catch (error) {
                console.log(error);
                this.error.indexs = error.response.data.errors[0];
                this.error.message = error.response.data.message;
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async handleEditSaveChange(status, item) {
            console.log(status, item, 'edit save change');
            // this.saveSO();
            this.so_header = item;
            await this.updateOrderProcessSale()
        },
        async handleOrderDelete(item) {
            console.log(item, 'delete');
            await this.fetchDeleteOrderPOSale(item.id);
        },
        async handleOrderEdit(item) {
            this.step = 2;
            this.so_header.id = item.id;
            await this.fetchOrderProcessSaleCreateByID();
        },
        async fetchOrderProcessSaleCreateByID() {
            try {
                this.is_loading = true;
                const { data, success } = await this.api_handler.get(this.api.get_all_po_sale_id + '/' + this.so_header.id);
                console.log(data);
                if (success) {
                    this.data_import.items = data.order_process_sale_items;
                    this.so_header.id = data.id;
                    this.so_header.title = data.title;
                    this.so_header.central_branch = data.central_branch;
                    this.so_header.description = data.description;
                    this.so_header.code = data.code;
                    this.so_header.status = data.status;
                    this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);
                    this.is_show_hide_modal_list_header_sale = false;
                    // this.po_sale_create_bies = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchOrderProcessSaleCreateBy() {
            try {
                this.is_loading = true;
                const { data, success } = await this.api_handler.get(this.api.get_all_po_sale_create_by);
                if (success) {
                    this.po_sale_create_bies = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchDeleteOrderPOSale(id) {
            try {
                this.is_loading = true;
                const { data, success } = await this.api_handler.delete(this.api.delete + '/' + id);
                if (success) {
                    this.$showMessage('success', 'Thông báo', 'Xóa đơn hàng thành công');
                    this.step = 1;
                    this.error = {
                        indexs: [],
                        message: '',
                    }
                    await this.fetchOrderProcessSaleCreateBy();
                }
            } catch (error) {
                console.log(error);
                this.error.indexs = error.response.data.errors[0];
                this.error.message = error.response.data.message;
                this.$showMessage('error', 'Lỗi', error.response.data.message);
            } finally {
                this.is_loading = false;
            }
        },
        handelCloseDialogSave() {
            this.is_show_hide_dialog_save = false;
            console.log('close dialog save', this.is_show_hide_dialog_save);
        },
        handleCloseDialog() {
            this.is_show_hide_dialog = false;
        },
        btnShowDialogBook() {
            // $('#SODialogCreateBook').modal('show');
            this.is_show_hide_dialog = true;
            this.prop_items = this.data_import.items;
        },
        async handleSaveChanges(is_boolean, so_header_data) {
            this.is_show_hide_dialog_save = false;
            this.so_header = so_header_data;
            await this.saveSO();
            await this.fetchOrderProcessSaleCreateBy();
            this.step = 1;
        },
        async saveSO() {
            try {
                this.is_loading = true;
                const { data, success, errors, message } = await this.api_handler.post(this.api.save, {}, {
                    items: this.data_import.items,
                    so_header: this.so_header,
                });
                if (success) {
                    this.$showMessage('success', 'Thông báo', 'Lưu đơn hàng thành công');
                    // this.step = 2;
                    this.error = {
                        indexs: [],
                        message: '',
                    }

                }
            } catch (error) {
                console.log(error);
                this.error.indexs = error.response.data.errors;
                this.error.message = error.response.data.message;
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async checkSapCompliance() {
            try {
                this.is_loading = true;
                const { data, success } = await this.api_handler.post(this.api.check_sap_compliance, {}, {
                    items: this.data_import.items,
                });
                if (success) {
                    this.data_import.items = data;
                    this.$showMessage('success', 'Thông báo', 'Check Quy cách thành công');
                    this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);

                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async checkBookStore() {
            try {
                this.is_loading = true;
                const { data, success } = await this.api_handler.post(this.api.check_book_store, {}, {
                    items: this.data_import.items,
                });
                if (success) {
                    this.data_import.items = data;
                    this.$showMessage('success', 'Thông báo', 'Lấy tên nhà sách thành công');
                    this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);

                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async checkSapcode() {
            try {
                this.is_loading = true;
                const { data, success } = await this.api_handler.post(this.api.check_sap_code, {}, {
                    items: this.data_import.items,
                });
                if (success) {
                    this.data_import.items = data;
                    this.$showMessage('success', 'Thông báo', 'Lấy mã SAP thành công');
                    this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);

                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        getReplaceItem(items) {
            this.item_selecteds.forEach(item => {
                items.forEach(item_order => {
                    this.data_import.items[item.row].barcode_cty = item_order.bar_code;
                    this.data_import.items[item.row].sap_code = item_order.sap_code;
                    this.data_import.items[item.row].sap_name = item_order.name;
                    this.data_import.items[item.row].unit = item_order.unit_id ? item_order.unit.unit_code : '';

                });
            });
            this.is_open_modal_search_order_processes = false;
            this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);

        },
        getReplaceItemAll(items) {
            this.item_selecteds.forEach(item_selected => {
                items.forEach(item_order => {
                    this.data_import.items.forEach(item => {
                        if (item.barcode == this.data_import.items[item_selected.row].barcode) {
                            item.barcode_cty = item_order.bar_code;
                            item.sap_code = item_order.sap_code;
                            item.sap_name = item_order.name;
                            item.unit = item_order.unit_id ? item_order.unit.unit_code : '';
                        }
                    });
                });
            });
            this.is_open_modal_search_order_processes = false;
            this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);
        },
        btnShowDialogSearchOrderProcesses() {
            this.is_open_modal_search_order_processes = true;
        },
        closeModalSearchOrderProcesses() {
            this.is_open_modal_search_order_processes = false;
        },
        sttRenderer(instance, td, row, col, prop, value, cellProperties) {
            td.innerHTML = row + 1; // Hiển thị số thứ tự (bắt đầu từ 1)
            return td;
        },
        btnStep(step) {
            this.step = step;
        },
        reset() {
            this.so_header = {
                id: -1,
                title: '',
                central_branch: '',
                description: '',
            };
            this.data_import.items = [];
            this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);
        },
        async uploadFiles() {
            try {
                const page_url = this.api.sales_import;
                const formData = new FormData();
                // formData.append('file', this.files, "customized-file.xlsx");
                // Thêm từng file vào FormData
                for (let i = 0; i < this.files.length; i++) {
                    formData.append(`file[]`, this.files[i], this.files[i].name || `customized-file-${i + 1}.xlsx`);
                }
                const response = await fetch(page_url, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Authorization": `Bearer ${this.api_handler.token}`,
                    },
                });

                if (!response.ok) {
                    throw new Error("Upload failed");
                }
                let items = [];
                const data = await response.json();
                if (data.success == false) {
                    this.$showMessage('error', 'Lỗi', data.message);
                    this.loading = false;
                    return;
                }
                data.data.forEach(item => {
                    items.push(item.items);
                });
                let merged_items = [].concat(...items);
                this.data_import.items = merged_items;
                // this.data_import.items = data.data.items;
                this.data_import.header = data.data.header;
                this.$refs.myHotTable.hotInstance.loadData(this.data_import.items);

            } catch (error) {
                console.error("Upload failed", error);
                this.loading = false;

            } finally {
                this.loading = false;

            }
        },
        async handleFileChangeCopy(event) {
            this.is_loading = true;
            this.reset();

            const files = this.$refs.fileInput.files; // Lấy danh sách file
            const reader = new FileReader();

            const readFile = (file) => {
                return new Promise((resolve, reject) => {
                    reader.onload = (e) => resolve({ data: new Uint8Array(e.target.result), file });
                    reader.onerror = (error) => reject(error);
                    reader.readAsArrayBuffer(file);
                });
            };

            try {
                const newWorkBooks = []; // Lưu trữ các workbook đã xử lý

                for (const file of files) {
                    const { data } = await readFile(file);
                    const work_book = XLSX.read(data, {
                        type: 'array',
                        cellFormula: false,
                        cellStyles: false,
                        cellText: true,
                        sheetRows: 10000,
                    });

                    const processSheet = (sheetName) => {
                        const work_sheet = work_book.Sheets[sheetName];
                        let rows = XLSX.utils.sheet_to_json(work_sheet, {
                            header: 1,
                            raw: false,
                        });
                        rows = rows.filter(row => row.some(cell => cell !== null && cell !== ""));

                        // Tạo workbook mới và thêm sheet đã xử lý
                        const newWorkBook = XLSX.utils.book_new();
                        const newWorkSheet = XLSX.utils.aoa_to_sheet(rows);
                        XLSX.utils.book_append_sheet(newWorkBook, newWorkSheet, sheetName);

                        return newWorkBook;
                    };

                    let newWorkBook;

                    if (work_book.SheetNames.length > 1) {
                        const targetSheet = work_book.SheetNames.find(sheetName =>
                            sheetName.toLowerCase() === 'chi tiet ns dat hang_2'.toLowerCase()
                        );

                        if (targetSheet) {
                            newWorkBook = processSheet(targetSheet);
                        } else {
                            throw new Error(`Không tìm thấy sheet "CHI TIET NS DAT HANG_2" trong file ${file.name}.`);
                        }
                    } else if (work_book.SheetNames.length === 1) {
                        const sheetName = work_book.SheetNames[0];
                        newWorkBook = processSheet(sheetName);
                    }

                    if (newWorkBook) {
                        newWorkBooks.push({ workBook: newWorkBook, fileName: file.name });
                    }
                }

                // Xuất và lưu các file Excel đã xử lý
                for (const { workBook, fileName } of newWorkBooks) {
                    const newExcelFile = XLSX.write(workBook, {
                        bookType: "xlsx",
                        type: "array",
                    });
                    const blob = new Blob([newExcelFile], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                    // this.files = blob; // Lưu blob vào biến của bạn (nếu cần)
                    this.files.push(blob);

                    // Tùy chỉnh upload cho từng file
                }
                console.log(this.files);
                this.step = 2;
                await this.uploadFiles();
                // await this.uploadFiles(blob, fileName);

            } catch (error) {
                console.error("File processing failed", error);
            } finally {
                this.is_loading = false;

            }
        },
        async handleFileChange(event) {
            this.is_loading = true;
            this.reset();


            const files = this.$refs.fileInput.files; // Lấy danh sách file
            const reader = new FileReader();

            const readFile = (file) => {
                return new Promise((resolve, reject) => {
                    reader.onload = (e) => resolve({ data: new Uint8Array(e.target.result), file });
                    reader.onerror = (error) => reject(error);
                    reader.readAsArrayBuffer(file);
                });
            };

            try {
                const newWorkBooks = []; // Lưu trữ các workbook đã xử lý

                for (const file of files) {
                    const { data } = await readFile(file);
                    const work_book = XLSX.read(data, {
                        type: 'array',
                        cellFormula: false,
                        cellStyles: false,
                        cellText: true,
                        sheetRows: 10000,
                    });

                    const processSheet = (sheetName) => {
                        const work_sheet = work_book.Sheets[sheetName];
                        let rows = XLSX.utils.sheet_to_json(work_sheet, {
                            header: 1,
                            raw: false,
                        });
                        rows = rows.filter(row => row.some(cell => cell !== null && cell !== ""));

                        // Tạo workbook mới và thêm sheet đã xử lý
                        const newWorkBook = XLSX.utils.book_new();
                        const newWorkSheet = XLSX.utils.aoa_to_sheet(rows);
                        XLSX.utils.book_append_sheet(newWorkBook, newWorkSheet, sheetName);

                        return newWorkBook;
                    };

                    let newWorkBook;

                    if (work_book.SheetNames.length > 1) {
                        const targetSheet = work_book.SheetNames.find(sheetName =>
                            sheetName.toLowerCase() === 'chi tiet ns dat hang_2'.toLowerCase()
                        );

                        if (targetSheet) {
                            newWorkBook = processSheet(targetSheet);
                        } else {
                            throw new Error(`Không tìm thấy sheet "CHI TIET NS DAT HANG_2" trong file ${file.name}.`);
                        }
                    } else if (work_book.SheetNames.length === 1) {
                        const sheetName = work_book.SheetNames[0];
                        newWorkBook = processSheet(sheetName);
                    }

                    if (newWorkBook) {
                        newWorkBooks.push({ workBook: newWorkBook, fileName: file.name });
                    }
                }

                // Xuất và lưu các file Excel đã xử lý
                for (const { workBook, fileName } of newWorkBooks) {
                    const newExcelFile = XLSX.write(workBook, {
                        bookType: "xlsx",
                        type: "array",
                    });
                    const blob = new Blob([newExcelFile], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                    // this.files = blob; // Lưu blob vào biến của bạn (nếu cần)
                    this.files.push(blob);

                    // Tùy chỉnh upload cho từng file
                }

                await this.uploadFiles();
                // await this.uploadFiles(blob, fileName);

            } catch (error) {
                console.error("File processing failed", error);
            } finally {
                this.is_loading = false;

            }
        },

        // async handleFileChange(event) {
        //     this.is_loading = true;
        //     this.reset();
        //     console.log(this.$refs.fileInput.files);
        //     const file = this.$refs.fileInput.files[0];
        //     const reader = new FileReader();

        //     const readFile = () => {
        //         return new Promise((resolve, reject) => {
        //             reader.onload = (e) => resolve(new Uint8Array(e.target.result));
        //             reader.onerror = (error) => reject(error);
        //             reader.readAsArrayBuffer(file);
        //         });
        //     };

        //     try {
        //         const data = await readFile();
        //         const work_book = XLSX.read(data, {
        //             type: 'array',
        //             cellFormula: false,  // Không cần công thức
        //             cellStyles: false,
        //             cellText: true, // Ưu tiên giá trị hiển thị
        //             sheetRows: 10000,
        //         });

        //         // Hàm xử lý sheet
        //         const processSheet = (sheetName) => {
        //             const work_sheet = work_book.Sheets[sheetName];
        //             let rows = XLSX.utils.sheet_to_json(work_sheet, {
        //                 header: 1,
        //                 raw: false, // Dùng giá trị hiển thị (giống Paste Values)
        //             });
        //             rows = rows.filter(row =>
        //                 row.some(cell => cell !== null && cell !== "")
        //             );

        //             // Tạo workbook mới và thêm sheet đã xử lý
        //             const newWorkBook = XLSX.utils.book_new();
        //             const newWorkSheet = XLSX.utils.aoa_to_sheet(rows);
        //             XLSX.utils.book_append_sheet(newWorkBook, newWorkSheet, sheetName);
        //             return newWorkBook;
        //         };

        //         let newWorkBook;

        //         if (work_book.SheetNames.length > 1) {
        //             // Tìm sheet có tên mong muốn
        //             const targetSheet = work_book.SheetNames.find(sheetName =>
        //                 toLower(sheetName) === toLower('CHI TIET NS DAT HANG_2')
        //             );

        //             if (targetSheet) {
        //                 newWorkBook = processSheet(targetSheet);
        //             } else {
        //                 throw new Error('Không tìm thấy sheet "CHI TIET NS DAT HANG_2".');
        //             }
        //         } else if (work_book.SheetNames.length === 1) {
        //             // Chỉ có một sheet
        //             const sheetName = work_book.SheetNames[0];
        //             newWorkBook = processSheet(sheetName);
        //         }

        //         // Xuất file từ workbook mới
        //         const newExcelFile = XLSX.write(newWorkBook, {
        //             bookType: "xlsx",
        //             type: "array",
        //         });
        //         const blob = new Blob([newExcelFile], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        //         this.files = blob;

        //         await this.uploadFiles();
        //     } catch (error) {
        //         console.error("File processing failed", error);
        //     } finally {
        //         this.is_loading = false;
        //     }
        // },
        async fetchCustomerGroup() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api.customer_groups);
                if (Array.isArray(data)) {
                    this.customer_groups = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async btnStepTwo() {
            if (this.data_import.items.length == 0) {
                this.$showMessage('error', 'Lỗi', 'Không có dữ liệu để lưu');
                return;
            }
            // add vào url là &step=2
            this.is_show_hide_dialog_save = true;
            // await this.saveSO();

        },
        filterCustomerIsNull() {
            const filters = this.$refs.myHotTable.hotInstance.getPlugin('filters');
            filters.clearConditions();
            filters.addCondition(1, 'eq', ['']);
            filters.filter();
        },
        filterSapCodeIsNull() {
            const filters = this.$refs.myHotTable.hotInstance.getPlugin('filters');
            filters.clearConditions();
            filters.addCondition(12, 'eq', ['']);
            filters.filter();
        },
        filterWrongSpecifications() {
            const hotInstance = this.$refs.myHotTable.hotInstance;
            const filter = hotInstance.getPlugin('filters');
            // Xóa điều kiện lọc hiện tại
            filter.clearConditions();
            // Thêm điều kiện lọc
            filter.addCondition(0, 'eq', [1]); // Lọc cột `is_specifications` có giá trị 1
            // Áp dụng bộ lọc
            filter.filter();

        },
        clearFilter() {
            const filters = this.$refs.myHotTable.hotInstance.getPlugin('filters');
            filters.clearConditions();
            filters.filter();
        },
        getValueParseFloat(row, column) {
            return parseFloat(this.$refs.myHotTable.hotInstance.getDataAtRowProp(row, column)) || 0;
        },
        conditionalFormatting() {
            const hot = this.$refs.myHotTable.hotInstance;
            // Hàm cập nhật giá trị `is_specifications` cho một hàng cụ thể
            const updateSpecificationsRow = (row) => {
                const specValue = parseFloat(hot.getDataAtRowProp(row, 'specifications')) || 0;
                const quantityValue = parseFloat(hot.getDataAtRowProp(row, 'quantity')) || 0;
                const isSpecifications = specValue > quantityValue ? 1 : 0;

                // Cập nhật giá trị trong dữ liệu nguồn
                hot.setDataAtRowProp(row, 'is_specifications', isSpecifications, 'updateSpecifications');
            };

            // Áp dụng định dạng ô
            hot.updateSettings({
                cells: (row, col, prop) => {
                    const cellProperties = {};
                    switch (prop) {
                        case 'specifications':
                            const isSpecifications = hot.getDataAtRowProp(row, 'is_specifications');

                            // Gán class dựa trên giá trị `is_specifications`
                            if (isSpecifications === 1) {
                                cellProperties.className = 'htRight htNumeric htBold';
                                cellProperties.style = 'color: ' + hot.getDataAtRowProp(row, 'color'); // Màu đỏ
                            } else {
                                cellProperties.className = 'htLeft htNumeric htBold text-secondary';
                            }
                            break;
                        case 'barcode_cty':
                            cellProperties.className = 'htRight htNumeric htBold';
                            cellProperties.style = 'color: ' + hot.getDataAtRowProp(row, 'theme.background.barcode_cty'); // Màu đỏ
                            break;
                        default:
                            break;
                    }
                    // if (prop === 'specifications') {
                    //     const isSpecifications = hot.getDataAtRowProp(row, 'is_specifications');

                    //     // Gán class dựa trên giá trị `is_specifications`
                    //     if (isSpecifications === 1) {
                    //         cellProperties.className = 'htRight htNumeric htBold';
                    //         cellProperties.style = 'color: ' + hot.getDataAtRowProp(row, 'color'); // Màu đỏ
                    //     } else {
                    //         cellProperties.className = 'htLeft htNumeric htBold text-secondary';
                    //     }

                    // }
                    return cellProperties;
                },
            });

            hot.addHook('beforeRenderer', (TD, row, col, prop, value, cellProperties) => {
                switch (prop) {
                    case 'specifications':
                        const isSpecifications = hot.getDataAtRowProp(row, 'is_specifications');
                        if (isSpecifications === 1) {
                            TD.classList.add('htRight', 'htNumeric', 'htBold', 'text-danger');
                        } else {
                            TD.classList.add('htLeft', 'htNumeric', 'htBold', 'text-secondary');
                        }
                        break;
                    case 'barcode_cty':
                        let text_color_barcode_cty = hot.getDataAtRowProp(row, 'theme.text.barcode_cty');
                        if (text_color_barcode_cty !== '') {
                            TD.style.color = text_color_barcode_cty;
                        } else {
                            TD.style.color = 'black';
                        }
                        break;
                    case 'sap_code':
                        let text_color_sap_code = hot.getDataAtRowProp(row, 'theme.text.sap_code');
                        if (text_color_sap_code !== '') {
                            TD.style.color = text_color_sap_code;
                        } else {
                            TD.style.color = 'black';
                        }
                        break;

                    default:
                        break;
                }
                // if (prop === 'specifications') {
                //     const isSpecifications = hot.getDataAtRowProp(row, 'is_specifications');
                //     if (isSpecifications === 1) {
                //         TD.classList.add('htRight', 'htNumeric', 'htBold', 'text-danger');
                //     } else {
                //         TD.classList.add('htLeft', 'htNumeric', 'htBold', 'text-secondary');
                //     }
                // }
            });

            // Hook xử lý khi dữ liệu thay đổi
            hot.addHook('afterChange', (changes, source) => {
                if (source !== 'loadData' && source !== 'updateSpecifications' && changes) {
                    changes.forEach(([row, prop]) => {
                        if (prop === 'specifications' || prop === 'quantity') {
                            // Cập nhật giá trị `is_specifications` khi cột liên quan thay đổi
                            updateSpecificationsRow(row);
                        }
                    });
                }
            });
        },
        async btnListOrderProcess() {
            this.is_show_hide_modal_list_header_sale = true;
            await this.fetchOrderProcessSaleCreateBy();

        },
        handleCloseModalListSaleCR() {
            this.is_show_hide_modal_list_header_sale = false;
        }

    }
}
</script>
<style lang="scss" scoped>
.stepper {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.step {
    text-align: center;
    position: relative;
}

.step .circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    line-height: 40px;
    font-size: 16px;
    margin: 0 auto;
}

.step .circle-two {
    background: gray !important;
    opacity: 0.5;
}

.step .circle-three {
    background: gray !important;
    opacity: 0.5;
}

.step .line {
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: #007bff;
    z-index: -1;
}

.step:last-child .line {
    display: none;
}

.step .label {
    margin-top: 10px;
    font-size: 14px;
    color: #333;
}

.filter-red {
    width: 90px;
    height: 20px;
    background-color: red;
}

.filter-blue {
    width: 90px;
    height: 20px;
    background-color: #3366CC;
}

.filter-purple {
    width: 90px;
    height: 20px;
    background-color: #CC66FF;
}

// .handsontable > .htDropdownMenu >
::v-deep .htMenu .htDropdownMenu .handsontable {
    //   width: 300px !important;
    //   border: 3px solid red !important;
    left: 10px !important;
}

::v-deep .htDropdownMenu>div.ht_master.handsontable>div.wtHolder>div.wtHider>div.wtSpreader>table.htCore {
    border: 4px solid #fd0404 !important;
    border-bottom-width: 2px !important;
    border-right-width: 2px !important;
}

::v-deep .htDropdownMenu table tbody tr td .htItemWrapper {

    color: red !important;
}

// ::v-deep .handsontable.customFilterButtonExample1 .changeType {
//     background: #e2e2e2;
//     border-radius: 100%;
//     width: 16px;
//     color: #0075ff;
//     height: 16px;
//     padding: 3px 2px;
//     border: none;
// }

// ::v-deep .handsontable.customFilterButtonExample1 .changeType::before {
//     content: '▼ ';
//     zoom: 0.9;
// }</style>
