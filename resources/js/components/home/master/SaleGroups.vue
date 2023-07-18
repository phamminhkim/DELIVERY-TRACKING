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
                        class: "text-nowrap",
                    },
                    {
                        key: "name",
                        label: "Tên SaleGroups",
                        sortable: true,
                        class: "text-nowrap",
                    },
                ],
                table_cells: [
                    //theo lý thuyết nên có đủ khai báo cho tất cả các cells
                    // {...},
                    // {...}
                    {
                        target_key: "active",
                        type: "template", // 'text', 'bool', 'number', 'image', 'template'
                    },
                    {
                        target_key: "image",
                        type: "image",
                    },
                ],
            },
            form: {
                unique_name: "customers",
                form_name: "khách hàng",
                form_fields: [
                    {
                        label: "Tên người dùng",
                        placeholder: "Nhập tên người dùng..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                    {
                        label: "Email",
                        placeholder: "Nhập email..",
                        key: "email",
                        type: "email",
                        required: false,
                    },
                    {
                        label: "Số điện thoại",
                        placeholder: "Nhập số điện thoại..",
                        key: "phone_number",
                        type: "text",
                        required: false,
                    },
                ],
            },
            api_url: "/api/master/sale_groups",
        };
    }

}
</script>