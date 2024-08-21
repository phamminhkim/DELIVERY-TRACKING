<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesLoadingConvertFile" data-backdrop="static" data-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase">Convert File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pb-0">
                        <div class="form-group table-responsive">
                            <table class="table table-sm table-bordered  text-xs">
                                <thead>
                                    <tr class="font-weight-bold bg-gray-light">
                                        <td>STT</td>
                                        <td>Tên File</td>
                                        <td>Trạng thái</td>
                                        <td>Download file Excel</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(order, index) in api_data_orders" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <!-- <td>{{ order.order }}</td> -->
                                        <td>{{ order.name }}</td>
                                        <td>
                                            <span v-show="!order.success"
                                                class="text-danger font-italic font-weight-bold">Lỗi</span>
                                            <span v-show="order.success"
                                                class="text-success font-italic font-weight-bold">Thành công</span>
                                        </td>
                                        <td>
                                            <div v-if="!order.success">
                                                <button v-if="order.errors.csv_data[order.name] !== 0"
                                                    @click="downloadCsvItem(order.errors.csv_data[order.name], index, order.name)"
                                                    class="btn text-xs btn-sm btn-light  shadow-btn border">
                                                    <i class="fas fa-file-download mr-2"></i>Download
                                                </button>
                                                <p v-else>
                                                    <span class="text-danger font-italic font-weight-bold">Không hỗ
                                                        trợ</span>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group text-center">
                            <small>Đang xử lý file: {{ processing_index }}/{{ file_length }}</small>
                            <b-progress height="1rem" :max="max" show-progress :animated="value == 100 ? false : true"
                                variant="success">
                                <b-progress-bar :value="value" :label="`${value}%`"></b-progress-bar>
                            </b-progress>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-show="isShowBtnDataOrders() == true" @click="emitCreateDataOrders()" type="button"
                            class="btn btn-primary btn-sm text-xs px-2" data-dismiss="modal">Tạo mới</button>
                        <button v-show="isShowBtnDataOrders() == true" @click="emitMoreDataOrders()" type="button"
                            class="btn btn-info btn-sm text-xs px-2" data-dismiss="modal">Thêm dữ liệu</button>
                        <button type="button" class="btn btn-secondary btn-sm text-xs px-2"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        file_length: { type: Number, default: 0 },
        file: { type: Object, default: () => { } },
        processing_index: { type: Number, default: 0 },
        api_data_orders: { type: Array, default: () => [] },
        processing_files: { type: Array, default: () => [] },
        orders: { type: Array, default: () => [] },

    },
    data() {
        return {
            value: 0,
            max: 100,
            is_error: false,
        }
    },
    watch: {
        // file_length() {
        //     this.showModal();
        // },
        processing_index() {
            // if (this.processing_index == this.file_length) {
            //     this.hideModal();
            // }
            this.value = (this.processing_index / this.file_length).toFixed(2) * 100;
            if (this.value == this.max) {
                this.$emit('processingSuccess');
                this.autoLoadDataOrders();
                // this.is_error =  false; 
            }
        },

    },
    methods: {
        emitConvertFile(file) {
            this.$emit('convertFile', file);
        },
        hideModal() {
            $('#DialogOrderProcessesLoadingConvertFile').modal('hide');
        },
        showModal() {
            $('#DialogOrderProcessesLoadingConvertFile').modal('show');
        },
        downloadCsvItem(item, key, name) {
            const csvString = new Blob([`\uFEFF${item.join('\n')}`], { type: 'text/csv;charset=utf-8' });
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = name + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        },
        emitCreateDataOrders() {
            this.$emit('createDataOrders');
        },
        emitMoreDataOrders() {
            this.$emit('moreDataOrders');
        },
        isShowBtnDataOrders() {
            return this.value == this.max ? true : false;
        },
        autoLoadDataOrders() {
            if (this.orders.length == 0) {
                if (!this.isCheckSuccesssApiDataOrders()) {
                    // this.emitMoreDataOrders();
                    this.emitCreateDataOrders();
                    this.hideModal();
                } 
                // else {
                //     this.emitCreateDataOrders();
                // }
            
            }
        },
        isCheckSuccesssApiDataOrders() {
            this.is_error = false;
            if (!this.api_data_orders.every(item => item.success == true)) {
                this.is_error = true;
            }
            return this.is_error;
        }

    }
}
</script>
<style lang="scss" scoped></style>