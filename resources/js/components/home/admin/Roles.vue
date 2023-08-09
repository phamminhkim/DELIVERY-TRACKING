<template>
	<CrudPage :structure="page_structure">
		<!-- <template #cell(is_active)="data">
            <span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
            <span class="badge bg-warning" v-else-if="data.item.is_active == 0">Ngưng hoạt động</span>
        </template> -->
	</CrudPage>
</template>

<script>
	import CrudPage from '../general/crudform/CrudPage.vue';
	import Vue from 'vue';

	export default {
		components: {
			CrudPage,
			Vue,
		},
		data() {
			return {
				page_structure: {},
			};
		},
		created() {
			this.page_structure = {
				header: {
					title: 'Vai trò (role)',
					title_icon: 'fas fa-user-circle',
				},
				table: {
					table_fields: [
						{
							key: 'name',
							label: 'Tên vai trò',
							sortable: true,
							class: 'text-center',
						},
						{
							key: 'guard_name',
							label: 'Guard',
							sortable: true,
							class: 'text-center',
						},
					],
				},
				form: {
					unique_name: 'roles',
					form_name: 'Vai trò',
					form_fields: [
						{
							label: 'Tên vai trò',
							placeholder: 'Nhập tên vai trò..',
							key: 'name',
							type: 'text', //html input type
							required: true,
						},
						{
							label: 'Tên guard',
							placeholder: 'Nhập tên guard..',
							key: 'guard_name',
							type: 'text', //html input type
							required: true,
						},
					],
				},
				api_url: '/api/admin/roles',
			};
		},
	};
</script>
