<template>
    <div>
        <button @click="createRow()" type="button" class="btn btn-sm btn-info">
            <i class="fas fa-plus mr-1"></i>
            <span class="font-weight-bold">Thêm dòng</span>
        </button>
        <button @click="editRow()" type="button" class="btn btn-sm btn-light">
            <i class="fas fa-edit mr-1"></i>
            <span v-if="!case_boolean.is_show_hide " class="font-weight-bold ">Bật chỉnh sửa</span>
            <span v-else class="font-weight-bold text-danger">Tắt chỉnh sửa</span>
        </button>
        <div class="form-group d-inline-block border-bottom p-2 px-4 rounded mb-0"
            style="background: rgb(234 234 234 / 50%);">
            <span clsas="font-weight-normal">Tiêu đề: </span>
            <span v-if="case_save_so.title !== ''">
                <span  class="font-weight-bold mr-2 text-danger"> {{ case_save_so.title }} </span>
            </span>
            <span v-else>
                <small class="font-weight-italic"><i>(Bản nháp)</i></small>
            </span>
        </div>
        <TableOrderSuffice ref="tableOrderSuffice" @deleteRow="getDeleteRow" :current_page="current_page"
            :per_page="per_page" :material_combos="material_combos" :material_donateds="material_donateds"
            :orders="orders" :order_lacks="order_lacks" :tab_value="tab_value"
            @onChangeCategoryType="getOnChangeCategoryType" :is_loading_detect_sap_code="is_loading_detect_sap_code"
            @checkBoxRow="getCheckBoxRow"
            @sortingChanged="sortingChanged"
            @isHandleDbClick="getIsHandleDbClick"
            @handleItem="getHandleItem"
            @btnDuplicateRow="getBtnDuplicateRow"
            @pasteItem="getPasteItem"
            @btnCopyDeleteRow="getBtnCopyDeleteRow"
            @btnParseCreateRow="getBtnParseCreateRow"></TableOrderSuffice>
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
export default {
    props: {
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
        }
    },
    components: {
        TableOrderSuffice,
        PaginationTable,
        HeaderOrderColorNote
    },
    data() {
        return {
            per_page: 10,
            page_options: [10, 20, 50, 100],
            current_page: 1,
            case_data_temporary: {
                item_selecteds: [],
                orders: [],
                material_donateds: [],
                material_combos: [],
            },
            case_boolean: {
                is_show_hide: false,
                is_hide: true
            }

        }
    },
    methods: {
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
        },
        getHandleItem(item, field, index, orders) {
            this.$emit('handleItem', item, field, index, orders);
        },
        getIsHandleDbClick(is_dbclick) {
            this.case_boolean.is_show_hide = is_dbclick;
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
        }

    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>