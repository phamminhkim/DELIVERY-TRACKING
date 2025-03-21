<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="text-xs">
                    <button @click="getDemoClickModal()" class="btn btn-sm btn-primary text-xs px-2">
                        <i class="fas fa-plus mr-1"></i> Tạo đơn hàng
                    </button>
                    <button @click="addColumnDefault()"
                        class="btn btn-sm btn-outline-info border border-info text-xs px-2">
                        <i class="fas fa-plus mr-1"></i> Thêm dòng
                    </button>
                    <button @click="openModal()" class="btn btn-sm btn-outline-info border border-info text-xs px-2">
                        <i class="fas fa-plus mr-1"></i> Thêm cột
                    </button>
                    <button class="btn btn-sm btn-success text-xs px-2">
                        <i class="fas fa-save mr-1"></i> Lưu đơn hàng
                    </button>
                    <button class="btn btn-sm btn-outline-warning text-xs px-4">
                        <i class="fas fa-boxes mr-1"></i> Check tồn
                    </button>
                </div>
            </div>
        </div>
        <hot-table ref="myHotTableSecond" :data="data_api.items" :settings="settings">
            <!-- <hot-column v-for="(header, index) in data_api.headers" :key="index" :title="header" :data="header"
                :type="header === 'promotion' ? 'dropdown' : ''"
                :source="header === 'promotion' ? setting_types.source : null"
                :strict="header === 'promotion' ? setting_types.strict : false">
            </hot-column> -->
        </hot-table>

        <b-modal v-model="showModal" title="THÊM CỘT MỚI" button-size="sm" hide-footer>
            <!-- <b-form @submit.prevent="addColumn"> -->
            <label for="" class="text-xs">Tên cột mới</label>
            <b-form-group v-for="(col, index) in cols" :key="index" label-size="sm">
                <b-form-input v-model="col.value" required placeholder="Nhập tên cột mới"
                    input-size="sm"></b-form-input>
                <!-- <input class="form-control form-control-sm" v-model="col.value"  /> -->
                <b-form-invalid-feedback :state="col.value.length > 0 ? null : false">Tên cột không được để
                    trống</b-form-invalid-feedback>
            </b-form-group>
            <button @click="addNewColumns()" class="btn btn-sm btn-info px-2 text-xs"><i
                    class="fas fa-plus mr-1"></i>Thêm cột</button>
            <b-button size="sm" type="button" variant="success" @click="addColumn()">OK</b-button>
            <b-button @click="closeModel()" variant="secondary" size="sm">Hủy</b-button>

        </b-modal>
        <!-- <SODialogCreateColumn /> -->
    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import ApiHandler from '../../../../../ApiHandler';
import { HotTable, HotColumn } from '@handsontable/vue';
import { ContextMenu } from 'handsontable/plugins/contextMenu';
import { registerAllModules } from 'handsontable/registry';
import 'handsontable/dist/handsontable.full.css';
import SODialogCreateColumn from '../dialog/SODialogCreateColumn.vue';
import { plPL } from 'handsontable/i18n';
registerAllModules();

