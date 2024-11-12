<template>
    <div class="text-xs">
        <!-- <div class="form-goup">
            <h6>Thống kê</h6>
        </div> -->
        <b-tabs content-class="mt-3 text-xs">
            <b-tab title="Dashboard" active>
                <div>
                    <div class="mb-1">
                        <div class="mb-1">
                            <div class="row">
                                <div class="col-lg-8">
                                    <button @click="btnCreateOneDayAgo()"
                                        class="btn btn-light bg-white text-primary btn-sm text-xs">1 ngày trước</button>
                                    <button @click="btnCreateSevenDayAgo()"
                                        class="btn btn-light bg-white text-primary btn-sm text-xs">7 ngày trước</button>
                                    <button @click="btnCreateThirtyDayAgo()"
                                        class="btn btn-light bg-white text-primary btn-sm text-xs">30 ngày
                                        trước</button>
                                    <button @click="btnCreateNinetyDayAgo()"
                                        class="btn btn-light bg-white text-primary btn-sm text-xs">90 ngày
                                        trước</button>
                                    <button @click="btnCreateOneHundredEightyDayAgo()"
                                        class="btn btn-light bg-white text-primary btn-sm text-xs">180 ngày
                                        trước</button>

                                </div>
                                <div class="col-lg-4">
                                    <div class="text-right">
                                        <button @click="btnCreateCurrentDate()"
                                            class="btn btn-primary px-4  btn-sm text-xs"><i
                                                class="fas fa-calendar-day mr-2"></i>Hôm
                                            nay</button>
                                    </div>
                                </div>

                            </div>

                            <!-- <button @click="btnCreateOneYear()" class="btn btn-light bg-white text-primary btn-sm text-xs">1 Year</button> -->


                        </div>
                        <div class="mb-1 rounded p-2" style="background: rgba(30, 41, 59, 0.1);">
                            <div class="text-left mb-2">
                                <button @click="btnSearch()"
                                    class="btn btn-warning bg-white text-warning btn-sm text-xs px-2 "><i
                                        class="fas fa-search mr-2"></i>Tìm kiếm</button>
                                <button @click="btnRefresh()"
                                    class="btn btn-secondary bg-white text-secondary btn-sm text-xs px-2 "><i
                                        class="fas fa-sync-alt mr-2"></i>Làm mới</button>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 set-shrink text-right"><span for=""
                                                class="mb-0 px-2  span-start-date">Từ
                                                ngày</span></div>
                                        <div class="flex-fill"><b-form-datepicker v-model="order.start_date"
                                                class="text-xs mt-1" :max="order.end_date"></b-form-datepicker></div>
                                    </div>

                                </div>
                                <div class="col-lg-9">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 set-shrink text-right"><span for=""
                                                class="mb-0 px-2 span-start-date">Nhóm KH</span></div>
                                        <div class="flex-fill">
                                            <treeselect placeholder="Nhóm khách hàng" v-model="order.customer_group_ids"
                                                :value-consists-of="'LEAF_PRIORITY'" :show-count="true"
                                                :options="customer_groups" :multiple="true" class="text-xs mb-1" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 set-shrink text-right">
                                            <span for="" class="mb-0 px-2  span-start-date">Đến
                                                ngày</span>
                                        </div>
                                        <div class="flex-fill">
                                            <b-form-datepicker v-model="order.end_date" class="text-xs mt-1"
                                                :min="order.start_date"></b-form-datepicker>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 set-shrink text-right"><span for=""
                                                class="mb-0 px-2 span-start-date">Trạng thái</span></div>
                                        <div class="flex-fill">
                                            <treeselect placeholder="Trạng thái đồng bộ" v-model="order.sync_sap_status"
                                             :value-consists-of="'LEAF_PRIORITY'" :show-count="true"
                                                :options="status_syncs" class="text-xs" :multiple="true" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 set-shrink text-right"><span for=""
                                                class="mb-0 px-2 span-start-date">Người dùng</span></div>
                                        <div class="flex-fill">
                                            <treeselect placeholder="Người dùng" v-model="order.user_ids"
                                                :value-consists-of="'LEAF_PRIORITY'" :options="user_roles"
                                                :show-count="true" :multiple="true" class="text-xs mb-1" />
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card  border mb-0">
                                    <!-- style="background: rgba(51, 65, 85) !important;" -->
                                    <div class="card-body p-0">
                                        <ChildDashboardLineOrderProcesses :data_dashboard_line="data_dashboard_line" />
                                        <div v-show="is_loading" class="text-center text-white icon-loading"><i
                                                class="fas fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 ">
                                <div class="h-100 bg-white">
                                    <!-- <ChildDashboardCardOrderProcesses /> -->
                                    <ChildDashboardPieOrderProcesses :data_dashboard_pie="data_dashboard_pie" />
                                    <div v-show="is_loading" class="text-center  icon-loading"><i
                                            class="fas fa-spinner fa-pulse"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="card  shadow-lg border">
                            <div class="card-body p-0 row">
                                <div class="col-lg-4 px-4 py-1">
                                    <!-- <ChildDashboardPieOrderProcesses_copy /> -->
                                    <ChildDashboardCardOrderProcesses :dashboard_shared="dashboard_shared" />
                                    <div v-show="is_loading" class="text-center text-black  icon-loading"><i
                                            class="fas fa-spinner fa-pulse"></i>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="rounded h-100">
                                        <!-- style="background: rgb(51, 65, 85,0.91);" -->
                                        <!-- <ChildDashboardPieOrderProcesses_copy /> -->
                                        <ChildDashboardBarOrderProcesses :data_dashboard_user="data_dashboard_user" />
                                        <div v-show="is_loading" class="text-center text-warning  icon-loading"><i
                                                class="fas fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-tab>
            <b-tab title="Báo cáo so sánh mức độ đáp ứng đơn hàng MT">
                <div class="text-xs ">
                    <!-- <h6>MỨC ĐỘ ĐÁP ỨNG ĐƠN HÀNG KÊNH MT</h6>
                    <div class="mb-1">
                        <button class="btn btn-sm text-xs btn-success bg-white text-success px-4"><i
                                class="fas fa-file-export mr-2"></i>Xuất báo cáo</button>
                    </div> -->
                    <div class="form-search mb-1 p-2 rounded" style="background: rgba(30, 41, 59, 0.1);">
                        <ChildOrderProcessesFormSearchResponseLevel @search-change="onSearchChange"
                            @start-date="handleStartDate" @end-date="handleEndDate" @search="handleSearch"
                            :customer_groups="customer_groups" :customers="customers" :user_roles="user_roles" />
                    </div>
                    <div class="form-group bg-white">
                        <ChildDashboardResponseLevelOrderProcesses ref="ChildDashboardResponseLevelOrderProcesses" :search_change="search_change" :change_search="change_search" :start_date="start_date" :end_date="end_date" />
                    </div>
                </div>
            </b-tab>

        </b-tabs>



    </div>
