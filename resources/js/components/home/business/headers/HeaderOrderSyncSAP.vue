<template>
    <div>
        <div class="row">
            <div class="col-lg-9">
                <div class="form-group">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-warning btn-sm" @click="is_show_search = !is_show_search"
                            v-b-toggle.collapse-1>
                            <span v-if="!is_show_search">Hiện tìm kiếm</span>
                            <span v-if="is_show_search">Ẩn tìm kiếm</span>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" @click="is_show_search = !is_show_search"
                            v-b-toggle.collapse-1>
                            <i v-if="is_show_search" class="fas fa-angle-up"></i>
                            <i v-else class="fas fa-angle-down"></i>
                        </button>
                    </div>
                    <button class="btn btn-secondary btn-sm ml-1">
                        <i class="fas fa-sync-alt" title="Tải lại"></i>
                    </button>
                </div>
            </div>
        </div>
        <b-collapse id="collapse-1">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Số SO SAP</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input v-model="case_filter.so_uid" class="form-control form-control-sm"
                                        name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Từ ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input v-model="case_filter.from_date" type="date"
                                        class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Đến ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input v-model="case_filter.to_date" type="date"
                                        class="form-control form-control-sm" name="" id="" />
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Số PO</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input v-model="case_filter.po_number" class="form-control form-control-sm" name=""
                                        id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Từ ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input type="date" class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Đến ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input type="date" class="form-control form-control-sm" name="" id="" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Mã KH</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input v-model="case_filter.customer_code" class="form-control form-control-sm"
                                        name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Tên KH</label>
                                <div class="col-lg-7 mt-1 mb-1">
                                    <input v-model="case_filter.customer_name" class="form-control form-control-sm"
                                        name="" id="" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Nhóm KH</label>
                                <div class="col-lg-11 mt-1 mb-1">
                                    <treeselect v-model="case_filter.customer_group_ids" :multiple="true"
                                        :options="case_data.customer_groups" placeholder="Chọn nhóm khách hàng">
                                    </treeselect>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                <div><button @click="emitFormFilterOrderSync()" type="button" class="btn btn-warning btn-sm mt-1 mr-2 mb-1">
                                        <i class="fa fa-search"></i>
                                        Tìm kiếm
                                    </button></div>
                                <div><button @click="resetFilter()" type="button" class="btn btn-secondary btn-sm mt-1 mb-1">
                                        <i class="fa fa-reset"></i>
                                        Xóa bộ lọc
                                    </button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </b-collapse>
    </div>
</template>
<script>
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import ApiHandler, { APIRequest } from '../../ApiHandler';
export default {
    components: {
        Treeselect
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_show_search: false,
            case_loading: {
                customer_groups: false,
            },
            case_filter: {
                from_date: '',
                to_date: '',
                so_uid: '',
                po_number: '',
                customer_name: '',
                customer_code: '',
                customer_group_ids: [],
            },
            case_data: {
                customer_groups: [],
            },
            case_api: {
                customer_groups: 'api/master/customer-groups'
            }
        }
    },
    created() {
        this.fetchCustomerGroup();
    },
    methods: {
        async fetchCustomerGroup() {
            try {
                this.case_loading.customer_groups = true;
                const { data } = await this.api_handler.get(this.case_api.customer_groups);
                if (Array.isArray(data)) {
                    this.case_data.customer_groups = data.map(item => {
                        return {
                            id: item.id,
                            label: item.name,
                        }
                    })
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.case_loading.customer_groups = false;
            }
        },
        emitFormFilterOrderSync() {
            this.$emit('emitFormFilterOrderSync', this.case_filter);
        },
        resetFilter() {
            this.case_filter = {
                from_date: '',
                to_date: '',
                so_uid: '',
                po_number: '',
                customer_name: '',
                customer_code: '',
                customer_group_ids: [],
            }
            this.emitFormFilterOrderSync();
        }
    },
}
</script>
<style lang="scss" scoped></style>