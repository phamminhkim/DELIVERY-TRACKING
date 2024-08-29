<template>
    <div>
        <b-table responsive small hover :fields="fields" :items="items" head-variant="light" 
        :sticky-header="window_height + 'px'"  thead-class="thead-light custom-thead"
            :class="{
                'position-relative-custom': use_component === 'DialogOrderSync',
                'custom-set-height' : class_modal_v2 === 'V2OrderProcesses',
            }"
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
                <a class="link-item cursor-poiner" @click="getUrl(data.item)">{{ data.item.sap_so_number }}{{ data.item.note }}</a>
            </template>
            <template #cell(warehouse_code)="data">
                <!-- <span class="badge badge-sm badge-info px-2">{{ findWarehouse(data.item.warehouse_id)
                    }}</span> -->
                <div style="width:20rem;">
                        <treeselect placeholder="Chọn kho.." :multiple="false" :disable-branch-nodes="true"
                        :show-count="true" v-model="data.item.warehouse_id" :options="warehouses"
                        @input="emitWarehouseId(data.item.warehouse_id, data.item.id)"
                        :load-options="loadOptions" />
                </div>
                <!-- <input class="form-control form-control-sm border" v-model="data.item.warehouse_id"
                    placeholder="Nhập mã kho" /> -->
            </template>
            <template #cell(shipping_id)="data">
                <div style="width:10rem">
                    <select v-model="data.item.shipping_id" class="form-control form-control-sm">
                    <option value="">Chọn shipping</option>
                    <option v-for="(ship, index) in shipping_datas" :key="index" :value="ship.id">
                        {{ ship.code }}
                    </option>
                </select>
                </div>
            </template>
            <template #cell(sync_sap_status)="data">
                <span class="badge bg-success" v-if="data.item.sync_sap_status == 1">Đã đồng bộ</span>
                <span class="badge bg-warning" v-if="data.item.sync_sap_status == 0">Chưa đồng bộ</span>
            </template>
        </b-table>
    </div>
</template>
<script>
import ApiHandler, { APIRequest } from '../../ApiHandler';
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import { LOAD_CHILDREN_OPTIONS } from '@riophae/vue-treeselect'

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
        warehouses: Array,
        shipping_datas: Array,
        class_modal_v2: {
            type: String,
            default: ''
        },
    },
    components: {
        Treeselect
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
            window_height: 0,
            window_width: 0,
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
    mounted() {
        this.updateWindownHeight();
        window.addEventListener('resize', this.updateWindownHeight);
    },
    methods: {
        updateWindownHeight() {
            this.window_height = window.innerHeight - 220;
            this.window_width = window.innerWidth;
        },
        loadOptions({ action, parentNode, callback }) {
            // Typically, do the AJAX stuff here.
            // Once the server has responded,
            // assign children options to the parent node & call the callback.

            if (action === LOAD_CHILDREN_OPTIONS) {
                switch (parentNode.id) {
                    case 'success': {
                        simulateAsyncOperation(() => {
                            parentNode.children = [{
                                id: 'child',
                                label: 'Child option',
                            }]
                            callback()
                        })
                        break
                    }
                    case 'no-children': {
                        simulateAsyncOperation(() => {
                            parentNode.children = []
                            callback()
                        })
                        break
                    }
                    case 'failure': {
                        simulateAsyncOperation(() => {
                            callback(new Error('Failed to load options: network error.'))
                        })
                        break
                    }
                    default: /* empty */
                }
            }
        },
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
        },
        emitWarehouseId(warehouse_id, id) {
            this.$emit('emitWarehouseId', warehouse_id, id);
           
        }
    }
}
</script>
<style lang="scss" scoped>
.cursor-poiner {
    cursor: pointer;
}
.position-relative-custom {
    height: 25rem !important;
}
.custom-set-height{
    min-height: 30rem !important;
}
::v-deep .custom-thead {
    position: sticky;
    top: 0;
    z-index: 3;
}
</style>
