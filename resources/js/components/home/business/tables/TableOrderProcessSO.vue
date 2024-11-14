<template>
    <div>
        <div class="row">
            <div class="col-lg-6 ml-auto">
                <div class="input-group input-group-sm mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                    </div>
                    <input v-model="case_filter.search" @input="handleSearch" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
            </div>
        </div>
        <b-table :sticky-header="height_window + 'px'" @row-dblclicked="handleDoubleClick"
            responsive hover small striped head-variant="true" :per-page="per_page"
            thead-class="text-xs sticky-header-table-order-process-so" :items="list_order_process_so" :fields="fields"
            table-class="table-order-process-so text-xs" @sort-changed="onSortChange" :busy="loading">
            <template #table-busy>
                <div class="text-center text-primary my-2">
                    <b-spinner class="align-middle" type="grow"></b-spinner>
                    <strong>Đang tải dữ liệu...</strong>
                </div>
            </template>
            <template #cell(index)="data">
                {{ (data.index + 1) + (current_page * per_page) - per_page }}
            </template>
            <template #cell(serial_number)="data">
                <div>
                    <a class="link-item" @click="getUrl(data.item)" style="cursor: pointer;">{{ data.item.serial_number
                        }}</a>
                </div>
            </template>
            <template #cell(created_at)="data">
                {{ data.item.created_at | formatDate }}
            </template>
            <template #cell(customer_group_id)="data">
                <div v-if="data.item.customer_group">
                    {{ data.item.customer_group.name }}
                </div>
            </template>

            <template #cell(synchronized_so_count)="data">
                <!-- <b-button size="sm" @click="data.toggleDetails" class="mr-2">
                    {{ data.detailsShowing ? 'Hide' : 'Show' }} Details
                </b-button> -->
                <!-- <ul>
                    <li v-for="(so_header, index) in data.item.so_headers" :key="index">
                        {{ so_header.sap_so_number }}{{ so_header.promotive_name }}
                    </li>
                </ul> -->
                <!-- <div class="text-xs" >
                    <p class="mb-0">{{ so_header.sap_so_number }}{{ so_header.promotive_name }}</p>
                </div> -->
                <div class="text-center">
                    <span class="badge badge-sm  px-2" :class="{
                        'badge-danger': data.item.synchronized_so_count == 0,
                        'badge-success': data.item.synchronized_so_count == data.item.so_headers.length,
                        'badge-warning': data.item.synchronized_so_count > 0 && data.item.synchronized_so_count < data.item.so_headers.length
                    }">
                    {{ data.item.synchronized_so_count }}/{{ data.item.so_headers.length }}
                    </span>
                    <!-- <small  @click="getUrlDetail(data.item)" class="text-primary" style="cursor: pointer;"><i class="fas fa-eye"></i></small> -->
                    <button @click="getUrlDetail(data.item)" class="text-xs btn btn-sm border-white badge">
                        <small  class="text-primary" style="cursor: pointer;"><i
                                class="fas fa-eye"></i></small>
                    </button>
                    <button @click="data.toggleDetails" class="text-xs btn btn-sm border-white badge">
                        <small  class="text-info text-xs" style="cursor: pointer;"><i
                                class="fas fa-caret-down"></i></small>

                    </button>
                </div>
            </template>
            <template #cell(updated_by)="data">
                <div>
                    <span class="mr-1 text-primary"><i class="fas fa-id-card-alt"></i></span>
                    <span class="mr-1 text-primary font-weight-bold"> {{ data.item.updated_by.name }}</span>
                </div>
            </template>
            <template #cell(updated_at)="data">
                {{ data.item.updated_at | formatDate }}
            </template>
            <template #cell(created_by)="data">
                <div>
                    <span class="mr-1 text-primary"><i class="fas fa-id-card-alt"></i></span>
                    <span class="mr-1 text-primary font-weight-bold">{{ data.item.created_by.name }} </span>
                </div>
            </template>
            <template #cell(action)="data">
                <button @click="emitDltOrderProcessSO(data.index, data.item)" type="button"
                    class="btn btn-sm btn-danger text-xs">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </template>
            <template #row-details="row">
                <b-row class="mb-2">
                    <b-col sm="2" class="bg-white "></b-col>
                    <b-col sm="10" class="bg-white p-1 ">
                        <table class="table table-responsive mb-0 table-bordered ml-4">
                            <thead>
                                <tr class="bg-white font-weight-bold">
                                    <td>STT</td>
                                    <td>SO_Key</td>
                                    <td>Sap_Note</td>
                                    <td>Khuyến mãi</td>
                                    <td>Sap_So Num</td>
                                    <td>Trạng thái</td>
                                    <td>Level2</td>
                                    <td>Level3</td>
                                    <td>Level4</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(so_header, index) in row.item.so_headers" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ so_header.sap_so_number }}{{ so_header.promotive_name }}</td>
                                    <td>{{ so_header.so_sap_note }}{{ so_header.promotive_name }}</td>
                                    <td>{{ so_header.promotive_name }}</td>
                                    <td>{{ so_header.so_uid }}</td>
                                    <td>
                                        <span class="badge badge-sm badge-warning"
                                            v-if="so_header.sync_sap_status == 0">Chưa đồng bộ</span>
                                        <span class="badge badge-sm badge-success"
                                            v-else-if="so_header.sync_sap_status == 1">Đã đồng bộ</span>
                                    </td>
                                    <td>{{ so_header.level2 }}</td>
                                    <td>{{ so_header.level3 }}</td>
                                    <td>{{ so_header.level4 }}</td>
                                    <td>
                                        <button @click="getUrlDetailId(so_header)" class="text-xs btn btn-sm border-white badge">
                                            <small  class="text-xs text-primary">
                                                <i class="fas fa-eye"></i>
                                            </small>
                                        </button>
                                        <button v-show="so_header.sync_sap_status == 0" @click="deleteSoHeader(row.index,index,so_header)" class="text-xs btn btn-sm border-white badge">
                                            <small class="text-xs text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </small>
                                        </button>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </b-col>
                </b-row>

            </template>
        </b-table>
    </div>
