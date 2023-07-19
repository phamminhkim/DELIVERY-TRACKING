<template>
    <CrudPage
    :structure="page_structure"
    ></CrudPage>
</template>

<script>
import Vue from 'vue';
import CrudPage from '../general/crudform/CrudPage.vue';
export default {
    name: 'SaleGroups',
    components: {
        Vue,
        CrudPage
    },
    data() {
        return {
            page_structure: {}
        }
    },
    created() {
        this.page_structure = {
            header: {
                title: "SaleGroups",
                title_icon: "fas fa-truck",
            },
            table: {
                table_fields: [
                    {
                        key: "code",
                        label: "Mã (code)",
                        sortable: true,
                        class: "text-center",
                    },
                    {
                        key: "name",
                        label: "Tên Sale Group",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                ],
                table_cells: [
                    //theo lý thuyết nên có đủ khai báo cho tất cả các cells
                    // {...},
                    // {...}
                ],
            },
            form: {
                unique_name: "sale-groups",
                form_name: "Sale Groups",
                form_fields: [
                    {
                        label: "Mã (code)",
                        placeholder: "Nhập mã..",
                        key: "code",
                        type: "text",
                        required: true,
                    },
                    {
                        label: "Tên sale group",
                        placeholder: "Nhập tên sale group..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                ],
            },
            api_url: "/api/master/sale-groups",
        };
    }

}
</script>
