<template>
    <div>
        <div class="modal bounce-in" id="template_excel" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase font-weight-bold" id="exampleModalLabel"> {{ title }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-import-excel ">
                            <input type="file" ref="fileInput" @change="handleFileChange"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                            <p class="mx-sm-auto" v-if="load !== ''" style="position:relative"><i
                                    class="fas fa-file-contract mt-4" style="font-size:30px"></i><br>File:
                                {{ load }}
                                <span class="text-danger"
                                    style="position:absolute;top:0;right:10px;font-size:25px;cursor:pointer;"
                                    @click="clearFileInput()"><i class="fas fa-times-circle"></i></span>
                            </p>
                            <p class="mx-sm-auto" v-else><i class="fas fa-file-contract mt-4"
                                    style="font-size:30px"></i><br>
                                Thêm file
                                Excel <br><span class="text-secondary" style="font-size:10px">hoặc kéo và thả</span></p>
                            <div class="text-right update_data_excel" style="position:absolute;top:5px;right:5px;">
                                <button v-if="load !== ''" class="btn btn-outline-info btn-xs"
                                    @click="showDataExcel()"><i class="fas fa-eye"></i> Xem dữ liệu Excel</button> <br>
                                <button v-if="load !== ''" class="btn btn-outline-warning btn-xs"
                                    @click="chooseNewFile()"><i class="fas fa-edit"></i> Thay đổi file</button>
                            </div>
                        </div>
                        <div class="text-center mt-1">
                            <button v-if="load !== ''" type="button" class="btn btn-success"
                                @click="convertFileExcel()"><i class="fas fa-upload"></i> Upload</button>

                        </div>
                        <div class="form-group table-responsive">
                            <label>Trường mẫu File Excel: </label>
                            <a class="float-right" style="cursor:pointer" @click="exportExcel"> <i
                                    class="fas fa-download"></i>
                                <u>Dowload templates Excel</u> </a>
                            <table class="table table-responsive text-nowrap table-bordered text-center">
                                <thead>
                                    <tr class="font-weight-bold">
                                        <td class="px-md-1" v-for="field_excel in header_fields">{{
                            field_excel }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-secondary font-italic">
                                        <td v-for="data_excel in selectedExcel"> {{ data_excel.value }} <span
                                                class="text-danger">
                                                ({{ data_excel.label }}) </span> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal bounce-in" tabindex="-1" role="dialog" id="data_excel">
            <div class="modal-dialog fullscreen" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }} <small>(xem chi tiết)</small></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="loading" class="text-center">
                            <i class="far fa-hourglass fa-spin" style="font-size:30px"></i>
                            <h6 class="loading-text">Đang tải</h6>
                        </div>

                        <label>Danh sách Excel đã thêm vào: <i class="fas fa-sort-amount-down text-success"></i></label>
                        <div v-if="!loading" type="button" class="btn bg-gradient-success float-right css-upload-excel"
                            style="position:sticky;top:10px;z-index:1;border-radius: 30px;" @click="convertFileExcel()">
                            <i class="fas fa-upload"> Upload</i>
                        </div>
                        <div v-if="!loading" class="table-responsive custom-table-import">
                            <table class="table table-bordered table-hover text-nowrap table-sm">
                                <thead>
                                    <tr class="font-weight-bold">
                                        <!-- <th>Tùy chỉnh</th> -->
                                        <!-- <th class="text-center bg-gradient-cyan">#</th> -->
                                        <th v-for="column in columns" class="bg-gradient-cyan">{{ column }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, row_index) in row_excels" :key="row_index">

                                        <!-- <td class="font-weight-bold text-secondary text-center">{{ row_index + 1 }}</td> -->
                                        <td v-for="(cell, cell_index) in row" :key="cell_index" :class="{
                            'bg-warning ': isError(cell_index, row[cell_index]) || isErrorNull(row[cell_index])
                        }">
                                            <span v-if="isOtherIndex(row_index)">{{ cell }}
                                                <span v-if="case_boolean.import_success" class="float-right">
                                                    <i class="fas fa-check text-success"></i></span>
                                                <i v-if="isError(cell_index, row[cell_index]) || isErrorNull(row[cell_index])"
                                                    class="fas fa-exclamation-circle text-danger mt-1 float-right">
                                                </i>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

