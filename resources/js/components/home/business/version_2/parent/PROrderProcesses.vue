<template>
    <div class="text-xs">
        <!-- <div class="d-inline-block">
            <ChildOrderProcessesInputHeader @convertFile="openModalDialogOrderProcessesConvertFile" />
        </div> -->
        <div class="d-inline-block">
            <ChildOrderProcessesConvertFileHeader :processing_success="processing_success" :order="order" :file="file"
                :customer_groups="customer_groups" @inputCustomerGroupId="emitInputCustomerGroupId"
                @inputExtractConfigID="emitInputExtractConfigID" @extractFilePDF="emitExtractFilePDF" />
        </div>
        <div class="d-inline-flex float-right mt-2">
            <div >
                <ChildOrderProcessesListOrder @modalListOrder="modalListOrder" />
            </div>
            <!-- <div>
                <ChildOrderProcessesLayoutHeader @listLayout="listLayout" />
            </div> -->


        </div>

        <div class="card">
            <div class="card-header bg-white mb-0">
                <ChildOrderProcessesHeader :order="order" @saveUpdateOrder="saveUpdateOrder"
                    @detectSapCodeOrder="detectSapCodeOrder" @updateOrder="updateOrder"
                    @emitDetectCustomerKey="emitDetectCustomerKey" @checkPromotion="checkPromotion"
                    @checkInventory="checkInventory" @checkCompliance="checkCompliance" @checkPrice="checkPrice"
                    @orderSyncSap="orderSyncSap" @exportExcel="exportExcel" @changeMaterial="changeMaterial"
                    @saveUpdateLayout="saveUpdateLayout" @deleteOrders="deleteOrders" @deleteOrdersHistory="deleteOrdersHistory" />
                <!-- <ChildOrderProcessesColorDefHeader @inputBackgroundColor="inputBackgroundColor" @inputTextColor="inputTextColor"/> -->
                
                <div class="d-flex">
                    <div class="mr-2">
                        <span>Số đơn hàng : <b>{{ CountOrderSoNumber }}</b></span>
                    </div>
                    <div class="mr-2">
                        <span>Số phiếu : <b>{{ CountGrpSoNumber }}</b></span>
                    </div>
                    <div class=""><span>Tiêu đề: </span><small class="font-weight-bold text-xs">{{ order.title }}</small><small class="text-danger text-xs">(*)</small></div>
                </div>
                <!-- <TableHelper :columns="user_field_tables" eventname="updateColumnHeader"
                v-on:updateColumnHeader="updateColumnHeader"></TableHelper> -->
            </div>
            <div class="card-body p-0">
                <div ref="zoomContainer" class="zoom-container">
                    <div class="content">
                        <ChildOrderProcessesBody :columns="columns" :filteredOrders="filteredOrders"
                            :material_category_types="material_category_types" :item_change_checked="item_change_checked"
                            :range="range" :update_column="update_column" :item_filters="item_filters"
                            :update_status_function="update_status_function" :position_order="position_order"
                            :item_filter_backgrounds="item_filter_backgrounds" :item_filter_texts="item_filter_texts"
                            :range_items="range_items" :hidden_columns="hidden_columns" @table="emitTable"
                            :selected_indexs="selected_indexs"
                            @inputSearch="emitInputSearch" @emitRangeChanged="emitRangeChanged"
                            @filterOrder="filterOrder" @editPromotion="editPromotion" @addRow="addRow"
                            @duplicateRow="duplicateRow" @copyRow="copyRow" @pasteRow="pasteRow" @deleteRow="deleteRow"
                            @rowSelectionChanged="rowSelectionChanged" @cellEdited="cellEdited"
                            @clipboardPasted="clipboardPasted" @inputBackgroundColor="inputBackgroundColor"
                            @inputTextColor="inputTextColor" @toggleColumn="toggleColumn" @hiddenColumns="hiddenColumns"
                            @toggleColumnShow="toggleColumnShow" @columnResized="columnResized"
                            @emitGetRangesData="emitGetRangesData" @popupOpened="popupOpened"
                            @itemChangeChecked="itemChangeChecked" @searchItem="searchItem"
                            @resetItem="resetItem" @deleteRowSuccess="deleteRowSuccess" 
                            @columnMoved="columnMoved" @emitRangeRemoved="emitRangeRemoved" @headerClick="headerClick" />
                    </div>
                </div>
            </div>
        </div>
        <!-- <DialogOrderProcessesConvertFile :order="order" :file="file" :customer_groups="customer_groups"
            @inputCustomerGroupId="emitInputCustomerGroupId" @inputExtractConfigID="emitInputExtractConfigID"
            @extractFilePDF="emitExtractFilePDF" /> -->

    </div>
