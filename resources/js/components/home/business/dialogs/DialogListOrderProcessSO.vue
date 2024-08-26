<template>
    <div>
        <div class="modal fade" id="listOrderProcessSO" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-blue">
                        <h5 class="modal-title font-weight-bold text-uppercase">Danh sách xử lý đơn hàng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-light">
                        <div class="form-group">
                            <TableOrderProcessSO :list_order_process_so="list_order_process_so"
                                @handleDoubleClick="getHandleDoubleClick" @dltOrderProcessSO="getDltOrderProcessSO"
                                :current_page="current_page" :per_page="per_page">
                            </TableOrderProcessSO>
                            <PaginationTable :rows="list_order_process_so.length" :per_page="per_page"
                                :page_options="page_options" :current_page="current_page" @pageChange="getPageChange"
                                @perPageChange="getPerPageChange">
                            </PaginationTable>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TableOrderProcessSO from '../tables/TableOrderProcessSO.vue';
import PaginationTable from '../paginations/PaginationTable.vue';
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    components: {
        TableOrderProcessSO,
        PaginationTable
    },
    props: {
        is_loading: { type: Boolean, default: false },
        update_status_function: { type: Object, default: () => { } }
    },
    watch: {
        'update_status_function.fetch_api_list_orders': {
            handler: function (val) {
                if (val) {
                    this.fetchOrderProcessSO();
                }
            },
            deep: true
        }
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            api_order_process_so: '/api/sales-order',
            case_is_loading: {
                fetch_api: false,
            },
            list_order_process_so: [],
            errors: [],
            per_page: 10,
            page_options: [10, 20, 50, 100],
            current_page: 1,
        }
    },
    created() {
        this.fetchOrderProcessSO();
    },
    methods: {
        getPerPageChange(per_page) {
            this.per_page = per_page;
        },
        getPageChange(page) {
            this.current_page = page;
        },
        showModal() {
            this.fetchOrderProcessSO();
            $('#listOrderProcessSO').modal('show');
        },
        hideModal() {
            $('#listOrderProcessSO').modal('hide');
        },
        async fetchOrderProcessSO() {
            try {
                this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.get(this.api_order_process_so);
                this.list_order_process_so = data;
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },

        getHandleDoubleClick(item) {
            this.fetchOrderProcessSODetail(item.id);
        },
        async fetchOrderProcessSODetail(id) {
            try {
                this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.get(this.api_order_process_so + '/' + id);
                this.$emit('fetchOrderProcessSODetail', data);
                this.hideModal();
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        async DeleteOrderProcessSO(id) {
            try {
                this.case_is_loading.fetch_api = true;
                const { data } = await this.api_handler.delete(this.api_order_process_so + '/' + id);
                this.$showMessage('success', 'Xóa thành công');
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        getDltOrderProcessSO(index, item) {
            if (confirm('Bạn có chắc chắn muốn xóa không?')) {
                this.list_order_process_so.splice(index, 1);
                this.DeleteOrderProcessSO(item.id);
            }

        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>