<template>
	<CrudPage :structure="page_structure">
		<template #cell(roles)="data">
			<span v-for="role in data.value" :key="role.id" class="badge bg-info mr-1">{{
				role.name
			}}</span>
		</template>

		<template #cell(active)="data">
			<span class="badge bg-success" v-if="data.item.active == 1">Đang hoạt động</span>
			<span class="badge bg-warning" v-if="data.item.active == 0">Ngưng hoạt động</span>
		</template>
	</CrudPage>
</template>

<script>
	import Vue from 'vue';
	import CrudPage from '../general/crudform/CrudPage.vue';
	import { APIRequest } from '../ApiHandler';
	export default {
		name: 'Users',
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
					title: 'Người dùng',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
						{
							key: 'name',
							label: 'Tên người dùng',
							sortable: true,
							class: 'text-nowrap ',
						},
						{
							key: 'email',
							label: 'Email',
							sortable: true,
							class: 'text-nowrap  ',
						},
						{
							key: 'phone_number',
							label: 'Số điện thoại',
							sortable: true,
							class: 'text-nowrap',
						},
						{
							key: 'roles',
							label: 'Vai trò',
							sortable: true,
							class: 'text-nowrap text-center',
						},
						{
							key: 'active',
							label: 'Khả dụng',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [
						{
							target_key: 'active',
							type: 'template',
						},
						{
							target_key: 'roles',
							type: 'template',
						},
					],
				},
				form: {
					unique_name: 'users',
					form_name: 'người dùng',
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
							required: false,
						},
						{
							label: 'Password',
							placeholder: 'Nếu có đổi mật khẩu, nhập tại đây..',
							key: 'password',
							type: 'password',
							required: false,
						},
						{
							label: 'Số điện thoại',
							placeholder: 'Nhập số điện thoại..',
							key: 'phone_number',
							type: 'number',
							required: false,
						},
						{
							label: 'Roles',
							placeholder: 'Nhập roles..',
							key: 'role_ids',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: true,
								option_id_key: 'id',
								option_label_key: 'label',
								api_for_options_request: new APIRequest(
									'get',
									'/api/admin/roles?format=treeselect',
								),
							},
						},
					],
				},
				api_url: 'api/master/users',
			};
		},
	};
</script>
