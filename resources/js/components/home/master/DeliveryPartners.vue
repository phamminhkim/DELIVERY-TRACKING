<template>
	<CrudPage :structure="page_structure">
		<template #cell(is_external)="data">
			<span v-if="data.item.is_external" class="badge bg-primary">Đối tác ngoài</span>
			<span v-else class="badge bg-info">Nội bộ</span>
		</template>
		<template #cell(is_active)="data">
			<span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
			<span class="badge bg-warning" v-else-if="data.item.is_active == 0"
				>Ngưng hoạt động</span
			>
		</template>
	</CrudPage>
</template>

<script>
	import Vue from 'vue';
	import CrudPage from '../general/crudform/CrudPage.vue';
	import { APIRequest } from '../ApiHandler';
	export default {
		name: 'DeliveryPartners',
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
					title: 'Đối tác vận chuyển',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
						{
							key: 'code',
							label: 'Mã',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'name',
							label: 'Tên nhà vận chuyển',
							sortable: true,
							class: 'text-nowrap',
						},
						{
							key: 'api_url',
							label: 'API Url',
							sortable: true,
							class: 'text-center',
						},
						{
							key: 'api_key',
							label: 'API Key',
							sortable: true,
							class: 'text-center',
						},
						{
							key: 'api_secret',
							label: 'API Secret',
							sortable: true,
							class: 'text-center',
						},
						{
							key: 'is_external',
							label: 'Phạm vị',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'is_active',
							label: 'Khả dụng',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [
						//theo lý thuyết nên có đủ khai báo cho tất cả các cells
						// {...},
						// {...}
						{
							target_key: 'is_external',
							type: 'template', // 'text', 'bool', 'number', 'image', 'template'
						},
						{
							target_key: 'is_active',
							type: 'template', // 'text', 'bool', 'number', 'image', 'template'
						},
					],
				},
				form: {
					unique_name: 'delivery-partners',
					form_name: 'đối tác vận chuyển',
					form_fields: [
						{
							label: 'Mã của nhà vận chuyển',
							placeholder: 'Nhập mã nhà vận chuyển..',
							key: 'code',
							type: 'text', //html input type
							required: true,
						},
						{
							label: 'Tên của nhà vận chuyển',
							placeholder: 'Nhập tên nhà vận chuyển',
							key: 'name',
							type: 'text',
							required: true,
						},
						{
							label: 'Api Url',
							placeholder: 'Chỉ nhập nếu có tích hợp API nhà vận chuyển',
							key: 'api_url',
							type: 'url',
							required: false,
						},
						{
							label: 'Api Key',
							placeholder: 'Chỉ nhập nếu có tích hợp API nhà vận chuyển',
							key: 'api_key',
							type: 'text',
							required: false,
						},
						{
							label: 'Api Secret',
							placeholder: 'Chỉ nhập nếu có tích hợp API nhà vận chuyển',
							key: 'api_secret',
							type: 'password',
							required: false,
						},
						{
							label: 'Danh sách tài khoản',
							placeholder: 'Chọn tài khoản liên quan..',
							key: 'user_ids',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: true,
								option_id_key: 'id',
								option_label_key: 'label',
								api_for_options_request: new APIRequest(
									'get',
									'/api/master/users?format=treeselect',
								),
							},
						},
						{
							label: 'Kênh phân phối',
							placeholder: 'Chọn kênh phân phối..',
							key: 'channel_ids',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: true,
								option_id_key: 'id',
								option_label_key: 'label',
								api_for_options_request: new APIRequest(
									'get',
									'/api/master/distribution-channels?format=treeselect',
								),
							},
						},
					],
				},
				api_url: '/api/master/delivery-partners',
			};
		},
	};
</script>
