<template>
    <CrudPage :structure="page_structure">
        <template #cell(is_active)="data">
			<span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
			<span class="badge bg-warning" v-if="data.item.is_active == 0">Ngưng hoạt động</span>
		</template>

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
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "name",
                        label: "Tên kênh phân phối",
                        sortable: true,
                        class: "text-nowrap text-center",
                    },
                    {
                        key: "is_active",
                        label: "Trạng thái",
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
                    {
							label: 'Trạng thái',
							// placeholder: 'Nhập SMS',
							required: false,

							key: 'is_active',
							type: 'checkbox',
							checkbox: {
								default_value: true,
							},
						},
                ],
            },
            api_url: "/api/master/distribution-channels",
        };
    }


}
</script>
