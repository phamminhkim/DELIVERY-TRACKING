<template>
    <div>
        <b-table responsive small hover :fields="fields" :items="items" head-variant="light" :filter="query">
            <template #head(select)="data">
                <input type="checkbox" v-model="case_checkbox.select_all" @change="changeSelectAll()" />
            </template>
            <template #cell(select)="data">
                <input type="checkbox" v-model="case_checkbox.selected" :value="data.item" />
            </template>
            <template #cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template #cell(sap_so)="data">
                <a class="link-item" @click="getUrl(data.item)">{{ data.item.sap_so }}</a>
            </template>
            <template #cell(warehouse_code)="data">
                <input class="form-control form-control-sm border" v-model="data.item.warehouse_code" placeholder="Nhập mã kho" />
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
        query: String
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
            } else {
                this.case_checkbox.selected = [];
            }
        },
        getUrl(item) {
            const url = window.location.origin + this.$route.path + '#' + item.id + '?so_key=' + item.so_key;
            window.open(url, '_blank');
        },
    }
}
</script>
<style lang="scss" scoped></style>