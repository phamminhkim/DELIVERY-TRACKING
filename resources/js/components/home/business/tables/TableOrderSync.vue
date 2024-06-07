<template>
    <div>
        <b-table responsive small hover :fields="fields" :items="items" head-variant="light"
            :current-page="current_page" :per-page="per_page" :filter="query">
            <template #head(select)="data">
                <input type="checkbox" v-model="case_checkbox.select_all" @change="changeSelectAll()" />
            </template>
            <template #cell(select)="data">
                <input type="checkbox" v-model="case_checkbox.selected" :value="data.item" />
            </template>
            <template #cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template #cell(sap_so_number)="data">
                <a class="link-item" @click="getUrl(data.item)">{{ data.item.sap_so_number }}</a>
            </template>
            <template #cell(warehouse_code)="data">
                <input class="form-control form-control-sm border" v-model="data.item.warehouse_id"
                    placeholder="Nhập mã kho" />
            </template>
        </b-table>
    </div>
</template>
<script>
export default {
    props: {
        use_component: 'DialogOrderSync',
        fields: Array,
        items: Array,
        query: String,
        current_page: Number,
        per_page: Number,
    },
    data() {
        return {
            case_checkbox: {
                selected: [],
                select_all: false
            }
        }
    },
    watch: {
        'case_checkbox.selected': function (val) {
            this.$emit('emitSelectedOrderSync', val);
            if (val.length === this.items.length) {
                this.case_checkbox.select_all = true;
            } else {
                this.case_checkbox.select_all = false;
            }
        }
    },
    methods: {
        changeSelectAll() {
            if (this.case_checkbox.select_all) {
                this.case_checkbox.selected = this.items;
                this.$emit('emitSelectedOrderSync', this.case_checkbox.selected);

            } else {
                this.case_checkbox.selected = [];
                this.$emit('emitSelectedOrderSync', this.case_checkbox.selected);
            }
        },
        getUrl(item) {
            let url = '';
            switch (this.use_component) {
                case 'OrderSyncSAP':
                    url = window.location.origin + '/sap-syncs-detail' + '#' + item.id + '?sap_so_number=' + item.sap_so_number;
                    break;
                default:
                    url = window.location.origin + '/sap-syncs-detail' + '#' + item.so_header_id + '?sap_so_number=' + item.sap_so_number;
                    break;
            }
            window.open(url, '_blank');

            // console.log(window.location.origin + '/sap-syncs-detail');
        },
    }
}
</script>
<style lang="scss" scoped></style>