export default {
    components: {
        HotTable,
        HotColumn,
        SODialogCreateColumn,
    },
    props: {
        prop_items: {
            type: Array,
            default: () => []
        },
        step: {
            type: Number,
            default: 0
        }
    },
    watch: {
        step: {
            handler: function (val) {
                if (val === 3) {
                    this.fetchData();
                }
            },
            immediate: true
        }
    },
    data() {
        return {
            is_loading: false,
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_show_modal_create_column: false,
            settings: {
                height: '500',
                width: '100%',
                manualColumnResize: true,
                autoWrapRow: true,
                autoWrapCol: true,
                rowHeaders: true,
                colHeaders: true,
                afterChange: this.handleAfterChange,
                stretchH: 'all',
                hiddenColumns: {
                    columns: [9], // Chỉ số cột `is_specifications`
                    indicators: false, // Không hiển thị chỉ báo ẩn
                },
                cells: (row, col, prop) => {
                    const cellProperties = {};
                    // Kiểm tra nếu row là hàng 3 (chỉ số 2 do đếm từ 0)
                    // const colorField = `${prop}_color`; // Tìm field lưu thông tin màu
                    // const colorInfo = this.instance.getDataAtRowProp(row, colorField); // Lấy thông tin màu
                    // const colorInfo = this.data_api.items[row][colorField]; // Lấy thông tin màu
                    switch (row) {
                        case 0:
                            if (col >= 8) {
                                cellProperties.className = "highlight-row-blue";
                            }
                            if (col === 7) {
                                cellProperties.className = "text-danger";
                            }

                            break;
                        case 1:
                            if (col === 7) {
                                cellProperties.className = "text-right font-weight-bold bg-yellow";
                            }
                            break;

                        case 2:
                            if (col >= 8) {
                                cellProperties.className = "highlight-row-lightblue";
                            }
                            if (col === 7) {
                                cellProperties.className = "bg-yellow";
                            }
                            break;

                        case 3:
                            if (col <= 8) {
                                cellProperties.className = "highlight-row";
                            }
                            break;
                        default:
                            break;
                    }

                    if (row > 3) {
                        switch (col) {
                            case 0:
                                cellProperties.type = 'dropdown'; // Đặt kiểu dữ liệu là dropdown
                                cellProperties.source = this.setting_categories.source;
                                break;
                            case 7:
                                cellProperties.className = "text-success";
                                break;
                            case 8:
                                cellProperties.className = "text-success";
                                break;

                            default:

                                break;
                        }
                        // if (colorInfo !== undefined) {
                        //     console.log(colorInfo, 'colorInfo');
                        //     // cellProperties.style = `background-color: ${colorInfo.background}; color: ${colorInfo.text}`;
                        //     // css cellProperties
                        //     cellProperties.renderer = function (instance, td, row, col, prop, value) {
                        //         console.log(instance, 'instance', td, row, col, prop, value);
                        //         // this.instance = instance;
                        //         td.style.backgroundColor = colorInfo.background || ""; // Áp dụng màu nền
                        //         td.style.color = colorInfo.text || ""; // Áp dụng màu chữ
                        //         // return td;
                        //     };

                        // }
                    } else {
                        if (row <= 2 && col <= 6) {
                            cellProperties.className = "border-transparent"
                        }
                        if (col <= 8) {
                            cellProperties.readOnly = true;
                        }
                    }
                    return cellProperties;
                },
                afterRenderer: (TD, row, col, prop, value, cellProperties) => {
                    // console.log(TD, row, col, prop, value, cellProperties);
                    if (row > 3) {
                        let is_field_color = this.data_api.items[row].color ?? false;
                        if (is_field_color) {
                            // console.log('is_field_color', is_field_color); // Hàm này luôn luôn render
                            const colorInfo = is_field_color.background[prop]; // Lấy thông tin màu
                            const color_info_text = is_field_color.text[prop];
                            if (colorInfo !== undefined) {
                                TD.style.background = colorInfo;
                            }
                            if (color_info_text !== undefined) {
                                TD.style.color = color_info_text == '' ? 'black' : color_info_text;
                            }
                        }
                    }


                },

                // hiddenColumns: {
                //     columns: [0], // Chỉ số cột `is_specifications`
                //     indicators: false, // Không hiển thị chỉ báo ẩn
                // },
                dropdownMenu: {
                    items: {
                        filter_by_value: {
                            name: 'Tìm kiếm',
                        },
                        filter_action_bar: {},
                    }
                },

                filters: true,
                contextMenu: {
                    items: {
                        "export": {
                            name: 'Xuất Excel',
                            callback: function (key, options) {
                                this.getPlugin('exportFile').downloadFile('csv', {
                                    filename: 'Data xử lý'
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
                        separator: ContextMenu.SEPARATOR,
                        // Custom menu item
                        create_column: {
                            name: 'Tạo cột',
                            callback: (key, options) => {
                                // this.addColumn();
                                this.showModal = true;
                            },
                        },
                        // // Custom menu item
                        // create_row: {
                        //     name: 'Tạo dòng',
                        //     callback: (key, options) => {
                        //         this.addColumnDefault();
                        //     },
                        // },
                        // Custom menu item Xòa cột tùy chỉnh
                        remove_column: {
                            name: 'Xóa cột',
                            callback: (key, options) => {
                                // this.addColumn();
                                // this.showModal = true;
                                // lấy vị trí cột đang selectRanges
                                const selectedRange = this.$refs.myHotTableSecond.hotInstance.getSelectedRange();
                                selectedRange.map((range) => {
                                    const col = range.from.col;
                                    const colEnd = range.to.col;
                                    const field = Object.keys(this.data_api.items[3])[col];
                                    this.data_api.items.map((item, index) => {
                                        delete item[field];
                                    });
                                    this.loadDataHandsontable();



                                });
                                // console.log(selectedRange);

                            },
                        },

                        color_background:
                        {
                            name: 'Tô màu nền',
                            submenu: {
                                items: this.getColorItems('color_background'),
                            },
                        },
                        color_text:
                        {
                            name: 'Tô màu chữ',
                            submenu: {
                                items: this.getColorItems('color_text'),
                            },
                        },
                    },
                },
                licenseKey: 'non-commercial-and-evaluation'
            },
            setting_types: {
                type: 'dropdown',
                source: ['_GK', '_Hộp'],
                strict: true,
            },
            api: {
                so_processing_data: 'api/sales-order/so-processing-data',
                material_category: 'api/master/material-category',


            },
            error: {
                indexs: [],
                message: '',
            },
            data_api: {
                items: [
                    {
                        barcode: '',
                        price_vat: '',
                        promotion: '',
                        sap_code: '',
                        sap_name: '',
                        specifications: '',
                        sum_quantity: 'T_Tiền (-VAT)',
                        tax: 'Số lượng NS',
                        unit: '',
                    },
                    {
                        barcode: '',
                        price_vat: '',
                        promotion: '',
                        sap_code: '',
                        sap_name: '',
                        specifications: '',
                        sum_quantity: 'Ký hiệu ĐH',
                        tax: '',
                        unit: '',

                    },
                    {
                        barcode: 'Mã vạch',
                        price_vat: 'Giá chưa VAT',
                        promotion: 'Phân loại',
                        sap_code: 'Mã SAP',
                        sap_name: 'Tên sản phẩm',
                        specifications: 'Quy cách',
                        sum_quantity: 'Tổng SL',
                        tax: 'Thuế',
                        unit: '',
                    },
                ],
                headers: [],
                material_categories: [],
            },
            setting_categories: {
                source: [],
                strict: true,
            },
            showModal: false,
            newColumnName: "",
            cols: [
                {
                    value: ""
                }
            ],
            color_items: [
                {
                    key: 'colors:red',
                    name: '<div style="background: red;width: 50%;" >Red</div> ',
                    callback: (key, options) => {
                        this.demoColor('red');
                    },
                },
                {
                    key: 'colors:blue',
                    name: 'Blue',
                    callback: (key, options) => {
                        this.demoColor('blue');
                    },
                },
                {
                    key: 'colors:green',
                    name: 'Green',
                    callback: (key, options) => {
                        this.demoColor('green');
                    },
                },

            ]

        }
    },
    created() {
        this.fetchMaterialCategories();
    },
    methods: {
        getDemoClickModal(){
            console.log('click modal');
            this.$emit('click-modal');
        },
        getColorItems(theme) {
            const colors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'gray'];
            return colors.map(color => ({
                key: `${theme}:${color}`,
                name: `<div style="background: ${color}; width: 100%; height: 15px;"></div>`,
                callback: () => this.demoColor(color, theme)
            }));
        },
        // demoColor(text_color, theme) {
        //     const selectedRange = this.$refs.myHotTableSecond.hotInstance.getSelectedRange();
        //     const field_color = theme == 'color_background' ? 'background' : 'text';
        //     if (selectedRange) {
        //         selectedRange.forEach((range) => {
        //             const startRow = range.from.row;
        //             const endRow = range.to.row;
        //             const startCol = range.from.col;
        //             const endCol = range.to.col;

        //             for (let row = startRow; row <= endRow; row++) {
        //                 for (let col = startCol; col <= endCol; col++) {
        //                     const field = Object.keys(this.data_api.items[row])[col];
        //                     console.log(field, 'field');
        //                     console.log(this.data_api.items, 'color', row);
        //                     this.data_api.items[row].color[field_color][field] = text_color;
        //                     console.log(this.data_api.items[row].color[field_color][field], 'color');
        //                 }
        //             }
        //         });

        //         this.loadDataHandsontable();
        //     }

        // },
        // demoColor(text_color, theme) {
        //     const selectedRange = this.$refs.myHotTableSecond.hotInstance.getSelectedRange();
        //     const sourceData = this.$refs.myHotTableSecond.hotInstance.getSourceData();
        //     const getData = this.$refs.myHotTableSecond.hotInstance.getData();
        //     const field_color = theme === 'color_background' ? 'background' : 'text';
        //     if (sourceData.length === getData.length) {
        //         if (selectedRange) {
        //             selectedRange.forEach((range) => {
        //                 const startRow = range.from.row;
        //                 const endRow = range.to.row;
        //                 const startCol = range.from.col;
        //                 const endCol = range.to.col;

        //                 for (let row = startRow; row <= endRow; row++) {
        //                     for (let col = startCol; col <= endCol; col++) {
        //                         const field = Object.keys(sourceData[row])[col];
        //                         if (!sourceData[row].color) {
        //                             this.$set(sourceData[row], 'color', { background: {}, text: {} });
        //                         }
        //                         this.$set(sourceData[row].color[field_color], field, text_color);
        //                     }
        //                 }
        //             });

        //             this.$refs.myHotTableSecond.hotInstance.render(); // Render lại Handsontable
        //         }
        //     } else {
        //         if (selectedRange) {
        //             selectedRange.forEach((range) => {
        //                 const startRow = range.from.row;
        //                 const endRow = range.to.row;
        //                 const startCol = range.from.col;
        //                 const endCol = range.to.col;

        //                 for (let row = startRow; row <= endRow; row++) {
        //                     for (let col = startCol; col <= endCol; col++) {
        //                         const field = Object.keys(getData[row])[col];
        //                         if (!getData[row].color) {
        //                             this.$set(getData[row], 'color', { background: {}, text: {} });
        //                         }
        //                         this.$set(getData[row].color[field_color], field, text_color);
        //                     }
        //                 }
        //             });

        //             this.$refs.myHotTableSecond.hotInstance.render(); // Render lại Handsontable
        //         }
        //     }

        // },
        demoColor(text_color, theme) {
            const hotInstance = this.$refs.myHotTableSecond.hotInstance;
            const selectedRange = hotInstance.getSelectedRange();
            const sourceData = hotInstance.getSourceData(); // Dữ liệu gốc
            const field_color = theme === 'color_background' ? 'background' : 'text';

            if (selectedRange) {
                selectedRange.forEach((range) => {
                    const startRow = range.from.row;
                    const endRow = range.to.row;
                    const startCol = range.from.col;
                    const endCol = range.to.col;

                    for (let row = startRow; row <= endRow; row++) {
                        for (let col = startCol; col <= endCol; col++) {
                            // Lấy tên cột từ vị trí cột hiển thị
                            const field = hotInstance.colToProp(col);

                            // Map từ dữ liệu hiển thị sang dữ liệu gốc
                            const visualRowIndex = hotInstance.toPhysicalRow(row);
                            console.log(visualRowIndex, 'visualRowIndex', row, 'row');
                            if (visualRowIndex !== null && visualRowIndex !== undefined) {
                                const rowData = sourceData[visualRowIndex];

                                if (!rowData.color) {
                                    this.$set(rowData, 'color', { background: {}, text: {} });
                                }
                                // Gán màu cho dữ liệu gốc
                                this.$set(rowData.color[field_color], field, text_color);
                                // Cập nhật màu hiển thị cho displayedData
                                const displayedData = hotInstance.getDataAtRow(row); // Dữ liệu hiển thị (sau filter)
                                // const displayRow = displayedData[row];
                                if (!displayedData[9].color) {
                                    this.$set(displayedData[9], 'color', { background: {}, text: {} });
                                }
                                this.$set(displayedData[9].color[field_color], field, text_color);
                                console.log(displayedData, 'displayedData');

                            }
                        }
                    }
                });

                // Render lại bảng để hiển thị màu
                hotInstance.render();
            }
        },


        handleAfterChange(changes, source) {
            if (!changes) return;

            let calculator = {};
            let skipFields = this.skipFields();

            // Tạo object calculator cho từng cột được chỉnh sửa
            changes.forEach(([row, prop, oldValue, newValue]) => {

                if (!skipFields.includes(prop)) {
                    calculator[prop] = {
                        sum_price_vat: 0,
                        sum_quantity: 0,
                        index_sum: 0,
                        total_price_vat: 0,
                    };
                }
            });

            // Tính toán lại giá trị cho các cột được chỉnh sửa
            Object.keys(calculator).forEach((column) => {
                let sum_price_vat = 0;
                let sum_quantity = 0;
                let index_sum = 0;

                this.data_api.items.forEach((item, index) => {
                    if (index > 3 && item[column] != null) {
                        sum_price_vat += parseFloat(item.price_vat) || 0;
                        sum_quantity += parseFloat(item[column]) || 0;
                        index_sum++;
                        item.sum_quantity = Object.keys(item)
                            .filter((key) => !skipFields.includes(key)) // Loại bỏ các field không cần thiết
                            .reduce((sum, key) => {
                                let value = parseFloat(item[key]) || 0; // Lấy giá trị số hoặc mặc định là 0
                                return sum + value; // Cộng dồn các giá trị
                            }, 0); // Bắt đầu từ 0
                    }
                });

                calculator[column].sum_price_vat = sum_price_vat;
                calculator[column].sum_quantity = sum_quantity;
                calculator[column].index_sum = index_sum;
                calculator[column].total_price_vat =
                    index_sum > 0
                        ? (sum_price_vat * sum_quantity) / index_sum
                        : 0;

                // Cập nhật giá trị vào dòng đầu tiên của cột
                this.data_api.items[0][column] = calculator[column].total_price_vat;

            });

            // Tải lại Handsontable
            this.loadDataHandsontable();
        },

        skipFields() {
            return ['promotion', 'sap_code', 'sap_name', 'unit', 'price_vat', 'specifications', 'barcode', 'tax', 'sum_quantity'];
        },
        calculatorTotalPriceVat() {
            let calculator = {};

            // Lọc ra các key không nằm trong skipFields
            let filtered_keys = Object.keys(this.data_api.items[0]).filter(key => !this.skipFields().includes(key));
            filtered_keys.forEach(key => {
                calculator[key] = { // Khởi tạo object cho từng key
                    sum_price_vat: 0,
                    sum_quantity: 0,
                    index_sum: 0,
                    total_price_vat: 0,
                };
            });

            // Duyệt qua từng item sau dòng index > 3
            this.data_api.items.forEach((item, index) => {
                if (index > 3) {
                    filtered_keys.forEach(key => {
                        if (item[key] != null) {
                            calculator[key].sum_price_vat += parseFloat(item.price_vat); // Tính tổng price_vat cho từng key
                            calculator[key].sum_quantity += parseFloat(item[key]);      // Tính tổng quantity
                            calculator[key].index_sum++;                                // Đếm số lần tính
                            calculator[key].total_price_vat =
                                (calculator[key].sum_price_vat * calculator[key].sum_quantity) / calculator[key].index_sum; // Tổng giá trị
                        }
                    });
                }
            });

            filtered_keys.forEach(key => {
                this.data_api.items[0][key] = calculator[key].total_price_vat;
            });
            this.loadDataHandsontable();
        },


        loadDataHandsontable() {
            this.settings.data = [...this.data_api.items];
            this.$refs.myHotTableSecond.hotInstance.loadData(this.data_api.items);
        },
        addColumnDefault() {
            // lấy key của dòng 3 để thêm dòng mới
            const keys = Object.keys(this.data_api.items[3]);
            const new_item = {};// gộp mảng keys với data_api.items[3] để tạo dòng mới
            keys.map((key) => {
                new_item[key] = "";
            });
            this.data_api.items.push(new_item);
            this.loadDataHandsontable();
        },
        addNewColumns() {
            this.cols.push({
                value: ""
            });
        },
        closeModel() {
            this.showModal = false;
        },
        openModal() {
            this.showModal = true;
        },
        addColumn() {
            let skipFields = this.skipFields();
            this.cols.map((col) => {
                // this.newColumnName = col.value;
                this.data_api.items.map((item, index) => {
                    if (index === 3) {
                        item[col.value] = col.value;
                    } else {
                        item[col.value] = "";
                    }
                });
            });
            this.closeModel();
            let count_store = Object.keys(this.data_api.items[3])
                .filter((key) => !skipFields.includes(key)) // Loại bỏ các field không cần thiết
            console.log(count_store.length, count_store);
            this.data_api.items[1].tax = count_store.length;
            this.loadDataHandsontable();

        },
        mapSettingCategoryToSource(categories) {

            let source = [];
            categories.map((category) => {
                source.push(category.name);
            });
            this.setting_categories.source = source;
            // console.log(this.setting_categories.source);
        },
        async fetchMaterialCategories() {
            try {
                const { data, success, errors, message } = await this.api_handler.get(this.api.material_category);
                if (data.success) {
                    this.mapSettingCategoryToSource(data.items);
                    // this.data_api.material_categories = data;
                }
            } catch (error) {
                this.error.message = error.response.data.message;
                this.error.indexs = error.response.data.indexs;
            }
        },
        // Map headers sang tiêu đề tiếng Việt
        mapHeadersToColHeaders(headers) {
            const headerMapping = {
                promotion: "Phân loại",
                sap_material_code: "Mã SAP",
                sap_material_name: "Tên SP",
                unit: "Đơn vị tính",
                price_vat: "Giá chưa VAT",
                specifications: "Quy cách",
                barcode: "Barcode",
                tax: "Thuế",
            };
            // Chuyển đổi headers
            return headers.map((header) => headerMapping[header] || header);
        },
        async fetchData() {
            this.is_loading = true;
            try {
                const { data, success, errors, message } = await this.api_handler.post(this.api.so_processing_data, {}, {
                    items: this.prop_items
                });
                if (success) {
                    console.log(data, 'datasucess true', data.items);

                    this.data_api.items = data.items;
                    this.calculatorTotalPriceVat();
                    // this.data_api.headers = data.headers;
                    // const colHeaders = this.mapHeadersToColHeaders(this.data_api.headers);
                    // Tạo cấu hình columns
                    // const columns = this.data_api.headers.map((header) => {
                    //     if (header === "promotion") {
                    //         return {
                    //             data: header,
                    //             type: "dropdown",
                    //             source: this.setting_categories.source,
                    //             strict: this.setting_categories.strict,
                    //         };
                    //     }
                    //     return { data: header }; // Cấu hình mặc định
                    // });
                    // this.settings.colHeaders = colHeaders;
                    console.log(this.data_api.items);
                    this.$refs.myHotTableSecond.hotInstance.loadData(this.data_api.items);
                    // this.$refs.myHotTableSecond.hotInstance.updateSettings({
                    //     colHeaders: colHeaders,
                    //     columns: columns,

                    // });
                    this.$showMessage('success', 'Thông báo', 'Lấy dữ liệu thành công');
                }
            } catch (error) {
                this.error.message = error.response.data.message;
                this.error.indexs = error.response.data.indexs;
            }
            this.is_loading = false;
        }
    },

}
</script>
<style lang="scss" scoped>
::v-deep .highlight-row {
    background-color: #ffc800 !important;
    /* Màu vàng nhạt */
    color: #000;
    /* Màu chữ đen */
    font-weight: bold;
}

::v-deep .highlight-row-blue {
    background-color: #008cff !important;
    /* Màu vàng nhạt */
    color: white;
    /* Màu chữ đen */
    font-weight: bold;
}

::v-deep .highlight-row-lightblue {
    background-color: #008cffb6 !important;
    /* Màu vàng nhạt */
    color: black;
    /* Màu chữ đen */
    font-weight: bold;
}

::v-deep .bg-yellow {
    background-color: #ffff00 !important;
}
</style>
