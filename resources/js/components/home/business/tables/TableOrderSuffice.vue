<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group">
            <!-- sticky-header="500px" -->
            <b-table small responsive hover sticky-header="500px" head-variant="light" :items="orders"
                :class="{ 'table-order-suffices': true, }" @sort-changed="sortingChanged" :fields="field_order_suffices"
                table-class="table-order-suffices" :current-page="current_page" :per-page="per_page">
                <template #cell(index)="data">
                    <div class="font-weight-bold">
                        {{ (data.index + 1) + (current_page * per_page) - per_page }}
                    </div>
                </template>
                <template #head(selected)="data">
                    <b-form-checkbox v-model="case_checkbox.selected_all"
                        @change="checkBoxAll(data.index)"></b-form-checkbox>
                </template>
                <template #cell(selected)="data">
                    <b-form-checkbox v-model="case_checkbox.selected" @change="emitCheckBox(data.index)"
                        :value="data.item"></b-form-checkbox>
                </template>
                <template #cell(barcode)="data">
                    <div tabindex="0" :ref="'keyListenerDiv_' + data.item.barcode"
                        @keydown="copyItem($event, data.item.barcode)"
                        @mousedown="startSelection($event, data.item.barcode, data.index)"
                        @mousemove="selectItem(data.item.barcode, $event)"
                        @mouseup="endSelection(data.item.barcode, $event)"
                        :class="{ 'change-border': isChangeBorder(data.item.barcode) }">
                        <span class="text-center rounded" :class="{
            // 'badge badge-warning': data.item.promotion_category == 'ExtraOffer',
            // 'badge badge-primary': data.item.promotion_category == 'Combo'
                      'badge badge-warning': data.item.extra_offer == 'X',
                      'badge badge-primary': data.item.promotion_category == 'X'
        }">
                            {{ data.item.barcode }}
                        </span>
                    </div>


                </template>
                <template #cell(customer_name)="data">
                    <div v-if="isCheckLack(data.item)">
                        {{ data.item.customer_name }}{{ data.item.promotive }} <br>
                        <small class="text-danger">Hàng thiếu</small>
                        <!-- <small v-if="rowColor(data.item) " class="text-danger ml-2 font-weight-bold">
                            <i class="fas fa-circle fa-xs mr-1" style="font-size: 6px;"></i>Đã lưu hàng thiếu
                        </small> -->
                    </div>
                    <div v-else>
                        {{ data.item.customer_name }}{{ data.item.promotive }}<br>
                        <!-- <small v-if="rowColor(data.item) || data.item.is_inventory == true" class="text-danger font-weight-bold">
                            <i class="fas fa-circle fa-xs mr-1" style="font-size: 6px;"></i>Đã lưu hàng thiếu
                        </small> -->
                    </div>
                </template>
                <template #cell(quantity1_po)="data">
                    <div :class="{
            'text-danger': isCheckLack(data.item)
        }">
                            <span><strong>{{ data.value.toLocaleString(locale_format) }}
                            </strong></span>
                         
                    </div>
                </template>
                <template #cell(quantity2_po)="data">
                    <div :class="{
            'text-danger': isCheckLack(data.item)
        }">
                        <span><strong>{{ data.value.toLocaleString(locale_format) }}
                            </strong></span>
                    </div>
                </template>
                <template #cell(inventory_quantity)="data">
                    <div :class="{
            'text-danger': isCheckLack(data.item)
        }">
                          <span><strong>{{ data.value.toLocaleString(locale_format) }}
                            </strong></span>
                    </div>
                </template>
                <template #head(barcode)="header">
                    <div class="text-center d-flex">
                        <label class="mb-0 " :class="{
            'text-danger': is_loading_detect_sap_code == true
        }">
                            <span v-if="is_loading_detect_sap_code == true"><i
                                    class="fas fa-spinner fa-spin fa-xs"></i></span>
                            {{ header.label }}
                        </label>
                        <div>
                            <b-dropdown id="dropdown-1" size="sm" variant="light"
                                toggle-class="text-center rounded p-0 px-1 ml-1">
                                <template #button-content class="button">
                                    <!-- <i class="fas fa-cog"></i> -->
                                    <i class="fas fa-clipboard-list"></i>
                                </template>
                                <b-dropdown-item active @click="fieldColumnHeader(header.column, $event)">Copy
                                    all</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </div>
                </template>
                <template #head(sku_sap_code)="header">
                    <div class="text-center d-flex ">
                        <label class="mb-0" :class="{
            'text-danger': is_loading_detect_sap_code == true
        }">
                            <span v-if="is_loading_detect_sap_code == true"><i
                                    class="fas fa-spinner fa-spin fa-xs"></i></span>
                            {{ header.label }}
                        </label>
                        <div>
                            <b-dropdown id="dropdown-1" size="sm" variant="light"
                                toggle-class="text-center rounded p-0 px-1 ml-1">
                                <template #button-content>
                                    <i class="fas fa-clipboard-list"></i>
                                </template>
                                <b-dropdown-item active @click="fieldColumnHeader(header.column, $event)">Copy
                                    all</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </div>
                </template>
                <template #head(sku_sap_name)="header">
                    <div class="text-center">
                        <label class="mb-0" :class="{
            'text-danger': is_loading_detect_sap_code == true
        }">
                            <span v-if="is_loading_detect_sap_code == true"><i
                                    class="fas fa-spinner fa-spin fa-xs"></i></span>
                            {{ header.label }}
                        </label>
                    </div>
                </template>
                <template #head(customer_sku_code)="header">
                    <div class="text-center d-flex  ">
                        <label class="mb-0">
                            {{ header.label }}
                        </label>
                        <div>
                            <b-dropdown id="dropdown-1" size="sm" variant="light"
                                toggle-class="text-center rounded p-0 px-1 ml-1">
                                <template #button-content>
                                    <i class="fas fa-clipboard-list"></i>
                                </template>
                                <b-dropdown-item active @click="fieldColumnHeader(header.column, $event)">Copy
                                    all</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </div>
                </template>
                <template #cell(sku_sap_code)="data">
                    <div tabindex="0" :ref="'keyListenerDiv_' + data.item.sku_sap_code"
                        @keydown="copyItem($event, data.item.sku_sap_code)"
                        @mousedown="startSelection($event, data.item.sku_sap_code, data.index)"
                        @mousemove="selectItem(data.item.sku_sap_code, $event)"
                        @mouseup="endSelection(data.item.sku_sap_code, $event)"
                        :class="{ 'change-border': isChangeBorder(data.item.sku_sap_code) }">
                        <span class="text-center rounded" :class="{
            'badge badge-warning': data.item.extra_offer == 'X',
            'badge badge-primary': data.item.promotion_category == 'X'
        }">
                            {{ data.item.sku_sap_code }}
                        </span>
                    </div>
                </template>
                <template #cell(sku_sap_name)="data">
                    {{ data.item.sku_sap_name }}
                </template>
                <template #cell(promotive_name)="data">
                    {{ data.item.promotive }}
                </template>
                <template #cell(note1)="data">
                    {{ data.item.note1 }}{{ data.item.promotive }}
                </template>
                <template #cell(note)="data">
                    {{ data.item.note }}{{ data.item.promotive }}
                </template>
                <template #cell(promotive)="data">
                    <div @click="onChangeShowModal(data.index, data.item)" class="">
                        <div class="d-flex justify-content-end">
                            <small v-if="data.item.promotive !== ''" class="font-weight-bold mr-2 p-0">{{
            data.item.promotive }}</small>
                            <i class="far fa-caret-square-down"></i>
                        </div>
                    </div>
                </template>
                <template #cell(amount_po)="data">
                    <span><strong>{{ data.value.toLocaleString(locale_format) }}
                        </strong></span>
                </template>

                <template #cell(variant_quantity)="data">
                    <span><strong>{{ data.value.toLocaleString(locale_format) }}
                        </strong></span>
                </template>
                <template #cell(price_po)="data">
                    <span><strong>{{ data.value.toLocaleString(locale_format) }}
                        </strong></span>
                </template>
                <template #cell(company_price)="data">
                    <span><strong>{{ data.value.toLocaleString(locale_format) }}
                        </strong></span>
                </template>
                <template #cell(customer_sku_code)="data">

                    <div tabindex="0" :ref="'keyListenerDiv_' + data.item.customer_sku_code"
                        @click.ctrl.exact="changeCtrl($event, data.item.customer_sku_code)"
                        @keydown="copyItem($event, data.item.customer_sku_code, data.field.key)"
                        @mousedown="startSelection($event, data.item.customer_sku_code, data.index)"
                        @mousemove="selectItem(data.item.customer_sku_code, $event)"
                        @mouseup="endSelection(data.item.customer_sku_code, $event)"
                        :class="{ 'change-border': isChangeBorder(data.item.customer_sku_code) }">
                        {{ data.item.customer_sku_code }}
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
import DialogMaterialCategoryTypes from '../../master/dialogs/DialogMaterialCategoryTypes.vue'
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

    },
    components: {
        DialogMaterialCategoryTypes
    },
    data() {
        return {
            locale_format: "de-DE",
            is_modal_material_category_type: false,
            case_is_status: {
                event: false,
            },
            case_index: {
                event: -1,
                copys: [],
                change: -1,
            },
            field_order_suffices: [
                {
                    key: 'row_custom',
                    label: '',
                    class: 'text-nowrap',
                },
                {
                    key: 'selected',
                    label: '',
                    class: 'text-nowrap',
                },
                {
                    key: 'index',
                    label: 'Stt',
                    class: 'text-nowrap text-center',
                    sortable: true,
                },
                {
                    key: 'customer_name',
                    label: 'Tenns',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'barcode',
                    label: 'Barcode_cty',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'sku_sap_code',
                    label: 'Masap',
                    class: 'text-nowrap text-center',
                    sortable: true,
                },
                {
                    key: 'sku_sap_name',
                    label: 'Tensp',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'sku_sap_unit',
                    label: 'Dvt',
                    class: 'text-nowrap',
                    sortable: true,
                },

                {
                    key: 'promotive',
                    label: 'Km',
                    class: 'text-nowrap',
                    tdClass: 'voucher-custom border p-0 ',
                    sortable: true,


                },
                {
                    key: 'note',
                    label: 'Ghi_chu',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'customer_code',
                    label: 'Makh',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'customer_sku_code',
                    label: 'Unit_barcode',
                    class: 'text-nowrap text-center',
                    sortable: true,
                },
                {
                    key: 'customer_sku_name',
                    label: 'Unit_barcode_description',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'customer_sku_unit',
                    label: 'Dvt_po',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'po',
                    label: 'Po',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'quantity1_po',
                    label: 'Qty',
                    class: "text-nowrap text-right",
                    sortable: true,
                },
                {
                    key: 'promotive_name',
                    label: 'Combo',
                    class: 'text-nowrap',
                    sortable: true,

                },
                {
                    key: 'inventory_quantity',
                    label: 'Check tồn',
                    class: "text-nowrap text-right",
                    sortable: true,
                },
                {
                    key: 'quantity2_po',
                    label: 'Po_qty',
                    class: "text-nowrap text-right",
                    sortable: true,
                },
                {
                    key: 'variant_quantity',
                    label: 'SL Chênh lệch',
                    class: "text-nowrap text-right",
                    sortable: true,
                },
                {
                    key: 'price_po',
                    label: 'Pur_price',
                    class: "text-nowrap text-right",
                    sortable: true,
                },
                {
                    key: 'amount_po',
                    label: 'Amount',
                    class: "text-right",
                    sortable: true,


                },
                {
                    key: 'note1',
                    label: 'Ghi chú 1',
                    class: 'text-nowrap',
                    sortable: true,



                },
                {
                    key: 'company_price',
                    label: 'Gia_cty',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'level2',
                    label: 'Level_2',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'level3',
                    label: 'Level_3',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'level4',
                    label: 'Level_4',
                    class: 'text-nowrap',
                    sortable: true,


                },
            ],
            case_checkbox: {
                selected: [],
                selected_all: false,
            },
            select_mode: 'multi',
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            modal_index: 0,

            material_category_types: [],

            api_material_category_types: '/api/master/material-category',
            items: ['Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'],
            isSelecting: false,
            selectedItems: []
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
            }else {
                this.case_checkbox.selected = [];
            } 
             this.$emit('checkBoxRow', this.case_checkbox.selected, 0)

        },
        emitCheckBox(index) {
           
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
        sortingChanged(sort, item) {
            this.$emit('sortingChanged', sort)
        },
        startSelection(e, item, index) {
            if (e !== undefined) {
                e.preventDefault();
                this.case_index.change = index;
            }
            this.isSelecting = true;
            this.refeshItem();
            this.selectedItems.push(item);
            this.case_index.copys.push(item);
            this.setFocusToKeyListener(item);
        },
        selectItem(item, event) {
            if (event !== undefined) {
                event.preventDefault();
            }
            if (this.isSelecting) {
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
            }
        },
        selectItemEventKey(item, event) {
            if (event !== undefined) {
                event.preventDefault();
            }
            let exits = false;
            this.case_index.copys.forEach((element, index) => {
                // if (element == item) {
                //     exits = true;
                // }
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
        copyItem(event, item, field) {
            console.log(field);
            console.log(event.keyCode, event.ctrlKey, event.shiftKey, event.altKey, event.metaKey);
            switch (event.keyCode) {
                case 67: // ctrl + c
                    if (event.ctrlKey) {
                        this.copyToClipboard(this.selectedItems.join('\n'));
                        this.$showMessage('success', 'Copy thành công');
                    }
                    break;
                case 16: // ctrl + shift
                    // this.isSelecting = true;
                    this.selectItemEventKey(item, event);
                    break;
                case 27: // esc
                    this.isSelecting = false;
                    this.refeshItem();
                    break;
                case 40: // down
                    // if(event.ctrlKey, event.shiftKey)
                    if (event.ctrlKey && event.shiftKey) {

                        this.orders.forEach((order, index) => {
                            if(this.case_index.change < index){
                                this.selectItemEventKey(order.customer_sku_code, event);
                            }
                            // this.selectItemEventKey(order.customer_sku_code, event);
                        });
                        console.log(this.selectedItems);
                        // this.isChangeBorder(item);
                        // this.setFocusToKeyListener(item);
                        // this.selectItemEventKey(item, event);
                    }
                    break;
            }
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
            this.case_index.copys.forEach((element, index) => {
                if (element == item) {
                    exits = true;
                }
            });
            return exits;
        },
        setFocusToKeyListener(customer_sku_code) {
            // Thiết lập focus vào div để lắng nghe sự kiện keyup theo index
            const ref = 'keyListenerDiv_' + customer_sku_code;
            this.$refs[ref].focus();
        },
        refeshItem() {
            this.case_index.copys = [];
            this.selectedItems = [];

        },
        changeCtrl(event, item) {
            this.selectItemEventKey(item, event);
        }
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
    // border: 2px solid lightgray !important;
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
}

.change-border {
    border: 1px solid #00fc11;
    background: rgb(227 227 227 / 50%);
}
</style>