</template>
<script>
export default {
    props: {
        list_order_process_so: {
            type: Array,
            required: true
        },
        current_page: {
            type: Number,
            default: 1
        },
        per_page: {
            type: Number,
            default: 10
        },
        loading: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap',
                    sortable: false,
                },
                {
                    key: 'serial_number',
                    label: 'Serial_number',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'synchronized_so_count',
                    label: 'Số SO đã đồng bộ',
                    class: 'text-nowrap',
                    thClass: 'text-center',
                    sortable: true,
                },
                {
                    key: 'customer_group_id',
                    label: 'Nhóm khách hàng',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'title',
                    label: 'Tiêu đề',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'created_by',
                    label: 'Người tạo',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'created_at',
                    label: 'Ngày tạo',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'updated_by',
                    label: 'Người cập nhật',
                    class: 'text-nowrap',
                    sortable: true,
                },

                {
                    key: 'updated_at',
                    label: 'Ngày cập nhật',
                    class: 'text-nowrap',
                    sortable: true,
                },

                {
                    key: 'action',
                    label: 'Hành động',
                    class: 'text-nowrap',
                    sortable: false,
                },
            ],
            case_filter: {
                search: '',
                status: '',
                type: '',
            },

            height_window: 0,
        }
    },
    mounted() {
        this.handleHeightWindow();
        window.addEventListener('resize', this.handleHeightWindow);
    },
    methods: {
        handleSearch() {
            this.$emit('search', this.case_filter.search);
        },
        onSortChange(ctx) {
            // ctx là object chứa thông tin về field và direction
            let sort_field = ctx.sortBy; // Lấy trường cần sắp xếp
            let sort_direction = ctx.sortDesc ? 'desc' : 'asc'; // Đặt hướng sắp xếp
            this.$emit('sort', sort_field, sort_direction);
        },
        handleHeightWindow() {
            this.height_window = window.innerHeight - 300;
        },
        emitEditOrderProcessSO(item) {
            this.$emit('editOrderProcessSO');
        },
        handleDoubleClick(item, index) {
            this.$emit('handleDoubleClick', item);
        },
        emitDltOrderProcessSO(index, item) {
            const idx_parent = index + (this.current_page * this.per_page) - this.per_page
            this.$emit('dltOrderProcessSO', idx_parent, item);
        },
        getUrl(item) {
            const url = window.location.origin + this.$route.path + '#' + item.id + '?seri=' + item.serial_number;
            window.open(url, '_blank');
        },
        getUrlDetail(item) {
            let ids = '';
            let url = '';
            ids = item.so_headers.map(so => so.id).join(',');
            url = window.location.origin + '/sap-syncs-detail' + '#' + ids + '?xem_chi_tiet';
            window.open(url, '_blank');
        },
        getUrlDetailId(item) {
            let ids = '';
            let url = '';
            ids = item.id;
            url = window.location.origin + '/sap-syncs-detail' + '#' + ids + '?xem_chi_tiet';
            window.open(url, '_blank');
        },
        deleteSoHeader(index_parent,index_child, item) {
            // const idx_parent = index_parent + (this.current_page * this.per_page) - this.per_page
            const idx_parent = index_parent
            this.$emit('deleteSoHeader', idx_parent,index_child, item);
        },
        countSoSyncStatus(so_headers) {
            let count = 0;
            so_headers.forEach(so_header => {
                if (so_header.sync_sap_status == 1) {
                    count++;
                }
            });
            return count;
        },
    },
}
</script>
<style lang="scss" scoped>
::v-deep .table-order-process-so {
    font-size: .875em;
}

::v-deep .sticky-header-table-order-process-so {
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: white;
}
</style>
