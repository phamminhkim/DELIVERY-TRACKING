<template>
    <CrudPage :structure="page_structure">
        <template #cell(is_active)="data">
            <span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
            <span class="badge bg-warning" v-else-if="data.item.is_active == 0">Ngưng hoạt động</span>
        </template>
    </CrudPage>
</template>

<script>
import CrudPage from "../general/crudform/CrudPage.vue";
import Vue from"vue";

export default {
    components: {
        CrudPage,
        Vue
    },
    data() {
        return {
            page_structure: {},
        };
    },
    created() {
        this.page_structure = {
            header: {
                title: "Danh sách nhà kho",
                title_icon: "fas fa-home",
            },
            table: {
                table_fields: [
                    {
                        key: "company_code",
                        label: "Mã công ty",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "code",
                        label: "Mã (code)",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "name",
                        label: "Tên Kho",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "is_active",
                        label: "Trạng thái",
                        sortable: true,
                        class: "text-nowrap",
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
                    // {
                    //     target_key: "image",
                    //     type: "image",
                    // },
                ],
            },
            form: {
                unique_name: "warehouses",
                form_name: "Danh sách nhà kho",
                form_fields: [
                {
                    label: "Mã (company)",
                    placeholder: "Nhập mã (company)..",
                    key: "company_code",
                    type: "text", //html input type
                    required: true,
                },
                {
                    label: "Mã (code)",
                    placeholder: "Nhập mã (code)..",
                    key: "code",
                    type: "text", //html input type
                    required: true,
                },
                {
                    label: "Tên kho",
                    placeholder: "Nhập tên kho..",
                    key: "name",
                    type: "text", //html input type
                    required: true,
                },

                ],
            },
            api_url: "/api/master/warehouses",
        };
    },
};
</script>
