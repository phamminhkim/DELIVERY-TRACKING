<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesNotiCheckSkuSapCode" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-uppercase ">Đồng bộ sap</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group text-xs">
                            <label class="text-xs text-danger">Phát hiện MaSap trống:</label>
                            <table class="table table-responsive text-xs table-bordered table-sm text-nowrap" style="height:450px">
                                <thead>
                                    <tr class="bg-white th-header">
                                        <th>STT</th>
                                        <th>Dòng</th>
                                        <th>MaKh_Key</th>
                                        <th>MaKh</th>
                                        <th>MaSap</th>
                                        <th>Unit_barcode</th>
                                        <th>Unit_barcode_description</th>
                                        <th>Ghi chú 1</th>
                                        <th>level2</th>
                                        <th>level3</th>
                                        <th>level4</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in item_sku_sap_code_nulls" :key="i">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ item.order }}</td>
                                        <td>{{ item.customer_name }}</td>
                                        <td>{{ item.customer_code }}</td>
                                        <td>{{ item.sku_sap_code }}</td>
                                        <td :style="{
                                            'background': item.theme_color.background.customer_sku_code,
                                            'color': item.theme_color.text.customer_sku_code
                                        }">{{ item.customer_sku_code }}</td>
                                        <td>{{ item.customer_sku_name }}</td>
                                        <td>{{ item.note1 }}</td>
                                        <td>{{ item.level2 }}</td>
                                        <td>{{ item.level3 }}</td>
                                        <td>{{ item.level4 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <div class="form-group text-center">
                            <div>
                                <label for="exampleFormControlTextarea1">Bạn có muốn đồng bộ SAP ?</label>
                            </div>
                            <button type="button" @click="submitOrderSyncSap()" class="btn btn-success btn-sm px-2">Yes</button>                        
                            <button type="button" class="btn btn-secondary btn-sm px-2" data-dismiss="modal">No</button>
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
        item_sku_sap_code_nulls: { type: Array, default: () => [] },
    },
    data() {
        return {

        }
    },
    methods: {
        submitOrderSyncSap() {
            this.$emit('submitOrderSyncSap');
            $('#DialogOrderProcessesNotiCheckSkuSapCode').modal('hide');
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped>
.th-header{
    position: sticky;
    top: -1px;
    z-index: 1;
}
</style>