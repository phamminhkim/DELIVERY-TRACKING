<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group p-1 mb-0" style="background: rgb(0 0 0 / 2%);">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <HeaderFileProcesses @changeFile="getChangeFile"></HeaderFileProcesses>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0 font-weight-bold" id="basic-addon1">Nhóm
                                    khách hàng</span>
                            </div>
                            <select v-model="form_filter.customer_group" @change="browserCustomerGroup()"
                                class="form-control">
                                <option v-for="(customer_group, index) in customer_groups" :key="index"
                                    :value="customer_group.id">{{ customer_group.name }}</option>
                            </select>
                        </div>
                        <div v-if="case_data_temporary.type_file == 'PDF'">
                            <button @click="showModalUploadPdf()" type="button"
                                class="shadow btn-sm btn-light text-uppercase text-orange btn-group__border">Upload đơn
                                hàng</button>
                            <div class="modal fade" id="modalUploadPdf" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Xử lý đơn hàng PDF</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <OrderUploads v-if="case_data_temporary.type_file == 'PDF'"
                                                @convertFilePDF="getConvertFilePDF"></OrderUploads>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input v-if="case_data_temporary.type_file == 'Excel'" @change="handleFileUpload" type="file"
                            class="custom-file-inputs w-100 shadow-sm mb-2" :disabled="isDisabledFile()">
                        <div class="btn-group-sm text-uppercase mb-2" role="group" aria-label="Basic example">
                            <!-- <button type="button" class="shadow btn-sm btn-light text-uppercase text-orange btn-group__border">coppy
                            đơn</button> -->

                            <button @click="detectSapCode()" type="button"
                                class="shadow btn-sm btn-light text-uppercase text-orange btn-group__border">Dò mã
                                SAP</button>
                            <button type="button"
                                class="shadow btn-sm btn-light text-uppercase text-orange btn-group__border">Check
                                Tồn</button>
                            <button type="button"
                                class="shadow btn-sm btn-light text-uppercase text-orange btn-group__border">Check
                                Giá</button>
                        </div>
                        <button type="button"
                            class="btn-sm px-5 btn-secondary text-uppercase btn-group__border">Refesh</button>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0 font-weight-bold" id="basic-addon1">Kho
                                    hàng</span>
                            </div>
                            <select v-model="form_filter.warehouse" class="form-control">
                                <option v-for="(warehouse, index) in warehouses" :key="index" :value="warehouse.code">{{
            warehouse.name }}</option>
                            </select>
                        </div>


                    </div>
                    <div class="form-group btn-group-sm" role="group">
                        <button type="button"
                            class="btn-sm btn font-weight-bold text-success px-5 btn-light text-uppercase text-center btn-group__border shadow-btn">Lưu
                            hàng thiếu</button>
                        <button
                            class="btn-sm btn font-weight-bold btn-light px-5 text-danger text-uppercase btn-group__border shadow-btn">Xóa
                            dữ liệu</button>
                    </div>
                    <div class="form-group btn-group-sm" role="group">
                        <button @click="openModalSearchOrderProcesses()" type="button"
                            class="btn-sm btn font-weight-bold px-5 btn-light text-uppercase text-center btn-group__border shadow-btn"><i
                                class="fas fa-search mr-2"></i>Tìm
                            mã...</button>
                        <button class="btn-sm btn btn-light text-success text-uppercase btn-group__border shadow-btn"><i
                                class="fas fa-file-upload mr-2"></i>Tạo
                            UPLOAD</button>
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-group">
                        <div class="form-group">
                            <button
                                class="btn-sm w-100 px-2 btn-warning text-uppercase btn-group__border shadow-btn">Khóa
                                Event</button>
                        </div>
                        <div class="form-group">
                            <button
                                class="btn-sm w-100 px-2 btn-success text-uppercase text-center btn-group__border shadow-btn">Mở
                                Event</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group form-note p-3">
                        <HeaderOrderColorNote></HeaderOrderColorNote>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as XLSX from 'xlsx';
