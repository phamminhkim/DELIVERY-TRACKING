<template>
    <div>
        <div v-if="tab_value == 'order'" class="form-group">
            <b-table small responsive hover :items="orders" :fields="field_order_suffices"
                table-class="table-order-suffices">
                <template #cell(selected)="data">
                    <b-form-checkbox v-model="selected" :value="data.item"></b-form-checkbox>
                </template>
                <template #cell(barcode_company)="data">
                    <div class="">
                        {{ data.item.barcode_company }}
                    </div>
                </template>
                <template #cell(promotive)="data">
                    <div @click="onChangeShowModal(data.index)" class="text-center">
                        <div class="d-flex align-items-baseline justify-content-around">
                            <p class="font-weight-bold mr-2">{{ data.item.promotive }}</p>
                            <button class="btn btn-xs btn-light p-1 px-2 rounded-circle"><i
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
        }
    },
    components: {
        DialogMaterialCategoryTypes
    },
    data() {
        return {
            is_modal_material_category_type: false,
            field_order_suffices: [
                {
                    key: 'selected',
                    label: '',
                    class: 'text-nowrap',
                    thClass: 'bg-secondary',


                },
                {
                    key: 'book_store',
                    label: 'TENNS',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',
                },
                {
                    key: 'barcode_company',
                    label: 'BARCODE_CTY',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'sap_code',
                    label: 'MaSAP',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'sap_material_name',
                    label: 'TenSP',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'unit_code',
                    label: 'DVT',
                    class: 'text-nowrap',
                    thClass: 'bg-success',

                },

                {
                    key: 'promotive',
                    label: 'KM',
                    class: 'text-nowrap',
                    thClass: 'bg-success',
                    tdClass: 'voucher-custom border',


                },
                {
                    key: 'description',
                    label: 'GHI_CHU',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',


                },
                {
                    key: 'code_customer',
                    label: 'MAKH',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',

                },
                {
                    key: 'unit_barcode',
                    label: 'UNIT_BARCODE',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',

                },
                {
                    key: 'unit_barcode_description',
                    label: 'UNIT_BARCODE_DESCRIPTION',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',

                },
                {
                    key: 'unit',
                    label: 'DVT_po',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',

                },
                {
                    key: 'po',
                    label: 'PO',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',

                },
                {
                    key: 'qty',
                    label: 'QTY',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',

                },
                {
                    key: 'combo',
                    label: 'combo',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'check_ton',
                    label: 'check tồn',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'po_qty',
                    label: 'PO_QTY',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',


                },
                {
                    key: 'pur_price',
                    label: 'PUR_PRICE',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',


                },
                {
                    key: 'amount',
                    label: 'AMOUNT',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',


                },
                {
                    key: 'description_2',
                    label: 'GHI_CHU_1',
                    class: 'text-nowrap',
                    thClass: 'bg-warning',


                },
                {
                    key: 'price_company',
                    label: 'GIA_CTY',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'level_two',
                    label: 'LEVEL_2',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'level_three',
                    label: 'LEVEL_3',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


                },
                {
                    key: 'level_four',
                    label: 'LEVEL_4',
                    class: 'text-nowrap',
                    thClass: 'bg-success',


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
        isBarcodeCompanyOrther(sap_material_name, unit_barcode_description) {
            sap_material_name = sap_material_name.toLowerCase()
            unit_barcode_description = unit_barcode_description.toLowerCase()
            if (sap_material_name != unit_barcode_description) {
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
    border: 2px solid lightgray !important;
    cursor: pointer !important;

    &:hover {
        border: 2px solid rgb(49, 49, 255) !important;
        transition: 0.1s all !important;

    }
}
</style>