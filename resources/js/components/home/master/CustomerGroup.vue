<template>
    <CrudPage
    :structure="page_structure"
    ></CrudPage>
</template>

<script>
import Vue from 'vue';
import CrudPage from '../general/crudform/CrudPage.vue';
export default {
    name: 'CustomerGroups',
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
                title: "Nhóm khách hàng",
                title_icon: "fas fa-truck",
            },
            table: {
                table_fields: [
                    {
                        key: "name",
                        label: "Tên nhóm",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "note",
                        label: "Ghi chú",
                        sortable: false,
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
                unique_name: "customer-groups",
                form_name: "Customer Groups",
                form_fields: [

                    {
                        label: "Tên nhóm",
                        placeholder: "Nhập tên nhóm..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                    {
                        label: "Cấu trúc Sap Note",
                        placeholder: "Nhập cấu trúc Sap Note..",
                        key: "sap_so_note_syntax",
                        type: "json",
                        required: false,
                    },
                    {
                        label: "Ghi chú",
                        placeholder: "Nhập ghi chú nếu có..",
                        key: "note",
                        type: "text",
                        required: false,
                    },
                ],
            },
            api_url: "/api/master/customer-groups",
        };
    }

}
</script>
