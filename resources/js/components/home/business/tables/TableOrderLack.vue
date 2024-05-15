<template>
    <div>
        <div v-show="tab_value == 'order_lack'" class="form-group">
            <b-table small responsive striped hover :items="order_lacks" :fields="field_order_lacks"
                :current-page="current_page" :per-page="per_page">
                <template #cell(index)="data">
                    <div class="font-weight-bold">
                        {{ (data.index + 1) + (current_page * per_page) - per_page }}
                    </div>
                </template>
                <template #cell(row_custom)="data">
                    <button @click="convertOrderLack(data.index, data.item)" type="button" class="btn btn-sm btn-warning px-4">
                        <i class="fas fa-undo mr-2"></i>Convert
                    </button>
                </template>
                <template #cell(customer_name)="data">
                    <div>
                        {{ data.item.customer_name }}{{ data.item.promotive }}
                    </div>
                </template>
                <template #cell(sku_sap_name)="data">
                    {{ data.item.sku_sap_name }}{{ data.item.promotive }}
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
            </b-table>
            <div class="form-group">
                <PaginationTable :rows="order_lacks.length" :per_page="per_page" :page_options="page_options"
                    :current_page="current_page" @pageChange="getPageChange" @perPageChange="getPerPageChange">
                </PaginationTable>
            </div>
        </div>
    </div>
</template>
<script>
import PaginationTable from '../paginations/PaginationTable.vue';
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order_lack'
        },
        order_lacks: {
            type: Array,
            default: () => []
        }

    },
    components: {
        PaginationTable
    },
    data() {
        return {
            field_order_lacks: [
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
                    tdClass: 'voucher-custom border',
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
                    class: 'text-nowrap',
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
                    class: 'text-nowrap',
                    sortable: true,


                },
                {
                    key: 'quantity2_po',
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
                    key: 'note',
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
            per_page: 10,
            page_options: [10, 20, 50, 100, 200, 300, 500],
            current_page: 1,
        }
    },
    mounted() {
        this.countOrderLack();
    },

    methods: {
        getPerPageChange(per_page) {
            this.per_page = per_page;
        },
        getPageChange(page) {
            this.current_page = page;
        },
        countOrderLack() {
            this.$emit('countOrderLack', this.order_lacks.length);
        },
        convertOrderLack(index, item) {
            this.$emit('convertOrderLack', index, item);
        }

    }
}
</script>
<style lang="scss" scoped></style>