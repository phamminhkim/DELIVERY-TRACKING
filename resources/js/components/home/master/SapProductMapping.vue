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
							key: 'customer_id',
							label: 'Khách hàng',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'sap_code',
							label: 'Mã sản phẩm SAP',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'customer_code',
							label: 'Mã sản phẩm khách hàng',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [],
					// fulltextsearch: true,
				},
				form: {
					unique_name: 'sap-product-mapping',
					form_name: 'bảng đối chiếu sản phẩm',
					form_fields: [
						{
							label: 'Khách hàng',
							placeholder: 'Nhập khách hàng..',
							key: 'customer_id',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: false,
								option_id_key: 'id',
								option_label_key: 'name',
								// api_for_options_request: new APIRequest(
								// 	'get',
								// 	'/api/admin/roles?format=treeselect',
								// ),
								async: true,
								api_async_load_options: 'api/master/customers/minified',
								key_async_field: 'customer_id',
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
							label: 'Mã sản phẩm khách hàng',
							placeholder: 'Nhập mã sản phẩm khách hàng..',
							key: 'customer_code',
							type: 'text',
							required: true,
						},
					],
				},
				api_url: '/api/master/sap_product_mappings',
			};
		},
	};
</script>
