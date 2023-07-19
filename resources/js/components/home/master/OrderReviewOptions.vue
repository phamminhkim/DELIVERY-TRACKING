<template>
    <CrudPage :structure="page_structure">

    </CrudPage>
</template>

<script>
import Vue from 'vue';
import CrudPage from '../general/crudform/CrudPage.vue';
export default {
    name: 'OrderReviewOptions',
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
                title: 'OrderReviewOptions',
                title_icon: 'fas fa-truck',
            },
            table: {
                table_fields: [
                    {
                        key: "name",
                        label: "Tên",
                        sortable: true,
                        class: "text-nowrap",
                    },
                ],
                table_cells: [

                ]
            },
            form: {
                unique_name: "order-review-options",
                form_name: "order review options",
                form_fields: [
                    {
                        label: "Tên",
                        placeholder: "Nhập tên ..",
                        key: "name",
                        type: "text", //html input type
                        required: true,
                    },
                ]
            },
            api_url: '/api/master/order-review-options'
        }
    }

}
</script>