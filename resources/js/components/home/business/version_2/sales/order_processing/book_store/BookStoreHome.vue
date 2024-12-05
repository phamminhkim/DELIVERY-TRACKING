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
                            <button class="btn btn-sm btn-primary text-xs px-2"><i class="fas fa-plus mr-1"></i>Thêm
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
                        <b-table :fields="fields" :items="book_stores" :filter="filter" responsive hover small bordered>
                            <template #cell(index)="data">
                                {{ data.index + 1 }}
                            </template>
                            <template #cell(action)="data">
                                <button class="btn btn-sm btn-primary text-xs px-2"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger text-xs px-2"><i class="fas fa-trash"></i></button>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
        </div>
        <BookStoreImport :is_show="is_show_hide_import_tool" @close-modal="handleCloseModal"
            :example_datas="example_data_imports" />
    </div>
</template>
<script>
import ApiHandler from '../../../../../ApiHandler';
import BookStoreImport from './BookStoreImport.vue';
export default {
    components: {
        BookStoreImport
    },
    data() {
        return {
            is_loading: false,
            is_show_hide_import_tool: false,
            api_handler: new ApiHandler(window.Laravel.access_token),
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
            }
        }
    },
    created() {
        this.fetchBookStores();
    },
    methods: {
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
        }
    }
}
</script>
<style lang="scss" scoped></style>
