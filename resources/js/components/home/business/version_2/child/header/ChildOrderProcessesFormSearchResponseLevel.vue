<template>
    <div>
        <div class="text-left mb-2">
            <button @click="" class="btn btn-warning bg-white text-warning btn-sm text-xs px-2 "><i
                    class="fas fa-search mr-2"></i>Tìm kiếm</button>
            <button @click="" class="btn btn-secondary bg-white text-secondary btn-sm text-xs px-2 "><i
                    class="fas fa-sync-alt mr-2"></i>Làm mới</button>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for=""
                            class="mb-0 px-2  span-start-date">Nhóm
                            KH</span></div>
                    <div>
                        <treeselect placeholder="Nhóm khách hàng" v-model="order.customer_group_ids"
                            :options="customer_groups" :multiple="true" class="text-xs mb-1" />
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
                            :min="order.start_date"></b-form-datepicker></div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Số PO</span>
                    </div>
                    <div class="flex-fill">
                        <input class="form-control form-control-sm text-xs" type="text" placeholder="Số PO">
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Số SO SAP</span>
                    </div>
                    <div class="flex-fill">
                        <input class="form-control form-control-sm text-xs" type="text" placeholder="Số SO SAP">
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Khách hàng</span>
                    </div>
                    <div class="flex-fill">
                        <treeselect placeholder="Khách hàng" v-model="order.customers" :options="customers"
                            :multiple="true" class="text-xs mb-1" />
                    </div>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right"><span for="" class="mb-0 px-2  span-start-date">Đến
                            ngày</span></div>
                    <div class="flex-fill"><b-form-datepicker v-model="order.end_date" class="text-xs mt-1"
                            :max="order.end_date"></b-form-datepicker></div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">PO MÃ SAP</span>
                    </div>
                    <div class="flex-fill">
                        <input class="form-control form-control-sm text-xs" type="text" placeholder="PO MÃ SAP">
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
            <div class="col-lg-5">
                <div class="d-flex">
                    <div class="flex-shrink-0 set-shrink text-right">
                        <span for="" class="mb-0 px-2  span-start-date">Người tạo</span>
                    </div>
                    <div class="flex-fill">
                        <treeselect placeholder="Người dùng" v-model="order.user_ids" :options="user_roles"
                            :multiple="true" class="text-xs mb-1" />
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
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
            order: {
                customer_group_ids: [],
                customers: [],
                user_ids: [],
                start_date: null,
                end_date: null,
            },
        }
    },
    created() {
        this.createdOneMonth();
    },
    methods: {
        createdOneMonth() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1);

            this.order.start_date = startDate;
            this.order.end_date = endDate;
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