<template>
	<div class="container-fluid">
		<div>
			<div class="row">
				<div class="col-md-9">
					<div class="form-group row">
						<div class="btn-group">
							<button type="button" class="btn btn-warning btn-xs"
								@click="is_show_search = !is_show_search" v-b-toggle.collapse-1>
								<span v-if="!is_show_search">Hiện tìm kiếm</span>
								<span v-if="is_show_search">Ẩn tìm kiếm</span>
							</button>
							<button type="button" class="btn btn-warning btn-xs"
								@click="is_show_search = !is_show_search" v-b-toggle.collapse-1>
								<i v-if="is_show_search" class="fas fa-angle-up"></i>
								<i v-else class="fas fa-angle-down"></i>
							</button>
						</div>
						<button @click="filterData()" class="btn btn-secondary btn-xs ml-1">
							<i class="fas fa-sync-alt" title="Tải lại"></i>
						</button>
					</div>
				</div>
				<div class="col-md-3">
					<div class="row"></div>
				</div>
			</div>
			<b-collapse class="row" id="collapse-1">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for="">Nhóm khách hàng</label>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect :multiple="false" id="customer_group_id"
										placeholder="Chọn nhóm khách hàng.." v-model="form_filter.customer_group"
										:options="customer_group_options" :normalizer="normalizerOption" required>
									</treeselect>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for="">Mã/tên khuyến mãi</label>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect placeholder="Nhập khách hàng.." :multiple="true" required
										:load-options="loadOptions" :async="true"
										v-model="form_filter.material_combo" />
								</div>
							</div>

							<div class="col-md-12" style="text-align: center">
								<button type="submit" class="btn btn-warning btn-sm mt-1 mb-1"
									@click.prevent="filterData()">
									<i class="fa fa-search"></i>
									Tìm
								</button>
								<button type="reset" class="btn btn-secondary btn-sm mt-1 mb-1"
									@click.prevent="clearFilter()">
									<i class="fa fa-reset"></i>
									Xóa bộ lọc
								</button>
							</div>
						</div>
					</div>
				</div>
			</b-collapse>
		</div>

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
								<button type="button" class="btn btn-info btn-sm ml-1 mt-1" @click="showExcelDialog">
									<strong>
										<i class="fas fa-upload mr-1 text-bold"></i>Import
										Excel</strong>
								</button>

								<button type="button" class="btn btn-info btn-sm ml-1 mt-1" @click="exportToExcel">
									<strong>
										<i class="fas fa-download mr-1 text-bold"></i>Download Excel
									</strong>
								</button>

								<button type="button" class="btn btn-light text-danger btn-sm ml-1 mt-1"
									@click="deleteMaterial()">
									<strong>
										<i class="fas fa-trash-alt mr-1 text-bold text-black"></i>Xóa dữ liệu
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
						<b-table responsive hover striped show-empty :busy="is_loading" :bordered="true"
							:current-page="pagination.current_page" :per-page="pagination.item_per_page"
							:filter="search_pattern" :fields="fields" :items="material_combos.data"
							:tbody-tr-class="rowClass">
							<template #head(selected)="data">
								<input type="checkbox" v-model="checkbox.selected_all" @change="selectAll()" />
							</template>
							<template #cell(selected)="data">
								<input type="checkbox" v-model="checkbox.selected" :value="data.item" />
							</template>
							<template #empty="scope">
								<h6 class="text-center">
									Không có khuyến mãi combo nào để hiển thị
								</h6>
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
							<template #cell(is_active)="data">
								<span class="badge bg-success" v-if="data.item.is_active == 1">Đang hoạt động</span>
								<span class="badge bg-warning" v-if="data.item.is_active == 0">Không hoạt động</span>
							</template>

							<template #cell(action)="data">
								<div class="margin">
									<button class="btn btn-xs mr-1" @click="showEditDialog(data.item)">
										<i class="fas fa-edit text-green" title="Edit"></i>
									</button>

									<button class="btn btn-xs mr-1" @click="deleteMaterialCombo(data.item.id)">
										<i class="fas fa-trash text-red bigger-120" title="Delete"></i>
									</button>
								</div>
							</template>
						</b-table>
					</div>
					<!-- end tạo nút -->
					<!-- phân trang -->
					<div class="row">
						<label class="col-form-label-sm col-md-2" style="text-align: left" for="">Số lượng mỗi
							trang:</label>
						<div class="col-md-2">
							<b-form-select size="sm" v-model="pagination.item_per_page" :options="pagination.page_options.map((option) => option.toString())
								"></b-form-select>
						</div>
						<label class="col-form-label-sm col-md-1" style="text-align: left"></label>
						<div class="col-md-3">
							<b-pagination v-model="pagination.current_page" :total-rows="material_combos.data.length"
								:per-page="pagination.item_per_page" :limit="3"
								:size="pagination.page_options.length.toString()" class="ml-1"></b-pagination>
						</div>
					</div>
					<!-- end phân trang -->

					<!-- tạo form -->
					<DialogAddUpdateMaterialCombo ref="AddUpdateDialog" :is_editing="is_editing"
						:editing_item="editing_item" :refetchData="fetchOptionsData"></DialogAddUpdateMaterialCombo>
					<DialogImportExcelToCreateMaterialCombo :refetchData="fetchOptionsData" />

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
import ApiHandler, { APIRequest } from '../../ApiHandler';
import DialogAddUpdateMaterialCombo from './dialog/DialogAddUpdateMaterialCombo.vue';
import DialogImportExcelToCreateMaterialCombo from './dialog/DialogImportExcelToCreateMaterialCombo.vue';