</template>
<script>
import ChildDashboardPieOrderProcesses from './child/body/ChildDashboardPieOrderProcesses.vue';
import ChildDashboardBarOrderProcesses from './child/body/ChildDashboardBarOrderProcesses.vue';
import ChildDashboardLineOrderProcesses from './child/body/ChildDashboardLineOrderProcesses.vue';
import ChildDashboardCardOrderProcesses from './child/body/ChildDashboardCardOrderProcesses.vue';
import ChildDashboardPieOrderProcesses_copy from './child/body/ChildDashboardPieOrderProcesses_copy.vue';
import ChildDashboardResponseLevelOrderProcesses from './child/body/ChildDashboardResponseLevelOrderProcesses.vue';
import ChildOrderProcessesFormSearchResponseLevel from './child/header/ChildOrderProcessesFormSearchResponseLevel.vue';
import ApiHandler, { APIRequest } from '../../ApiHandler';
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import { create } from 'lodash';


export default {
    components: {
        ChildDashboardPieOrderProcesses,
        ChildDashboardBarOrderProcesses,
        ChildDashboardLineOrderProcesses,
        ChildDashboardCardOrderProcesses,
        ChildDashboardPieOrderProcesses_copy,
        ChildDashboardResponseLevelOrderProcesses,
        ChildOrderProcessesFormSearchResponseLevel,
        Treeselect
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            search_change: '',
            change_search: 0,
            start_date: new Date(),
            end_date: new Date(),
            customers: [
                {
                    id: 1,
                    label: 'Khách hàng 1 - 330942',
                },
                {
                    id: 2,
                    label: 'Khách hàng 2 - 330943',
                },
                {
                    id: 3,
                    label: 'Khách hàng 3 - 330944',
                },
                {
                    id: 4,
                    label: 'Khách hàng 4 - 330945',
                },
            ],
            status_syncs: [
                {
                    id: 'status_syncs',
                    label: 'Tất cả',
                    children: [
                        {
                            id: 1,
                            label: 'Đã đồng bộ',
                        },
                        {
                            id: 0,
                            label: 'Chưa đồng bộ',
                        }
                    ]
                }
            ],
            order: {
                customer_group_ids: [],
                sync_sap_status: [],
                user_ids: [],
                start_date: new Date(),
                end_date: new Date(),
                customers: []
            },
            orderProcesses: [],
            customer_groups: [],
            user_roles: [],
            reportes: [],
            dashboard_shared: {
                synced_orders: 0,
                total_orders: 0,
                total_users: 0,
                unsynced_orders: 0
            },
            data_dashboard_line: {
                labels: [],
                datasets: []
            },
            data_dashboard_pie: {
                labels: [],
                datasets: []
            },
            data_dashboard_user: {
                users: [],
                unsynced_orders: [],
                total_orders: [],
                synced_orders: []
            },
            url_api: {
                customer_groups: 'api/master/customer-groups',
                dashboard_date: 'api/dashboard/MT/date',
                dashboard_group: 'api/dashboard/MT/group',
                dashboard_user: 'api/dashboard/MT/user',
                dashboard_shared: 'api/dashboard/MT',
                dashboard_report: 'api/dashboard/MT/report',
                user_role: 'api/master/users/role',
            }
        }
    },
    async created() {
        this.createdTwoWeek();
        await this.fetchUserRole();
        await this.fetchCustomerGroup();
        await this.createOrderCustomerGroup();
        await this.createOrderStatusSync();
        await this.createOrderUserRole();
        await this.fetchDashboardLine();
        await this.fetchDashboardPie();
        await this.fetchDashboardUser();
        await this.fetchDashboardShared();

    },
    methods: {

        async fetchDashboardShared() {
            try {
                this.is_loading = true;
                const body = {
                    from_date: this.order.start_date,
                    to_date: this.order.end_date,
                    customer_group_ids: this.order.customer_group_ids,
                    user_ids: this.order.user_ids,
                    sync_sap_status: this.order.sync_sap_status
                }
                const { data, success } = await this.api_handler.get(this.url_api.dashboard_shared, body);
                if (success) {
                    this.dashboard_shared = data;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchUserRole() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.url_api.user_role);
                if (Array.isArray(data)) {
                    // this.user_roles = this.forMatTreeSelect(data);
                    this.user_roles = this.parentTreeSelect(data, 'user_role', 'Tất cả');
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchDashboardUser() {
            try {
                this.is_loading = true;
                const body = {
                    from_date: this.order.start_date,
                    to_date: this.order.end_date,
                    customer_group_ids: this.order.customer_group_ids,
                    user_ids: this.order.user_ids,
                    sync_sap_status: this.order.sync_sap_status
                }
                const { data, success } = await this.api_handler.get(this.url_api.dashboard_user, body);
                if (success) {
                    this.data_dashboard_user = data
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchDashboardPie() {
            try {
                this.is_loading = true;
                const body = {
                    from_date: this.order.start_date,
                    to_date: this.order.end_date,
                    customer_group_ids: this.order.customer_group_ids,
                    user_ids: this.order.user_ids,
                    sync_sap_status: this.order.sync_sap_status
                }
                const { data, success } = await this.api_handler.get(this.url_api.dashboard_group, body);
                if (success) {
                    this.data_dashboard_pie.labels = data.group;
                    this.data_dashboard_pie.datasets = data.total;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchDashboardLine() {
            try {
                this.is_loading = true;
                const body = {
                    from_date: this.order.start_date,
                    to_date: this.order.end_date,
                    customer_group_ids: this.order.customer_group_ids,
                    user_ids: this.order.user_ids,
                    sync_sap_status: this.order.sync_sap_status
                }
                const { data, success } = await this.api_handler.get(this.url_api.dashboard_date, body);
                if (success) {
                    this.data_dashboard_line.labels = data.date;
                    this.data_dashboard_line.datasets = data.total;
                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        async fetchCustomerGroup() {
            try {
                this.is_loading = true;
                const { data } = await this.api_handler.get(this.url_api.customer_groups);
                if (Array.isArray(data)) {
                    this.customer_groups = this.parentTreeSelect(data, 'customer_group', 'Tất cả');

                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        parentTreeSelect(data, id_string, title) {
            return [
                {
                    id: id_string,
                    label: title,
                    children: this.forMatTreeSelect(data)
                }
            ]

        },
        forMatTreeSelect(data) {
            return data.map((item) => {
                return {
                    id: item.id,
                    label: item.name,
                    // children: item.children ? this.forMatTreeSelect(item.children) : []
                }
            })
        },
        async createOrderCustomerGroup() {
            this.order.customer_group_ids = this.customer_groups.map((item) => item.children.map((item) => item.id)).flat();

        },
        async createOrderStatusSync() {
            this.order.sync_sap_status = this.status_syncs.map((item) => item.children.map((item) => item.id)).flat();

        },
        async createOrderUserRole() {
            this.order.user_ids = this.user_roles.map((item) => item.children.map((item) => item.id)).flat();
        },
        async btnCreateOneDayAgo() {
            var curr = new Date;
            curr.setDate(curr.getDate() - 1);
            this.order.start_date = curr;
            this.order.end_date = new Date();
            await this.fetchDashboard();
        },
        async btnCreateSevenDayAgo() {
            var curr = new Date;
            curr.setDate(curr.getDate() - 7);
            this.order.start_date = curr;
            this.order.end_date = new Date();
            await this.fetchDashboard();
        },
        async btnCreateThirtyDayAgo() {
            var curr = new Date;
            curr.setDate(curr.getDate() - 30);
            this.order.start_date = curr;
            this.order.end_date = new Date();
            await this.fetchDashboard();
        },
        async btnCreateNinetyDayAgo() {
            var curr = new Date;
            curr.setDate(curr.getDate() - 90);
            this.order.start_date = curr;
            this.order.end_date = new Date();
            await this.fetchDashboard();
        },
        async btnCreateOneHundredEightyDayAgo() {
            var curr = new Date;
            curr.setDate(curr.getDate() - 180);
            this.order.start_date = curr;
            this.order.end_date = new Date();
            await this.fetchDashboard();
        },
        async btnCreateOneYear() {
            var curr = new Date;
            var first = new Date(curr.getFullYear(), 0, 1);
            var last = new Date(curr.getFullYear(), 11, 31);
            this.order.start_date = first;
            this.order.end_date = last;
            await this.fetchDashboard();
        },
        async btnCreateCurrentDate() {
            this.order.start_date = new Date();
            this.order.end_date = new Date();
            await this.fetchDashboard();
        },
        async btnSearch() {
            await this.fetchDashboard();
        },
        async btnRefresh() {
            await this.resetItem();
            await this.fetchDashboard();
        },
        async fetchDashboard() {
            await this.fetchDashboardLine();
            await this.fetchDashboardPie();
            await this.fetchDashboardUser();
            await this.fetchDashboardShared();
        },
        async resetItem() {
            this.order = {
                customer_group_ids: [],
                sync_sap_status: [],
                user_ids: [],
                start_date: new Date(),
                end_date: new Date(),
            }
        },
        onSearchChange(text) {
            this.search_change = text;
        },
        async handleSearch(search){
            await this.$refs.ChildDashboardResponseLevelOrderProcesses.setOrder(search);
            await this.change_search++;
        },
        handleStartDate(date) {
            this.start_date = date;
        },
        handleEndDate(date) {
            this.end_date = date;
        },
        createdTwoWeek() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 14);

            this.start_date = startDate;
            this.end_date = endDate;
        },

    },

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