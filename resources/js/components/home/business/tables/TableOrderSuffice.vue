<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group">
            <!-- sticky-header="500px" -->
            <b-table small responsive hover sticky-header="500px" head-variant="light" :items="orders"
                :fields="field_order_suffices" table-class="table-order-suffices" :current-page="current_page"
                :per-page="per_page">
                <!-- <template #cell(row_custom)="data">
                    <b-dropdown size="sm" id="dropdown-offset" offset="25" text="" variant="link"
                        toggle-class="text-decoration-none" no-caret class="">
                        <template #button-content>
                            <button class="btn btn-xs btn-light dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false"><i class="fas fa-th-list"></i></button>
                        </template>
<b-dropdown-item @click="deleteRow(data.index, data.item)" class="text-danger" href="#">Xóa
    dòng</b-dropdown-item>
</b-dropdown>
</template> -->
                <template #cell(index)="data">
                    <div class="font-weight-bold">
                        {{ (data.index + 1) + (current_page * per_page) - per_page }}
                    </div>
                </template>
                <template #head(selected)="data">
                    <b-form-checkbox v-model="case_checkbox.selected_all" @change="emitCheckBox(data.index)"
                    ></b-form-checkbox>
                </template>
                <template #cell(selected)="data">
                    <b-form-checkbox v-model="case_checkbox.selected" @change="emitCheckBox(data.index)"
                        :value="data.item"></b-form-checkbox>
                </template>
                <template #cell(barcode)="data">
                    <div class="">
                        {{ data.item.barcode }}
                    </div>
                </template>
                <template #cell(customer_name)="data">
                    <div v-if="isCheckLack(data.item) ">
                        {{ data.item.customer_name }}{{ data.item.promotive }} <br>
                        <small class="text-danger">Hàng thiếu</small>
                        <small v-if="rowColor(data.item) " class="text-danger ml-2 font-weight-bold">
                            <i class="fas fa-circle fa-xs mr-1" style="font-size: 6px;"></i>Đã lưu hàng thiếu
                        </small>
                    </div>
                    <div v-else>
                        {{ data.item.customer_name }}{{ data.item.promotive }}<br>
                        <small v-if="rowColor(data.item) || data.item.is_inventory == true" class="text-danger font-weight-bold">
                            <i class="fas fa-circle fa-xs mr-1" style="font-size: 6px;"></i>Đã lưu hàng thiếu
                        </small>
                    </div>
                </template>
                <template #cell(quantity1_po)="data">
                    <div :class="{
            'text-danger': isCheckLack(data.item)
        }">
                        {{ data.item.quantity1_po }}
                    </div>
                </template>
                <template #cell(quantity2_po)="data">
                    <div :class="{
            'text-danger': isCheckLack(data.item)
        }">
                         <span
                      ><strong
                        >{{ data.value.toLocaleString(locale_format) }}
                      </strong></span
                    >
                    </div>
                </template>
                <template #cell(inventory_quantity)="data">
                    <div :class="{
            'text-danger': isCheckLack(data.item)
        }">
                        {{ data.item.inventory_quantity }}
                    </div>
                </template>
                <template #head(barcode)="header">
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
                <template #head(sku_sap_code)="header">
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
                <template #cell(sku_sap_code)="data">
                    <span class="text-center rounded" :class="{
            'badge badge-warning': data.item.promotion_category == 'ExtraOffer',
            'badge badge-primary': data.item.promotion_category == 'Combo'
        }">
                        {{ data.item.sku_sap_code }}
                    </span>
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
                    <div @click="onChangeShowModal(data.index)" class="">
                        <div class="d-flex justify-content-end">
                            <small v-if="data.item.promotive !== ''" class="font-weight-bold mr-2 p-0">{{ data.item.promotive }}</small>
                            <i class="far fa-caret-square-down"></i>
                        </div>
                    </div>
                </template>
                <template #cell(amount_po)="data">
                    <span
                      ><strong
                        >{{ data.value.toLocaleString(locale_format) }}
                      </strong></span
                    >
                  </template>
                  <template #cell(price_po)="data">
                    <span
                      ><strong
                        >{{ data.value.toLocaleString(locale_format) }}
                      </strong></span
                    >
                  </template>
                 
            </b-table>
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
                    class: 'text-nowrap',
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
        onChangeShowModal(index) {
            this.$refs.dialogMaterialCategoryTypes.showModalCategoryType(index)
        },
        getOnChangeCategoryType(index, item) {
            this.$emit('onChangeCategoryType', index, item);
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
        emitCheckBox(index) {
            if(this.case_checkbox.selected_all){
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
                item.is_inventory = true;
                return true;
            }
            return item.is_inventory;
        },
        convertToNumber(value) {
            return Number(value);
        },




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
</style>