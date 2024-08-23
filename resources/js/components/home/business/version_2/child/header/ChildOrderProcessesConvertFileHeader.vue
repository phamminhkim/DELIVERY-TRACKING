<template>
    <div>
        <div class="form-group mb-0 d-flex align-items-center text-xs">
            <div class="mr-1">
                <div><small class="mb-0">Nhóm khách hàng</small><small class="text-danger ml-1">(*)</small></div>
                <select v-model="order.customer_group_id" aria-placeholder="Chọn nhóm khách hàng"
                    class="form-control form-control-sm text-xs" @change="emitInputCustomerGroupId($event)">
                    <option :value="-1">Chọn nhóm khách hàng</option>
                    <option v-for="(customer_group, index) in customer_groups" :key="index" :value="customer_group.id">
                        {{ customer_group.name }}</option>
                </select>
            </div>
            <div class="mr-1">
                <div><small class="mb-0">Cấu hình</small><small class="text-danger ml-1">(*)</small></div>
                <select v-model="selected_order.config_id" class="form-control form-control-sm text-xs"
                    aria-placeholder="Chọn cấu hình" @change="emitInputExtractConfigID($event)">
                    <option :value="-1">Chọn cấu hình</option>
                    <option v-for="(extract_config, index) in extract_order_configs" :key="index"
                        :value="extract_config.id">{{ extract_config.name }}</option>
                </select>
            </div>
            <div class="mr-1">
                <!-- <label class="mb-0">File</label><small class="text-danger ml-1">(*)</small> -->
                <div><small class="mb-0">File</small><small class="text-danger ml-1">(*)</small></div>
                <b-form-file @change="emitExtractFilePDF($event)" ref="file-input" plain multiple
                    browse-text="Chọn file" />
            </div>
        </div>

    </div>
</template>
<script>
export default {
    props: {
        file: { type: Object, default: () => { } },
        customer_groups: { type: Array, default: () => [] },
        order: { type: Object, default: () => { } },
        processing_success: { type: Number, default: 0 },
    },
    data() {
        return {
            selected_order: {
                customer_group_id: '',
                config_id: -1,
            }
        }
    },
    watch: {
        processing_success: function (val) {
            if (val) {
                this.$refs['file-input'].reset();
            }
        }
    },
    methods: {
        emitInputCustomerGroupId(event) {
            this.$emit('inputCustomerGroupId', event.target.value);
            if(this.extract_order_configs.length > 0) {
                this.selected_order.config_id = this.extract_order_configs[0].id;
            } else {
                this.selected_order.config_id = -1;
            }
        },
        emitInputExtractConfigID(event) {
            this.$emit('inputExtractConfigID', event.target.value);
           
        },
        emitExtractFilePDF(event) {
            this.$emit('extractFilePDF', event.target.files);
            // this.$refs['file-input'].reset();
        },
    },
    computed: {
        extract_order_configs() {
            var news = [];
            this.customer_groups.forEach((customer_group, index) => {
                customer_group.extract_order_configs.forEach((extract_order_config, index) => {
                    if (this.order.customer_group_id == extract_order_config.customer_group_id) {
                        news.push(extract_order_config)
                    }
                });
            });
            if (news.length > 0) {
                // this.selected_order.config_id = news[0].id;
                this.$emit('inputExtractConfigID', this.selected_order.config_id);
            } else {
                this.selected_order.config_id = -1;
                this.$emit('inputExtractConfigID', this.selected_order.config_id);
            }
            return news;
        }
    }
}
</script>
<style lang="scss" scoped></style>