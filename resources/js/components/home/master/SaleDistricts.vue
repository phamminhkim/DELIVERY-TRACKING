<template>
    <CrudPage :structure="page_structure">
        <!-- <template #cell(is_active)="data">
            <span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
            <span class="badge bg-warning" v-else-if="data.item.is_active == 0">Ngưng hoạt động</span>
        </template> -->
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
                title: "Sale District",
                title_icon: "fas fa-user-circle",
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
                        label: "Tên Sale",
                        sortable: true,
                        class: "text-center",
                    },

                ],
                // table_cells: [
                //     //theo lý thuyết nên có đủ khai báo cho tất cả các cells
                //     // {...},
                //     // {...}
                //     {
                //         target_key: "is_active",
                //         type: "template", // 'text', 'bool', 'number', 'image', 'template'
                //     },
                //     {
                //         target_key: "image",
                //         type: "image",
                //     },
                // ],
            },
            form: {
                unique_name: "sale-districts",
                form_name: "Sale District",
                form_fields: [
                {
                    label: "Mã (code)",
                    placeholder: "Nhập mã (code)..",
                    key: "code",
                    type: "text", //html input type
                    required: true,
                },
                {
                    label: "Tên Sale",
                    placeholder: "Nhập tên Sale..",
                    key: "name",
                    type: "text", //html input type
                    required: true,
                },

                ],
            },
            api_url: "/api/master/sale-districts",
        };
    },
};
</script>
