<template>
    <CrudPage :structure="page_structure">
    
    </CrudPage>

</template>

<script>
import Vue from 'vue';
import CrudPage from '../general/crudform/CrudPage.vue';
export default {
    name: 'DistributionChannels',
    components: {
        Vue,
        CrudPage,
    },
    data() {
        return{
            page_structure: {},
        }
    },
    created(){
        this.page_structure = {
            header: {
                title: "Kênh phân phối",
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
                        label: "Tên kênh phân phối",
                        sortable: true,
                        class: "text-nowrap",
                    },
                ],
                table_cells: [
                    //theo lý thuyết nên có đủ khai báo cho tất cả các cells
                    // {...},
                    // {...}
                ],
            },
            form: {
                unique_name: "distribution-channels",
                form_name: "kênh phân phối",
                form_fields: [
                    {
                        label: "Mã (code)",
                        placeholder: "Nhập mã..",
                        key: "code",
                        type: "text",
                        required: true,
                    },
                    {
                        label: "Tên kênh phân phối",
                        placeholder: "Nhập tên kênh phân phối..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                ],
            },
            api_url: "/api/master/distribution-channels",
        };
    }


}
</script>