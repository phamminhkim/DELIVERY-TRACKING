<template>
    <div>
        <div v-for="(order_sync, index) in case_data.order_syncs" :key="index"
            class="bg-white  mb-5 border-left-order-sync">
            <HeaderOrderSyncSAPDetail :rollBackUrl="rollBackUrl" :order_sync="order_sync" :index="index + 1">
            </HeaderOrderSyncSAPDetail>
            <div class="form-group px-3">
                <b-table :fields="fields" :items="order_sync.so_data_items"  responsive small
                    hover :bordered="true" head-variant="light">
                    <template #cell(index)="data">
                        <span>{{ data.index + 1 }}</span>
                    </template>
                </b-table>
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
                    key: 'quantity3_sap',
                    label: 'Số lượng',
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
                    key: 'company_price',
                    label: 'Đơn giá',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'amount_po',
                    label: 'Thành tiền',
                    sortable: true,
                    class: 'text-nowrap'
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
    }
}
</script>
<style lang="scss" scoped>
.border-left-order-sync{
    border-left: 5px solid #17a2b8;
}
</style>