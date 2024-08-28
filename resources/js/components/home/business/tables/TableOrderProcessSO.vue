<template>
    <div>
        <div class="row">
            <div class="col-lg-6 ml-auto">
                <div class="input-group input-group-sm mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                    </div>
                    <input v-model="case_filter.search" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
            </div>
        </div>
        <b-table :sticky-header="true" @row-dblclicked="handleDoubleClick" :filter="case_filter.search" responsive hover
            small striped head-variant="true" :current-page="current_page" :per-page="per_page"
            :items="list_order_process_so" :fields="fields">
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
                <div class="text-center">
                    {{ data.item.synchronized_so_count }}/{{ data.item.total_so_count }}
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
                    class="btn btn-sm btn-danger ">
                    <i class="fas fa-trash-alt"></i>
                </button>
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
    },
    data() {
        return {
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap',
                    sortable: true,
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
                    sortable: true,
                },
            ],
            case_filter: {
                search: '',
                status: '',
                type: '',
            }
        }
    },
    methods: {
        emitEditOrderProcessSO(item) {
            this.$emit('editOrderProcessSO');
        },
        handleDoubleClick(item, index) {
            this.$emit('handleDoubleClick', item);
        },
        emitDltOrderProcessSO(index, item) {
            this.$emit('dltOrderProcessSO', index, item);
        },
        getUrl(item) {
            const url = window.location.origin + this.$route.path + '#' + item.id + '?seri=' + item.serial_number;
            window.open(url, '_blank');
        },
    },
}
</script>
<style lang="scss" scoped></style>