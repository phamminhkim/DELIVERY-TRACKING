<template>
    <div>
        <div class="form-group d-inline-block mr-4">
            <span>Số đơn hàng: <b class="text-info">{{ CountGrpSoNumber }}</b></span>
        </div>
        <button @click="createRow()" type="button" class="btn btn-sm btn-info">
            <i class="fas fa-plus mr-1"></i>
            <span class="font-weight-bold">Thêm dòng</span>
        </button>
        <button @click="editRow()" type="button" class="btn btn-sm btn-light">
            <i class="fas fa-edit mr-1"></i>
            <span v-if="!case_boolean.is_show_hide" class="font-weight-bold ">Bật chỉnh sửa</span>
            <span v-else class="font-weight-bold text-danger">Tắt chỉnh sửa</span>
        </button>
        <div class="form-group d-inline-block">
            <TableHelper :columns="user_field_tables" eventname="updateColumnHeader"
                v-on:updateColumnHeader="updateColumnHeader"></TableHelper>
        </div>
        <div class="form-group d-inline-block border-bottom p-2 px-4 rounded mb-0"
            style="background: rgb(234 234 234 / 50%);">
            <span clsas="font-weight-normal">Tiêu đề: </span>
            <span v-if="case_save_so.title !== ''">
                <span class="font-weight-bold mr-2 text-danger"> {{ case_save_so.title }} </span>
            </span>
            <span v-else>
                <small class="font-weight-italic"><i>(Bản nháp)</i></small>
            </span>
        </div>
        <!-- @i_loading_detect_sap_sisHandleDbClick="getIsHandleDbClick -->
        <TableOrderSuffice ref="tableOrderSuffice" @deleteRow="getDeleteRow" :current_page="current_page"
            :per_page="per_page" :material_combos="material_combos" :material_donateds="material_donateds"
            :orders="orders" :order_lacks="order_lacks" :tab_value="tab_value" :count_reset_filter="count_reset_filter"
            @onChangeCategoryType="getOnChangeCategoryType" :iscode="is_loading_detect_sap_code"
            @checkBoxRow="getCheckBoxRow" @sortingChanged="sortingChanged" @isHandleDbClick="getIsHandleDbClick"
            @handleItem="getHandleItem" @btnDuplicateRow="getBtnDuplicateRow" @pasteItem="getPasteItem"
            @btnCopyDeleteRow="getBtnCopyDeleteRow" @btnParseCreateRow="getBtnParseCreateRow" @btnCopy="getBtnCopy"
            @emitFieldSetWidth="handleEmittedFieldSetWidth" :filterOrders="filterOrders" @filterItems="getFilterItems"
            @emitResetFilter="getResetFilter" :field_order_suffices="filterIsShowFields" @emitIndex="emitIndex" 
            @emitMosMouveSelectItem="emitMosMouveSelectItem">
        </TableOrderSuffice>
        <PaginationTable :rows="row_orders" :per_page="per_page" :page_options="page_options"
            :current_page="current_page" @pageChange="getPageChange" @perPageChange="getPerPageChange">
        </PaginationTable>

        <HeaderOrderColorNote></HeaderOrderColorNote>

    </div>
