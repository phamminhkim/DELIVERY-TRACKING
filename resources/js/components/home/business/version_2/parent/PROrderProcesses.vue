<template>
    <div>
        <ChildOrderProcessesInputHeader @convertFile="openModalDialogOrderProcessesConvertFile"  />
        <ChildOrderProcessesListOrder @modalListOrder="modalListOrder" />
        <div class="card">
            <div class="card-header bg-white mb-0">
                <ChildOrderProcessesHeader :order="order"
                 @saveUpdateOrder="saveUpdateOrder"
                 @detectSapCodeOrder="detectSapCodeOrder" @updateOrder="updateOrder"
                 @emitDetectCustomerKey="emitDetectCustomerKey"
                 @checkPromotion="checkPromotion"
                 @checkInventory="checkInventory"
                 @checkCompliance="checkCompliance"
                 @checkPrice="checkPrice"
                 @orderSyncSap="orderSyncSap"
                 @exportExcel="exportExcel"
                 @changeMaterial="changeMaterial" />
                <ChildOrderProcessesColorDefHeader @inputBackgroundColor="inputBackgroundColor" @inputTextColor="inputTextColor"/>
                <h6 class="font-weight-bold">Tiêu đề: {{ order.title }}</h6>
                <span>Số đơn hàng : <b>{{ CountGrpSoNumber }}</b></span>
                <!-- <TableHelper :columns="user_field_tables" eventname="updateColumnHeader"
                v-on:updateColumnHeader="updateColumnHeader"></TableHelper> -->
            </div>
            <div class="card-body p-0">
                <div ref="zoomContainer" class="zoom-container" >
                    <div class="content">
                        <ChildOrderProcessesBody :columns="columns" :filteredOrders="filteredOrders"
                        :material_category_types="material_category_types"
                        :update_status_function="update_status_function"
                        :position_order="position_order"
                         @table="emitTable" @inputSearch="emitInputSearch"
                         @emitRangeChanged="emitRangeChanged"
                         @filterOrder="filterOrder"
                         @editPromotion="editPromotion"
                         @addRow="addRow"
                         @duplicateRow="duplicateRow"
                         @copyRow="copyRow"
                         @pasteRow="pasteRow"
                         @deleteRow="deleteRow"
                         @rowSelectionChanged="rowSelectionChanged"
                         @cellEdited="cellEdited"
                         @clipboardPasted="clipboardPasted"
                         @inputBackgroundColor="inputBackgroundColor"
                         @inputTextColor="inputTextColor" />
                    </div>
                </div>
            </div>
        </div>
        <DialogOrderProcessesConvertFile :order="order" :file="file" :customer_groups="customer_groups"
         @inputCustomerGroupId="emitInputCustomerGroupId"
         @inputExtractConfigID="emitInputExtractConfigID"
         @extractFilePDF="emitExtractFilePDF" />

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
export default {
    props: {
        columns: { type: Array, default: () => [] },
        filteredOrders: { type: Array, default: () => [] },
        file: { type: Object, default: () => {} },
        customer_groups: { type: Array, default: () => [] },
        order: { type: Object, default: () => {} },
        material_category_types: { type: Array, default: () => [] },
        CountGrpSoNumber: { type: Number, default: 0 },
        update_status_function: { type: Object, default: () => {} },
        position_order: { type: Object, default: () => {} },

    },
    components: {
        ChildOrderProcessesInputHeader,
        ChildOrderProcessesHeader,
        ChildOrderProcessesBody,
        DialogOrderProcessesConvertFile,
        ChildOrderProcessesColorDefHeader,
        ChildOrderProcessesListOrder,
        TableHelper
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
        addRow(position) {
            this.$emit('addRow', position);
        },
        duplicateRow(position, data) {
            this.$emit('duplicateRow', position, data);
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
        rowSelectionChanged(selected, is_check_or_uncheck) {
            this.$emit('rowSelectionChanged', selected ,is_check_or_uncheck);
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