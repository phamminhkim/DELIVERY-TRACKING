<template>
    <CRUDPage :structure="page_structure"></CRUDPage>
</template>

<script>
import Vue from 'vue';
import CRUDPage from './CRUDPage.vue';
export default {
    components: {
        Vue,
        CRUDPage,
    },
    data() {
        return {
            page_structure: {}
        }
    },
    created() {
        this.page_structure = {
            header: {
                title: 'Đối tác vận chuyển',
                title_icon: 'fas fa-truck'
            },
            table: {
                table_fields: [
                    {
                        key: "index",
                        label: "STT",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "code",
                        label: "Mã",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "name",
                        label: "Tên nhà vận chuyển",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "api_url",
                        label: "API Url",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "api_key",
                        label: "API Key",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "api_secret",
                        label: "API Secret",
                        sortable: true,
                        class: "text-nowrap",
                    },
                    {
                        key: "is_external",
                        label: "Phạm vị",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "is_active",
                        label: "Khả dụng",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "action",
                        label: "Hành động",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                ],
                table_cells: [

                ]
            },
            form: {
                form_name: 'đối tác vận chuyển',
                form_fields: [
                    {
                        label: 'Mã của nhà vận chuyển',
                        key: 'code',
                        type: 'text',
                        required: true,
                    },
                ]
            },
            api_url: '/api/master/delivery-partners'
        }
    }

}

</script>