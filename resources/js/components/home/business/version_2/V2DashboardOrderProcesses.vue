<template>
    <div class="text-xs">
        <!-- <div class="form-goup">
            <h6>Thống kê</h6>
        </div> -->
        <div class="mb-1">
            <div class="mb-1">
                <div class="row">
                    <div class="col-lg-4">
                        <button @click="btnCreateOneDayAgo()"
                            class="btn btn-light bg-white text-primary btn-sm text-xs">1 ngày trước</button>
                        <button @click="btnCreateSevenDayAgo()"
                            class="btn btn-light bg-white text-primary btn-sm text-xs">7 ngày trước</button>
                        <button @click="btnCreateThirtyDayAgo()"
                            class="btn btn-light bg-white text-primary btn-sm text-xs">30 ngày trước</button>
                        <button @click="btnCreateNinetyDayAgo()"
                            class="btn btn-light bg-white text-primary btn-sm text-xs">90 ngày trước</button>

                    </div>
                    <div class="col-lg-4">
                        <div class="text-center">
                            <button @click="btnCreateCurrentDate()"
                                class="btn btn-primary bg-white text-primary btn-sm text-xs">Hôm
                                nay</button>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="text-right">
                            <button @click="btnSearch()"
                                class="btn btn-warning bg-white text-warning btn-sm text-xs px-2 "><i
                                    class="fas fa-search mr-2"></i>Tìm kiếm</button>
                            <button @click="btnRefresh()"
                                class="btn btn-secondary bg-white text-secondary btn-sm text-xs px-2 "><i
                                    class="fas fa-sync-alt mr-2"></i>Làm mới</button>
                        </div>
                    </div>
                </div>

                <!-- <button @click="btnCreateOneYear()" class="btn btn-light bg-white text-primary btn-sm text-xs">1 Year</button> -->


            </div>
            <div class="mb-1 rounded p-2" style="background: rgba(30, 41, 59, 0.1);">
                <div class="row align-items-end">
                    <div class="col-lg-3">
                        <b-form-datepicker v-model="order.start_date" class="text-xs mb-1"
                            :max="order.end_date"></b-form-datepicker>
                        <b-form-datepicker v-model="order.end_date" class="text-xs"
                            :min="order.start_date"></b-form-datepicker>
                    </div>
                    <div class="col-lg-3">
                        <treeselect placeholder="Trạng thái đồng bộ" v-model="order.sync_sap_status"
                            :options="status_syncs" class="text-xs" :multiple="true" />
                    </div>
                    <div class="col-lg-6">
                        <treeselect placeholder="Nhóm khách hàng" v-model="order.customer_group_ids"
                            :options="customer_groups" :multiple="true" class="text-xs mb-1" />
                        <treeselect placeholder="Người dùng" v-model="order.user_ids" :options="user_roles"
                            :multiple="true" class="text-xs mb-1" />

                    </div>
                    <!-- <div class="col-lg-2 text-xs">
                    <treeselect placeholder="Trạng thái đồng bộ" v-model="order.sync_sap_status" :options="status_syncs"
                        class="text-xs" :multiple="true" />
                </div> -->

                </div>
            </div>
        </div>

        <div class="">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card bg-card-line shadow-lg border" style="background: rgba(51, 65, 85) !important;">
                        <div class="card-body p-0">
                            <ChildDashboardLineOrderProcesses :data_dashboard_line="data_dashboard_line" />
                            <div v-show="is_loading" class="text-center text-white icon-loading"><i
                                    class="fas fa-spinner fa-pulse"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
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
            <div class="row">
                <div class="col-lg-12 ml-auto mr-auto">
                    <div class="card  shadow-lg border">
                        <div class="card-body p-0 row">
                            <div class="col-lg-4 p-4">
                                <!-- <ChildDashboardPieOrderProcesses_copy /> -->
                                <ChildDashboardCardOrderProcesses :dashboard_shared="dashboard_shared" />
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group"
                                    style="background-image: linear-gradient(310deg, #141727 0%, #3A416F 100%);">
                                    <!-- <ChildDashboardPieOrderProcesses_copy /> -->
                                    <ChildDashboardBarOrderProcesses :data_dashboard_user="data_dashboard_user" />
                                    <div v-show="is_loading" class="text-center text-white  icon-loading"><i
                                            class="fas fa-spinner fa-pulse"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>
<script>
import ChildDashboardPieOrderProcesses from './child/body/ChildDashboardPieOrderProcesses.vue';
import ChildDashboardBarOrderProcesses from './child/body/ChildDashboardBarOrderProcesses.vue';
import ChildDashboardLineOrderProcesses from './child/body/ChildDashboardLineOrderProcesses.vue';
import ChildDashboardCardOrderProcesses from './child/body/ChildDashboardCardOrderProcesses.vue';
import ChildDashboardPieOrderProcesses_copy from './child/body/ChildDashboardPieOrderProcesses_copy.vue';
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
        Treeselect
    },
    data() {
        return {
            api_handler: new ApiHandler(window.Laravel.access_token),
            is_loading: false,
            status_syncs: [
                {
                    id: 1,
                    label: 'Đã đồng bộ',
                },
                {
                    id: 0,
                    label: 'Chưa đồng bộ',
                }
            ],
            order: {
                customer_group_ids: [],
                sync_sap_status: [],
                user_ids: [],
                start_date: new Date(),
                end_date: new Date(),
            },
            orderProcesses: [],
            customer_groups: [],
            user_roles: [],
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
                user_role: 'api/master/users/role',
            }
        }
    },
    async created() {
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
                    this.user_roles = this.forMatTreeSelect(data);
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
                    this.customer_groups = this.forMatTreeSelect(data);

                }
            } catch (error) {
                this.$showMessage('error', 'Lỗi', error);
            } finally {
                this.is_loading = false;
            }
        },
        forMatTreeSelect(data) {
            return data.map((item) => {
                return {
                    id: item.id,
                    label: item.name,
                    children: item.children ? this.forMatTreeSelect(item.children) : []
                }
            })
        },
        async createOrderCustomerGroup() {
            this.order.customer_group_ids = this.customer_groups.map((item) => item.id);
        },
        async createOrderStatusSync() {
            this.order.sync_sap_status = this.status_syncs.map((item) => item.id);
        },
        async createOrderUserRole() {
            this.order.user_ids = this.user_roles.map((item) => item.id);
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
        }
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
</style>