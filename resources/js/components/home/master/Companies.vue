<template>
    <CrudPage :structure="page_structure" primary_key="code">
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
    name: 'Companies',
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
                title: "Công ty",
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
                        label: "Tên công ty",
                        sortable: true,
                        class: "text-nowrap text-center",
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
                unique_name: "companies",
                form_name: "Công ty",
                form_fields: [
                    {
                        label: "Mã (code)",
                        placeholder: "Nhập mã..",
                        key: "code",
                        type: "text",
                        required: true,
                    },
                    {
                        label: "Tên công ty",
                        placeholder: "Nhập tên công ty..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                ],
            },
            api_url: "/api/master/companies",
        };
    }

}
</script>
