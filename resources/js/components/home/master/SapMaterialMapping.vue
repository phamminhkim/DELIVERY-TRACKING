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
					title: 'Bảng đối chiếu mã sản phẩm',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
						{
							key: 'customer_material_id',
							label: 'Sản phẩm khách hàng',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'sap_material_id',
							label: 'Mã đối chiếu sản phẩm',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'percentage',
							label: 'Tỉ lệ sản phẩm',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [],
					// fulltextsearch: true,
				},
				form: {
					unique_name: 'sap-material-mappings',
					form_name: 'bảng đối chiếu sản phẩm',
					form_fields: [
						{
							label: 'Sản phẩm khách hàng',
							placeholder: 'Nhập mã sản phẩm khách hàng..',
							key: 'customer_material_id',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: false,
								option_id_key: 'id',
								option_label_key: 'name',
								async: true,
								api_async_load_options: 'api/master/customer-materials',
								key_async_field: 'customer_material_id',
							},
						},
						{
							label: 'Mã đối chiếu sản phẩm',
							placeholder: 'Nhập mã đối chiếu sản phẩm..',
							key: 'sap_material_id',
							type: 'text',
							required: true,
						},
						{
							label: 'Mã tỉ lệ sản phẩm',
							placeholder: 'Nhập mã tỉ lệ sản phẩm..',
							key: 'percentage',
							type: 'text',
							required: true,
						},
					],
				},
				api_url: '/api/master/sap-material-mappings',
			};
		},
	};
</script>
