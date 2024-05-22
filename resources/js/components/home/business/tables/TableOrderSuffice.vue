<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group">
            <!-- sticky-header="500px" responsive @sort-changed="sortingChanged" -->
            <b-table small hover sticky-header="500px" head-variant="light" :items="filterOrders"
                :class="{ 'table-order-suffices': true, }" :fields="field_order_suffices" ref="btable"
                :tbody-tr-class="hightLightCopy" table-class="table-order-suffices" :current-page="current_page"
                :per-page="per_page">
                <template #head(index)="header">
                    <div :ref="'header_' + header.column" class="col-resize"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'index')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'index')">
                        <span>{{ header.label }}</span>
                    </div>
                </template>
                <template #head(customer_name)="header">
                    <div tabindex="0" class="text-center col-resize d-flex justify-content-between"
                        :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'customer_name')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'customer_name')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(sap_so_number)="header">
                    <div class="text-center col-resize d-flex justify-content-between" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'sap_so_number')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'sap_so_number')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(barcode)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'barcode')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'barcode')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize" :class="{
            'text-danger': is_loading_detect_sap_code == true
        }">
                            <span v-if="is_loading_detect_sap_code == true"><i
                                    class="fas fa-spinner fa-spin fa-xs"></i></span>
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(sku_sap_code)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'sku_sap_code')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'sku_sap_code')"
                        class="text-center d-flex col-resize justify-content-between ">
                        <label class="mb-0 col-resize" :class="{
            'text-danger': is_loading_detect_sap_code == true
        }">
                            <span v-if="is_loading_detect_sap_code == true"><i
                                    class="fas fa-spinner fa-spin fa-xs"></i></span>
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(sku_sap_name)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'sku_sap_name')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'sku_sap_name')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize" :class="{
            'text-danger': is_loading_detect_sap_code == true
        }">
                            <span v-if="is_loading_detect_sap_code == true"><i
                                    class="fas fa-spinner fa-spin fa-xs"></i></span>
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #cell(index)="data">
                    <div class="font-weight-bold index overflow-hidden">
                        {{ data.item.order }}
                        <!-- {{ (data.index + 1) + (current_page * per_page) - per_page }} -->
                    </div>
                </template>
                <template #cell(action)="data">
                    <TagOrderSufficeAction :index="data.index" :item="data.item" :copy="case_is_status.copy"
                        @btnDuplicateRow="btnDuplicateRow" @btnParseCreateRow="btnParseCreateRow" @btnCopy="btnCopy"
                        @btnCopyDeleteRow="btnCopyDeleteRow">
                    </TagOrderSufficeAction>
                </template>
                <template #head(selected)="data">
                    <b-form-checkbox v-model="case_checkbox.selected_all"
                        @change="checkBoxAll(data.index)"></b-form-checkbox>
                </template>
                <template #head(sku_sap_unit)="header">
                    <div class="text-center d-flex justify-content-between col-resize" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'sku_sap_unit')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'sku_sap_unit')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(promotive)="header">
                    <div class="text-center d-flex justify-content-between col-resize" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'promotive')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'promotive')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(note)="header">
                    <div class="text-center  col-resize d-flex justify-content-between" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'note')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'note')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(customer_code)="header">
                    <div class="text-center col-resize d-flex justify-content-between" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'customer_code')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'customer_code')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(customer_sku_code)="header">
                    <div class="text-center d-flex col-resize justify-content-between" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'customer_sku_code')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'customer_sku_code')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(customer_sku_name)="header">
                    <div class="text-center col-resize d-flex justify-content-between" :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'customer_sku_name')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'customer_sku_name')">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(quantity1_po)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'quantity1_po')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'quantity1_po')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #cell(selected)="data">
                    <b-form-checkbox v-model="case_checkbox.selected" @change="emitCheckBox(data.index)"
                        :value="data.item"></b-form-checkbox>
                </template>
                <template #cell(barcode)="data">
                    <div class="barcode overflow-hidden">
                        <input v-if="isHandleDbClick()" class="px-2" v-model="data.item.barcode"
                            @keydown="copyItem($event, data.item.barcode)" @dblclick="handleDoubleClick($event)"
                            @input="handleItem(data.item.barcode, 'barcode', data.index)" />
                        <div v-else :style="'width: 100%;height: 1.5rem;'" tabindex="0"
                            :ref="'keyListenerDiv_' + data.item.barcode + data.item.order + data.field.key"
                            @keydown="copyItem($event, data.item.barcode, data.field.key)"
                            @mousedown="startSelection($event, data.item.barcode, data.item.order, data.field.key)"
                            @mousemove="selectItem(data.item.barcode, $event)"
                            @mouseup="endSelection(data.item.barcode, $event)" @dblclick="handleDoubleClick($event)"
                            :class="{ 'change-border': isChangeBorder(data.item.barcode) && isSameField(case_order.field_order, data.field.key) }">
                            <span class="text-center rounded " :class="{
            'badge badge-warning': data.item.extra_offer == 'X',
            'badge badge-primary': data.item.promotion_category == 'X'
        }">
                                {{ data.item.barcode }}
                            </span>
                        </div>
                    </div>

                </template>
                <template #cell(customer_name)="data">
                    <div v-if="isCheckLack(data.item)">
                        {{ data.item.customer_name }}{{ data.item.promotive }} <br>
                        <small class="text-danger">Hàng thiếu</small>
                    </div>
                    <div v-else class="customer_name overflow-hidden">
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.customer_name"
                            @input="handleItem(data.item.customer_name, 'customer_name', data.index)" />

                        <span v-if="!case_is_status.edit"> {{ data.item.customer_name }} </span>

                    </div>
                </template>
                <template #cell(sap_so_number)="data">
                    <div class="sap_so_number overflow-hidden">
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.sap_so_number"
                            @input="handleItem(data.item.sap_so_number, 'sap_so_number', data.index)" />
                        <span v-if="case_is_status.edit"> {{ data.item.promotive }}</span>
                        <span v-if="!case_is_status.edit"> {{ data.item.sap_so_number }}{{
            data.item.promotive }}</span>
                    </div>

                </template>
                <template #cell(quantity1_po)="data">
                    <div class="quantity1_po overflow-hidden" :class="{
            'text-danger': isCheckLack(data.item)
        }">
                        <!-- {{ data.item.quantity1_po }} -->
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.quantity1_po"
                            @input="handleItem(data.item.quantity1_po, 'quantity1_po', data.index)" />
                        <strong v-else>{{ data.value.toLocaleString(locale_format) }}</strong>
                    </div>
                </template>
                <template #cell(quantity2_po)="data">
                    <div class="quantity2_po overflow-hidden" :class="{
            'text-danger': isCheckLack(data.item)
        }">
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.quantity2_po"
                            @input="handleItem(data.item.quantity2_po, 'quantity2_po', data.index)" />
                        <strong v-else>{{ data.value.toLocaleString(locale_format) }}</strong>
                    </div>
                </template>
                <template #cell(inventory_quantity)="data">
                    <div class="inventory_quantity overflow-hidden" :class="{
            'text-danger': isCheckLack(data.item),
            'text-danger': data.item.inventory_quantity <= 0
        }">
                        <!-- {{ data.item.inventory_quantity }} -->
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.inventory_quantity"
                            @input="handleItem(data.item.inventory_quantity, 'inventory_quantity', data.index)" />
                        <strong v-else>{{ data.value.toLocaleString(locale_format) }}</strong>
                    </div>
                </template>
                <template #head(customer_sku_unit)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'customer_sku_unit')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'customer_sku_unit')"
                        class="text-center d-flex col-resize justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(po)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'po')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'po')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(promotive_name)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'promotive_name')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'promotive_name')"
                        class="text-center d-flex col-resize justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(inventory_quantity)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'inventory_quantity')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'inventory_quantity')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(quantity2_po)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'quantity2_po')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'quantity2_po')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(price_po)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'price_po')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'price_po')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(amount_po)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'amount_po')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'amount_po')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(compliance)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'compliance')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'compliance')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(is_compliant)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'is_compliant')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'is_compliant')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <span class="badge badge-sm badge-secondary badge-pill mt-1 ml-1"><i
                                class="fas fa-question fa-sm"></i></span>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(note1)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'note1')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'note1')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            @emitResetFilter="getResetFilter" @fieldColumnHeader="fieldColumnHeader"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(company_price)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'company_price')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'company_price')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(level2)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'level2')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'level2')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(level3)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'level3')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'level3')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(level4)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'level4')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'level4')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(po_number)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'po_number')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'po_number')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #head(po_delivery_date)="header">
                    <div :ref="'header_' + header.column"
                        @mousedown="handleMouseDownHeader($event, 'header_' + header.column, 'po_delivery_date')"
                        @mouseup="handleMouseUpHeader" @mouseleave="handleMouseLeaveHeader"
                        @mousemove="handleMouseMoveHeader($event, 'header_' + header.column, 'po_delivery_date')"
                        class="text-center col-resize d-flex justify-content-between">
                        <label class="mb-0 col-resize">
                            {{ header.label }}
                        </label>
                        <TagOrderSufficeHeader :column="header.column" :orders="case_filter.orders"
                            :count_reset_filter="count_reset_filter" @showHideDropdown="getShowHideDopdown"
                            @fieldColumnHeader="fieldColumnHeader" @emitResetFilter="getResetFilter"
                            @emitFilter="emitFilter" @filterItems="filterItems">
                        </TagOrderSufficeHeader>
                    </div>
                </template>
                <template #cell(sku_sap_code)="data">
                    <div class="overflow-hidden sku_sap_code">
                        <input v-if="isHandleDbClick()" class="px-2" v-model="data.item.sku_sap_code"
                        @keydown="copyItem($event, data.item.sku_sap_code)" @dblclick="handleDoubleClick($event)"
                        @input="handleItem(data.item.sku_sap_code, 'sku_sap_code', data.index)" />
                    <div class="sku_sap_code" v-else :style="'width: 100%;height: 1.5rem;'" tabindex="0"
                        :ref="'keyListenerDiv_' + data.item.sku_sap_code + data.item.order + data.field.key"
                        @click.ctrl.exact="changeCtrl($event, data.item.customer_sku_code)"
                        @keydown="copyItem($event, data.item.sku_sap_code, data.field.key)"
                        @dblclick="handleDoubleClick($event)"
                        @mousedown="startSelection($event, data.item.sku_sap_code, data.item.order, data.field.key)"
                        @mousemove="selectItem(data.item.sku_sap_code, $event)"
                        @mouseup="endSelection(data.item.sku_sap_code, $event)"
                        :class="{ 'change-border': isChangeBorder(data.item.sku_sap_code) && isSameField(case_order.field_order, data.field.key) }">
                        <span class="text-center rounded" :class="{
            'badge badge-warning': data.item.extra_offer == 'X',
            'badge badge-primary': data.item.promotion_category == 'X'
        }">
                            {{ data.item.sku_sap_code }}
                        </span>
                    </div>
                    </div>
                   
                </template>
                <template #cell(sku_sap_name)="data">
                    <!-- {{ data.item.sku_sap_name }} -->
                    <div class="sku_sap_name overflow-hidden">
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.sku_sap_name"
                            @input="handleItem(data.item.sku_sap_name, 'sku_sap_name', data.index)" />
                        <span v-else>{{ data.item.sku_sap_name }}</span>
                    </div>

                </template>
                <template #cell(customer_sku_name)="data">
                    <div class="customer_sku_name overflow-hidden">
                        <input v-if="case_is_status.edit" class="px-2" v-model="data.item.customer_sku_name"
                            @input="handleItem(data.item.customer_sku_name, 'customer_sku_name', data.index)" />
                        <span v-else>{{ data.item.customer_sku_name }}</span>
                    </div>

                </template>
                <template #cell(customer_sku_unit)="data">
                    <div class="customer_sku_unit overflow-hidden">
                        <input class="px-2" v-model="data.item.customer_sku_unit" v-if="case_is_status.edit"
                            @input="handleItem(data.item.customer_sku_unit, 'customer_sku_unit', data.index)" />
                        <span v-else>{{ data.item.customer_sku_unit }}</span>
                    </div>

                </template>
                <template #cell(po)="data">
                    <div class="po overflow-hidden">
                        <input class="px-2" v-if="case_is_status.edit" v-model="data.item.po"
                            @input="handleItem(data.item.po, 'po', data.index)" />
                        <span v-else>{{ data.item.po }}</span>
                    </div>
                </template>
                <template #cell(sku_sap_unit)="data">
                    <div class="sku_sap_unit overflow-hidden">
                        <input class="px-2" v-model="data.item.sku_sap_unit" v-if="case_is_status.edit"
                            @input="handleItem(data.item.sku_sap_unit, 'sku_sap_unit', data.index)" />
                        <span v-else>{{ data.item.sku_sap_unit }}</span>
                    </div>
                </template>
                <template #cell(customer_code)="data">
                    <div class="customer_code overflow-hidden">
                        <input class="px-2" v-model="data.item.customer_code" v-if="case_is_status.edit"
                            @input="handleItem(data.item.customer_code, 'customer_code', data.index)" />
                        <span v-else>{{ data.item.customer_code }}</span>
                    </div>
                </template>
                <template #cell(promotive_name)="data">
                    <div class="promotive_name overflow-hidden">
                        {{ data.item.promotive }}
                    </div>
                </template>
                <template #cell(note1)="data">
                    <!-- {{ data.item.note1 }}{{ data.item.promotive }} -->
                    <div class="note1 overflow-hidden">
                        <div v-if="case_is_status.edit">
                            <input class="px-2" v-model="data.item.note1"
                                @input="handleItem(data.item.note1, 'note1', data.index)" />
                            {{ data.item.promotive }}
                        </div>
                        <span v-else>{{ data.item.note1 }}{{ data.item.promotive }}</span>
                    </div>
                </template>
                <template #cell(note)="data">
                    <div class="note overflow-hidden">
                        <div v-if="case_is_status.edit">
                            <input class="px-2" v-model="data.item.note" v-if="case_is_status.edit"
                                @input="handleItem(data.item.note, 'note', data.index)" />
                            {{ data.item.promotive }}
                        </div>
                        <span v-else>{{ data.item.note }}</span>
                    </div>
                </template>
                <template #cell(level2)="data">
                    <div class="level2 overflow-hidden">
                        <div v-if="case_is_status.edit">
                            <input class="px-2" v-model="data.item.level2"
                                @input="handleItem(data.item.level2, 'level2', data.index)" />
                        </div>
                        <span v-else>{{ data.item.level2 }}</span>
                    </div>
                </template>
                <template #cell(level3)="data">
                    <div class="level3 overflow-hidden">
                        <div v-if="case_is_status.edit">
                            <input class="px-2" v-model="data.item.level3"
                                @input="handleItem(data.item.level3, 'level3', data.index)" />
                        </div>
                        <span v-else>{{ data.item.level3 }}</span>
                    </div>


                </template>
                <template #cell(level4)="data">
                    <div class="level4 overflow-hidden">
                        <input class="px-2" v-model="data.item.level4" v-if="case_is_status.edit"
                            @input="handleItem(data.item.level4, 'level4', data.index)" />
                        <span v-else>{{ data.item.level4 }}</span>
                    </div>

                </template>
                <template #cell(promotive)="data">
                    <div class="overflow-hidden promotive">
                        <div tabindex="0" :ref="'keyListenerDiv_' + data.item.promotive + data.item.order + data.field.key"
                        @keydown="copyItem($event, data.item.promotive, data.field.key, data.item.order)"
                        @mousedown="startSelection($event, data.item.promotive, data.item.order, data.field.key)"
                        @mousemove="selectItem(data.item.promotive, $event, data.item.order)"
                        @mouseup="endSelection(data.item.customer_sku_code, $event)"
                        :class="{ 'change-border': isChangeBorder(data.item.promotive) && isSameField(case_order.field_order, data.field.key) }">
                        <div class="d-flex justify-content-end py-2 ">
                            <small v-if="data.item.promotive !== ''" class="font-weight-bold mr-2 p-0">{{
            data.item.promotive }}</small>
                            <i @click="onChangeShowModal(data.index, data.item)" class="far fa-caret-square-down"></i>
                        </div>
                    </div>
                    </div>
                   
                </template>
                <template #cell(amount_po)="data">
                    <div class="amount_po overflow-hidden">
                        <input class="px-2" v-model="data.item.amount_po" v-if="case_is_status.edit"
                            @input="handleItem(data.item.amount_po, 'amount_po', data.index)" />
                        <span v-else><strong>{{ data.value.toLocaleString(locale_format) }}
                            </strong></span>
                    </div>

                </template>
                <template #cell(price_po)="data">
                    <div class="price_po overflow-hidden">
                        <input class="px-2" v-model="data.item.price_po" v-if="case_is_status.edit"
                            @input="handleItem(data.item.price_po, 'price_po', data.index)" />
                        <span v-else> <strong :class="{
            'text-danger': data.item.company_price != data.item.price_po
        }">{{ data.value.toLocaleString(locale_format) }}
                            </strong></span>
                    </div>

                </template>
                <template #cell(company_price)="data">
                    <div class="company_price overflow-hidden">
                        <input class="px-2" v-model="data.item.company_price" v-if="case_is_status.edit"
                            @input="handleItem(data.item.company_price, 'company_price', data.index)" />
                        <span v-else>
                            <strong :class="{
            'text-danger': data.item.company_price != data.item.price_po
        }">{{ data.value.toLocaleString(locale_format) }}
                            </strong>
                        </span>
                    </div>

                </template>
                <template #cell(customer_sku_code)="data">
                    <div class="customer_sku_code overflow-hidden">
                        <input v-if="isHandleDbClick()" class="px-2" v-model="data.item.customer_sku_code"
                            @keydown="copyItem($event, data.item.customer_sku_code)"
                            @dblclick="handleDoubleClick($event)"
                            @input="handleItem(data.item.customer_sku_code, 'customer_sku_code', data.item.order)" />
                        <div v-else :style="'width: 100%;height: 1.5rem;'" tabindex="0"
                            :ref="'keyListenerDiv_' + data.item.customer_sku_code + data.item.order + data.field.key"
                            @click.ctrl.exact="changeCtrl($event, data.item.customer_sku_code)"
                            @dblclick="handleDoubleClick($event)"
                            @mousedown="startSelection($event, data.item.customer_sku_code, data.item.order, data.field.key)"
                            @keydown="copyItem($event, data.item.customer_sku_code, data.field.key)"
                            @mousemove="selectItem(data.item.customer_sku_code, $event)"
                            @mouseup="endSelection(data.item.customer_sku_code, $event)"
                            :class="{ 'change-border': isChangeBorder(data.item.customer_sku_code) && isSameField(case_order.field_order, data.field.key) }">
                            {{ data.item.customer_sku_code }}
                        </div>
                    </div>

                </template>
                <template #cell(is_compliant)="data">
                    <div class="is_compliant overflow-hidden">
                        <div v-if="data.item.is_compliant == false && data.item.is_compliant !== null">
                            <span class="text-danger"><i class="fas fa-times"></i></span>
                        </div>
                        <div v-if="data.item.is_compliant == true && data.item.is_compliant !== null">
                            <span class="text-success"><i class="fas fa-check"></i></span>
                        </div>
                    </div>
                </template>
                <template #cell(po_number)="data">
                    <div class="po_number overflow-hidden">
                        <input class="px-2" v-model="data.item.po_number" v-if="case_is_status.edit"
                            @input="handleItem(data.item.po_number, 'po_number', data.index)" />
                        <span v-else>{{ data.item.po_number }}</span>
                    </div>
                </template>
                <template #cell(po_delivery_date)="data">
                    <div class="po_delivery_date overflow-hidden">
                        <input class="px-2" v-model="data.item.po_delivery_date" v-if="case_is_status.edit"
                            @input="handleItem(data.item.po_delivery_date, 'po_delivery_date', data.index)" />
                        <span v-else>{{ data.item.po_delivery_date }}</span>
                    </div>
                </template>
            </b-table>
            <textarea ref="clipboard" style="position: absolute; left: -9999px"></textarea>
            <DialogMaterialCategoryTypes ref="dialogMaterialCategoryTypes"
                :material_category_types="material_category_types" @onChangeCategoryType="getOnChangeCategoryType"
                @storeMaterialCategoryType="getStoreMaterialCategoryType"
                @updateMaterialCategoryType="getUpdateMaterialCategoryType"
                @deleteMaterialCategoryType="getDeleteMaterialCategoryType"
                :is_modal_material_category_type="is_modal_material_category_type">
            </DialogMaterialCategoryTypes>
        </div>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import DialogMaterialCategoryTypes from '../../master/dialogs/DialogMaterialCategoryTypes.vue';
