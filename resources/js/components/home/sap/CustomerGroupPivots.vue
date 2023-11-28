<template>
	<div class="container-fluid">
		<div class="container-fluid">
			<div class="content-header">
				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-6">
							<!-- <h5 class="m-0 text-dark">
								<i :class="form_icon" />
								{{ form_title }}
							</h5> -->
						</div>
						<div class="col-sm-6">
							<div class="float-sm-right">
								<button class="btn btn-info btn-sm" @click="showCreateDialog()">
									<i class="fa fa-plus"></i>
									Tạo mới
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<div class="row">
                        <div class="col-md-9"></div>
						<div class="col-md-3">
							<div class="input-group input-group-sm mt-1 mb-1">
								<input
									type="search"
									class="form-control -control-navbar"
									v-model="search_pattern"
									:placeholder="search_placeholder"
									aria-label="Search"
								/>
								<div class="input-group-append">
									<button
										class="btn btn-default"
										style="background: #1b1a1a; color: white"
									>
										<i class="fas fa-search"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<!-- tạo nút edit và delete -->
					<div class="row">
						<b-table
							responsive
							hover
							striped
							show-empty
							:busy="is_loading"
							:bordered="true"
							:current-page="pagination.current_page"
							:per-page="pagination.item_per_page"
							:filter="search_pattern"
							:fields="fields"
							:items="customer_group_pivots"
							:tbody-tr-class="rowClass"
						>
							<template #empty="scope">
								<h6 class="text-center">Không có khách hàng nào để hiển thị</h6>
							</template>
							<template #table-busy>
								<div class="text-center text-primary my-2">
									<b-spinner class="align-middle" type="grow"></b-spinner>
									<strong>Đang tải dữ liệu...</strong>
								</div>
							</template>
							<template #cell(index)="data">
								{{
									data.index +
									(pagination.current_page - 1) * pagination.item_per_page +
									1
								}}
							</template>

							<template #cell(action)="data">
								<div class="margin">
									<button
										class="btn btn-xs mr-1"
										@click="showEditDialog(data.item)"
									>
										<i class="fas fa-edit text-green" title="Edit"></i>
									</button>

									<button
										class="btn btn-xs mr-1"
										@click="deleteCustomerPivots(data.item.id)"
									>
										<i
											class="fas fa-trash text-red bigger-120"
											title="Delete"
										></i>
									</button>
								</div>
							</template>
						</b-table>
					</div>
					<!-- end tạo nút -->
					<!-- phân trang -->
					<div class="row">
						<label class="col-form-label-sm col-md-2" style="text-align: left" for=""
							>Số lượng mỗi trang:</label
						>
						<div class="col-md-2">
							<b-form-select
								size="sm"
								v-model="pagination.item_per_page"
								:options="pagination.page_options"
							>
							</b-form-select>
						</div>
						<label
							class="col-form-label-sm col-md-1"
							style="text-align: left"
							for=""
						></label>
						<div class="col-md-3">
							<b-pagination
								v-model="pagination.current_page"
								:total-rows="rows"
								:per-page="pagination.item_per_page"
								size="sm"
								class="ml-1"
							></b-pagination>
						</div>
					</div>
					<!-- end phân trang -->

					<!-- tạo form -->
					<DialogAddUpdateCustomerGroupPivots
						ref="AddUpdateDialog"
						:is_editing="is_editing"
						:editing_item="editing_item"
						:refetchData="fetchData"
					></DialogAddUpdateCustomerGroupPivots>

					<!-- end tạo form -->
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import Vue from 'vue';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import DialogAddUpdateCustomerGroupPivots from './dialogs/DialogAddUpdateCustomerGroupPivots.vue';

	export default {
		components: {
			Treeselect,
			Vue,
			DialogAddUpdateCustomerGroupPivots,
		},
		data() {
			return {
				api_handler: new ApiHandler(window.Laravel.access_token),

				form_title: window.Laravel.form_title,
				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',

				customer_group_options: [],

				is_editing: false,
				editing_item: {},
				is_loading: false,
				is_show_search: false,
				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
				fields: [
					{
						key: 'index',
						label: 'STT',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'code',
						label: 'Mã khách hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'customer_name',
						label: 'Tên khách hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'group_name',
						label: 'Nhóm khách hàng',
						sortable: true,
						class: 'text-nowrap text-left',
					},

					{
						key: 'action',
						label: 'Action',
						class: 'text-nowrap',
					},
				],

				customer_group_pivots: [],
				api_url: '/api/master/customer-group-pivots',
			};
		},
		created() {
			this.fetchData();
		},
		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.get(this.api_url, {});

					if (Array.isArray(data)) {
						this.customer_group_pivots = data;
					}
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},

			async deleteCustomerPivots(id) {
				if (confirm('Bạn muốn xoá?')) {
					try {
						let result = await this.api_handler.delete(`${this.api_url}/${id}`);
						if (result.success) {
							if (Array.isArray(result.data)) {
								this.customer_group_pivots = result.data;
							}
							this.showMessage('success', 'Xóa thành công', result.message);
							await this.fetchData(); // Load the data again after successful deletion
						} else {
							this.showMessage('error', 'Lỗi', result.message);
						}
					} catch (error) {
						this.$showMessage('error', 'Lỗi', error);
					}
				}
			},
			showCreateDialog() {
				this.is_editing = false;
				this.editing_item = {};
				$('#DialogAddUpdateCustomerGroupPivots').modal('show');
			},
			showEditDialog(item) {
				this.is_editing = true;
				this.editing_item = item;
				$('#DialogAddUpdateCustomerGroupPivots').modal('show');
			},
			// showExcelDialog() {
			// 	$('#DialogImportExcelToCreateMapping').modal('show');
			// },

			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
			showMessage(type, title, message) {
				if (!title) title = 'Information';
				toastr.options = {
					positionClass: 'toast-bottom-right',
					toastClass: this.getToastClassByType(type),
				};
				toastr[type](message, title); // Uncomment this line to show the message
			},
			getToastClassByType(type) {
				switch (type) {
					case 'success':
						return 'toastr-bg-green';
					case 'error':
						return 'toastr-bg-red';
					case 'warning':
						return 'toastr-bg-yellow';
					default:
						return '';
				}
			},
		},
		computed: {
			rows() {
				return this.customer_group_pivots.length;
			},
		},
	};
</script>
