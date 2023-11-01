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
				<div class="col-md">
					<div class="float-sm-right" style="padding-right: 40px;">
                        <button
							type="button"
							class="btn btn-info btn-sm ml-1 mt-1"
							@click="showExcelDialog"
						>
							<strong>
								<i class="fas fa-upload mr-1 text-bold"></i>Import Excel</strong
							>
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
									>Mã/tên sản phẩm</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn sản phẩm.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.sap_materials"
										:async="true"
										:load-options="loadOptions"
										:normalizer="normalizerOption"
										searchPromptText="Nhập mã/tên sản phẩm để tìm kiếm.."
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
		<CrudPage ref="CrudPage" :structure="page_structure">

    </CrudPage>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import CrudPage from '../general/crudform/CrudPage.vue';
	import Vue from 'vue';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	export default {
		name: 'SapMaterials',
		components: {
			Treeselect,
			Vue,
			CrudPage,
		},
		data() {
			return {
				// api_handler: new ApiHandler(window.Laravel.access_token),
                page_structure: {},
				is_loading: false,
				is_show_search: false,
				form_filter: {
					unit: null,
					sap_materials: [],
				},
				sap_materials: [],

				unit_options: [],
			};
		},
		created() {
			this.api_handler = new ApiHandler(window.Laravel.access_token);
			this.page_structure = {
				header: {
					title: 'Sản phẩm SAP',
					title_icon: 'fas fa-truck',
				},
				table: {
					table_fields: [
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
					],
				},
				form: {
					unique_name: 'sap_materials',
					form_name: 'Sản phẩm SAP',
					form_fields: [
						{
							label: 'Mã sản phẩm',
							placeholder: 'Nhập mã..',
							key: 'sap_code',
							type: 'number',
							required: true,
						},
						{
                            label: 'Mã unit',
                            placeholder: 'Nhập mã unit..',
                            key: 'unit_id',
                            type: 'treeselect',
                            required: true,
                            treeselect: {
                                multiple: false,
                                option_id_key: 'id',
                                option_label_key: 'unit_code',
                                async: true,
                                api_async_load_options: 'api/master/sap-units',
                                },
                            },
						{
							label: 'Tên sản phẩm',
							placeholder: 'Nhập tên sản phẩm..',
							key: 'name',
							type: 'text', //html input type
							required: true,
						},
					],
				},
				api_url: '/api/master/sap-materials',
			};
			this.fetchOptionsData();

		},

		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const [sap_materials] = await this.api_handler.get(this.page_structure.api_url, {
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_materials,
					});
					this.sap_materials = sap_materials;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchOptionsData() {
				try {
					const [unit_options] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/sap-units'),
					]);
					this.unit_options = unit_options.map((unit) => {
						return {
							id: unit.id,
							label: `(${unit.id}) ${unit.unit_code}`,
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
					const { data } = await this.api_handler.get(
						'api/master/sap-materials/minified',
						{
							search: searchQuery,
						},
					);
					const options = data;
					callback(null, options);
				}
			},

			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler.get(this.page_structure.api_url, {
						unit_ids: this.form_filter.unit,
						ids: this.form_filter.sap_materials,
					});
                    // console.log(this.page_structure.api_url);
                    // console.log(data,'u');

					this.sap_materials = data;
                    this.$refs.CrudPage.refValue(this.sap_materials)
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
					this.form_filter.sap_materials = [];

					await this.fetchData();
                    this.$refs.CrudPage.refValue(this.sap_materials)
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
            showExcelDialog() {
			},
		},
	};
</script>
