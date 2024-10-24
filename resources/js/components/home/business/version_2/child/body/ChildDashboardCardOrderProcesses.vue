<template>
    <div>
        <div class="card shadow border-left-success mb-1">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase font-weight-bold text-xs mb-1 text-success"><span>Đơn
                                hàng đã đồng bộ</span></div>
                        <div class="text-dark font-weight-bold h5 mb-0"><span>{{ dashboard_shared.synced_orders }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fab fa-first-order-alt  fa-2x text-black-50"></i></div>
                </div>
            </div>
        </div>
        <div class="card shadow border-left-warning mb-1">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase font-weight-bold text-xs mb-1 text-warning"><span>Đơn
                                hàng chưa đồng bộ</span></div>
                        <div class="text-dark font-weight-bold h5 mb-0"><span>{{ dashboard_shared.unsynced_orders }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dolly fa-2x text-black-50"></i></div>
                </div>
            </div>
        </div>
        <div class="card shadow border-left-info mb-1">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase text-info font-weight-bold text-xs mb-1"><span>Tổng số
                                đơn hàng</span></div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-dark font-weight-bold h5 mb-0 mr-3"><span>{{ dashboard_shared.total_orders }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-shipping-fast fa-2x text-black-50"></i></div>
                </div>
            </div>
        </div>
        <div class="card shadow border-left-warning mb-1">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase text-warning font-weight-bold text-xs mb-1"><span>Người
                                dùng</span></div>
                        <div class="text-dark font-weight-bold h5 mb-0"><span>{{ dashboard_shared.total_users }}</span></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-user fa-2x text-black-50"></i></div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
export default {
    props: {
        dashboard_shared: {
            type: Object,
        }
    },
    data() {
        return {

        }
    },
    methods: {

    },
}
</script>
<style lang="scss" scoped>
.border-left-success {
    border-left: 0.25rem solid #28a745 !important;
}

.border-left-warning {
    border-left: 0.25rem solid #ffc107 !important;
}

.border-left-info {
    border-left: 0.25rem solid #17a2b8 !important;
}
</style>