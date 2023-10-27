<template lang="">
	<div class="container-fluid">
		<div class="p-3 border" style="border-radius: 10px">
			<div>
				<!-- <div class="row">
					<div class="col-lg-12">
						<div class="form-group row">
							<div class="btn-group">
								<button
									type="button"
									class="btn btn-warning btn-xs"
									@click="is_show_search = !is_show_search"
									v-b-toggle.collapse-1
								>
									<span v-if="!is_show_search">Hiện tìm kiếm</span>
									<span v-if="is_show_search">Ẩn tìm kiếm</span>
								</button>
								<button
									type="button"
									class="btn btn-warning btn-xs"
									@click="is_show_search = !is_show_search"
									v-b-toggle.collapse-1
								>
									<i v-if="is_show_search" class="fas fa-angle-up"></i>
									<i v-else class="fas fa-angle-down"></i>
								</button>
							</div>
							<button @click="filterData()" class="btn btn-secondary btn-xs ml-1">
								<i class="fas fa-sync-alt" title="Tải lại"></i>
							</button>
						</div>
					</div>
				</div> -->
				<!-- <b-collapse class="row" id="collapse-1">
				
				</b-collapse> -->

				<div class="col-sm-12">
					<div class="card">
						<div class="card-body text-xs">
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="text-xs" for="">Nhóm khách hàng</label>
										</div>
									</div>
									<div class="col-md-8">
										<div class="mb-1">
											<treeselect
												:multiple="false"
												id="customer_group_id"
												placeholder="Chọn nhóm khách hàng.."
												v-model="form_filter.customer_group_id"
												:options="customer_group_options"
												:normalizer="normalizerOption"
											></treeselect>
										</div>
									</div>
								</div>
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class=" " for="">Khách hàng</label>
										</div>
									</div>
									<div class="col-md-8">
										<div class="mb-1">
											<treeselect
												placeholder="Chọn khách hàng.."
												:multiple="true"
												:disable-branch-nodes="false"
												v-model="form_filter.customers"
												:async="true"
												:load-options="loadOptions"
												:normalizer="normalizerOption"
												searchPromptText="Nhập tên khách hàng để tìm kiếm.."
											/>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="text-xs" for="">Từ ngày</label>
										</div>
									</div>
									<div class="col-md-8">
										<input
											type="date"
											v-model="form_filter.start_date"
											class="form-control form-control-sm"
										/>
									</div>
								</div>
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="text-xs" for="">Đến ngày</label>
										</div>
									</div>
									<div class="col-md-8">
										<input
											type="date"
											v-model="form_filter.end_date"
											class="form-control form-control-sm"
										/>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="text-xs" for="">Trạng thái</label>
										</div>
									</div>
									<div class="col-md-8">
										<treeselect
											placeholder="Chọn trạng thái .."
											:multiple="true"
											:disable-branch-nodes="false"
										/>
									</div>
								</div>
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-md-right">
											<label class="text-xs" for="">PO khách hàng</label>
										</div>
									</div>
									<div class="col-md-8">
										<input
											type="text"
											v-model="form_filter.sap_so_number"
											placeholder="Nhập PO.."
											class="form-control form-control-sm"
										/>
									</div>
								</div>
							</div>

							<div class="col-md-12" style="text-align: center">
								<button
									type="submit"
									class="btn btn-warning btn-sm mt-1 mb-1"
									@click.prevent="filterData()"
								>
									<i class="fa fa-search"></i>
									Tìm
								</button>
								<button
									type="reset"
									class="btn btn-secondary btn-sm mt-1 mb-1"
									@click.prevent="clearFilter()"
								>
									<i class="fa fa-reset"></i>
									Xóa bộ lọc
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- <div class="row"></div> -->
			</div>
			<div class="form-group">
				<div >
					<b-table
						:items="order_files"
						:fields="fields"
						responsive
						hover
						striped
						show-empty
						:busy="is_loading"
						:bordered="true"
						:current-page="pagination.current_page"
						:per-page="pagination.item_per_page"
						:tbody-tr-class="rowClass"
					>
						<template #table-busy>
							<div class="text-center text-primary my-2">
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
						</template>
						<template #cell(select)="data">
							<div>
								<input type="checkbox" />
							</div>
						</template>
						<template #cell(action)="data">
							<div>
								<b-button variant="warning"><i class="fas fa-sync-alt"></i></b-button>
								<b-button variant="success" @click="data.toggleDetails"><i class="fas fa-info"></i></b-button>
								<b-button variant="danger"><i class="fas fa-trash-alt"></i></b-button>
							</div>
						</template>
						<template #row-details="data">
							<b-card>
								<b-table
									:fields="child_fields"
									:items="data.item.raw_so_headers"
									responsive
									hover
									small
									head-variant="secondary"
								>
									<template #cell(action)="data">
										<b-button variant="danger"><i class="fas fa-trash-alt"></i></b-button>
									</template>
									<template #cell(created_at)="data">
										{{ data.value | formatDateTime }}
									</template>
									<template #cell(serial_number)="data">
										<a href="#"> {{ data.value }} </a>
									</template>
								</b-table>
							</b-card>
						</template>
						<template #cell(path)="data">
							<a href="#"> {{ getFileName(data.value) }} </a>
						</template>
						<template #cell(created_at)="data">
							{{ data.value | formatDateTime }}
						</template>
						<template #cell(status)="data">
							<span :class="data.value.badge_class">{{ data.value.name}}</span>
						</template>


					</b-table>
				</div>
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
					<label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
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
			</div>
		</div>
	</div>
