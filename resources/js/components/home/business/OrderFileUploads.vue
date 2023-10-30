<template lang="">
	<div class="container-fluid">
		<div class="p-3 border" style="border-radius: 10px">
			<div>
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="" for="">Nhóm khách hàng</label>
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
											<label class="" for="">Từ ngày</label>
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
											<label class="" for="">Đến ngày</label>
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
											<label class="" for="">Trạng thái</label>
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
											<label class="" for="">PO khách hàng</label>
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
				<div>
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
								<b-button variant="warning"
									><i class="fas fa-sync-alt"></i
								></b-button>
								<b-button variant="info" @click="data.toggleDetails"
									><i class="fas fa-info"></i
								></b-button>
								<b-button variant="danger"
									><i class="fas fa-trash-alt"></i
								></b-button>
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
										<b-button variant="danger"
											><i class="fas fa-trash-alt"></i
										></b-button>
									</template>
									<template #cell(created_at)="data">
										{{ data.value | formatDateTime }}
									</template>
									<template #cell(serial_number)="data">
										<a href="#" @click="showInfoDialog(data.item.id)">
											{{ data.value }}
										</a>
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
							<span :class="data.value.badge_class">{{ data.value.name }}</span>
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
			</div>
		</div>
		<DialogRawSoHeaderInfo :id="viewing_raw_so_header_id" />
	</div>
</template>
<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import DialogRawSoHeaderInfo from './dialogs/DialogRawSoHeaderInfo.vue';
	export default {
		components: {
			Treeselect,
			DialogRawSoHeaderInfo,
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
				viewing_raw_so_header_id: null,
				api_url_order_file: '/api/ai/file',
			};
		},
		created() {
			this.fetchOptionsData();
			this.fetchData();
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
					this.is_loading = true;
					const [order_files] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', this.api_url_order_file, {}),
					]);
					this.order_files = order_files;
					toastr.success('Lấy dữ liệu thành công');
				} catch (error) {
					toastr.error('Lỗi');
				} finally {
					this.is_loading = false;
				}
			},
			getFileName(path) {
				let name = path.split('/')[1].split('_');
				name.pop();
				return name.join('') + '.pdf';
			},
			showInfoDialog(raw_so_header_id) {
				this.viewing_raw_so_header_id = raw_so_header_id;
				$('#DialogRawSoHeaderInfo').modal('show');
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
