<template>
	<CrudPage :structure="page_structure"> </CrudPage>
</template>

<script>
	import CrudPage from '../general/crudform/CrudPage.vue';
	export default {
		components: {
			CrudPage,
		},
		data() {
			return {
				page_structure: {},
			};
		},
		created() {
			this.page_structure = {
				header: {
					title: 'Sản phẩm SAP ',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
						{
							key: 'sap_code',
							label: 'Mã sản phẩm SAP',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'name',
							label: 'Tên sản phẩm SAP',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [],
					// fulltextsearch: true,
				},
				form: {
					unique_name: 'SapMaterials',
					form_name: 'sản phẩm SAP',
					form_fields: [
                        {
							label: 'Mã công ty',
							placeholder: 'Nhập mã công ty..',
							key: 'company_id',
							type: 'treeselect',
							required: true,
							treeselect: {
                                multiple: false,
                                option_id_key: 'code',
                                option_label_key: 'name',
                                async: true,
                                api_async_load_options: 'api/master/companies',
                            },
						},
                        {
                            label: 'Mã unit',
                            placeholder: 'Nhập mã unit..',
                            key: 'unit_id',
                            type: 'treeselect',
                            required: true,
                            treeselect: {
                                multiple: false,
                                option_id_key: 'id',
                                option_label_key: 'unit_code',
                                async: true,
                                api_async_load_options: 'api/master/sap-units',
                                },
                            },
						{
							label: 'Mã sản phẩm SAP',
							placeholder: 'Nhập mã sản phẩm SAP..',
							key: 'sap_code',
							type: 'text',
							required: true,
						},
						{
							label: 'Tên sản phẩm SAP',
							placeholder: 'Nhập tên sản phẩm SAP..',
							key: 'name',
							type: 'text',
							required: true,
						},
					],
				},
				api_url: '/api/master/sap-materials',
			};
		},
	};
</script>
