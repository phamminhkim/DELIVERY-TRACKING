<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesConvertFile" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" :class="{
                        'bg-success': file.type == 'excel',
                        'bg-danger': file.type == 'pdf'
                    }">
                        <h5 class="modal-title">{{ file.note }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div>
                                    <label>Nhóm Khách Hàng</label><small class="text-danger ml-1">(*)</small>
                                    <select v-model="order.customer_group_id" aria-placeholder="Chọn nhóm khách hàng"
                                        class="form-control" @change="emitInputCustomerGroupId($event)">
                                        <option :value="-1">Chọn nhóm khách hàng</option>
                                        <option v-for="(customer_group, index) in customer_groups" :key="index"
                                            :value="customer_group.id">{{ customer_group.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <label>Cấu hình</label><small class="text-danger ml-1">(*)</small>
                                    <select class="form-control" aria-placeholder="Chọn cấu hình"
                                        @change="emitInputExtractConfigID($event)">
                                        <option :value="-1">Chọn cấu hình</option>
                                        <option v-for="(extract_config, index) in extract_order_configs" :key="index"
                                            :value="extract_config.id">{{ extract_config.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div>
                                    <label>File</label><small class="text-danger ml-1">(*)</small>
                                    <b-form-file @change="emitExtractFilePDF($event)" 
                                        ref="file-input" plain multiple browse-text="Chọn file" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        file: { type: Object, default: () => { } },
        customer_groups: { type: Array, default: () => [] },
        order: { type: Object, default: () => { } }
    },
    data() {
        return {
            processing_files: [
                {
                    id: 1,
                    name: 'Excel',
                    icon: 'fas fa-file-excel',
                    color: 'success',
                    note: 'Convert to Excel'
                },
                {
                    id: 2,
                    name: 'PDF',
                    icon: 'fas fa-file-pdf',
                    color: 'danger',
                    note: 'Convert to PDF'
                }
            ],
            selected_order: {
                customer_group_id: ''
            }
        }
    },
    methods: {
        emitInputCustomerGroupId(event) {
            this.$emit('inputCustomerGroupId', event.target.value);
        },
        emitInputExtractConfigID(event) {
            this.$emit('inputExtractConfigID', event.target.value);
        },
        emitExtractFilePDF(event) {
            this.$emit('extractFilePDF', event.target.files);
        }
    },
    computed: {
        extract_order_configs() {
            var news = [];
            this.customer_groups.forEach((customer_group, index) => {
                customer_group.extract_order_configs.forEach((extract_order_config, index) => {
                    if (this.file.type == extract_order_config.convert_file_type && this.order.customer_group_id == extract_order_config.customer_group_id) {
                        news.push(extract_order_config)
                    }
                });
            });
            return news;
        }
    }
}
</script>
<style lang="scss" scoped></style>