</template>

<script>
import * as XLSX from 'xlsx';
import axios from 'axios';
export default {
    props: {
        is_show_hide: Boolean,
        title: String,
        header_fields: Array,
        api_upload: String,
    },
    data() {
        return {
            case_boolean: {
                edit: false,
                import_success: false,
            },
            case_index: {
                edit: -1,
            },
            load: '',
            errors: [
                {
                    index_error: -1,
                    is_error: false,
                    text: '',
                }
            ],
            token: '',
            columns: [],
            row_excels: [],
            row_excel_alls: [],
            properties: [],
            table_data: null,
            loading: false,
            table_header: null,
            error_import_excel: {},
            failed: {},
        }
    },
    watch: {
        is_show_hide: function (val) {
            if (val) {
                this.showTemplateExcel();
            } else {
                this.hideTemplateExcel();
            }
        },
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
    },
    methods: {
        isErrorNull(cell) {
            return cell === null || cell === '' || cell === undefined;
        },
        isError(cell_index, cell) {
            if (this.errors.length > 0) {
                for (let i = 0; i < this.errors.length; i++) {
                    if (this.errors[i].text === cell) {
                        return true;
                    }
                }
            }
        },
        isOtherIndex(index) {
            return this.case_index.edit !== index;
        },
        editRow(index) {
            this.case_boolean.edit = true;
            this.case_index.edit = index;
        },
        deleteRow(index) {
            this.row_excels.splice(index, 1);
        },
        showTemplateExcel() {
            this.reset();
            $('#template_excel').modal('show');
        },
        hideTemplateExcel() {
            $('#template_excel').modal('hide');
        },
        exportExcel() {
            const selectedFieldsArray = Array.isArray(this.header_fields) ? this.header_fields : [this.header_fields];
            const headers = [...selectedFieldsArray];
            const worksheet = XLSX.utils.aoa_to_sheet([headers]) // tạo worksheet từ dữ liệu và header
            const workbook = XLSX.utils.book_new() // tạo workbook mới
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1') // thêm worksheet vào workbook
            XLSX.writeFile(workbook, 'template_import_xử_lý_đơn_hàng.xlsx') // tạo file excel và tải xuống
        },
        handleFileChange(event) {
            this.load = event.target.files[0].name;
            const file = this.$refs.fileInput.files[0];
            const reader = new FileReader();

            reader.onload = (e) => {
                const data = e.target.result;
                const workbook = XLSX.read(data, { type: 'binary' });
                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];
                const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                if (rows.length > 0) {
                    this.columns = rows[0];
                    this.row_excels = rows.slice(1);
                    this.row_excel_alls = rows;
                }
            };

            reader.readAsBinaryString(file);
            $("#data_excel").modal("show");
        },
        uploadFiles() {
            this.loading = true;
            this.case_boolean.import_success = false;
            const file = this.$refs.fileInput.files[0];
            var page_url = this.api_upload;
            const formData = new FormData();
            formData.append('file', file);
            axios.post(page_url, formData, {
                headers: {
                    'Authorization': this.token,
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(response => {
                    this.failed = JSON.stringify(response.data);
                    const failedData = JSON.parse(this.failed);
                    if (failedData.success == 0) {
                        this.loading = false;
                        this.case_boolean.import_success = false;
                        this.errors = failedData.errors;
                        this.showMessage('warning', failedData.message);
                    } else {
                        this.showMessage('success', 'Import File Excel thành công');
                        this.table_data = response.data.data;
                        this.loading = false;
                        this.case_boolean.import_success = true;
                        // $('#template_excel').modal('hide');
                        // $("#data_excel").modal("hide");
                        // this.clearFileInput();
                    }
                })
                .catch(error => {
                    console.error(error);
                    if (error.response) {
                        // Server trả về mã lỗi HTTP và thông báo lỗi
                        this.error_import_excel = error.response.data.message;
                        this.showMessage('warning', this.error_import_excel);
                        this.loading = false;
                    } else {
                        // Lỗi xảy ra khi gửi yêu cầu AJAX
                        this.loading = false;
                    }
                });
        },
        convertFileExcel() {
            this.$emit('convertFileExcel', this.row_excel_alls);
            // $('#template_excel').modal('hide');
            // $("#data_excel").modal("hide");
            // this.clearFileInput();
        },
        clearFileInput() {
            this.load = '';
            const fileInput = this.$refs.fileInput;
            fileInput.type = 'text';
            fileInput.type = 'file';
        },
        showModal() {
            $('#template_excel').modal('show');
        },
        showDataExcel() {
            this.loading = false;
            $("#data_excel").modal("show");
        },
        chooseNewFile() {
            this.clearFileInput();
            this.$refs.fileInput.click();
        },
        showMessage(type, title, message) {
            if (!title)
                title = "Information";
            toastr.options = {
                positionClass: "toast-bottom-right",
                toastClass: this.getToastClassByType(type),
            };

            toastr[type](message, title);
        },
        getToastClassByType(type) {
            switch (type) {
                case "success":
                    return "toastr-bg-green";
                case "error":
                    return "toastr-bg-red";
                case "warning":
                    return "toastr-bg-yellow";
                default:
                    return "";
            }
        },
        reset() {
            this.case_boolean.import_success = false;
            this.case_boolean.edit = false;
            this.case_index.edit = -1;
            this.load = '';
            this.errors = [
                {
                    index_error: -1,
                    is_error: false,
                    text: '',
                }
            ];
        }
    },
    computed: {
        selectedExcel() {
            return this.header_fields.map(function (field) {
                return { label: field, value: "Nhập" };
            });
        }
    },
}

</script>

<style lang="scss" scoped>
.form-import-excel {
    position: relative;
    width: 100%;
    height: 150px;
    background: rgb(247, 248, 250);

}

.form-import-excel p {
    width: 50%;
    height: 100%;
    text-align: center;
    color: black;

    border-radius: 10px;
    border: 3px dotted lightgray;

}

.form-import-excel:hover p {
    background: rgb(234, 235, 237);

}

.form-import-excel input {
    position: absolute;
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    outline: none;
    opacity: 0;
    cursor: pointer;
}

.fullscreen {
    width: 100% !important;
    max-width: none !important;
    height: 100% !important;
    margin: 0 !important;
}

.css-upload-excel {
    background: rgb(18, 162, 109);
    color: white;

    &:hover {
        background: rgb(21, 208, 140);
        color: white;
    }
}

.animation-upload {
    background: green;
}

.animate-charcter {
    text-transform: uppercase;
    background-image: linear-gradient(-225deg,
            #231557 0%,
            #44107a 29%,
            #ff1361 67%,
            #fff800 100%);
    background-size: auto auto;
    background-clip: border-box;
    background-size: 200% auto;
    color: #fff;
    background-clip: text;
    text-fill-color: transparent;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: textclip 2s linear infinite;
    display: inline-block;
    font-size: 190px;
}

@keyframes textclip {
    to {
        background-position: 200% center;
    }
}

@keyframes loading {
    0% {
        content: "";
    }

    25% {
        content: ".";
    }

    50% {
        content: "..";
    }

    75% {
        content: "...";
    }

    100% {
        content: "";
    }
}

.loading-text::after {
    content: "";
    animation: loading 1s infinite;
}

.delete {
    background: linear-gradient(to top, #ff0453, #ee0f56);
    border-radius: 8px;
}

.edit {
    background: linear-gradient(to top, #10d4ff, #2180a1);
    border-radius: 8px;
}
.custom-table-import{
    overflow: scroll;
    max-height: 500px;

}
</style>