import TagOrderSufficeAction from './Tags/TagOrderSufficeAction.vue';
import TagOrderSufficeHeader from './Tags/TagOrderSufficeHeader.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order'
        },
        orders: {
            type: Array,
            default: []
        },
        material_donateds: {
            type: Array,
            default: []
        },
        material_combos: {
            type: Array,
            default: []
        },
        current_page: {
            type: Number,
            default: 1
        },
        per_page: {
            type: Number,
            default: 10
        },
        is_loading_detect_sap_code: {
            type: Boolean,
            default: false
        },
        order_lacks: {
            type: Array,
            default: () => []
        },
        filterOrders: {
            type: Array
        },
        count_reset_filter: {
            type: Number,
            default: 0
        },

    },
    components: {
        DialogMaterialCategoryTypes,
        TagOrderSufficeAction,
        TagOrderSufficeHeader
    },
    data() {
        return {
            locale_format: "de-DE",
            is_modal_material_category_type: false,
            case_is_status: {
                event: false,
                sort: false,
                edit: false,
                copy: false,
                click: false,
                show_dropdown: false,
                reeset_filter_header: false,
            },
            case_index: {
                event: -1,
                copys: [],
                change: -1,
                orders: [],
                order: -1,
            },
            case_filter: {
                orders: [],
                search: '',

            },
            case_order: {
                customer_name: '',
                db_click: false,
                parses: [],
                field_order: '',
            },
            field_order_suffices: [
                {
                    key: 'selected',
                    label: '',
                    class: 'text-nowrap   ',
                    tdClass: 'checkbox-sticky-left text-center',
                    thClass: 'checkbox-sticky-left text-center',
                },
                {
                    key: 'action',
                    label: '',
                    class: 'text-nowrap ',
                    tdClass: 'checkbox-sticky-center text-center',
                    thClass: 'checkbox-sticky-center text-center',
                },
                {
                    key: 'index',
                    label: 'Vị trí',
                    class: 'text-nowrap text-center  ',
                    sortable: false,
                    tdClass: 'checkbox-sticky-end text-center border',
                    thClass: 'checkbox-sticky-header-end text-center',
                },
                {
                    key: 'customer_name',
                    label: 'Makh Key',
                    class: 'text-nowrap  ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'sap_so_number',
                    label: 'Mã Sap So',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'
                },
                {
                    key: 'barcode',
                    label: 'Barcode_cty',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'sku_sap_code',
                    label: 'Masap',
                    class: 'text-nowrap text-center  ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'sku_sap_name',
                    label: 'Tensp',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'


                },
                {
                    key: 'sku_sap_unit',
                    label: 'Dvt',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },

                {
                    key: 'promotive',
                    label: 'Km',
                    class: 'text-nowrap   ',
                    tdClass: 'voucher-custom border p-0 ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'note',
                    label: 'Ghi_chu',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'customer_code',
                    label: 'Makh',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'customer_sku_code',
                    label: 'Unit_barcode',
                    class: 'text-nowrap text-center  ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'customer_sku_name',
                    label: 'Unit_barcode_description',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'customer_sku_unit',
                    label: 'Dvt_po',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'po',
                    label: 'Po',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'quantity1_po',
                    label: 'Qty',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'promotive_name',
                    label: 'Combo',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'


                },
                {
                    key: 'inventory_quantity',
                    label: 'Check tồn',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'quantity2_po',
                    label: 'Po_qty',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'price_po',
                    label: 'Pur_price',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border'

                },

                {
                    key: 'amount_po',
                    label: 'Amount',
                    class: "text-nowrap  ",
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'compliance',
                    label: 'QC',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'
                },
                {
                    key: 'is_compliant',
                    label: 'Đúng_QC',
                    sortable: false,
                    thClass: 'border',
                    class: 'text-center   text-nowrap'
                },
                {
                    key: 'note1',
                    label: 'Ghi chú 1',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'company_price',
                    label: 'Gia_cty',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'level2',
                    label: 'Level_2',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'level3',
                    label: 'Level_3',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'level4',
                    label: 'Level_4',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'po_number',
                    label: 'po_number',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
                {
                    key: 'po_delivery_date',
                    label: 'po_delivery_date',
                    class: 'text-nowrap   ',
                    sortable: false,
                    thClass: 'border'

                },
            ],
            case_checkbox: {
                selected: [],
                selected_all: false,
                items: [],
            },
            select_mode: 'multi',
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            modal_index: 0,

            material_category_types: [],

            api_material_category_types: '/api/master/material-category',
            items: ['Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'],
            isSelecting: false,
            selectedItems: [],
            mouse_hould_timeout: null,
            is_mouse_down: false,
            width: 100,
            previous_mouse_position: 0,
        }
    },
    created() {
        this.fetchMaterialCategoryType();
    },
    methods: {
        async fetchMaterialCategoryType() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.api_material_category_types);
                if (data.success) {
                    this.material_category_types = data.items;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        isBarcodeCompanyOrther(sku_sap_name, customer_sku_name) {
            sku_sap_name = sku_sap_name.toLowerCase()
            customer_sku_name = customer_sku_name.toLowerCase()
            if (sku_sap_name != customer_sku_name) {
                return true
            }
        },
        onChangeShowModal(index, item) {
            this.$refs.dialogMaterialCategoryTypes.showModalCategoryType(index, item)
        },
        getOnChangeCategoryType(index, item, order) {
            this.$emit('onChangeCategoryType', index, item, order);
        },
        getStoreMaterialCategoryType(data) {
            this.material_category_types.unshift({ ...data })
        },
        getUpdateMaterialCategoryType(index, data) {
            this.material_category_types.splice(index, 1, { ...data })
        },
        getDeleteMaterialCategoryType(index) {
            this.material_category_types.splice(index, 1)
        },
        isMaterialDonated(sku_sap_code) {
            for (let index = 0; index < this.material_donateds.length; index++) {
                if (sku_sap_code == this.material_donateds[index].sap_code) {
                    return true;
                }
            }
            return false;
        },
        isMaterialCombo(sku_sap_code) {
            for (let index = 0; index < this.material_combos.length; index++) {
                if (sku_sap_code == this.material_combos[index].sap_code) {
                    return true;
                }
            }
            return false;
        },
        deleteRow(index, item) {
            this.$emit('deleteRow', index, item)
        },
        checkBoxAll(index) {
            if (this.case_checkbox.selected_all) {
                this.case_checkbox.selected = this.orders;
            } else {
                this.case_checkbox.selected = [];
            }
            this.$emit('checkBoxRow', this.case_checkbox.selected, 0)

        },
        emitCheckBox(index) {
            if (this.case_checkbox.selected_all) {
                this.case_checkbox.selected = this.orders;
            }
            this.$emit('checkBoxRow', this.case_checkbox.selected, index)

        },
        refeshCaseCheckBox() {
            this.case_checkbox.selected = [];
        },
        rowColor(item, index) {
            for (let index = 0; index < this.order_lacks.length; index++) {
                if (this.order_lacks[index].customer_sku_code == item.customer_sku_code && this.order_lacks[index].sku_sap_unit == item.sku_sap_unit) {
                    return true;
                }
            }
        },
        isCheckLack(item) {
            let result = this.convertToNumber(item.quantity1_po) * this.convertToNumber(item.quantity2_po);
            if (result > this.convertToNumber(item.inventory_quantity) && this.convertToNumber(item.inventory_quantity) > 0) {
                // item.is_inventory = true;
                return true;
            }
            // return item.is_inventory;
            return false;
        },
        convertToNumber(value) {
            return Number(value);
        },
        sortingChanged() {
            this.case_is_status.sort = true;
            let sort = this.$refs.btable.sortedItems;
            this.$emit('sortingChanged', sort)
        },
        getPushHeader(item, header) {
            this.case_order.field_order = header;
        },
        startSelection(e, item, index, header) {
            this.case_order.db_click = false;
            if (e !== undefined) {
                e.preventDefault();
                this.case_index.change = index;
                this.isSelecting = true;
            }
            this.refeshItem();
            this.selectedItems.push(item);
            this.case_index.copys.push(item);
            this.case_index.orders.push(index);
            this.getPushHeader(item, header);
            this.setFocusToKeyListener(item, index, header);
        },
        selectItem(item, event, index) {
            if (event !== undefined) {
                event.preventDefault();
            }
            if (this.isSelecting) {
                let exits = false;
                let exit_indexs = false;
                this.case_index.copys.forEach((item_copy, index) => {
                    if (item_copy == item) {
                        exits = true;
                    }
                });
                if (!exits) {
                    this.case_index.copys.push(item);
                    this.selectedItems.push(item);
                };
                this.case_index.orders.forEach((index_order) => {
                    if (index_order == index) {
                        exit_indexs = true;
                    }
                });
                if (!exit_indexs) {
                    this.case_index.orders.push(index);
                };
            }
        },
        selectItemEventKey(item, event) {
            if (event !== undefined) {
                event.preventDefault();
            }
            let exits = false;
            this.case_index.copys.forEach((element, index) => {
                if (element == item) {
                    exits = true;
                }
            });
            if (!exits) {
                this.case_index.copys.push(item);
                this.selectedItems.push(item);
            }

        },
        copyToClipboard(text) {
            this.$refs.clipboard.value = text;
            this.$refs.clipboard.select();
            document.execCommand('copy');
        },
        endSelection(item, event) {
            if (event !== undefined) {
                event.preventDefault();
            }
            this.isSelecting = false;

        },
        copyItem(event, item, field, index) {
            console.log(event.keyCode, event.ctrlKey, event.shiftKey, event.altKey, event.metaKey);
            switch (event.keyCode) {
                case 67: // ctrl + c
                    if (event.ctrlKey && !this.case_is_status.edit) {
                        let new_items = this.selectedItems.map((item) => {
                            return {
                                index_order: index,
                                promotive: item,
                            }
                        });
                        this.case_order.parses = [];
                        this.case_order.parses = new_items;
                        this.copyToClipboard(this.selectedItems.join('\n'));
                        this.$showMessage('success', 'Copy thành công');

                    }
                    break;
                case 86: // ctrl + v
                    if (event.ctrlKey) {
                        this.pasteItem(this.case_order.parses, this.case_index.orders, field, event);
                    }
                    break;
                case 16: // ctrl + shift
                    // this.isSelecting = true;
                    this.selectItemEventKey(item, event);
                    break;
                case 27: // esc
                    this.isSelecting = false;
                    this.case_order.db_click = false;
                    this.case_is_status.edit = false;
                    this.$emit('isHandleDbClick', this.case_order.db_click);
                    this.refeshItem();
                    break;
                case 40: // down
                    // if(event.ctrlKey, event.shiftKey)
                    if (event.ctrlKey && event.shiftKey) {
                        this.orders.forEach((order, index) => {
                            if (this.case_index.change < index) {
                                this.selectItemEventKey(order[field], event);
                            }
                            // this.selectItemEventKey(order.customer_sku_code, event);
                        });
                        // this.isChangeBorder(item);
                        // this.setFocusToKeyListener(item);
                        // this.selectItemEventKey(item, event);
                    }
                    break;
            }
        },
        pasteItem(items, index, field, event) {
            this.$emit('pasteItem', items, index, field, event);
            this.$showMessage('success', 'Paste thành công');
        },
        fieldColumnHeader(column, e) {
            this.refeshItem();
            this.orders.forEach((order, index) => {
                if (order[column] != null && order[column] != '') {
                    this.selectItemEventKey(order[column], e);
                }
            });
            this.copyAll(this.selectedItems.join('\n'));

        },
        copyAll(items) {
            this.copyToClipboard(items);
            this.$showMessage('success', 'Copy thành công');
        },
        isChangeBorder(item) {
            let exits = false;
            this.case_index.copys.forEach((item_copy, index) => {
                if (item_copy == item && item !== null) {
                    exits = true;
                }

            });
            return exits;
        },
        isSameField(item, field) {
            return item == field;
        },
        renderRefDefault(default_value, name) {
            let ref = default_value + name;
            return ref;
        },
        setFocusToKeyListener(customer_sku_code, index, header) {
            const ref = 'keyListenerDiv_' + customer_sku_code + index + header;
            this.$refs[ref].focus();
        },
        refeshItem() {
            this.case_index.copys = [];
            this.selectedItems = [];
            this.case_index.orders = [];

        },
        changeCtrl(event, item) {
            this.selectItemEventKey(item, event);
        },
        handleItem(item, field, index) {
            let orders = this.$refs.btable.sortedItems;
            this.$emit('handleItem', item, field, index, orders);
        },
        isHandleDbClick() {
            return this.case_order.db_click;
        },
        handleDoubleClick(e) {
            if (e.type == 'dblclick') {
                this.isSelecting = false;
                this.case_order.db_click = true;
                this.case_is_status.edit = true;
                this.$emit('isHandleDbClick', this.case_order.db_click);

            }
        },
        editRow(status) {
            this.case_order.db_click = status;
            this.isSelecting = false;
            this.case_is_status.edit = status;
        },
        btnDuplicateRow(index, item) {
            this.$emit('btnDuplicateRow', index, item);
        },
        btnCopyDeleteRow(index, item) {
            this.case_is_status.copy = true;
            this.case_index.order = item.order;
            this.$emit('btnCopyDeleteRow', index, item);
        },
        btnParseCreateRow(index) {
            this.refeshCaseCopy();
            this.$emit('btnParseCreateRow', index);
        },
        btnCopy(index, item) {
            this.case_is_status.copy = true;
            this.case_index.order = item.order;
            this.$emit('btnCopy', index, item);
        },
        hightLightCopy(item) {
            if (item.order == this.case_index.order) {
                return 'font-italic text-secondary highlight-copy';
            }
        },
        refeshCaseCopy() {
            this.case_is_status.copy = false;
            this.case_index.order = -1;
        },
        getUnique(field) {
            let unique = [...new Set(this.orders.map(item => item[field]))];
            return unique;
        },
        filterItems(field) {
            this.case_filter.orders = this.getUnique(field);
        },
        getShowHideDopdown(boolean) {
            this.case_is_status.show_dropdown = boolean;
        },
        emitFilter(items, field, boolean) {
            this.$emit('filterItems', items, field, boolean)
        },
        handleMouseDownHeader(e, ref_header, class_cell) {
            if (!this.case_is_status.show_dropdown) {
                e.preventDefault();
                const style = window.getComputedStyle(this.$refs[ref_header]);
                const width = parseFloat(style.getPropertyValue('width'));
                this.width = width;
                this.is_mouse_down = true;
                this.mouse_hould_timeout = setTimeout(() => {
                }, 10);
            }

        },
        handleMouseUpHeader() {
            this.is_mouse_down = false;
            clearTimeout(this.mouse_hould_timeout);
        },
        handleMouseLeaveHeader() {
            this.is_mouse_down = false;
            clearTimeout(this.mouse_hould_timeout);
        },
        handleMouseMoveHeader(e, ref_header, class_cell) {
            if (this.is_mouse_down) {
                // lấy class customer_name
                let get_class = document.getElementsByClassName(class_cell);
                if (e.clientX > this.previous_mouse_position) {
                    this.width = this.width++ + 5;
                } else if (e.clientX < this.previous_mouse_position) {
                    this.width = this.width-- - 5;
                }
                this.$refs[ref_header].style.width = this.width + 'px';
                for (let index = 0; index < get_class.length; index++) {
                    get_class[index].style.width = this.width + 'px';
                }
                this.previous_mouse_position = e.clientX;
            }
        },
        getResetFilter() {
            this.$emit('emitResetFilter');
        }

    },
    computed: {

    }
}
</script>
<style lang="scss" scoped>
::v-deep tr.b-table-row-selected.table-active {
    background-color: rgb(255 243 0 / 23%) !important;
    color: deeppink !important;
}

