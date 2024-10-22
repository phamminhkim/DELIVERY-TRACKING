<template>
    <div>
        <div class="modal fade" id="listOrderProcessSO" tabindex="-1">
            <div class="modal-dialog modal-xl m-0" >
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
                                :current_page="current_page" :per_page="per_page"
                                @deleteSoHeader="handleDeleteSoHeader" @sort="handleSort" :loading="case_is_loading.fetch_api"
                                @search="handleSearch">
                            </TableOrderProcessSO>
                            <PaginationTable :rows="total_items" :per_page="per_page"
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
            api_order_so_header_dlt: '/api/sales-order/delete-multiple',
            case_is_loading: {
                fetch_api: false,
            },
            list_order_process_so: [],
            errors: [],
            per_page: 10,
            page_options: [10, 20, 50, 100],
            current_page: 1,
            total_pages: 0,
            total_items: 0,
            search_query: '',
            sort_field: '',
            sort_direction: '',
        }
    },
    created() {
        this.fetchOrderProcessSO();
    },
    methods: {
        handleSearch(search_text) {
            this.search_query = search_text;
            this.current_page = 1; // Reset to the first page when searching
            this.fetchOrderProcessSO();
        },
        handleSort(sort_field, sort_direction) {
            this.sort_field = sort_field;
            this.sort_direction = sort_direction;
            this.fetchOrderProcessSO();
        },
        getPerPageChange(per_page) {
            this.per_page = per_page;
            this.fetchOrderProcessSO();
        },
        getPageChange(page) {
            this.current_page = page;
            this.fetchOrderProcessSO();
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

                const { data } = await this.api_handler.get(this.api_order_process_so,
                    {
                        page: this.current_page,
                        per_page: this.per_page === 'All' ? 0 : this.per_page,
                        search: this.search_query,
                        sort_field: this.sort_field,
                        sort_direction: this.sort_direction,
                    }
                );
                this.list_order_process_so = Object.values(data.data);
                this.current_page = data.current_page;
                this.total_pages = data.last_page;
                this.total_items = data.total;
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
                const { data, success, errors } = await this.api_handler.delete(this.api_order_process_so + '/' + id);
                if (success) {
                    this.$showMessage('success', 'Xóa thành công');
                }
                return success;
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error.response.data.errors);
                return error.response.data.success;
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        },
        getDltOrderProcessSO(index, item) {
            if (confirm('Bạn có chắc chắn muốn xóa không?')) {
                const is_deleted = this.DeleteOrderProcessSO(item.id);
                is_deleted.then((is_deleted) => {
                    if(is_deleted){
                        this.list_order_process_so.splice(index, 1);
                    }
                });

            }

        },
        getDltSoHeader(index_parent, index_child, item) {
            if (confirm('Bạn có chắc chắn muốn xóa không?')) {
                this.apiDeleteSoHeader(item.id, index_parent, index_child);
            }
        },
        handleDeleteSoHeader(index_parent, index_child, item) {
            // this.DeleteOrderProcessSO(id);
            this.getDltSoHeader(index_parent, index_child, item);
        },
        async apiDeleteSoHeader(id, index_parent, index_child) {
            try {
                this.case_is_loading.fetch_api = true;
                const { data, success } = await this.api_handler.post(this.api_order_so_header_dlt, {},
                    {
                        so_header_ids: [id]
                    }
                );
                if(success){
                    this.$showMessage('success', 'Xóa thành công');
                    this.list_order_process_so[index_parent].so_headers.splice(index_child, 1);
                }
                // this.fetchOrderProcessSO();
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error
                );
            } finally {
                this.case_is_loading.fetch_api = false;
            }
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>
