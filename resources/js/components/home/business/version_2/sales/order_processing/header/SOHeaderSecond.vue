<template>
    <div>
        <div class="row">
            <div class="col-lg-5 text-xs">
                <div class="mr-1">
                    <div><small class="mb-0">Check Tồn</small><small class="text-danger ml-1">(*)</small>
                    </div>
                    <div class="d-flex">
                        <select class="form-control form-control-sm flex-shrink-0">
                            <option value="">Chọn kho</option>
                            <option value="1">1</option>
                        </select>
                        <!-- <div class="flex-fill" >
                            <i class="fas fa-spinner fa-pulse fa-xs"></i>
                        </div> -->
                    </div>

                </div>
            </div>
        </div>
        <hot-table ref="myHotTableSecond" :data="data_api.items" :settings="settings">
            <hot-column v-for="(header, index) in data_api.headers" :key="index" :title="header" :data="header"
                :type="header === 'promotion' ? 'dropdown' : ''"
                :source="header === 'promotion' ? setting_types.source : null"
                :strict="header === 'promotion' ? setting_types.strict : false">
            </hot-column>
        </hot-table>

    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import ApiHandler from '../../../../../ApiHandler';
import { HotTable, HotColumn } from '@handsontable/vue';
import { ContextMenu } from 'handsontable/plugins/contextMenu';
import { registerAllModules } from 'handsontable/registry';
import 'handsontable/dist/handsontable.full.css';

registerAllModules();

export default {
    components: {
        HotTable,
        HotColumn,
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
                if (val === 2) {
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
            settings: {
                height: '500',
                width: '100%',
                manualColumnResize: true,
                autoWrapRow: true,
                autoWrapCol: true,
                rowHeaders: true,
                colHeaders: [
                    'Phân loại',
                    'Mã SAP',
                    'Tên SP',
                    'Đơn vị tính',
                    'Giá chưa VAT',
                    'Quy cách',
                    'Barcode',
                    'Thuế',
                ],

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
                items: [],
                headers: [],
                material_categories: [],
            },
            setting_categories: {
                source: [],
                strict: true,
            }

        }
    },
    created() {
        this.fetchMaterialCategories();
    },
    methods: {
        mapSettingCategoryToSource(categories) {
            let source = [];
            categories.map((category) => {
                source.push(category.name);
            });
            this.setting_categories.source = source;
            console.log(this.setting_categories.source);
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
                    this.data_api.items = data.items;
                    this.data_api.headers = data.headers;
                    const colHeaders = this.mapHeadersToColHeaders(this.data_api.headers);
                    // Tạo cấu hình columns
                    const columns = this.data_api.headers.map((header) => {
                        if (header === "promotion") {
                            return {
                                data: header,
                                type: "dropdown",
                                source: this.setting_categories.source,
                                strict: this.setting_categories.strict,
                            };
                        }
                        return { data: header }; // Cấu hình mặc định
                    });
                    this.settings.colHeaders = colHeaders;
                    this.$refs.myHotTableSecond.hotInstance.loadData(this.data_api.items);
                    this.$refs.myHotTableSecond.hotInstance.updateSettings({
                        colHeaders: colHeaders,
                        columns: columns,

                    });
                    this.$showMessage('success', 'Thông báo', 'Lấy dữ liệu thành công');
                }
            } catch (error) {
                this.error.message = error.response.data.message;
                this.error.indexs = error.response.data.indexs;
            }
            this.is_loading = false;
        }
    }
}
</script>
<style lang="scss" scoped></style>
