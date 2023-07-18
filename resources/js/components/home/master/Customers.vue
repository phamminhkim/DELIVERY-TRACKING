<template>
    <CrudPage :structure="page_structure">
        <template #cell(is_active)="data">
            <span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
            <span class="badge bg-warning" v-else-if="data.item.is_active == 0">Ngưng hoạt động</span>
        </template>
    </CrudPage>
</template>

<script>
import Vue from 'vue';
import CrudPage from "../general/crudform/CrudPage.vue";
export default {
    name: 'Customers',
    components: {
        Vue,
        CrudPage,
    },
    data() {
        return {
            page_structure: {},
        };
    },
    created() {
        this.page_structure = {
            header: {
                title: "Khách hàng",
                title_icon: "fas fa-truck",
            },
            table: {
                table_fields: [
                    {
                        key: "code",
                        label: "Mã (code)",
                        sortable: true,
                        class: "text-nowrap"
                    },
                    {
                        key: "name",
                        label: "Tên khách hàng",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "email",
                        label: "Email",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "phone_number",
                        label: "Số điện thoại",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "address",
                        label: "Địa chỉ",
                        sortable: true,
                        class: "text-nowrap"
                    },
                    {
                        key: "is_active",
                        label: "Khả dụng",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                ],
                table_cells: [
                    //theo lý thuyết nên có đủ khai báo cho tất cả các cells
                    // {...},
                    // {...}
                    {
                        target_key: "is_active",
                        type: "template", // 'text', 'bool', 'number', 'image', 'template'
                    },
                ],
            },
            form: {
                unique_name: "customers",
                form_name: "khách hàng",
                form_fields: [
                    {
                        label: "Mã (code)",
                        placeholder: "Nhập mã (code)..",
                        key: "code",
                        type: "text",
                        required: true
                    },
                    {
                        label: "Tên khách hàng",
                        placeholder: "Nhập tên khách hàng..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                    {
                        label: "Email",
                        placeholder: "Nhập email..",
                        key: "email",
                        type: "email",
                        required: true,
                    },
                    {
                        label: "Số điện thoại",
                        placeholder: "Nhập số điện thoại..",
                        key: "phone_number",
                        type: "text",
                        required: true,
                    },
                    {
                        label: "Địa chỉ",
                        placeholder: "Nhập địa chỉ..",
                        key: "address",
                        type: "text",
                        required: true
                    }
                ],
            },
            api_url: "/api/master/customers",
        };
    },
};
</script>
