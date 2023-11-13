<template>
	<CrudPage :structure="page_structure">
		<!-- <template #cell(is_active)="data">
			<span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
			<span class="badge bg-warning" v-else-if="data.item.is_active == 0"
				>Ngưng hoạt động</span
			>
		</template> -->
	</CrudPage>
</template>

<script>
	import Vue from 'vue';
	import CrudPage from '../general/crudform/CrudPage.vue';
	export default {
		name: 'CustomerGroupPivots',
		components: {
			Vue,
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
					title: 'Khách hàng liên kết',
					title_icon: 'fas fa-user-plus',
				},
				table: {
					table_fields: [
						{
							key: 'code',
							label: 'Mã khách hàng',
							sortable: true,
							class: 'text-center',
						},
						{
							key: 'customer_name',
							label: 'Tên khách hàng',
							sortable: true,
							class: 'text-left',
						},
						{
							key: 'group_name',
							label: 'Nhóm khách hàng',
							sortable: true,
							class: 'text-center',
						},
					],
					// table_cells: [
					// 	//theo lý thuyết nên có đủ khai báo cho tất cả các cells
					// 	// {...},
					// 	// {...}
					// 	{
					// 		target_key: 'is_active',
					// 		type: 'template', // 'text', 'bool', 'number', 'image', 'template'
					// 	},
					// ],
					// fulltextsearch: true,
				},
				form: {
					unique_name: 'customers',
					form_name: 'khách hàng',
					form_fields: [
						{
							label: 'Khách hàng',
							placeholder: 'Nhập khách hàng..',
							key: 'customer_id',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: false,
								option_id_key: 'code',
								option_label_key: 'name',
								async: true,
								api_async_load_options: 'api/master/customers/minified',
								key_async_field: 'customer_id',
							},
						},

						{
							label: 'Nhóm khách hàng',
							placeholder: 'Nhập nhóm khách hàng..',
							key: 'customer_group_id',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: false,
								option_id_key: 'id',
								option_label_key: 'name',
								async: true,
								api_async_load_options: 'api/master/customer-groups',
							},
						},
					],
				},
				api_url: '/api/master/customer-group-pivots',
			};
		},
	};
</script>
