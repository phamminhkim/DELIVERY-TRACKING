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
				<div class="col-md-3">
					<div class="row"></div>
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
									>Mã/tên sản phẩm khách hàng</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Mã/tên sản phẩm khách hàng.."
										:multiple="true"
										id="customer_material_id"
										:disable-branch-nodes="false"
										v-model="form_filter.customer_material"
										:options="customer_options"
									></treeselect>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Mã/tên đối chiếu sản phẩm</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn mã/tên đối chiếu sản phẩm.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.sap_material"
										:async="true"
										:load-options="loadOptions"
										:normalizer="normalizerOption"
										searchPromptText="Nhập mã/tên đối chiếu sản phẩm để tìm kiếm.."
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
		</div>

		<CrudPage ref="CrudPage" :structure="page_structure"> </CrudPage>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import CrudPage from '../general/crudform/CrudPage.vue';
	import Vue from 'vue';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	export default {
		components: {
			Treeselect,
			Vue,
			CrudPage,
		},
		data() {
			return {
				page_structure: {},
				is_loading: false,
				is_show_search: false,
				form_filter: {
					customer_material: null,
					sap_material: null,
				},
				sap_materials: [],
				sap_material_mappings: [],
				customer_options: [],
			};
		},
		created() {
			this.api_handler = new ApiHandler(window.Laravel.access_token);
			this.page_structure = {
				header: {
					title: 'Bảng đối chiếu mã sản phẩm',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
						{
							key: 'customer_material.customer_sku_name',
							label: 'Sản phẩm khách hàng',
							sortable: true,
							class: 'text-nowrap text-left',
						},
						{
							key: 'sap_material.name',
							label: 'Mã đối chiếu sản phẩm',
							sortable: true,
							class: 'text-nowrap text-left',
						},
						{
							key: 'percentage',
							label: 'Tỉ lệ sản phẩm',
							sortable: true,
							class: 'text-nowrap text-center',
						},
					],
					table_cells: [],
					// fulltextsearch: true,
				},
				form: {
					unique_name: 'sap-material-mappings',
					form_name: 'bảng đối chiếu sản phẩm',
					form_fields: [
						{
							label: 'Mã sản phẩm khách hàng',
							placeholder: 'Nhập sản phẩm khách hàng..',
							key: 'customer_material_id',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: false,
								option_id_key: 'id',
								option_label_key: 'customer_sku_name',
								async: true,
								api_async_load_options: 'api/master/customer-materials',
							},
						},

						{
							label: 'Mã đối chiếu sản phẩm',
							placeholder: 'Nhập mã Mã đối chiếu sản phẩm..',
							key: 'sap_material_id',
							type: 'treeselect',
							required: true,
							treeselect: {
								multiple: false,
								option_id_key: 'id',
								option_label_key: 'name',
								async: true,
								api_async_load_options: 'api/master/sap-materials',
							},
						},
						{
							label: 'Mã tỉ lệ sản phẩm',
							placeholder: 'Nhập mã tỉ lệ sản phẩm..',
							key: 'percentage',
							type: 'text',
							required: true,
						},
					],
				},
				api_url: '/api/master/sap-material-mappings',
			};
			this.fetchOptionsData();
		},
		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const [sap_material_mappings] = await this.api_handler.get(
						this.page_structure.api_url,
						{
							customer_material_ids: this.form_filter.customer_material,
							sap_material_ids: this.form_filter.sap_material,
						},
					);
					this.sap_material_mappings = sap_material_mappings;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchOptionsData() {
				try {
					const [customer_options] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-materials'),
					]);
					this.customer_options = customer_options.map((customer_material) => {
						return {
							id: customer_material.id,
							label: `(${customer_material.id}) ${customer_material.customer_sku_name}`,
						};
					});
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
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
						// customer_material_ids: this.form_filter.customer_material,
						sap_material_ids: [this.form_filter.sap_material],

					};
					const { data } = await this.api_handler.get(
						'api/master/sap-materials/minified',
						params,
					);
                    console.log(data);
					const options = data;
					callback(null, options);
				}
			},

			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler.get(this.page_structure.api_url, {
						customer_material_ids: this.form_filter.customer_material,
						sap_material_ids: this.form_filter.sap_material,
					});
					// console.log(this.page_structure.api_url);
					// console.log(data,'u');

					this.sap_material_mappings = data;
					this.$refs.CrudPage.refValue(this.sap_material_mappings);
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

					this.form_filter.customer_material = null;
					this.form_filter.sap_materials = []; // Ensure it is an array

					// this.fetchData();
					this.$refs.CrudPage.refValue(this.sap_material_mappings);
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
		},
		computed: {
			rows() {
				return this.sap_material_mappings.length;
			},
		},
	};
</script>
