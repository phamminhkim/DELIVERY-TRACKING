<template>
	<div class="container-fluid">
		<div>
			<div class="row">
				<div class="col-md-9">
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
			</div>

			<b-collapse class="row" id="collapse-1">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Đơn vị</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn đơn vị.."
										:multiple="true"
										id="unit_id"
										:disable-branch-nodes="false"
										v-model="form_filter.unit"
										:options="unit_options"
									></treeselect>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Mã/tên sản phẩm SAP</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn sản phẩm.."
										:multiple="true"
										required
										:load-options="loadOptions"
										:async="true"
										v-model="form_filter.sap_compliance"
									/>
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
			</b-collapse>
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
							<div class="col-md-9">
								<div class="form-group row">
									<button
										type="button"
										class="btn btn-info btn-sm ml-1 mt-1"
										@click="showExcelDialog"
									>
										<strong>
											<i class="fas fa-upload mr-1 text-bold"></i>Import
											Excel</strong
										>
									</button>
									<button
										type="button"
										class="btn btn-info btn-sm ml-1 mt-1"
										@click="exportToExcel"
									>
										<strong>
											<i class="fas fa-download mr-1 text-bold"></i>Download
											Excel
										</strong>
									</button>
								</div>
							</div>
							<!-- <div class="col-md-3">
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
							</div> -->
						</div>
						<!-- tạo nút edit và delete -->
						<div class="row">
							<b-table
								responsive
								hover
								striped
								show-empty
								:busy="is_loading"
								bordered
								:current-page="pagination.current_page"
								:per-page="pagination.item_per_page"
								:filter="search_pattern"
								:fields="fields"
								:items="sap_compliances.data"
								:tbody-tr-class="rowClass"
							>
								<template #empty="scope">
									<h6 class="text-center">Không có đơn hàng nào để hiển thị</h6>
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
								<template #cell(sap_code)="data">
									<span> {{ data.item.sap_code }} </span>
								</template>
								<template #cell(check_qc)="data">
									<span class="badge bg-success" v-if="data.item.check_qc == 1"
										>x</span
									>
									<span
										class="badge bg-warning"
										v-if="data.item.check_qc == 0"
									></span>
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
											@click="deleteSapCompliance(data.item.id)"
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
							<label
								class="col-form-label-sm col-md-2"
								style="text-align: left"
								for=""
								>Số lượng mỗi trang:</label
							>
							<div class="col-md-2">
								<b-form-select
									size="sm"
									v-model="pagination.item_per_page"
									:options="
										pagination.page_options.map((option) => option.toString())
									"
								></b-form-select>
							</div>
							<label
								class="col-form-label-sm col-md-1"
								style="text-align: left"
							></label>
							<div class="col-md-3">
								<b-pagination
									v-model="pagination.current_page"
									:total-rows="sap_compliances.data.length"
									:per-page="pagination.item_per_page"
									:limit="3"
									:size="pagination.page_options.length.toString()"
									class="ml-1"
								></b-pagination>
							</div>
						</div>
						<!-- end phân trang -->

						<!-- tạo form -->
						<DialogAddUpdateSapCompliance
							ref="AddUpdateDialog"
							:is_editing="is_editing"
							:editing_item="editing_item"
							:refetchData="fetchOptionsData"
						></DialogAddUpdateSapCompliance>
						<DialogImportExcelToCreateSapCompliance :refetchData="fetchOptionsData" />

						<!-- end tạo form -->
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import Vue from 'vue';
	import ApiHandler, { APIRequest } from '../../ApiHandler';
	import DialogAddUpdateSapCompliance from './dialog/DialogAddUpdateSapCompliance.vue';
	import DialogImportExcelToCreateSapCompliance from './dialog/DialogImportExcelToCreateSapCompliance.vue';

	export default {
		name: 'SapCompliances',
		components: {
			Treeselect,
			Vue,
			DialogAddUpdateSapCompliance,
			DialogImportExcelToCreateSapCompliance,
		},
		data() {
			return {
				api_handler: new ApiHandler(window.Laravel.access_token),
				form_title: window.Laravel.form_title,
				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',
				is_editing: false,
				editing_item: {},
				is_loading: false,
				is_show_search: false,
				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500],
				},
				form_filter: {
					unit: null,
					sap_compliance: [],
				},
				fields: [
					{
						key: 'sap_code',
						label: 'Mã SAP',
						sortable: true,
						class: 'text-center',
					},
					{
						key: 'unit.unit_code',
						label: 'Mã đơn vị',
						sortable: true,
						class: 'text-center',
					},
					// {
					// 	key: 'bar_code',
					// 	label: 'Mã Barcode',
					// 	sortable: true,
					// 	class: 'text-center',
					// },
					{
						key: 'name',
						label: 'Tên sản phẩm',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'compliance',
						label: 'Quy Cách',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'check_qc',
						label: 'Check QC',
						sortable: true,
						class: 'text-nowrap text-center',
						formatter: (value) => {
							return value ? 'x' : '';
						},
					},
					{
						key: 'action',
						label: 'Action',
						class: 'text-nowrap',
					},
				],
				sap_compliances: {
					data: [], // Mảng dữ liệu
					paginate: [], // Mảng thông tin phân trang
				},
				unit_options: [],
				api_url: 'api/master/sap-compliances',
			};
		},
		created() {
			this.fetchOptionsData();
		},

		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const params = {
						page: this.page,
						per_page: this.perPage,
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_compliance,
					};
					const response = await this.api_handler.get(this.api_url, { params });
					const { data, paginate } = response.data.sap_compliances;

					if (Array.isArray(data)) {
						this.sap_compliances = data.map((item) => ({
							sap_code: item.sap_code,
							unit_id: item.unit_id,
							// bar_code: item.bar_code,
							name: item.name,
							// Thêm các trường khác nếu cần
						}));
					}

					// Gán thông tin phân trang
					this.pagination.current_page = paginate.current_page;
					this.pagination.last_page = paginate.last_page;
					this.pagination.total_items = paginate.total;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchOptionsData() {
				try {
					this.is_loading = true;
					const [unit_options, sap_compliances] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/master/sap-units'),
							new APIRequest('get', '/api/master/sap-compliances'),
						]);

					this.sap_compliances = sap_compliances;

					this.unit_options = unit_options.map((unit) => ({
						id: unit.id,
						label: `(${unit.id}) ${unit.unit_code}`,
					}));
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			normalizerOption(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},

			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const params = {
						search: searchQuery,
					};
					const { data } = await this.api_handler.get(
						'api/master/sap-compliances/minified',
						params,
					);
					let options = data.data.map((item) => ({
						id: item.id,
						label: `(${item.bar_code}) (${item.sap_code}) (${item.unit.unit_code})  ${item.name}`,
					}));
					// console.log(data);
					//const options = data;
					callback(null, options);
				}
			},

			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const response = await this.api_handler.get(this.api_url, {
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_compliance,
					});
					const data = response.data;

					this.sap_compliances = data;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},

			async clearFilter() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					this.form_filter.unit = null;
					this.form_filter.sap_compliance = [];

					await this.fetchOptionsData();
					// this.$refs.CrudPage.refValue(this.sap_compliances)
				} catch (error) {
					this.showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			showExcelDialog() {
				$('#DialogImportExcelToCreateSapCompliance').modal('show');
			},

			async deleteSapCompliance(id) {
				if (confirm('Bạn muốn xoá?')) {
					try {
						const result = await this.api_handler.delete(`${this.api_url}/${id}`);
						if (result.success) {
							if (Array.isArray(result.data)) {
								this.sap_compliances.data = result.data;
							}
							this.showMessage('success', 'Xóa thành công', result.message);
							await this.fetchOptionsData(); // Load the data again after successful deletion
						} else {
							this.showMessage('error', 'Lỗi', result.message);
						}
					} catch (error) {
						this.showMessage('error', 'Lỗi', error);
					}
				}
			},
            async exportToExcel() {
				try {
					const params = {
						search: this.search,
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_compliance,
					};

					const response = await this.api_handler.get(
						'api/master/sap-compliances/exportToExcel',
						params,
						'blob',
					);

					const blobData = new Blob([response]);

					const url = window.URL.createObjectURL(blobData);
					const link = document.createElement('a');
					link.href = url;
					link.setAttribute('download', 'Dữ liệu Sap.xlsx');
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);

					// Giải phóng URL đã tạo ra
					window.URL.revokeObjectURL(url);
				} catch (error) {
					// Xử lý lỗi khi không thể tải xuống file
					console.error(error);
				}
			},
			showCreateDialog() {
				console.log('object');
				this.is_editing = false;
				this.editing_item = {};
				$('#DialogAddUpdateSapCompliance').modal('show');
			},
			showEditDialog(item) {
				this.is_editing = true;
				this.editing_item = item;
				$('#DialogAddUpdateSapCompliance').modal('show');
			},

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
				return this.sap_compliances.length;
			},
		},
	};
</script>
