<template>
    <div>
        <div v-for="(order_sync, index) in case_data.order_syncs" :key="index"
            class="bg-white  mb-5 border-left-order-sync">
            <HeaderOrderSyncSAPDetail :rollBackUrl="rollBackUrl" :order_sync="order_sync" :index="index + 1">
            </HeaderOrderSyncSAPDetail>
            <div class="form-group px-3">
                <b-table-simple hover small responsive>
                    <b-thead head-variant="light">
                        <b-tr>
                            <b-th v-for="(field, index) in fields" :key="index" class="text-nowrap" :class="{
            'text-right': isToRight(field.key),
        }">
                                {{ field.label }}
                            </b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <b-tr v-for="(item, index) in order_sync.so_data_items" :key="index">
                            <b-td>{{ index + 1 }}</b-td>
                            <b-td>{{ item.sku_sap_code }}</b-td>
                            <b-td>{{ item.sku_sap_name }}</b-td>
                            <b-td>{{ item.sku_sap_unit }}</b-td>
                            <b-td class="text-right">{{ item.quantity3_sap }}</b-td>
                            <b-td class="text-right">{{ toLocaleString(item.price_po) }}</b-td>
                            <b-td class="text-right"><b>{{ toLocaleString(item.amount_po) }}</b></b-td>
                        </b-tr>
                    </b-tbody>
                    <b-tfoot>
                        <b-tr>
                            <b-td colspan="7" variant="light" class="text-right">
                                Tổng tiền: <b class="text-danger">{{ sumAmountPO(order_sync.so_data_items) }}</b>
                            </b-td>
                        </b-tr>
                    </b-tfoot>
                </b-table-simple>
            </div>
        </div>
    </div>
</template>
<script>
import HeaderOrderSyncSAPDetail from '../headers/HeaderOrderSyncSAPDetail.vue';
import TableOrderSync from './TableOrderSync.vue';
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    components: {
        TableOrderSync,
        HeaderOrderSyncSAPDetail
    },
    data() {
        return {
            locale_format: "de-DE",
            api_handler: new ApiHandler(window.Laravel.access_token),
            case_filter: {
                query: '',
            },
            case_api: {
                order_sync_detail: 'api/so-header/so-header-details'
            },
            fields: [
                {
                    key: 'index',
                    label: 'Stt',
                    sortable: true,
                    class: 'text-nowrap text-center',
                },
                {
                    key: 'sku_sap_code',
                    label: 'Mã SAP',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'sku_sap_name',
                    label: 'Tên sản phẩm SAP',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'sku_sap_unit',
                    label: 'ĐVT',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'quantity3_sap',
                    label: 'Số lượng',
                    sortable: true,
                    class: 'text-nowrap',
                    thClass: 'text-center',
                    tdClass: 'text-right'
                },
                {
                    key: 'price_po',
                    label: 'Đơn giá',
                    sortable: true,
                    class: 'text-nowrap',
                    thClass: 'text-center',
                    tdClass: 'text-right'
                },
                {
                    key: 'amount_po',
                    label: 'Thành tiền',
                    sortable: true,
                    class: 'text-nowrap',
                    thClass: 'text-center',
                    tdClass: 'text-right'
                },
            ],
            items: [],
            case_data: {
                order_syncs: [],
                orders: []
            }
        }
    },
    created() {
        this.getUrl();
    },
    methods: {
        async fetchOrderSyncDetail(ids) {
            try {
                let items = [];
                if (ids.length > 0) {
                    ids.forEach(id => {
                        items.push({
                            id: id
                        });
                    });
                }
                let body = {
                    items: items
                }
                // this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.post(this.case_api.order_sync_detail, {}, body);
                this.case_data.order_syncs = data;
                // this.case_data.orders = data.map(item => item.so_data_items)
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                // this.case_is_loading.fetch_api = false;
            }
        },
        rollBackUrl() {
            // this.$router.push('/sap-syncs');
            // this.$emit('rollBackUrl', false);
        },
        getUrl() {
            const url = window.location.href;
            const id_string = url.split('#')[1];
            const ids = id_string.split('?')[0].split(',');
            if (ids) {
                this.fetchOrderSyncDetail(ids);
            }
        },
        sumAmountPO(items) {
            let sum_amout = items.reduce((total, item) => total + item.amount_po, 0);
            if(sum_amout === null || sum_amout === undefined){
                return '';
            } else {
                return sum_amout.toLocaleString(this.locale_format);
            }
        },
        toLocaleString(value) {
          if(value === null || value === undefined) {
            return '';
          } else {
            return value.toLocaleString(this.locale_format);
          }
        },
        isToRight(field) {
            return field === 'quantity3_sap' || field === 'price_po' || field === 'amount_po';
        }
    }
}
</script>
<style lang="scss" scoped>
.border-left-order-sync {
    border-left: 5px solid #17a2b8;
}
</style>