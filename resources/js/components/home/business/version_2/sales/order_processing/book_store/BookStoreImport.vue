<template>
    <div>
        <div class="modal fade" id="BookStoreImport" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Thêm Nhanh</h5>
                        <button @click="closeModal()" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="mr-1">
                                <div><small class="mb-0">File</small><small class="text-danger ml-1">(*)</small>
                                </div>
                                <div class="d-flex">
                                    <input @change="readXlsxFile" type="file" ref="fileInput" class="form-control-file form-control-sm"
                                    accept=".xlsx, .xls, .csv">
                                    <!-- <div class="flex-fill" v-show="is_loading">
                                        <i class="fas fa-spinner fa-pulse fa-xs" style="color: #c7c6c7;"></i>
                                    </div> -->
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-success px-2 text-xs"><i class="fas fa-file-upload mr-1"></i>Upload</button>
                        </div>
                        <div class="form-group">
                            <hot-table ref="myHotTable" :data="data_files" :settings="settings">
                                <!-- <hot-column title="STT" :renderer="sttRenderer"></hot-column> -->

                            </hot-table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeModal()" type="button" class="btn btn-secondary btn-sm px-4 text-xs"
                            data-dismiss="modal">Đóng</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import { HotTable, HotColumn } from '@handsontable/vue';
import { ContextMenu } from 'handsontable/plugins/contextMenu';
import { registerAllModules } from 'handsontable/registry';
import 'handsontable/dist/handsontable.full.css';
import { head } from 'lodash';
registerAllModules();

export default {
    components: {
        HotTable,
        HotColumn
    },
    props: {
        is_show: {
            type: Boolean,
            default: false
        },
        example_datas: {
            type: Array,
            default: () => []
        }
    },
    watch: {
        is_show: {
            handler: function (val) {
                if (val) {
                    $('#BookStoreImport').modal('show');
                } else {
                    $('#BookStoreImport').modal('hide');
                }
            },
            immediate: true
        }
    },
    data() {
        return {
            file: null,
            data_files: [],
            headers: [],
            settings: {
                height: '500',
                width: '100%',
                autoWrapRow: true,
                autoWrapCol: true,
                rowHeaders: true,
                // colHeaders: true,
                // colHeaders: [
                //     'STT',
                //     'Loại phiếu',
                //     'Tên nhà sách',
                //     'Mã vạch',
                //     '',
                //     'Tên sản phẩm',
                //     'Số lượng',
                //     'Quy cách',
                //     'barcode_cty',
                //     'Mã SAP',
                //     'Tên SP',
                //     'Mã SAP',
                //     'Dvt',
                // ],

                // dropdownMenu: true,
                // dropdownMenu: ['filter_by_value', 'filter_action_bar'],

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
                        separator: ContextMenu.SEPARATOR,

                    },
                },
                // columns: this.columns,

                licenseKey: 'non-commercial-and-evaluation'
            },
        }
    },
    methods: {
        closeModal() {
            this.$emit('close-modal', false);
        },
        readXlsxFile() {
            const file = this.$refs.fileInput.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const sheet_name_list = workbook.SheetNames;
                const data_files = XLSX.utils.sheet_to_json(workbook.Sheets[sheet_name_list[0]]);
                this.data_files = data_files;
                console.log(this.data_files);
                this.getFieldKeyInArray();
                this.$refs.myHotTable.hotInstance.loadData(this.data_files);
                this.$refs.myHotTable.hotInstance.updateSettings({
                    columns: this.headers.map(item => {
                        return {
                            data: item,
                            type: 'text'
                        }
                    }),
                    colHeaders: this.headers
                });

            };
            reader.readAsArrayBuffer(file);
        },
        getFieldKeyInArray() {
            this.headers = [...new Set(this.data_files.map(item => Object.keys(item)).flat())];
            console.log(this.headers);
        },
    }
}
</script>
<style lang="scss" scoped></style>
