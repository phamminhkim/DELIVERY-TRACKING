<template>
    <div>
        <div class="card shadow border-left-primary mb-1 border-0">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase text-primary font-weight-bold text-xs mb-1"><span>Người
                                dùng</span></div>
                        <div class="text-dark font-weight-bold h5 mb-0 box-shadow-primary"><span>{{
                                dashboard_shared.total_users }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow border-left-success mb-1 border-0">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase font-weight-bold text-xs mb-1 text-success"><span>Đơn
                                hàng đã đồng bộ</span></div>
                        <div class="text-dark font-weight-bold h5 mb-0 box-shadow-success"><span>{{
                            dashboard_shared.synced_orders
                                }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow border-left-warning mb-1 border-0">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase font-weight-bold text-xs mb-1 text-warning"><span>Đơn
                                hàng chưa đồng bộ</span></div>
                        <div class="text-dark font-weight-bold h5 mb-0 box-shadow-warning"><span>{{
                            dashboard_shared.unsynced_orders
                                }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow border-left-info mb-1 border-0">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-uppercase text-info font-weight-bold text-xs mb-1"><span>Tổng số
                                đơn hàng</span></div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-dark font-weight-bold h5 mb-0  box-shadow-info"><span>{{
                                    dashboard_shared.total_orders }}</span></div>
                            </div>
                        </div>
                    </div>
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

.border-left-primary {
    border-left: 0.25rem solid #007bff !important;
}

.box-shadow-success {
    // box-shadow: 0 0 1rem 0 rgba(40, 167, 69, 0.1) !important;
    box-shadow: inset 260px 0px 1px 11px whitesmoke;
}

.box-shadow-warning {
    // box-shadow: 0 0 1rem 0 rgba(255, 193, 7, 0.1) !important;
    box-shadow: inset 260px 0px 1px 11px whitesmoke;


}

.box-shadow-info {
    // box-shadow: 0 0 1rem 0 rgba(23, 162, 184, 0.1) !important;
    box-shadow: inset 260px 0px 1px 11px whitesmoke;


}

.box-shadow-primary {
    // box-shadow: 0 0 1rem 0 rgba(0, 123, 255, 0.1) !important;
    box-shadow: inset 260px 0px 1px 11px whitesmoke;


}
</style>