<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group">
            <b-table small responsive hover head-variant="light" :items="orders" :fields="field_order_suffices" sticky-header="500px"
                table-class="table-order-suffices" :current-page="current_page" :per-page="per_page">
                <template #cell(row_custom)="data">
                    <b-dropdown size="sm" id="dropdown-offset" offset="25" text=""
                    variant="link" toggle-class="text-decoration-none" no-caret
                        class="">
                        <template #button-content>
                            <button class="btn btn-xs btn-light dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false"><i class="fas fa-th-list"></i></button>
                        </template>
                        <b-dropdown-item @click="deleteRow(data.index)" class="text-danger" href="#">Xóa dòng</b-dropdown-item>
                    </b-dropdown>
                </template>
                <template #cell(index)="data">
                    <div class="font-weight-bold">
                        {{ (data.index + 1)  + (current_page * per_page) - per_page }}
                    </div>
                </template>
                <template #cell(selected)="data">
                    <b-form-checkbox v-model="selected" :value="data.item" ></b-form-checkbox>
                </template>
                <template #cell(barcode)="data">
                    <div class="">
                        {{ data.item.barcode }}
                    </div>
                </template>
                <template #cell(sku_sap_code)="data">
                    <div :class="{
            'is-donated': isMaterialDonated(data.item.sku_sap_code),
            'is-combo': isMaterialCombo(data.item.sku_sap_code)
        }">
                        {{ data.item.sku_sap_code }}
                    </div>
                </template>
                <template #cell(promotive)="data">
                    <div @click="onChangeShowModal(data.index)" class="text-center">
                        <div class="d-flex align-items-baseline justify-content-around">
                            <p class="font-weight-bold mr-2">{{ data.item.promotive }}</p>
                            <button class="btn btn-sm btn-light p-1 px-2 "><i
                                    class="far fa-caret-square-down"></i></button>
                        </div>
                    </div>
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
        
    },
    components: {
        DialogMaterialCategoryTypes
    },
    data() {
        return {
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
                    key: 'so_num',
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
                    tdClass: 'voucher-custom border',
                    sortable: true,


                },
                {
                    key: 'description',
                    label: 'Ghi_chu',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'code_customer',
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
                    key: 'quantity_po',
                    label: 'Qty',
                    class: 'text-nowrap',
                    sortable: true,

                },
                {
                    key: 'combo',
                    label: 'Combo',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'check_ton',
                    label: 'Check tồn',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'po_qty',
                    label: 'Po_qty',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'price_po',
                    label: 'Pur_price',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'amount_po',
                    label: 'Amount',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'description_2',
                    label: 'Ghi chú 1',
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'price_company',
                    label: 'Gia_cty',
                    class: 'text-nowrap',
                    sortable: true,

                },
                {
                    key: 'level_two',
                    label: 'Level_2',
                    class: 'text-nowrap',
                    sortable: true,

                },
                {
                    key: 'level_three',
                    label: 'Level_3',
                    class: 'text-nowrap',
                    sortable: true,

                },
                {
                    key: 'level_four',
                    label: 'Level_4',
                    class: 'text-nowrap',
                    sortable: true,

                },
            ],
            selected: [],
            select_mode: 'multi',
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            modal_index: 0,

            material_category_types: [],

            api_material_category_types: '/api/master/materials/get-all',
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
                if (Array.isArray(data)) {
                    this.material_category_types = data;
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
            this.material_category_types.unshift({ ...data.data })
        },
        getUpdateMaterialCategoryType(index, data) {
            this.material_category_types.splice(index, 1, { ...data.data })
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
        deleteRow(index) {
           this.$emit('deleteRow', index)
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
}

.is-combo {
    background: #FF0000;
    color: white;
    padding: 3px;
}

</style>