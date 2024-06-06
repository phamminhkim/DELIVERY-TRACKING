<template>
    <div>
        <HeaderOrderSyncSAPDetail :rollBackUrl="rollBackUrl"></HeaderOrderSyncSAPDetail>
        <div class="form-group">
            <div class="row mb-1">
                <div class="col-lg-6 ml-auto">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input v-model="case_filter.query" type="text" class="form-control form-control-sm"
                            placeholder="Tìm kiếm...">
                    </div>
                </div>
            </div>
        </div>
        <TableOrderSync :fields="fields" :items="items" :query="case_filter.query"></TableOrderSync>
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
                    key: 'select',
                    label: '',
                    class: 'text-nowrap'
                },
                {
                    key: 'index',
                    label: 'Stt',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'sap_so',
                    label: 'SAP SO num',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'so_key',
                    label: 'SO Key',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_key',
                    label: 'Mã KH',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_name',
                    label: 'Tên KH',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'status',
                    label: 'Trạng thái',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'po_delivery_date',
                    label: 'Ngày giao',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'status_sync',
                    label: 'TT Đồng bộ',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'noti_sync',
                    label: 'Thông báo',
                    sortable: true,
                    class: 'text-nowrap'
                },
            ],
            items: [],
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
                console.log(body, 'body');
                // this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.post(this.case_api.order_sync_detail, {}, body);
                this.items = data;
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
<style lang="scss" scoped></style>