</template>
<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import APIHandler from '../ApiHandler';
	export default {
		components: {
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_loading: false,
				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
				fields: [
					{
						key: 'select',
						label: '',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'created_at',
						label: 'Ngày tạo',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'batch.customer.code',
						label: 'Mã khách hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'path',
						label: 'Tên file PDF',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'batch.customer.group.name',
						label: 'Nhóm khách hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_header.po_number',
						label: 'PO khách hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'status',
						label: 'Trạng thái',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'action',
						label: 'Action',
						sortable: true,
						class: 'text-nowrap',
					},
				],
				child_fields: [
					{
						key: 'created_at',
						label: 'Ngày tạo',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'serial_number',
						label: 'Số đơn hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'po_note',
						label: 'Ghi chú',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'sap_so_number',
						label: 'Đồng bộ SAP',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'sap_so_number',
						label: 'SAP SO',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'action',
						label: 'Action',
						sortable: true,
						class: 'text-nowrap text-center',
					},
				],
				demo_items: [
					{
						id: 1,
						created_at: '2021-08-01',
						customer_code: 'KH001',
						pdf_file_name: 'file1.pdf',
						customer_group_name: 'Nhóm khách hàng 1',
						sap_so_number: 'PO001',
						status_name: 'Đã duyệt',
						child_items: [
							{
								id: 1,
								created_at: '2021-08-01',
								order_number: 'SO00001',
								note: 'Ghi chú 1',
								is_sync_sap: true,
								sap_so_number: 'SO00001',
							},
							{
								id: 2,
								created_at: '2021-08-02',
								order_number: 'SO00002',
								note: 'Ghi chú 2',
								is_sync_sap: true,
								sap_so_number: 'SO00002',
							},
							{
								id: 3,
								created_at: '2021-08-03',
								order_number: 'SO00003',
								note: 'Ghi chú 3',
								is_sync_sap: true,
								sap_so_number: 'SO00003',
							},
						],
					},
					{
						id: 2,
						created_at: '2021-08-02',
						customer_code: 'KH002',
						pdf_file_name: 'file1.pdf',
						customer_group_name: 'Nhóm khách hàng 2',
						sap_so_number: 'PO002',
						status_name: 'Đã duyệt',
						child_items: [
							{
								id: 4,
								created_at: '2021-08-01',
								order_number: 'SO00004',
								note: 'Ghi chú 4',
								is_sync_sap: true,
								sap_so_number: 'SO00004',
							},
							{
								id: 5,
								created_at: '2021-08-05',
								order_number: 'SO00005',
								note: 'Ghi chú 5',
								is_sync_sap: true,
								sap_so_number: 'SO00005',
							},
							{
								id: 6,
								created_at: '2021-08-06',
								order_number: 'SO00006',
								note: 'Ghi chú 6',
								is_sync_sap: true,
								sap_so_number: 'SO00006',
							},
						],
					},
				],

				is_show_search: false,
				form_filter: {
					start_date: '',
					end_date: '',
					// statuses: [],
					customers: [],
					customer_group_id: null,
				},
				customer_options: [],
				customer_group_options: [],
				order_files: [],
				loading: false,
				//api_url_ais: '/api/ai/',
				api_url_order_file: '/api/ai/file',
			};
		},
		created() {
			this.fetchOptionsData();
			this.fetchData();
		},
		mounted() {
			console.log(this.$refs.myTable);
			this.$nextTick(() => {
				this.$refs.myTable.rows.forEach((row) => {
					row.toggleDetails();
				});
			});
		},
		methods: {
			async fetchOptionsData() {
				const [customer_group_options] = await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
				]);
				this.customer_group_options = customer_group_options;
			},
			normalizerOption(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const { data } = await this.api_handler.get('api/master/customers/minified', {
						search: searchQuery,
					});
					const options = data;
					callback(null, options);
				}
			},
			async fetchData() {
				try {
					// if (this.is_loading) return;
					this.is_loading = true;
					const [order_files] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', this.api_url_order_file, {}),
					]);
					this.order_files = order_files;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
				} finally {
					this.is_loading = false;
				}
			},
			getFileName(path) {
				let name = path.split('/')[1].split('_');
				name.pop();
				return name.join('') + '.pdf';
			},
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
		},
		computed: {
			rows() {
				return this.order_files.length;
			},
		},
	};
</script>
<style lang="scss"></style>