</template>
<script>
import TableOrderSuffice from '../tables/TableOrderSuffice.vue';
import PaginationTable from '../paginations/PaginationTable.vue';
import HeaderOrderColorNote from '../headers/HeaderOrderColorNote.vue';
import TableHelper from '../tables/TableHelper.vue';
export default {
    props: {
        api_handler: Object,
        tab_value: {
            type: String,
            default: 'order'
        },
       
        getOnChangeCategoryType: {
            type: Function
        },
        material_donateds: {
            type: Array
        },
        material_combos: {
            type: Array
        },
        orders: {
            type: Array
        },
        getDeleteRow: {
            type: Function
        },
        row_orders: {
            type: Number,
            default: 0
        },
        is_loading_detect_sap_code: {
            type: Boolean,
            default: false
        },
        order_lacks: {
            type: Array,
            default: () => []
        },
        case_save_so: {
            type: Object
        },
        filterOrders: {
            type: Array
        },
        count_reset_filter: {
            type: Number,
            default: 0
        },
        user_field_tables: {
            type: Array
        },
        filterIsShowFields: {
            type: Array
        },
    },
    components: {
        TableOrderSuffice,
        PaginationTable,
        HeaderOrderColorNote,
        TableHelper
    },
    data() {
        return {
            per_page: 100,
            page_options: [10, 20, 50, 100, 200, 300, 500],
            current_page: 1,
            case_data_temporary: {
                item_selecteds: [],
                orders: [],
                material_donateds: [],
                material_combos: [],
                field_selecteds: [],
                duppliacte_orders: [],
                count_dupplicate: 0,
                user_field_tables: [],

            },
            case_boolean: {
                is_show_hide: false,
                is_hide: true,
                is_mousedown: false,
            },
            field_order_suffices: [
                {
                    key: 'selected',
                    label: '',
                    class: 'text-nowrap   ',
                    tdClass: 'checkbox-sticky-left text-center',
                    thClass: 'checkbox-sticky-left text-center',
                    isShow: true,
                },
                {
                    key: 'action',
                    label: '',
                    class: 'text-nowrap ',
                    tdClass: 'checkbox-sticky-center text-center',
                    thClass: 'checkbox-sticky-center text-center',
                    isShow: true,

                },
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap text-center  ',
                    sortable: false,
                    tdClass: 'checkbox-sticky-end text-center border',
                    thClass: 'checkbox-sticky-header-end text-center',
                    isShow: true,

                },
                {
                    key: 'customer_name',
                    label: 'Makh Key',
                    class: 'text-nowrap  ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'sap_so_number',
                    label: 'Mã Sap So',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,

                },
                {
                    key: 'so_sap_note',
                    label: 'Sap note',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,

                },
                {
                    key: 'barcode',
                    label: 'Barcode_cty',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'sku_sap_code',
                    label: 'Masap',
                    class: 'text-nowrap text-center  ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'sku_sap_name',
                    label: 'Tensp',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,



                },
                {
                    key: 'quantity3_sap',
                    label: 'SL_sap',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,



                },
                {
                    key: 'sku_sap_unit',
                    label: 'Dvt',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },

                {
                    key: 'promotive',
                    label: 'Km',
                    class: 'text-nowrap   ',
                    tdClass: 'voucher-custom border p-0 ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'note',
                    label: 'Ghi_chu',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'customer_code',
                    label: 'Makh',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'customer_sku_code',
                    label: 'Unit_barcode',
                    class: 'text-nowrap text-center  ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,
                    tdClass: (key, index, order) => {
                        const arr_duplicate = this.duplicateFilterOrder(key, index);
                        if (this.checkDuplicateOrder(arr_duplicate, key)) {
                            return 'hover-field-order bg-duplicate';
                        }
                        return 'hover-field-order';
                    },
                },
                {
                    key: 'customer_sku_name',
                    label: 'Unit_barcode_description',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'customer_sku_unit',
                    label: 'Dvt_po',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'po',
                    label: 'Po',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'quantity1_po',
                    label: 'Qty',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'promotive_name',
                    label: 'Combo',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,



                },
                {
                    key: 'inventory_quantity',
                    label: 'Check tồn',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'quantity2_po',
                    label: 'Po_qty',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'variant_quantity',
                    label: 'SL Chênh lệch',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border',
                    isShow: true,
                },
                {
                    key: 'price_po',
                    label: 'Pur_price',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },

                {
                    key: 'amount_po',
                    label: 'Amount',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'compliance',
                    label: 'QC',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,

                },
                {
                    key: 'is_compliant',
                    label: 'Đúng_QC',
                    sortable: false,
                    thClass: 'border',
                    class: 'text-center   text-nowrap',
                    isShow: true,

                },
                {
                    key: 'note1',
                    label: 'Ghi chú 1',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'company_price',
                    label: 'Gia_cty',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'level2',
                    label: 'Level_2',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'level3',
                    label: 'Level_3',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'level4',
                    label: 'Level_4',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'po_number',
                    label: 'po_number',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,


                },
                {
                    key: 'po_delivery_date',
                    label: 'po_delivery_date',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border',
                    isShow: true,

                },
            ],
            api_user_field_table: '/api/master/user-field-table',
        }
    },
    async created() {
        // this.case_data_temporary.field_selecteds = this.field_order_suffices;
        // this.case_data_temporary.field_selecteds = this.case_data_temporary.user_field_tables;
        this.case_data_temporary.field_selecteds = this.user_field_tables;   
        // await this.fetchUserFieldTable();
    },
    methods: {
        emitMosMouveSelectItem(indexs, key) {
            this.$emit('emitMosMouveSelectItem', indexs, key);
        },
        emitIndex(index, key) {
            this.$emit('emitIndex', index, key);
        },
        async updateColumnHeader(data) {
            // this.field_order_suffices = data;
            // this.case_data_temporary.user_field_tables = data;
            this.$emit('emitUpdateColumnHeader', data);
            // await this.fetchUserFieldTable(this.case_data_temporary.user_field_tables);
        },
        getPerPageChange(per_page) {
            this.per_page = per_page;
        },
        getPageChange(page) {
            this.current_page = page;
        },
        getCheckBoxRow(items, index) {
            this.$emit('checkBoxRow', items, index);
        },
        refeshCheckBox() {
            this.$refs.tableOrderSuffice.refeshCaseCheckBox();
        },
        isEmptyObject() {
            return Object.keys(this.case_save_so).length === 0;
        },
        sortingChanged(sort) {
            this.$emit('sortingChanged', sort);
        },
        createRow() {
            this.$emit('createRow');
        },
        editRow() {
            this.case_boolean.is_show_hide = !this.case_boolean.is_show_hide;
            this.$refs.tableOrderSuffice.editRow(this.case_boolean.is_show_hide);
            this.$emit('editRow', this.case_boolean.is_show_hide);
        },
        getHandleItem(item, field, index, orders) {
            this.$emit('handleItem', item, field, index, orders);
        },
        getIsHandleDbClick(is_dbclick) {
            this.case_boolean.is_show_hide = is_dbclick;
            this.$emit('editRow', this.case_boolean.is_show_hide);
        },
        getBtnDuplicateRow(index, item) {
            this.$emit('btnDuplicateRow', index, item);
        },
        getPasteItem(items, index, field, event) {
            this.$emit('pasteItem', items, index, field, event);
        },
        getBtnCopyDeleteRow(index, item) {
            this.$emit('btnCopyDeleteRow', index, item);
        },
        getBtnParseCreateRow(index) {
            this.$emit('btnParseCreateRow', index);
        },
        getBtnCopy(index, item) {
            this.$emit('btnCopy', index, item);
        },
        getFilterItems(items, field, boolean) {
            this.$emit('filterItems', items, field, boolean);
        },
        getResetFilter() {
            this.$emit('emitResetFilter');
        },
        getDuplicates(arr) {
            const seen = new Set();
            const duplicates = new Set();

            for (const value of arr) {
                if (seen.has(value)) {
                    duplicates.add(value);
                } else {
                    seen.add(value);
                }
            }
            return Array.from(duplicates);
        },
        duplicateFilterOrder(value, key) {
            const duplicate = this.getDuplicates(this.filterOrders.map(item => item[key]));
            return duplicate;
        },
        checkDuplicateOrder(arr_duplicate, value) {
            return arr_duplicate.includes(value);
        },
        async handleEmittedFieldSetWidth(width, key) {
            // this.case_data_temporary.user_field_tables = this.case_data_temporary.user_field_tables.map((item) => {
            //     if (item.key == key) {
            //         item.set_width = width;
            //     }
            //     return item;
            // });
            // await this.fetchUserFieldTable(this.case_data_temporary.user_field_tables);
            let data = [...this.user_field_tables];
            data = this.user_field_tables.map((item) => {
                if (item.key == key) {
                    item.set_width = width;
                }
                return item;
            });
            this.$emit('emitUpdateColumnHeader', data);
            // await this.fetchUserFieldTable(this.case_data_temporary.user_field_tables);user_field_tables
        }

    },
    computed: {
        CountGrpSoNumber() {
            const group_by_so_num = Object.groupBy(this.orders, ({ sap_so_number, promotive_name }) => sap_so_number + (promotive_name == null ? '' : promotive_name));
            return Object.keys(group_by_so_num).length
        },
       
    }
}
</script>
<style lang="scss" scoped>
.hover-field-order {
    cursor: pointer;

    &:hover {
        background: #f8f9fa;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
}

::v-deep .bg-duplicate {
    background: #f8d7da !important;
    color: #721c24 !important;
    font-weight: bold;
}
</style>