import ApiHandler, { APIRequest } from '../../ApiHandler';
import HeaderFileProcesses from './HeaderFileProcesses.vue';
import OrderUploads from '../OrderUploads.vue';
import HeaderOrderColorNote from './HeaderOrderColorNote.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order'
        },

    },
    components: {
        HeaderFileProcesses,
        OrderUploads,
        HeaderOrderColorNote
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            is_open_modal_search_order_processes: false,
            data_excels: [],
            case_index: {
                code_type: 0,
                name_product: 0,
                customer_sku_code: 0,
                store: 0,
                customer_sku_unit: 0,
                po_qty: 0,
                price_po: 0,
            },
            case_data_temporary: {
                customer_codes: [],
                sap_codes: [],
                type_file: 'Excel',
            },
            form_filter: {
                customer_sku_unit: null,
                sap_material: [],
                customer_group: null,
                warehouse: null,
            },
            orders: [],
            bar_codes: [],
            sap_materials: [],
            material_donateds: [],
            material_combos: [],
            customer_groups: [],
            warehouses: [],
            api_sap_materials: 'api/master/sap-materials',
            api_customer_groups: 'api/master/customer-groups',
            api_warehouses: 'api/master/warehouses',
            api_material_donateds: '/api/master/material-donateds/get-all',
            api_material_combos: '/api/master/material-combos/get-all',


        }
    },
    created() {
        this.fetchCustomerGroup();
        this.fetchWarehouse();

    },
    methods: {
        async fetchSapMaterial() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_sap_materials, {
                    bar_codes: this.bar_codes,
                });
                if (Array.isArray(data)) {
                    this.sap_materials = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchCustomerGroup() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_customer_groups);
                if (Array.isArray(data)) {
                    this.customer_groups = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchWarehouse() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_warehouses);
                if (Array.isArray(data)) {
                    this.warehouses = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchMaterialDonated() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_material_donateds, {
                    sap_codes: this.case_data_temporary.sap_codes,
                });
                if (Array.isArray(data)) {
                    this.material_donateds = data;
                    this.$emit('listMaterialDonated', data);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchMaterialCombo() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_material_combos, {
                    sap_codes: this.case_data_temporary.sap_codes,
                });
                if (Array.isArray(data)) {
                    this.material_combos = data;
                    this.$emit('listMaterialCombo', data);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        openModalSearchOrderProcesses() {
            this.$emit('openModalSearchOrderProcesses');
        },
        handleFileUpload(event) {
            this.resetFile();
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];
                const excelData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                this.browserExcelHeader(excelData[0]);
                this.browserExcelData(excelData);
                this.fetchSapMaterial();
            };
            reader.readAsArrayBuffer(file);
        },
        browserExcelHeader(data_firsts) {
            for (let index = 0; index < data_firsts.length; index++) {
                switch (data_firsts[index]) {
                    case "Mã phân loại":
                        this.case_index.code_type = index;
                        break;
                    case "Tên sản phẩm":
                        this.case_index.name_product = index;
                        break;
                    case "Mã bán hàng":
                        this.case_index.customer_sku_code = index;
                        break;
                    case "Cửa hàng":
                        this.case_index.store = index;
                        break;
                    case "Đơn vị":
                        this.case_index.customer_sku_unit = index;
                        break;
                    case "Số lượng đặt hàng":
                        this.case_index.po_qty = index;
                        break;
                    case "Đơn giá":
                        this.case_index.price_po = index;
                        break;
                    default:
                        break;
                }

            }
        },
        browserExcelData(data) {
            for (let index = 1; index < data.length; index++) {
                this.orders.push({
                    barcode: '',
                    sku_sap_code: '',
                    sku_sap_name: '',
                    sku_sap_unit: '',
                    promotive: '',
                    combo: '',
                    so_num: data[index][this.case_index.store],
                    description: data[index][this.case_index.store],
                    description_2: data[index][this.case_index.store],
                    customer_sku_code: data[index][this.case_index.customer_sku_code],
                    customer_sku_name: data[index][this.case_index.name_product],
                    customer_sku_unit: data[index][this.case_index.customer_sku_unit],
                    quantity_po: '',
                    po_qty: data[index][this.case_index.po_qty],
                    price_po: data[index][this.case_index.price_po],
                    amount_po: this.calculatorAmount(data[index][this.case_index.price_po]),
                    code_customer: this.browserCustomerCode(data[index][this.case_index.store] == undefined ? '' : data[index][this.case_index.store]),

                });
                this.bar_codes.push(data[index][this.case_index.customer_sku_code]);
            }
            this.$emit('listOrders', this.orders);
        },
        calculatorAmount(price_po, number = 10) {
            price_po = this.replaceString(price_po);
            let caculator = price_po * number;
            caculator = this.replaceCalculatorAmount(caculator);
            return caculator;
        },
        replaceString(string) {
            return string.toString.replace(/,/g, '');
        },
        replaceCalculatorAmount(string) {
            return string.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        detectSapCode() {
            for (let index = 0; index < this.orders.length; index++) {
                for (let index_sap_material = 0; index_sap_material < this.sap_materials.length; index_sap_material++) {
                    if (this.orders[index].customer_sku_code == this.sap_materials[index_sap_material].bar_code) {
                        this.orders[index].barcode = this.sap_materials[index_sap_material].bar_code;
                        this.orders[index].sku_sap_code = this.sap_materials[index_sap_material].sap_code;
                        this.orders[index].sku_sap_name = this.sap_materials[index_sap_material].name;
                        this.orders[index].sku_sap_unit = this.sap_materials[index_sap_material].unit_code;
                        this.case_data_temporary.sap_codes.push(this.sap_materials[index_sap_material].sap_code);
                    }
                }
            }
            this.fetchMaterialDonated();
            this.fetchMaterialCombo();
        },
        isDisabledFile() {
            if (this.form_filter.customer_group == null) {
                return true;
            }
            return false;
        },
        resetFile() {
            this.orders = [];
            this.bar_codes = [];
            this.sap_materials = [];
        },
        browserCustomerGroup() {
            this.customer_groups.forEach(customer_group => {
                if (customer_group.id == this.form_filter.customer_group) {
                    for (let index = 0; index < customer_group.customers.length; index++) {
                        this.case_data_temporary.customer_codes.push({
                            name: customer_group.customers[index].name,
                            code: customer_group.customers[index].code,
                        });
                    }
                }
            });
        },
        browserCustomerCode(store) {
            for (let index = 0; index < this.case_data_temporary.customer_codes.length; index++) {
                if (store == this.case_data_temporary.customer_codes[index].name && store != "" && store != null) {
                    return this.case_data_temporary.customer_codes[index].code;
                }
            }
        },
        getChangeFile(change_file) {
            this.case_data_temporary.type_file = change_file.type;
        },
        showModalUploadPdf() {
            $('#modalUploadPdf').modal('show');
        },
        closeModalUploadPdf() {
            $('#modalUploadPdf').modal('hide');
        },
        updateMaterialCategoryTypeInOrder(index, item) {
            if (this.orders[index].promotive != item.code) {
                this.orders[index].promotive = item.code;
                this.orders[index].sku_sap_name = this.orders[index].sku_sap_name + item.name;
                this.orders[index].description = this.orders[index].description + item.name;
                this.orders[index].combo = this.orders[index].combo + item.name;
                this.orders[index].description_2 = this.orders[index].description_2 + item.name;
            }
        },
        getConvertFilePDF(file_response) {
            for (let index = 0; index < file_response.data.length; index++) {
                let files = file_response.data[index].items;
                for (let index_item = 0; index_item < files.length; index_item++) {
                    let item = files[index_item];
                    this.orders.push({
                        barcode: '',
                        sku_sap_code: '',
                        sku_sap_name: '',
                        sku_sap_unit: '',
                        promotive: '',
                        combo: '',
                        so_num: file_response.data[index].headers.PoPerson,
                        description: file_response.data[index].headers.PoPerson,
                        description_2: '',
                        customer_sku_code: item.ProductID,
                        customer_sku_name: item.ProductName,
                        customer_sku_unit: item.OrdUnit,
                        quantity_po: item.Quantity1,
                        po_qty: item.Quantity2,
                        price_po: item.ProductPrice,
                        amount_po: this.calculatorAmount(item.ProductAmount),
                        code_customer: '',
                    });
                    this.bar_codes.push(item.ProductID);
                }
            }
            this.fetchSapMaterial();
            this.$emit('listOrders', this.orders);

        },


    }
}

</script>

<style lang="scss" scoped>
.btn-group {
    &__border {
        font-weight: 500;
        border: 1px solid #f0f0f0;
    }
}

.custom-file-inputs::-webkit-file-upload-button {
    visibility: hidden;
}

.custom-file-inputs::before {
    content: 'COPY ĐƠN';
    color: #fd7e14;
    background: white;
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
    padding: 10px 8px;
    outline: none;
    white-space: nowrap;
    user-select: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 10pt;
    border-right: 1px solid;
}

.custom-file-inputs:hover {
    border-right: 1px solid;
    background: lightgray;
    color: #f8f9fa;
    cursor: pointer;
}

// .custom-file-inputs:hover::before {
//     border-right: 1px solid;
//     background: #505a64;
//     cursor: pointer;
// }
.custom-file-inputs:disabled {
    background: lightgray !important;
    color: #f8f9fa !important;
    cursor: not-allowed !important;
}

.custom-file-inputs:disabled::before {
    background: lightgray !important;
    color: #f8f9fa !important;
    cursor: not-allowed !important;
}

.shadow-btn {
    box-shadow: 0px 8px 10px -6px rgba(0, 0, 0, 0.4);
}

.form-note {
    background: rgb(255 227 82 / 20%);
}
</style>