<template>
    <div>
        <div class="text-xs">
            <div class="header mb-4">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="text-uppercase">
                            <h6><i class="fas fa-store mr-1"></i>Nhà sách Mapping</h6>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-right">
                            <button @click="showModalCreateBookV2()" class="btn btn-sm btn-primary text-xs px-2"><i
                                    class="fas fa-plus mr-1"></i>Thêm
                                mới</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-6">
                        <div>
                            <button @click="showHideToolImport()" class="text-xs btn btn-sm btn-outline-success px-2"><i
                                    class="fas fa-plus mr-1"></i>Import Excel</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group input-group-sm mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" v-model="filter" class="form-control form-control-sm"
                                placeholder="Tìm kiếm" aria-label="Tìm kiếm" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <b-table :fields="fields" :items="book_stores" :filter="filter" responsive hover small bordered head-variant="light"
                        :current-page="pagination.current_page" :per-page="pagination.item_per_page" striped>
                            <template #cell(index)="data">
                                {{ data.index + 1 }}
                            </template>
                            <template #cell(action)="data">
                                <button @click="editItem(data.item)" class="btn btn-sm btn-warning text-xs px-2"><i
                                        class="fas fa-edit"></i></button>
                                <button @click="deleteBookStore(data.item.id)"
                                    class="btn btn-sm btn-danger text-xs px-2"><i class="fas fa-trash"></i></button>
                            </template>
                        </b-table>
                        <div class="row">
                            <label class="col-form-label-sm col-md-2" style="text-align: left" for="per-page-select">
                                Số lượng mỗi trang:
                            </label>
                            <div class="col-md-2">
                                <b-form-select size="sm" v-model="pagination.item_per_page" :options="pagination.page_options.map((option) => option.toString())
                                    "></b-form-select>
                            </div>
                            <label class="col-form-label-sm col-md-1" style="text-align: left"></label>
                            <div class="col-md-3">
                                <b-pagination v-model="pagination.current_page" :total-rows="rows"
                                    :per-page="pagination.item_per_page" :limit="3"
                                    :size="pagination.page_options.length.toString()" class="ml-1"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <BookStoreImport id="BookStoreImport" :is_show="is_show_hide_import_tool" @close-modal="handleCloseModal"
            :example_datas="example_data_imports"
            @save="handelSaveToolImport" />
        <SODialogCreateBookV2 id="SODialogCreateBookV2" :is_show_hide_dialog="is_show_hide_dialog" :item_parent="item"
            @close-dialog="handleCloseModalCreateBookV2" @save-success="handleFetchBookStore"
            @update-success="handleFetchBookStore" />
    </div>
</template>
<script>
import ApiHandler from '../../../../../ApiHandler';
import BookStoreImport from './BookStoreImport.vue';
import SODialogCreateBookV2 from '../dialog/SODialogCreateBookV2.vue';
export default {
    components: {
        BookStoreImport,
        SODialogCreateBookV2
    },
    data() {
        return {
            is_loading: false,
            is_show_hide_import_tool: false,
            is_show_hide_dialog: false,
            api_handler: new ApiHandler(window.Laravel.access_token),
            data_files: [],
            example_data_imports: [
                {
                    name: 'NS FAHASA Tân Phú',
                    code: 'TAN_PHU'
                },
                {
                    name: 'NS Tân Bình',
                    code: 'T_BINH'
                },
                {
                    name: 'NS Phú Nhuận',
                    code: 'P_NHUAN'
                }
            ],
            book_stores: [],
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    sortable: true,
                    class: 'text-center text-nowrap',
                },
                {
                    key: 'name',
                    label: 'Tên nhà sách',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'code',
                    label: 'Mã',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'action',
                    label: 'Hành động',
                    sortable: false,
                    class: 'text-center text-nowrap',

                }
            ],
            filter: '',
            api: {
                book_store_get_all: 'api/book-store/get-all',
                delete_book_store: '/api/book-store/delete-book-store',
                tool_create_book_store: '/api/book-store/tool-create-book-store',

            },
            item: {
                id: -1,
                code: '',
                name: '',
            },
            pagination: {
                current_page: 1,
                item_per_page: 10,
                total_items: 0,
                last_page: 0,
                page_options: [10, 20, 50, 100, 500],
            },
        }
    },
    created() {
        this.fetchBookStores();
    },
    methods: {
        async handleFetchBookStore() {
            this.is_show_hide_dialog = false;
            await this.fetchBookStores();
        },
        handleCloseModal(is_show) {
            this.is_show_hide_import_tool = is_show;
        },
        showHideToolImport() {
            this.is_show_hide_import_tool = true;
        },
        async fetchBookStores() {
            this.is_loading = true;
            try {
                const { data, success } = await this.api_handler.get(this.api.book_store_get_all);
                if (success) {
                    this.book_stores = data;
                }
            } catch (error) {
                console.log(error);
            }
            this.is_loading = false;
        },
        showModalCreateBookV2() {
            this.resetItem();
            this.is_show_hide_dialog = true;
        },
        handleCloseModalCreateBookV2(is_show) {
            this.is_show_hide_dialog = is_show;
        },
        editItem(item) {
            this.item.id = item.id;
            this.item.code = item.code;
            this.item.name = item.name;
            this.is_show_hide_dialog = true;
        },
        resetItem() {
            this.item.id = -1;
            this.item.code = '';
            this.item.name = '';
        },
        async deleteBookStore(id) {
            try {
                // Trước khi xóa cần xác nhận
                if (!confirm('Bạn có chắc chắn muốn xóa không?')) {
                    return;
                }
                const { data, success } = await this.api_handler.delete(this.api.delete_book_store + '/' + id);
                if (success) {
                    this.$showMessage('success', 'Thành công', 'Xóa thành công');
                    this.fetchBookStores();
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            }
        },
        async toolSaveImport(){
            try {
                const { data, success } = await this.api_handler.post(this.api.tool_create_book_store, {}, { ...this.data_files });
                if (success) {
                    this.$showMessage('success', 'Thành công', 'Import thành công');
                    this.fetchBookStores();
                }
            } catch (error) {
                console.log(error);
                this.$showMessage('error', 'Lỗi', error);
            }
        },
        async handelSaveToolImport(data_files) {
            this.is_show_hide_import_tool = false;
            this.data_files = data_files;
            await this.toolSaveImport();
            // this.fetchBookStores();
        }
    },
    computed: {
        rows() {
            return this.book_stores.length;
        }
    }
}
</script>
<style lang="scss" scoped></style>