export default {
	components: {
		Treeselect,
		Vue,
		DialogAddUpdateMaterialCombo,
		DialogImportExcelToCreateMaterialCombo,
	},
	data() {
		return {
			api_handler: new ApiHandler(window.Laravel.access_token),

			form_title: window.Laravel.form_title,
			search_placeholder: 'Tìm kiếm..',
			search_pattern: '',

			form_filter: {
				customer_group: null,
				material_combo: null,
			},
			customer_group_options: [],
			is_editing: false,
			editing_item: {},
			is_loading: false,
			is_show_search: false,
			pagination: {
				item_per_page: 10,
				current_page: 1,
				page_options: [10, 50, 100, 500],
			},
			fields: [
				{
					key: 'selected',
					label: '',
					class: 'text-nowrap',
				},
				{
					key: 'index',
					label: 'STT',
					sortable: true,
					class: 'text-nowrap',
				},
				{
					key: 'customer_group.name',
					label: 'Nhóm khách hàng',
					sortable: true,
					class: 'text-nowrap text-center',
				},
				{
					key: 'sap_code',
					label: 'Mã sản phẩm',
					sortable: true,
					class: 'text-nowrap text-left',
				},
				{
					key: 'name',
					label: 'Tên sản phẩm',
					sortable: true,
					class: 'text-nowrap text-left',
				},
				{
					key: 'bar_code',
					label: 'Mã BarCode',
					sortable: true,
					class: 'text-nowrap text-left',
				},
				{
					key: 'is_active',
					label: 'Trạng thái',
					sortable: true,
					class: 'text-nowrap text-left',
				},

				{
					key: 'action',
					label: 'Action',
					class: 'text-nowrap',
				},
			],
			checkbox: {
				selected_all: false,
				selected: [],
			},
			material_combos: {

				data: [], // Mảng dữ liệu
				paginate: [], // Mảng thông tin phân trang
			},

			api_url: 'api/master/material-combos',
		};
	},
	created() {
		this.fetchOptionsData();
	},
	methods: {
		async deleteMaterial() {
			if (confirm('Bạn muốn xoá?')) {
				try {
					let result = await this.api_handler.delete(`${this.api_url}`, {
						ids: this.checkbox.selected.map((item) => item.id),
					});
					if (result.success) {
						this.deletePaginatedItemsFromSelected();
						this.showMessage('success', 'Xóa thành công', result.message);
						this.checkbox.selected_all = false;
						// this.fetchOptionsData(); // Load the data again after successful deletion
					} else {
						this.showMessage('error', 'Lỗi', result.message);
					}
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				}
			}
		},
		deletePaginatedItemsFromSelected() {
			let indexs = [];
			this.checkbox.selected.forEach((item, index_selected) => {
				const index = this.material_combos.data.findIndex((paginatedItem) => paginatedItem.id === item.id);
				if (index > -1) {
					indexs.push(index_selected);
					this.material_combos.data.splice(index, 1);
				}
			});
			indexs.forEach((index) => {
				this.checkbox.selected.splice(index, 1);
			});
		},
		selectAll() {
			if (this.checkbox.selected_all) {
				this.checkbox.selected = this.paginatedItems;
			} else {
				this.checkbox.selected = [];
			}
		},
		async fetchData() {
			try {
				this.is_loading = true;
				const params = {
					page: this.pagination.current_page,
					per_page: this.pagination.item_per_page,
					ids: this.form_filter.material_combo,
					customer_group_ids: this.form_filter.customer_group,
				};
				const response = await this.api_handler.get(this.api_url, { params });
				const { data, paginate } = response.data.material_combos;

				if (Array.isArray(data)) {
					this.material_combos.data = data.map();
				}
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
				const { data } = await this.api_handler.get(this.api_url, {
					customer_group_ids: this.form_filter.customer_group,
					ids: this.form_filter.material_combo,
				});

				this.material_combos = data;
				const [customer_group_options] = await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
				]);
				this.customer_group_options = customer_group_options;
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
					'api/master/material-combos/minified',
					params,
				);
				let options = data.data.map((item) => {
					return {
						id: item.id,
						label: `(${item.bar_code}) (${item.sap_code}) ${item.name}`,
					};
				});
				// console.log(data);
				//const options = data;
				callback(null, options);
			}
		},
		async filterData() {
			try {
				if (this.is_loading) return;
				this.is_loading = true;

				const { data } = await this.api_handler.get(this.api_url, {
					customer_group_ids: this.form_filter.customer_group,

					ids: this.form_filter.material_combo,
				});
				// console.log(this.page_structure.api_url);
				// console.log(data,'u');

				this.material_combos = data;
				// this.$refs.CrudPage.refValue(this.sap_material_mappings);
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

				this.form_filter.material_combo = [];
				this.form_filter.customer_group = null;
				await this.fetchOptionsData();
			} catch (error) {
				this.$showMessage('error', 'Lỗi', error);
			} finally {
				this.is_loading = false;
			}
		},
		async deleteMaterialCombo(id) {
			if (confirm('Bạn muốn xoá?')) {
				try {
					let result = await this.api_handler.delete(`${this.api_url}/${id}`);
					if (result.success) {
						if (Array.isArray(result.data)) {
							this.material_combos.data = result.data;
						}
						this.showMessage('success', 'Xóa thành công', result.message);
						await this.fetchOptionsData(); // Load the data again after successful deletion
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
			$('#DialogAddUpdateMaterialCombo').modal('show');
		},
		showEditDialog(item) {
			this.is_editing = true;
			this.editing_item = item;
			$('#DialogAddUpdateMaterialCombo').modal('show');
		},
		showExcelDialog() {
			$('#DialogImportExcelToCreateMaterialCombo').modal('show');
		},
		async exportToExcel() {
			try {
				const params = {
					search: this.search,
					customer_group_ids: this.form_filter.customer_group,
					ids: this.form_filter.material_combo,
				};

				const response = await this.api_handler.get(
					'api/master/material-combos/exportToExcel',
					params,
					'blob',
				);

				const blobData = new Blob([response]);

				const url = window.URL.createObjectURL(blobData);
				const link = document.createElement('a');
				link.href = url;
				link.setAttribute('download', 'Dữ liệu Combo.xlsx');
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
			return this.material_combos.length;
		},
		paginatedItems() {
			const start = (this.pagination.current_page - 1) * this.pagination.item_per_page;
			const end = start + this.pagination.item_per_page;
			// Lấy ra dữ liệu hiện tại dựa trên trang hiện tại
			return this.material_combos.data.slice(start, end);
		},
	},
};
</script>