.barcode-orther {
    background: yellow;
}

::v-deep .voucher-custom {
    cursor: pointer !important;

    &:hover {
        border: 2px solid rgb(49, 49, 255) !important;
        transition: 0.1s all !important;
    }
}

.is-donated {
    background: yellow;
    padding: 3px;
    color: white;
}

.is-combo {
    background: blue;
    color: white;
    padding: 3px;
}

.table-order-suffices {
    cursor: crosshair;
    height: 25rem !important;
}

.change-border {
    border: 1px solid #00fc11;
}

// background: rgb(227 227 227 / 50%);

::v-deep .highlight-copy {
    background: rgb(178 178 178 / 21%) !important;
}

::v-deep .checkbox-sticky-left {
    position: sticky !important;
    left: 0;
    z-index: 3 !important;
    background: white;
    border-right: 1px solid #e9ecef;
}

::v-deep .checkbox-sticky-center {
    position: sticky !important;
    left: 32px;
    z-index: 3 !important;
    background: white;
    border-right: 1px solid #e9ecef;
}

::v-deep .checkbox-sticky-end {
    position: sticky !important;
    left: 65px;
    // z-index: 3 !important;
    background: white;
    border-right: 1px solid #e9ecef;
}

::v-deep .checkbox-sticky-header-end {
    position: sticky !important;
    left: 65px;
    z-index: 3 !important;
    background: white;
    border-right: 1px solid #e9ecef;
}

.col-resize {
    cursor: col-resize;
}
</style>