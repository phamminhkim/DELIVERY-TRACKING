<template>
    <CRUDPage :structure="page_structure">
        <template #cell(active)="data">
            <span class="badge bg-success" v-if="data.item.active == 1">Đang hoạt động</span>
            <span class="badge bg-warning" v-if="data.item.active == 0">Ngưng hoạt động</span>
        </template>
    </CRUDPage>
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
                title: 'Khách hàng',
                title_icon: 'fas fa-truck'
            },
            table: {
                table_fields: [
                    {
                        key: "name",
                        label: "Tên người dùng",
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
                        key: "active",
                        label: "Khả dụng",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "image",
                        label: "Hình ảnh",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                ],
                table_cells: [
                    //theo lý thuyết nên có đủ khai báo cho tất cả các cells
                    // {...},
                    // {...}
                    {
                        target_key: 'active',
                        type: 'template' // 'text', 'bool', 'number', 'image', 'template'
                    },
                    {
                        target_key: 'image',
                        type: 'image'
                    }
                ]
            },
            form: {
                form_name: 'khách hàng',
                form_fields: [
                    {
                        label: 'Tên người dùng',
                        placeholder: 'Nhập tên người dùng..',
                        key: 'name',
                        type: 'text', //html input type
                        required: true,
                    },
                    {
                        label: 'Email',
                        placeholder: 'Nhập email..',
                        key: 'email',
                        type: 'email',
                        required: false
                    },
                    {
                        label: 'Số điện thoại',
                        placeholder: 'Nhập số điện thoại..',
                        key: 'phone_number',
                        type: 'text',
                        required: false
                    },
                ]
            },
            api_url: '/api/master/users'
        }
    }

}

</script>