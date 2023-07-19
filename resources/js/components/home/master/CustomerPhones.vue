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
import CrudPage from '../general/crudform/CrudPage.vue';
export default {
    name: 'CustomerPhones',
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
                title: 'Số điện thoại khách hàng',
                title_icon: 'fas fa-truck'
            },
            table: {
                table_fields: [
                    {
                        key: "customer_id",
                        label: "ID khách hàng",
                        sortable: true,
                        class: "text-nowrap"
                    },
                    {
                        key: "phone_number",
                        label: "Số điện thoại",
                        sortable: true,
                        class: "text-nowrap"
                    },
                    {
                        key: "name",
                        label: "Tên gợi nhớ",
                        sortable: true,
                        class: "text-nowrap"
                    },
                    {
                        key: "description",
                        label: "Mô tả",
                        sortable: true,
                        class: "text-nowrap"
                    },
                    {
                        key: "is_active",
                        label: "Trạng thái",
                        sortable: true,
                        class: "text-nowrap text-center"
                    },
                ],
                table_cells: [
                    {
                        target_key: "is_active",
                        type: "template", // 'text', 'bool', 'number', 'image', 'template'
                    },
                ]
            },
            form: {
                unique_name: "customer-phones",
                form_name: "số điện thoại khách hàng",
                form_fields: [
                    {
                        label: "ID khách hàng",
                        placeholder: "Nhập ID khách hàng..",
                        key: "customer_id",
                        type: "text",
                        required: true
                    },
                    {
                        label: "Số điện thoại",
                        placeholder: "Nhập số điện thoại..",
                        key: "phone_number",
                        type: "number",
                        required: true
                    },
                    {
                        label: "Tên gợi nhớ",
                        placeholder: "Nhập tên gợi nhớ..",
                        key: "name",
                        type: "text",
                        required: true
                    },
                    {
                        label: "Mô tả",
                        placeholder: "Nhập mô tả..",
                        key: "description",
                        type: "text",
                        required: true
                    },
                ]
            },
            api_url: '/api/master/customer-phones'
        }
    }
}
</script>