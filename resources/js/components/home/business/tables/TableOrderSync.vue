<template>
    <div>
        <b-table responsive small hover :fields="fields" :items="items" head-variant="light"
            :current-page="current_page" :per-page="per_page" :filter="query">
            <template #head(select)="data">
                <input type="checkbox" v-model="case_checkbox.select_all" @change="changeSelectAll()" />
            </template>
            <template #cell(select)="data">
                <input type="checkbox" v-model="case_checkbox.selected" :value="data.item" />
            </template>
            <template #cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template #cell(sap_so_number)="data">
                <a class="link-item cursor-poiner" @click="getUrl(data.item)">{{ data.item.sap_so_number }}</a>
            </template>
            <template #cell(warehouse_code)="data">
                <span class="badge badge-sm badge-info px-2">{{ findWarehouse(data.item.warehouse_id)
                    }}</span>
                <!-- <input class="form-control form-control-sm border" v-model="data.item.warehouse_id"
                    placeholder="Nhập mã kho" /> -->
            </template>
            <template #cell(sync_sap_status)="data">
                <span v-if="!data.sync_sap_status" class="badge badge-sm badge-warning">Chưa đồng bộ</span>
                <span v-else class="badge badge-sm badge-success">Đã đồng bộ</span>
            </template>
        </b-table>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    props: {
        use_component: {
            type: String,
            default: 'DialogOrderSync'
        },
        fields: Array,
        items: Array,
        query: String,
        current_page: Number,
        per_page: Number,
        un_selecteds: Array,
        warehouses: Array
    },
    
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            case_checkbox: {
                selected: [],
                select_all: false
            },
            case_data_temporary: {
                warehouses: [],

            },
            case_api: {
                warehouse: 'api/master/warehouses/company-3000',
            },
        }
    },
    watch: {
        'case_checkbox.selected': function (val) {
            this.$emit('emitSelectedOrderSync', val);
            if (val.length === this.items.length) {
                this.case_checkbox.select_all = true;
            } else {
                this.case_checkbox.select_all = false;
            }
        },
    },
    created() {
        this.fetchWarehouses();
    },
    methods: {
        async fetchWarehouses() {
            let { data, success } = await this.api_handler.get(this.case_api.warehouse);
            if (success) {
                this.case_data_temporary.warehouses = data;
            }
        },
        changeSelectAll() {
            if (this.case_checkbox.select_all) {
                this.case_checkbox.selected = this.items;
                this.$emit('emitSelectedOrderSync', this.case_checkbox.selected);

            } else {
                this.case_checkbox.selected = [];
                this.$emit('emitSelectedOrderSync', this.case_checkbox.selected);
            }
        },
        getUrl(item) {
            let url = '';
            switch (this.use_component) {
                case 'OrderSyncSAP':
                    url = window.location.origin + '/sap-syncs-detail' + '#' + item.id + '?sap_so_number=' + item.sap_so_number;
                    break;
                default:
                    url = window.location.origin + '/sap-syncs-detail' + '#' + item.so_header_id + '?sap_so_number=' + item.sap_so_number;
                    break;
            }
            window.open(url, '_blank');

            // console.log(window.location.origin + '/sap-syncs-detail');
        },
        findWarehouse(warehouse_id) {
            if (!warehouse_id) {
                return '';
            } else {
                if (this.case_data_temporary.warehouses.length !== 0) { 
                    let warehouse = this.case_data_temporary.warehouses.find(warehouse => warehouse.id == warehouse_id);
                    return warehouse ? warehouse.name : '';

                } else {
                    return '';
                }
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.cursor-poiner {
    cursor: pointer;
}
</style>