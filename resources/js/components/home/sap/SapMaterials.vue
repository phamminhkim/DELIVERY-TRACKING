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
										v-model="form_filter.sap_material"
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


							</div>
						</div>
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
							:items="sap_materials"
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
										@click="deleteSapMaterial(data.item.id)"
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
					<DialogAddUpdateSapMaterial
						ref="AddUpdateDialog"
						:is_editing="is_editing"
						:editing_item="editing_item"
						:refetchData="fetchData"
					></DialogAddUpdateSapMaterial>
					<!-- <DialogImportExcelToCreateMapping :refetchData="fetchData" /> -->

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
	import CrudPage from '../general/crudform/CrudPage.vue';
	import Vue from 'vue';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import DialogAddUpdateSapMaterial from './dialogs/DialogAddUpdateSapMaterial.vue';

	export default {
		name: 'SapMaterials',
		components: {
			Treeselect,
			Vue,
			DialogAddUpdateSapMaterial,
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
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
				form_filter: {
					unit: null,
					sap_material: [],
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
						{
							key: 'name',
							label: 'Tên sản phẩm',
							sortable: true,
							class: 'text-nowrap text-center',
						},
                        {
						key: 'action',
						label: 'Action',
						class: 'text-nowrap',
					},
				],
				sap_materials: [],
				unit_options: [],
                api_url: 'api/master/sap-materials',
			};
		},
		created() {
			this.fetchOptionsData();

		},

		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const {data} = await this.api_handler.get(this.api_url, {
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_material,
					});
					if (Array.isArray(data)) {
						this.sap_materials = data;
					}
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchOptionsData() {
				try {
                    this.is_loading = true;
					const [unit_options, sap_materials] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/sap-units'),
						new APIRequest('get', '/api/master/sap-materials'),
					]);
                    this.sap_materials = sap_materials;
					this.unit_options = unit_options.map((unit) => {
						return {
							id: unit.id,
							label: `(${unit.id}) ${unit.unit_code}`,
						};
					});
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
						'api/master/sap-materials/minified',
						params,
					);
					let options = data.map((item) => {
						return {
							id: item.id,
							label: `(${item.sap_code}) (${item.unit.unit_code})  ${item.name}`,
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
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_material,
					});
                    // console.log(this.page_structure.api_url);
                    // console.log(data,'u');

					this.sap_materials = data;
                    // this.$refs.CrudPage.refValue(this.sap_materials)
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
					this.form_filter.sap_material = [];

					await this.fetchData();
                    //this.$refs.CrudPage.refValue(this.sap_materials)
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
            showExcelDialog() {
			},


			async deleteSapMaterial(id) {
				if (confirm('Bạn muốn xoá?')) {
					try {
						let result = await this.api_handler.delete(`${this.api_url}/${id}`);
						if (result.success) {
							if (Array.isArray(result.data)) {
								this.sap_materials = result.data;
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
                console.log("object");
				this.is_editing = false;
				this.editing_item = {};
				$('#DialogAddUpdateSapMaterial').modal('show');
			},
			showEditDialog(item) {
				this.is_editing = true;
				this.editing_item = item;
				$('#DialogAddUpdateSapMaterial').modal('show');
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
				return this.sap_materials.length;
			},
		},

	};
</script>
