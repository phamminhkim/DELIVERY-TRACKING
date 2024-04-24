<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group p-1 mb-0" style="background: rgb(0 0 0 / 2%);">
            <b-button v-b-toggle.collapse-1 class="btn btn-sm btn-warning px-3 mt-1 mb-2"><i class="fas fa-recycle mr-2"></i>Xử lý đơn hàng</b-button>
            <button @click="emitListOrderProcessSO()" type="button" class="btn btn-sm btn-primary px-3 mt-1 mb-2 ml-2"><i class="fas fa-list-ol mr-2"></i>Danh sách xử lý đơn hàng</button>
            <b-collapse id="collapse-1" class="mt-2">
                <b-card class="border-0 shadow-sm">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <HeaderFileProcesses @changeFile="getChangeFile"></HeaderFileProcesses>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 font-weight-bold font-smaller"
                                                id="basic-addon1">Nhóm
                                                khách hàng</span>
                                        </div>
                                        <select v-model="form_filter.customer_group" @change="browserCustomerGroup()"
                                            class="form-control font-smaller">
                                            <option v-for="(customer_group, index) in customer_groups" :key="index"
                                                :value="customer_group.id">{{ customer_group.name
                                                }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 font-weight-bold font-smaller"
                                                id="basic-addon1">Cấu hình</span>
                                        </div>
                                        <select v-model="form_filter.config_id" class="form-control font-smaller"
                                            :disabled="form_filter.customer_group == null">
                                            <option v-for="(extract_order, index) in type_file_extract_order_configs"
                                                :key="index" :value="extract_order.id">{{ extract_order.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-8">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-smaller border-0 font-weight-bold"
                                            id="basic-addon1">Kho
                                            hàng</span>
                                    </div>
                                    <select v-model="form_filter.warehouse" class="font-smaller form-control">
                                        <option v-for="(warehouse, index) in warehouses" :key="index"
                                            :value="warehouse.code">{{
            warehouse.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group" v-if="case_data_temporary.type_file == 'Excel'">
                                <input @change="extractFilePDF" type="file"
                                    class="custom-file-inputs w-100 shadow-sm mb-2" :disabled="isDisabledFile()">
                            </div>
                            <div v-else class="form-group">
                                <b-form-file @change="extractFilePDF" v-model="form_filter.pdf_files" ref="file-input"
                                    plain multiple browse-text="Chọn file" />
                            </div>
                        </div>
                    </div>
                    <!-- <div>
                        <div class="form-group form-note p-3">
                            <HeaderOrderColorNote></HeaderOrderColorNote>
                        </div>
                    </div> -->
                </b-card>
            </b-collapse>
            <div class="form-group btn-group btn-group-custom" role="group">
                <button @click="detectSapCode()" type="button"
                    class="shadow btn-sm btn-light  rounded text-orange btn-group__border">Dò mã
                    SAP</button>
                <button type="button" v-on:click="handleCheckInventory"
                    class="shadow btn-sm btn-light rounded  text-orange btn-group__border">Check
                    tồn</button>
                <input type="file" ref="file_check_ton" style="display: none" accept=".xls,.xlsx"
                    @change="eventChooseFile($event)" class="shadow btn-sm btn-light text">
                <input type="file" ref="file_check_price" style="display: none" accept=".xls,.xlsx"
                    @change="chooseFileEventCheckPrice($event)" class="shadow btn-sm btn-light text">
                <button @click="handleCheckPrice()" type="button"
                    class="shadow btn-sm btn-light rounded text-orange btn-group__border">Check
                    giá</button>
                <button @click="emitOrderLack()" type="button"
                    class="btn-sm font-smaller btn font-weight-bold text-success rounded btn-light  text-center btn-group__border shadow-btn">Lưu
                    hàng thiếu</button>
                <button @click="emitOrderDelete()"
                    class="btn-sm font-smaller btn font-weight-bold btn-light rounded  text-danger  btn-group__border shadow-btn">Xóa
                    dữ liệu</button>
                <button @click="openModalSearchOrderProcesses()" type="button"
                    class="btn-sm font-smaller btn font-weight-bold btn-light rounded text-center btn-group__border shadow-btn"><i
                        class="fas fa-search mr-2"></i>Tìm
                    mã...</button>
                <button @click="downloadExcel()"
                    class="btn-sm font-smaller btn btn-light text-success rounded  btn-group__border shadow-btn"><i
                        class="fas fa-file-upload mr-2"></i>Tạo
                    upload</button>
                <!-- <button type="button"
                    class="btn-sm btn btn-secondary shadow-btn rounded btn-group__border">Refesh</button> -->
                <button @click="emitSaveOrderProcess()" type="button"
                    class="btn-sm font-smaller btn btn-success px-4 rounded btn-group__border shadow-btn">
                    <i class="fas fa-save mr-2"></i>Lưu</button>


            </div>
            <div class="modal fade" id="modalNotificationExtractPDF" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 notification-header">
                            <h5 class="modal-title font-weight-bold" id="staticBackdropLabel">Thông báo
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div v-show="case_error.extract_pdf == ''" class="form-group text-center">
                                <p>
                                    <i class="fas fa-spinner fa-spin fa-xs"></i> Đang giải nén tập
                                    tin...
                                </p>
                            </div>
                            <div v-show="case_error.extract_pdf !== ''" class="form-group text-center">
                                <label for="" class="text-danger">{{ case_error.extract_pdf
                                    }}</label><br>
                                <small class="text-warning">Không thể giải nén File PDF, vui lòng thử
                                    lại sau...</small>
                            </div>
                        </div>

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
import HeaderOrderColorNote from './HeaderOrderColorNote.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order'
        },
        item_selecteds: {
            type: Array,
            default: () => []
        }


    },
    components: {
        HeaderFileProcesses,
        HeaderOrderColorNote
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            is_open_modal_search_order_processes: false,
            data_excels: [],
            is_case_loading: {
                extract_pdf: false,
                extract_client: false,
            },
            case_error: {
                extract_pdf: '',
            },
           
            case_index: {
                code_type: 0,
                name_product: 0,
                customer_sku_code: 0,
                store: 0,
                customer_sku_unit: 0,
                quantity2_po: 0,
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
                config_id: null,
                pdf_files: null,
            },
            orders: [],
            bar_codes: [],
            sap_materials: [],
            material_donateds: [],
            material_combos: [],
            customer_groups: [],
            extract_order_configs: [],
            warehouses: [],
            api_sap_materials: 'api/master/sap-materials',
            api_customer_groups: 'api/master/customer-groups',
            api_warehouses: 'api/master/warehouses',
            api_material_donateds: '/api/master/material-donateds/get-all',
            api_material_combos: '/api/master/material-combos/get-all',
            api_detect_sap_code: '/api/check-data/check-material-sap',
            api_check_inventory: '/api/check-data/check-inventory',
            api_check_price: '/api/check-data/check-price',


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
        async fetchSapCodeFromSkuCustomer() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.post(this.api_detect_sap_code, {},
                    {
                        customer_group_id: this.form_filter.customer_group,
                        items: this.case_data_temporary.sap_codes,
                    }
                );
                //this.sap_codes =  data.original.mappingData;
                if (data.success == true) {
                    this.$emit('getListMaterialDetect', data.items);
                } else {
                    this.$showMessage('error', 'Lỗi');
                }


            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchCheckInventory(file) {
            try {
                var form_data = new FormData();
                form_data.append('file', file);
                form_data.append('warehouse_code', '3101');

                const { data } = await this.api_handler.post(this.api_check_inventory, {}, form_data);
                if (data.success == true) {
                    this.$emit('getInventory', data.inventory);
                } else {
                    this.$showMessage('error', 'Lỗi');
                }


            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchCheckPrice(file) {

            try {
                this.is_loading = true;
                var form_data = new FormData();
                form_data.append('file', file);
                const { data } = await this.api_handler.post(this.api_check_price, {}, form_data);
                if (data.success == true) {
                    this.$emit('checkPrice', data.price);
                } else {
                    this.$showMessage('error', 'Lỗi');
                }

            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        handleCheckPrice() {
            this.$refs.file_check_price.click();
        },
        async chooseFileEventCheckPrice(event) {
            await this.fetchCheckPrice(event.target.files[0]);
            this.resetEventTargetFile(event);
        },
        async eventChooseFile(event) {
            await this.fetchCheckInventory(event.target.files[0]);
            this.resetEventTargetFile(event);
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
                console.log(excelData);
                this.browserExcelHeader(excelData[0]);
                this.browserExcelData(excelData);
                this.fetchSapMaterial();
            };
            reader.readAsArrayBuffer(file);
        },

        handleCheckInventory() {
            this.$refs.file_check_ton.click();
        },
        mappingCheckInventory(data) {
            var list = [...data];
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
                        this.case_index.quantity2_po = index;
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
                    id: '',
                    barcode: '',
                    sku_sap_code: '',
                    sku_sap_name: '',
                    sku_sap_unit: '',
                    promotive: '',
                    promotive_name: '',
                    customer_name: data[index][this.case_index.store],
                    description: data[index][this.case_index.store],
                    note: data[index][this.case_index.store],
                    customer_sku_code: data[index][this.case_index.customer_sku_code],
                    customer_sku_name: data[index][this.case_index.name_product],
                    customer_sku_unit: data[index][this.case_index.customer_sku_unit],
                    quantity1_po: '',
                    quantity2_po: data[index][this.case_index.quantity2_po],
                    price_po: data[index][this.case_index.price_po],
                    amount_po: this.calculatorAmount(data[index][this.case_index.price_po]),
                    customer_code: this.browserCustomerCode(data[index][this.case_index.store] == undefined ? '' : data[index][this.case_index.store]),
                    inventory_quantity:'',
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
            return string.toString().replace(/,/g, '');
        },
        replaceCalculatorAmount(string) {
            return string.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        updateLoadingState(state, message = null, messageType = null, messageTitle = null) {
            this.is_case_loading.extract_client = state;
            this.$emit('isLoadingDetectSapCode', state);
            if (message) {
                this.$showMessage(messageType, messageTitle, message);
            }
        },
        updateLoadingState(state, message = null, messageType = null, messageTitle = null) {
            this.is_case_loading.extract_client = state;
            this.$emit('isLoadingDetectSapCode', state);
            if (message) {
                this.$showMessage(messageType, messageTitle, message);
            }
        },
        async detectSapCode() {
            this.updateLoadingState(true);
            try {
                this.orders.forEach(element => {
                    this.case_data_temporary.sap_codes.push({
                        customer_sku_code: element.customer_sku_code,
                        customer_sku_unit: element.customer_sku_unit
                    });
                });
                await this.fetchSapCodeFromSkuCustomer();
                this.updateLoadingState(false, 'Dò mã SAP thành công', 'success', 'Thành công');
            } catch (error) {
                console.error(error);
                this.updateLoadingState(false, error, 'error', 'Lỗi');
            }
            //  this.fetchMaterialDonated();
            // this.fetchMaterialCombo();
        },
        isDisabledFile() {
            if (this.form_filter.config_id == null) {
                return true;
            }
            return false;
        },
        resetFile() {
            this.orders = [];
            this.bar_codes = [];
            this.sap_materials = [];
        },
        resetConfig() {
            this.form_filter.config_id = null;
        },
        resetError() {
            this.case_error.extract_pdf = '';
        },
        browserCustomerGroup() {
            this.resetConfig();
            this.customer_groups.forEach(customer_group => {
                if (customer_group.id == this.form_filter.customer_group) {
                    this.extract_order_configs = customer_group.extract_order_configs;
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
        updateMaterialCategoryTypeInOrder(index, item) {
            if (this.orders[index].promotive != item.name) {
                this.orders[index].promotive = item.name;
                this.orders[index].promotive_name = item.name;
            }
        },
        async getConvertFilePDF(file_response) {
            for (let index = 0; index < file_response.data.length; index++) {
                let files = file_response.data[index].items;
                for (let index_item = 0; index_item < files.length; index_item++) {
                    let item = files[index_item];
                    this.orders.push({
                        id: '',
                        barcode: '',
                        sku_sap_code: '',
                        sku_sap_name: '',
                        sku_sap_unit: '',
                        promotive: '',
                        promotive_name: '',
                        customer_name: file_response.data[index].headers.CustomerKey,
                        note: file_response.data[index].headers.CustomerKey,
                        note1: file_response.data[index].headers.CustomerNote,
                        customer_sku_code: item.ProductID,
                        customer_sku_name: item.ProductName,
                        customer_sku_unit: item.OrdUnit,
                        quantity1_po: item.Quantity1,
                        quantity2_po: item.Quantity2,
                        price_po: item.ProductPrice,
                        amount_po: this.calculatorAmount(item.ProductAmount),
                        customer_code: file_response.data[index].headers.CustomerCode,
                        company_price: '',
                        level2: file_response.data[index].headers.CustomerLevel2,
                        level3: file_response.data[index].headers.CustomerLevel3,
                        level4: file_response.data[index].headers.CustomerLevel4,
                        is_promotive: false,
                        is_inventory: false,
                        inventory_quantity: '',
                    });
                    this.bar_codes.push(item.ProductID);
                }
            }
            await this.fetchSapMaterial();
            this.$emit('listOrders', this.orders);
        },
        appendFormData(pdf_files, config_id) {
            let formData = new FormData();
            if (pdf_files == null) {
                return formData;
            }
            for (let i = 0; i < pdf_files.length; i++) {
                formData.append(`file[]`, pdf_files[i]);
            }
            formData.append('config_id', config_id);
            return formData;
        },
        async apiConvertPDF(formData) {
            let file_response = await this.api_handler
                .setHeaders({
                    'Content-Type': 'multipart/form-data',
                })
                .post(
                    '/api/sales-order/convert-orders',
                    {},
                    formData,
                );
            return file_response;
        },
        async extractFilePDF(event) {
            try {
                this.is_case_loading.extract_pdf = true;
                this.resetFile();
                this.resetError();
                this.showModalExtractPDF();
                let formData = this.appendFormData(event.target.files, this.form_filter.config_id);
                let file_response = await this.apiConvertPDF(formData);
                await this.getConvertFilePDF(file_response);
                this.resetEventTargetFile(event);
                this.$showMessage('success', 'Thành công', 'Giải nén file thành công');
            } catch (error) {
                this.hideModalExtractPDF();
                this.case_error.extract_pdf = error;
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.hideModalExtractPDF();
                this.is_case_loading.extract_pdf = false;
                this.resetEventTargetFile(event);
            }
        },
        resetEventTargetFile(event) {
            event.target.value = '';
        },
        showModalExtractPDF() {
            $('#modalNotificationExtractPDF').modal('show');
        },
        hideModalExtractPDF() {
            $('#modalNotificationExtractPDF').modal('hide');
        },
        clearFileExtractPDF() {
            this.form_filter.pdf_files = [];
        },
        downloadExcel() {
            const group_by_so_num = Object.groupBy(this.orders, ({ customer_name, promotive_name }) => customer_name + promotive_name);
            const convert_array = Object.values(Object.keys(group_by_so_num));
            var data_header = [
                ['Số lượng phiếu: ' + Object.keys(group_by_so_num).length],
                ...convert_array.map(item => [item])
            ];
            const data_news = this.orders.map((item) => {
                return {
                    'Số SO': item.customer_name + item.promotive_name,
                    'Mã khách hàng': item.customer_code,
                    'Mã sản phẩm': item.customer_sku_code,
                    'Số lượng': (item.quantity2_po * item.quantity1_po),
                    'Đơn vị tính': item.sku_sap_unit,
                    'Combo': item.promotive_name,
                    'Phiên bản BOM Sale': '',
                    'level2': item.level2,
                    'level3': item.level3,
                    'level4': item.level4,
                    'Ghi_chú': item.note + item.promotive_name,
                    'Barcode': item.barcode,
                };
            });
            var ws = XLSX.utils.json_to_sheet(data_news);
            XLSX.utils.sheet_add_aoa(ws, data_header, { origin: 'N1' });
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });
            const blob = new Blob([this.s2ab(wbout)], { type: 'application/octet-stream' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'xử_lý_đơn_hàng.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        s2ab(s) {
            const buf = new ArrayBuffer(s.length);
            const view = new Uint8Array(buf);
            for (let i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        },
        emitOrderLack() {
            this.$emit('changeEventOrderLack');
        },
        emitOrderDelete() {
            this.$emit('changeEventOrderDelete');
        },
        emitSaveOrderProcess() {
            this.$emit('saveOrderProcess');
        },
        emitListOrderProcessSO() {
            this.$emit('listOrderProcessSO');
        }
    },
    computed: {
        type_file_extract_order_configs() {
            var type_file = this.case_data_temporary.type_file;
            return this.extract_order_configs.map((extract_order) => {
                if (type_file.toLowerCase() == extract_order.convert_file_type.toLowerCase()) {
                    return extract_order;
                }
            });
        }
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
    content: 'Copy đơn';
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

.font-smaller {
    font-size: smaller !important;
}

.btn-group-custom {
    display: flow-root !important;
}

.notification-header {
    background: linear-gradient(to right, #009fff, #ff0026) !important;
    color: white !important;
    opacity: 0.8 !important;
}
</style>
