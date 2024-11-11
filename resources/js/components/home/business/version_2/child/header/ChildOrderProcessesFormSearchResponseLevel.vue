<template>
    <div>
        <div class="text-left mb-2">
            <button @click="handleSearch()" class="btn btn-warning bg-white text-warning btn-sm text-xs px-2 "><i
                    class="fas fa-search mr-2"></i>Tìm kiếm</button>
            <button @click="" class="btn btn-secondary bg-white text-secondary btn-sm text-xs px-2 "><i
                    class="fas fa-sync-alt mr-2"></i>Làm mới</button>
        </div>
        <div class="row">
            <div class="col-lg-7">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for=""
                            class="mb-0 px-2  span-start-date">Nhóm
                            KH</span></div>
                    <div>
                        <treeselect placeholder="Nhóm khách hàng" v-model="order.customer_group_ids" :show-count="true"
                            :value-consists-of="'LEAF_PRIORITY'" :options="customer_groups" :multiple="true"
                            class="text-xs mb-1" />
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Người tạo</span>
                    </div>
                    <div class="flex-fill">
                        <treeselect placeholder="Người tạo" v-model="order.created_bys" :options="user_roles"
                            :multiple="true" class="text-xs mb-1" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-lg-3">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for="" class="mb-0 px-2  span-start-date">Mã
                            KH</span>
                    </div>
                    <div class="flex-fill">
                        <input v-model="order.customer_code" class="form-control form-control-sm text-xs" type="text" placeholder="Mã KH">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for="" class="mb-0 px-2  span-start-date">Tên
                            KH</span>
                    </div>
                    <div class="flex-fill">
                        <input v-model="order.customer_name" class="form-control form-control-sm text-xs" type="text" placeholder="Tên KH">
                    </div>
                </div>
            </div>
           
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for=""
                            class="mb-0 px-2 flex-shrink-0 set-shrink text-right span-start-date">Từ
                            ngày</span></div>
                    <div class="flex-fill"><b-form-datepicker v-model="order.start_date" class="text-xs"
                            @input="handleStartDate()"
                            :max="order.end_date"></b-form-datepicker></div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Số PO</span>
                    </div>
                    <div class="flex-fill">
                        <input v-model="order.po_number" class="form-control form-control-sm text-xs" type="text" placeholder="Số PO">
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Số SO SAP</span>
                    </div>
                    <div class="flex-fill">
                        <input v-model="order.so_uid" class="form-control form-control-sm text-xs" type="text" placeholder="Số SO SAP">
                    </div>
                </div>
            </div>
            <!-- <div class="col-lg-5">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Khách hàng</span>
                    </div>
                    <div class="flex-fill">
                        <treeselect placeholder="Khách hàng" v-model="order.customers" :options="customer_partners"
                            @input="handleIPutCustomerPartner" @search-change="onSearchChange" :multiple="true"
                            class="text-xs mb-1" />
                    </div>
                </div>

            </div> -->

        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for="" class="mb-0 px-2  span-start-date">Đến
                            ngày</span></div>
                    <div class="flex-fill"><b-form-datepicker v-model="order.end_date" class="text-xs mt-1"
                        @input="handleEndDate()"
                            :min="order.start_date"></b-form-datepicker></div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">PO MÃ SAP</span>
                    </div>
                    <div class="flex-fill">
                        <input v-model="order.sap_code" class="form-control form-control-sm text-xs" type="text" placeholder="PO MÃ SAP">
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">SO MÃ SAP</span>
                    </div>
                    <div class="flex-fill">
                        <input class="form-control form-control-sm text-xs" type="text" placeholder="SO MÃ SAP">
                    </div>
                </div>
            </div>
          
        </div>
    </div>
</template>
<script>
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import ApiHandler from '../../../../ApiHandler';
export default {
    components: {
        Treeselect
    },
    props: {
        customer_groups: { type: Array, default: () => [] },
        customers: { type: Array, default: () => [] },
        user_roles: { type: Array, default: () => [] },
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            order: {
                customer_group_ids: [],
                customers: [],
                created_bys: [],
                start_date: null,
                end_date: null,
                po_number: '',
                so_uid: '',
                sap_code: '',
                customer_code: '',
                customer_name: '',
                created_by: -1,
            },
            searchText: '',
            searchTimeout: null, // Biến lưu trữ timeout
            customer_partners: [],
            url_api: {
                dashboard_report: 'api/dashboard/MT/report',
                customer_partners: 'api/master/customer-partners',
            },
        }
    },
    async created() {
        this.createdTwoWeek();
        await this.fetchCustomerPartner();
    },
    methods: {
        createdTwoWeek() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 14);

            this.order.start_date = startDate;
            this.order.end_date = endDate;
            this.handleStartDate();
            this.handleEndDate();
        },
        handleIPutCustomerPartner(state, value) {
            console.log('event', state, value);
        },
        onSearchChange(text) {
            // setTimeOut
            // Xóa timeout trước đó nếu có
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            // Thiết lập timeout mới để trì hoãn xử lý
            this.searchTimeout = setTimeout(() => {
                this.searchText = text;
                console.log('search', this.searchText);
                this.$emit('search-change', this.searchText);
                this.fetchCustomerPartner();
            }, 800); // Điều chỉnh thời gian (ms) phù hợp

            // tôi sẽ gửi request sau 800ms kể từ lần cuối cùng người dùng nhập


        },
        async fetchCustomerPartner() {
            try {
                this.is_loading = true;
                const body = {
                    // from_date: this.order.start_date,
                    // to_date: this.order.end_date,
                    // customer_group_ids: this.order.customer_group_ids,
                    // created_bys: this.order.created_bys,
                    search: this.searchText,
                    per_page: 100,
                }
                const { data, success } = await this.api_handler.get(this.url_api.customer_partners, body);
                if (success) {
                    this.customer_partners = this.mapTreeSelect(data.data);
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        mapTreeSelect(data) {
            return data.map(item => {
                return {
                    id: item.id,
                    label: item.name + ' (' + item.code + ')',
                }
            });
        },
        handleStartDate(){
            this.$emit('start-date', this.order.start_date)
        },
        handleEndDate(){
            this.$emit('end-date', this.order.end_date)
        },
        handleSearch(){
            this.$emit('search')

        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped>
.bg-card-line {
    // background: linear-gradient(to right, #bbd2c587, rgb(83 105 118 / 18%));
    background: linear-gradient(to right, #0099f700, #f1171200);
}

.icon-loading {
    position: absolute;
    top: 0;
    width: 100%;
    font-size: 42px;
    top: 42%;
}

.span-start-date {
    font-size: 10px;
    color: blue;
    border-bottom: 2px solid white;
}

.set-shrink {
    width: 6em;
}
</style>