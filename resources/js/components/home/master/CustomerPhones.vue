<template>
	<CrudPage :structure="page_structure">
		<template #cell(is_active)="data">
			<span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
			<span class="badge bg-warning" v-else-if="data.item.is_active == 0"
				>Ngưng hoạt động</span
			>
		</template>
		<template #cell(is_receive_sms)="data">
			<span class="badge bg-success" v-if="data.item.is_receive_sms">Nhận tin nhắn</span>
			<span class="badge bg-warning" v-else-if="!data.item.is_receive_sms"
				>Không nhận tin nhắn</span
			>
		</template>
	</CrudPage>
</template>

<script>
	import Vue from 'vue';
	import CrudPage from '../general/crudform/CrudPage.vue';
	export default {
		name: 'CustomerPhones',
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
					title: 'Số điện thoại khách hàng',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
						{
							key: 'customer_name',
							label: 'Khách hàng',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'phone_number',
							label: 'Số điện thoại',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'name',
							label: 'Tên gợi nhớ',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'description',
							label: 'Mô tả',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'is_active',
							label: 'Trạng thái',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'is_receive_sms',
							label: 'Nhận SMS',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [
						{
							target_key: 'is_active',
							type: 'template', // 'text', 'bool', 'number', 'image', 'template'
						},
						{
							target_key: 'is_receive_sms',
							type: 'template', // 'text', 'bool', 'number', 'image', 'template'
						},
					],
					fulltextsearch: true,
				},
				form: {
					unique_name: 'customer-phones',
					form_name: 'số điện thoại khách hàng',
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
							label: 'Số điện thoại',
							placeholder: 'Nhập số điện thoại..',
							key: 'phone_number',
							type: 'number',
							required: true,
						},
						{
							label: 'Tên gợi nhớ',
							placeholder: 'Nhập tên gợi nhớ..',
							key: 'name',
							type: 'text',
							required: true,
						},
						{
							label: 'Mô tả',
							placeholder: 'Nhập mô tả..',
							key: 'description',
							type: 'text',
							required: true,
						},
						{
							label: 'Nhận SMS',
							// placeholder: 'Nhập SMS',
							required: false,
							key: 'is_receive_sms',
							type: 'checkbox',
							checkbox: {
								default_value: false,
							},
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
				api_url: '/api/master/customer-phones',
			};
		},
	};
</script>