</template>
<script>
import ChildOrderProcessesInputHeader from '../child/header/ChildOrderProcessesInputHeader.vue';
import ChildOrderProcessesHeader from '../child/header/ChildOrderProcessesHeader.vue';
import ChildOrderProcessesColorDefHeader from '../child/header/ChildOrderProcessesColorDefHeader.vue';
import ChildOrderProcessesBody from '../child/body/ChildOrderProcessesBody.vue';
import DialogOrderProcessesConvertFile from '../dialog/DialogOrderProcessesConvertFile.vue';
import ChildOrderProcessesListOrder from '../child/header/ChildOrderProcessesListOrder.vue';
import TableHelper from '../../../business/tables/TableHelper.vue';
import ChildOrderProcessesLayoutHeader from '../child/header/ChildOrderProcessesLayoutHeader.vue';
import ChildOrderProcessesConvertFileHeader from '../child/header/ChildOrderProcessesConvertFileHeader.vue';
export default {
    props: {
        columns: { type: Array, default: () => [] },
        filteredOrders: { type: Array, default: () => [] },
        file: { type: Object, default: () => { } },
        customer_groups: { type: Array, default: () => [] },
        order: { type: Object, default: () => { } },
        material_category_types: { type: Array, default: () => [] },
        CountGrpSoNumber: { type: Number, default: 0 },
        CountOrderSoNumber: { type: Number, default: 0 },
        update_status_function: { type: Object, default: () => { } },
        position_order: { type: Object, default: () => { } },
        range_items: { type: Array, default: () => [] },
        hidden_columns: { type: Array, default: () => [] },
        processing_success: { type: Number, default: 0 },
        range: { type: Object, default: () => { } },
        update_column:  { type: Number, default: 0 },
        item_filters: { type: Array, default: () => [] },
        item_change_checked: { type: Array, default: () => [] },
        item_filter_backgrounds: { type: Array, default: () => [] },
        item_filter_texts: { type: Array, default: () => [] },
        selected_indexs: { type: Array, default: () => [] }

    },
    components: {
        ChildOrderProcessesInputHeader,
        ChildOrderProcessesHeader,
        ChildOrderProcessesBody,
        DialogOrderProcessesConvertFile,
        ChildOrderProcessesColorDefHeader,
        ChildOrderProcessesListOrder,
        TableHelper,
        ChildOrderProcessesLayoutHeader,
        ChildOrderProcessesConvertFileHeader
    },
    data() {
        return {

            scale: 1,
            isZoomed: false
        }
    },
    mounted() {
        // this.$root.$on('openModal', this.openModal);
        // this.$root.$on('closeModal', this.closeModal);
        // this.openModal();
    },
    methods: {
        openModal() {
            $('#PROrderProcessHeader').modal('show');
        },
        closeModal() {
            $('#PROrderProcessHeader').modal('hide');
        },
        openModalDialogOrderProcessesConvertFile(file) {
            $('#DialogOrderProcessesConvertFile').modal('show');
            this.$emit('convertFile', file);
        },
        emitInputCustomerGroupId(customer_group_id) {
            this.$emit('inputCustomerGroupId', customer_group_id);
        },
        emitInputExtractConfigID(extract_config_id) {
            this.$emit('inputExtractConfigID', extract_config_id);
        },
        emitTable(table) {
            this.$emit('table', table);
        },
        emitInputSearch(search) {
            this.$emit('inputSearch', search);
        },
        emitRangeChanged(range) {
            this.$emit('emitRangeChanged', range);
        },
        inputBackgroundColor(color) {
            this.$emit('inputBackgroundColor', color);
        },
        inputTextColor(color) {
            this.$emit('inputTextColor', color);
        },
        saveUpdateOrder() {
            this.$emit('saveUpdateOrder');
        },
        emitExtractFilePDF(files) {
            this.$emit('extractFilePDF', files);
        },
        detectSapCodeOrder() {
            this.$emit('detectSapCodeOrder');
        },
        updateOrder() {
            this.$emit('updateOrder');
        },
        emitDetectCustomerKey() {
            this.$emit('emitDetectCustomerKey');
        },
        checkPromotion() {
            this.$emit('checkPromotion');
        },
        checkInventory() {
            this.$emit('checkInventory');
        },
        checkCompliance() {
            this.$emit('checkCompliance');
        },
        checkPrice() {
            this.$emit('checkPrice');
        },
        filterOrder(value, field, event) {
            this.$emit('filterOrder', value, field, event);
        },
        editPromotion(value, position) {
            this.$emit('editPromotion', value, position);
        },
        orderSyncSap() {
            this.$emit('orderSyncSap');
        },
        addRow(position, positions) {
            this.$emit('addRow', position, positions);
        },
        duplicateRow(position, data, positions) {
            this.$emit('duplicateRow', position, data, positions);
        },
        copyRow(position, data) {
            this.$emit('copyRow', position, data);
        },
        pasteRow(position) {
            this.$emit('pasteRow', position);
        },
        deleteRow(position, data) {
            this.$emit('deleteRow', position, data);
        },
        rowSelectionChanged(selected, is_check_or_uncheck, positions) {
            this.$emit('rowSelectionChanged', selected, is_check_or_uncheck, positions);
        },
        changeMaterial() {
            this.$emit('changeMaterial');
        },
        modalListOrder() {
            this.$emit('modalListOrder');
        },
        cellEdited(cell) {
            this.$emit('cellEdited', cell);
        },
        clipboardPasted(rows) {
            this.$emit('clipboardPasted', rows);
        },
        exportExcel() {
            this.$emit('exportExcel');
        },
        toggleColumn(column) {
            this.$emit('toggleColumn', column);
        },
        hiddenColumns(columns) {
            this.$emit('hiddenColumns', columns);
        },
        listLayout() {
            this.$emit('listLayout');
        },
        toggleColumnShow(column, field) {
            this.$emit('toggleColumnShow', column, field);
        },
        columnResized(column) {
            this.$emit('columnResized', column);
        },
        columnMoved(column) {
            this.$emit('columnMoved', column);
        },
        saveUpdateLayout() {
            this.$emit('saveUpdateLayout');
        },
        emitRangeRemoved(range) {
            this.$emit('emitRangeRemoved', range);
        },
        headerClick(column, getRanges) {
            this.$emit('headerClick', column, getRanges);
        },
        emitGetRangesData(table, positiones) {
            this.$emit('emitGetRangesData', table, positiones);
        },
        popupOpened(field) {
            this.$emit('popupOpened', field);
        },
        itemChangeChecked(item, checked) {
            this.$emit('itemChangeChecked', item, checked);
        },
        searchItem(column, event) {
            this.$emit('searchItem', column, event);
        },
        resetItem() {
            this.$emit('resetItem');
        },
        deleteOrders() {
            this.$emit('deleteOrders');
        },
        deleteRowSuccess() {
            this.$emit('deleteRowSuccess');
        },
        deleteOrdersHistory() {
            this.$emit('deleteOrdersHistory');
        }
       
    }
}
</script>
<style lang="scss" scoped>
.zoom-container {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
    transition: transform 0.3s ease, width 0.3s ease, height 0.3s ease;
}

.zoom-container.zoomed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: white;
    z-index: 9999;
    transform: scale(1.2);
}

.content {
    width: 100%;
    height: 100%;
    transform-origin: center center;
}

// .zoom-button {
//   position: absolute;
//   top: 10px;
//   right: 10px;
//   background: rgba(0, 0, 0, 0.5);
//   color: white;
//   border: none;
//   padding: 10px;
//   cursor: pointer;
//   border-radius: 5px;
// }</style>