<template>
    <div>
        <div v-show="tab_value == 'order_lack'" class="form-group">
            <b-table small responsive striped hover :items="order_lacks" :fields="field_order_lacks"></b-table>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        tab_value: {
            type: String,
            default: 'order_lack'
        }
    },
    data() {
        return {
            order_lacks: [
                {
                    book_store: 'TENNS',
                    barcode_company: 'BARCODE_CTY',
                    code_sap: 'MaSAP',
                    name: 'TenSP',
                    unit: 'DVT',
                    voucher_free: 'KM',
                    description: 'GHI_CHU',
                    code_customer: 'MAKH',
                    unit_barcode: 'UNIT_BARCODE',
                    unit_barcode_description: 'UNIT_BARCODE_DESCRIPTION',
                    po: 'PO',
                    qty: 'QTY',
                    combo: 'combo',
                    check_ton: 'check tồn',
                    po_qty: 'PO_QTY',
                    pur_price: 'PUR_PRICE',
                    amount: 'AMOUNT',
                    description_2: 'GHI_CHU_2',
                    price_company: 'GIA_CTY',
                    level_two: 'LEVEL_2',
                    level_three: 'LEVEL_3',
                    level_four: 'LEVEL_4',
                },
                {
                    book_store: 'TENNS',
                    barcode_company: 'BARCODE_CTY',
                    code_sap: 'MaSAP',
                    name: 'TenSP',
                    unit: 'DVT',
                    voucher_free: 'KM',
                    description: 'GHI_CHU',
                    code_customer: 'MAKH',
                    unit_barcode: 'UNIT_BARCODE',
                    unit_barcode_description: 'UNIT_BARCODE_DESCRIPTION',
                    po: 'PO',
                    qty: 'QTY',
                    combo: 'combo',
                    check_ton: 'check tồn',
                    po_qty: 'PO_QTY',
                    pur_price: 'PUR_PRICE',
                    amount: 'AMOUNT',
                    description_2: 'GHI_CHU_2',
                    price_company: 'GIA_CTY',
                    level_two: 'LEVEL_2',
                    level_three: 'LEVEL_3',
                    level_four: 'LEVEL_4',
                },
                {
                    book_store: 'TENNS',
                    barcode_company: 'BARCODE_CTY',
                    code_sap: 'MaSAP',
                    name: 'TenSP',
                    unit: 'DVT',
                    voucher_free: 'KM',
                    description: 'GHI_CHU',
                    code_customer: 'MAKH',
                    unit_barcode: 'UNIT_BARCODE',
                    unit_barcode_description: 'UNIT_BARCODE_DESCRIPTION',
                    po: 'PO',
                    qty: 'QTY',
                    combo: 'combo',
                    check_ton: 'check tồn',
                    po_qty: 'PO_QTY',
                    pur_price: 'PUR_PRICE',
                    amount: 'AMOUNT',
                    description_2: 'GHI_CHU_2',
                    price_company: 'GIA_CTY',
                    level_two: 'LEVEL_2',
                    level_three: 'LEVEL_3',
                    level_four: 'LEVEL_4',
                },
                {
                    book_store: 'TENNS',
                    barcode_company: 'BARCODE_CTY',
                    code_sap: 'MaSAP',
                    name: 'TenSP',
                    unit: 'DVT',
                    voucher_free: 'KM',
                    description: 'GHI_CHU',
                    code_customer: 'MAKH',
                    unit_barcode: 'UNIT_BARCODE',
                    unit_barcode_description: 'UNIT_BARCODE_DESCRIPTION',
                    po: 'PO',
                    qty: 'QTY',
                    combo: 'combo',
                    check_ton: 'check tồn',
                    po_qty: 'PO_QTY',
                    pur_price: 'PUR_PRICE',
                    amount: 'AMOUNT',
                    description_2: 'GHI_CHU_2',
                    price_company: 'GIA_CTY',
                    level_two: 'LEVEL_2',
                    level_three: 'LEVEL_3',
                    level_four: 'LEVEL_4',
                },
                {
                    book_store: 'TENNS',
                    barcode_company: 'BARCODE_CTY',
                    code_sap: 'MaSAP',
                    name: 'TenSP',
                    unit: 'DVT',
                    voucher_free: 'KM',
                    description: 'GHI_CHU',
                    code_customer: 'MAKH',
                    unit_barcode: 'UNIT_BARCODE',
                    unit_barcode_description: 'UNIT_BARCODE_DESCRIPTION',
                    po: 'PO',
                    qty: 'QTY',
                    combo: 'combo',
                    check_ton: 'check tồn',
                    po_qty: 'PO_QTY',
                    pur_price: 'PUR_PRICE',
                    amount: 'AMOUNT',
                    description_2: 'GHI_CHU_2',
                    price_company: 'GIA_CTY',
                    level_two: 'LEVEL_2',
                    level_three: 'LEVEL_3',
                    level_four: 'LEVEL_4',
                },
            ],
            field_order_lacks: [
                {
                    key: 'book_store',
                    name: 'TENNS',
                },
                {
                    key: 'barcode_company',
                    name: 'BARCODE_CTY',
                },
                {
                    key: 'code_sap',
                    name: 'MaSAP',
                },
                {
                    key: 'name',
                    name: 'TenSP',
                },
                {
                    key: 'unit',
                    name: 'DVT',
                },
                {
                    key: 'voucher_free',
                    name: 'KM',
                },
                {
                    key: 'description',
                    name: 'GHI_CHU',
                },
                {
                    key: 'code_customer',
                    name: 'MAKH',
                },
                {
                    key: 'unit_barcode',
                    name: 'UNIT_BARCODE',
                },
                {
                    key: 'unit_barcode_description',
                    name: 'UNIT_BARCODE_DESCRIPTION',
                },
                {
                    key: 'po',
                    name: 'PO',
                },
                {
                    key: 'qty',
                    name: 'QTY',
                },
                {
                    key: 'combo',
                    name: 'combo',
                },
                {
                    key: 'check_ton',
                    name: 'check tồn',
                },
                {
                    key: 'po_qty',
                    name: 'PO_QTY',
                },
                {
                    key: 'pur_price',
                    name: 'PUR_PRICE',
                },
                {
                    key: 'amount',
                    name: 'AMOUNT',
                },
                {
                    key: 'description_2',
                    name: 'GHI_CHU_2',
                },
                {
                    key: 'price_company',
                    name: 'GIA_CTY',
                },
                {
                    key: 'level_two',
                    name: 'LEVEL_2',
                },
                {
                    key: 'level_three',
                    name: 'LEVEL_3',
                },
                {
                    key: 'level_four',
                    name: 'LEVEL_4',
                },
            ]
        }
    },
    mounted() {
        this.countOrderLack();
    },
    
    methods: {
        countOrderLack() {
            this.$emit('countOrderLack', this.order_lacks.length);
        }
    }
}
</script>
<style lang="scss" scoped></style>