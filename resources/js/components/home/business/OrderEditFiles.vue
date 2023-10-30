<template>
    <div>
        <div class="container-fluid">
            <div class="card border" style="border-radius:10px">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-8">
                            <div class="text-xs">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6 p-0 text-lg-right">
                                                <label class="ml-lg-2 mr-lg-4">Nhóm khách hàng</label>
                                            </div>
                                            <div class="col-lg-6 p-0">
                                                <input class="form-control form-control-sm" placeholder="MapWin" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 ">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6 p-0 text-lg-right">
                                                <label class="ml-lg-2 mr-lg-4">Mã khách hàng SAP</label>
                                            </div>
                                            <div class="col-lg-6 p-0">
                                                <input class="form-control form-control-sm" placeholder="1000069" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs">
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6 p-0 text-lg-right">
                                                <label class="ml-lg-2 mr-lg-4">Số PO</label>
                                            </div>
                                            <div class="col-lg-6 p-0">
                                                <input class="form-control form-control-sm" placeholder="MapWin" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6 p-0 text-lg-right">
                                                <label class="ml-lg-2 mr-lg-4">Ngày đặt hàng</label>
                                            </div>
                                            <div class="col-lg-6 p-0">
                                                <input class="form-control form-control-sm" placeholder="1000069" />
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="form-group row align-items-center text-xs">
                                <div class="col-lg-3 p-0 text-lg-right">
                                    <label class="ml-lg-2 mr-lg-4">Địa chỉ giao</label>
                                </div>
                                <div class="col-lg-9 p-0">
                                    <input class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="form-group row align-items-center text-xs">
                                <div class="col-lg-3 p-0 text-lg-right">
                                    <label class="ml-lg-2 mr-lg-4">Ghi chú</label>
                                </div>
                                <div class="col-lg-9 p-0">
                                    <input class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group text-xs">
                                <div class="row align-items-center ">
                                    <div class="col-lg-6 p-0 text-lg-right">
                                        <label class="ml-lg-2 mr-lg-4">Người đặt hàng</label>
                                    </div>
                                    <div class="col-lg-6 p-0">
                                        <input class="form-control form-control-sm" placeholder="MapWin" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-xs">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 p-0 text-lg-right">
                                        <label class="ml-lg-2 mr-lg-4">Số điện thoại</label>
                                    </div>
                                    <div class="col-lg-6 p-0">
                                        <input class="form-control form-control-sm" placeholder="MapWin" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-xs">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 p-0 text-lg-right">
                                        <label class="ml-lg-2 mr-lg-4">Email</label>
                                    </div>
                                    <div class="col-lg-6 p-0">
                                        <input class="form-control form-control-sm" placeholder="MapWin" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-xs">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 p-0 text-lg-right">
                                        <label class="ml-lg-2 mr-lg-4">Ngày giao</label>
                                    </div>
                                    <div class="col-lg-6 p-0">
                                        <input class="form-control form-control-sm" placeholder="MapWin" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-info mr-2 px-4 mb-1">Thêm dòng mới</button>
                        <button class="btn btn-sm btn-info mr-2 px-4 mb-1">Lưu</button>
                        <button class="btn btn-sm btn-info mr-2 px-4 mb-1">Coppy</button>
                        <button class="btn btn-sm btn-secondary mr-2 px-4 mb-1">Download excel</button>
                        <button class="btn btn-sm btn-secondary px-4 mb-1">Đồng bộ SAP</button>
                    </div>
                    <div class="form-group">
                        <b-table :items="demo_items" :fields="fields" responsive striped hover :bordered="true" small head-variant="light">
                            <template #cell(index)="data">
                                <span> {{ data.index + 1 }} </span>
                            </template>
                            <template #cell(action)="data">
                                <button class="btn btn-sm btn-info px-4">Sửa</button>
                                <button class="btn btn-sm btn-danger px-4">Xóa</button>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "OrderEditFiles",
    data() {
        return {
            fields: [
                {
                    key: 'index',
                    label: '#',
                    class: 'text-nowrap'
                },
                {
                    key: 'sku',
                    label: 'Tên Skus-PO',
                    class: 'text-nowrap'
                },
                {
                    key: 'quantity',
                    label: 'Số lượng PO',
                    class: 'text-nowrap'
                },
                {
                    key: 'unit_po',
                    label: 'DVT - PO',
                    class: 'text-nowrap'
                },
                {
                    key: 'price',
                    label: 'Đơn giá',
                    class: 'text-nowrap'
                },
                {
                    key: 'total',
                    label: 'Thành tiền',
                    class: 'text-nowrap'
                },
                {
                    key: 'unit_sap',
                    label: 'Skus - SAP',
                    class: 'text-nowrap'
                },
                {
                    key: 'price_sap',
                    label: 'Tiền Skus - SAP',
                    class: 'text-nowrap'
                },
                {
                    key: 'quantity_sap',
                    label: 'Số lượng SAP',
                    class: 'text-nowrap'
                },
                {
                    key: 'unit_dvt',
                    label: 'DVT - SAP',
                    class: 'text-nowrap'
                },
                {
                    key: 'rate',
                    label: 'Tỷ lệ',
                    class: 'text-nowrap'
                },
                {
                    key: 'action',
                    label: 'Action',
                    class: 'text-nowrap'
                },
            ],
            demo_items: [
                {
                    id: 1,
                    sku: 'Sku 1',
                    quantity: '100',
                    unit_po: 'Cái',
                    price: '100.000',
                    total: '10.000.000',
                    unit_sap: 'Cái',
                    price_sap: '100.000',
                    quantity_sap: '100',
                    unit_dvt: 'Cái',
                    rate: '100%',
                },
                {
                    id: 2,
                    sku: 'Sku 2',
                    quantity: '200',
                    unit_po: 'Cái',
                    price: '200.000',
                    total: '20.000.000',
                    unit_sap: 'Cái',
                    price_sap: '200.000',
                    quantity_sap: '200',
                    unit_dvt: 'Cái',
                    rate: '10%',
                }

            ],
        }
    },
    methods: {
        //
    },
    mounted() {
        //
    }
}
</script>
<style lang="">

</style>
