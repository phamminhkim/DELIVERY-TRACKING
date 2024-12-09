<template>
    <div class="modal fade" id="BookStoreImport" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-uppercase">Thêm Nhanh</h5>
                    <button @click="closeModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <button @click="downloadExcelTemplate()" class="btn btn-sm btn-outline-primary text-xs px-2"><i
                                class="fas fa-download text-xs mr-1"></i>Download Template Mẫu</button>
                    </div>
                    <div class="form-group">
                        <div class="mr-1">
                            <div><small class="mb-0 text-xs font-weight-bold">Chọn File</small><small
                                    class="text-danger ml-1">(*)</small>
                            </div>
                            <div class="d-flex">
                                <input @change="readXlsxFile" type="file" ref="fileInput"
                                    class="form-control-file form-control-sm" accept=".xlsx, .xls, .csv">
                                <!-- <div class="flex-fill" v-show="is_loading">
                                        <i class="fas fa-spinner fa-pulse fa-xs" style="color: #c7c6c7;"></i>
                                    </div> -->
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <small class="text-xs font-weight-bold">Review</small>
                            </div>
                            <div class="col-lg-6 text-right">
                                <button @click="emitSave()" type="button"
                                    class="btn btn-success btn-sm px-4 text-xs">Lưu</button>
                            </div>
                        </div>
                        <b-table :fields="fields" :items="data_files" responsive hover small bordered
                            head-variant="light" striped>
                            <template #cell(index)="data">
                                {{ data.index + 1 }}
                            </template>
                        </b-table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="closeModal()" type="button" class="btn btn-secondary btn-sm px-4 text-xs"
                        data-dismiss="modal">Đóng</button>
                    <button @click="emitSave()" type="button" class="btn btn-success btn-sm px-4 text-xs">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as XLSX from 'xlsx';

export default {
    components: {

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
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-center text-xs text-nowarp',
                },
                {
                    key: 'Tên nhà sách',
                    label: 'Tên nhà sách',
                    class: 'text-center text-xs text-nowarp',
                },
                {
                    key: 'ten_ns',
                    label: 'ten_ns',
                    class: 'text-center text-xs text-nowarp',
                }
            ]
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
            };
            reader.readAsArrayBuffer(file);
        },
        downloadExcelTemplate() {
            // Dữ liệu mẫu
            const data = [
                { "Tên nhà sách": "TMNSDT HO CHI MINH", ten_ns: "HCM" },
                { "Tên nhà sách": "HNNSB1 NS FAHASA LONG BIÊN", ten_ns: "" },
                { "Tên nhà sách": "HNNSDH NS FAHASA TRẦN DUY HƯNG", ten_ns: "" },
            ];

            // Chuyển dữ liệu thành Sheet
            const worksheet = XLSX.utils.json_to_sheet(data);

            // Tạo Workbook
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Template");

            // Xuất file
            XLSX.writeFile(workbook, "template.xlsx");
        },
        emitSave() {
            this.$emit('save', this.data_files);
        }
    }
}
</script>
<style lang="scss" scoped></style>
