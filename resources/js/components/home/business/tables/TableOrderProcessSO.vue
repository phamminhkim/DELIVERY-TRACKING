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
            <template #cell(created_at)="data">
                {{ data.item.created_at | formatDate }}
            </template>
            <template #cell(updated_at)="data">
                {{ data.item.updated_at | formatDate }}
            </template>
            <template #cell(action)="data">
                <!-- <button @click="emitEditOrderProcessSO(data.item)" type="button" class="btn btn-sm btn-info px-4">
                    Chỉnh sửa
                </button> -->
                <button @click="emitDltOrderProcessSO(data.index, data.item)" type="button"
                    class="btn btn-sm btn-danger px-4">
                    Xóa
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
                    key: 'title',
                    label: 'Tiêu đề',
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
                    key: 'updated_at',
                    label: 'Ngày cập nhật',
                    class: 'text-nowrap',
                    sortable: true,
                },

                {
                    key: 'action',
                    label: '',
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
        }

    },
}
</script>
<style lang="scss" scoped></style>