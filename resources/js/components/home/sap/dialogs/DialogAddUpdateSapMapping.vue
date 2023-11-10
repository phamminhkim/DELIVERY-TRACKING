<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateSapMapping"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addSapMapping">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>
								{{
									is_editing
										? 'Cập nhật Mapping SKU SAP - SKU Khách hàng'
										: 'Thêm mới Mapping SKU SAP - SKU Khách hàng'
								}}</span
							>
						</h5>

						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<label>Nhóm khách hàng</label>
							<!-- <small class="text-danger">(*)</small> -->
							<treeselect
								v-model="sap_material_mapping.customer_group_id"
								:multiple="false"
								id="customer_group_id"
								placeholder="Chỉ chọn khi chưa có sản phầm sku khách hàng.."
								:options="customer_group_options"
								:normalizer="normalizerOption"
								v-bind:class="hasError('v') ? 'is-invalid' : ''"
							></treeselect>
							<span
								v-if="hasError('customer_group_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_group_id') }}</strong>
							</span>
						</div>
						<div class="form-group" v-if="!editing_item || !editing_item.id">
							<label>SKU SAP</label>
							<!-- <small class="text-danger"></small> -->
							<treeselect
                                v-model="sap_material_mapping.sap_material_id"
								placeholder="Chọn sản phẩm.."

								required
								:load-options="loadOptions"
								:async="true"

							/>
							<span
								v-if="hasError('sap_material_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('sap_material_id') }}</strong>
							</span>
						</div>
                        <div class="form-group" v-if="editing_item && editing_item.id">
							<label>SKU SAP</label>
							<!-- <small class="text-danger"></small> -->
							<treeselect
                                v-model="sap_material_mapping.sap_material_code"
								placeholder="Chọn sản phẩm.."

								required
								:load-options="loadOptions"
								:async="true"

							/>
							<span
								v-if="hasError('sap_material_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('sap_material_id') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Tỉ lệ </label>
							<!-- <small class="text-danger"></small> -->
							<input
								v-model="sap_material_mapping.percentage"
								class="form-control"
								id="percentage"
								name="percentage"
								placeholder="Nhập tỉ lệ sản phẩm..."
								v-bind:class="hasError('percentage') ? 'is-invalid' : ''"
								type="number" min="1"   max="100"
							/>
							<span
								v-if="hasError('percentage')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('percentage') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Mã SKU khách hàng</label>
							<input
								v-model="sap_material_mapping.customer_sku_code"
								class="form-control"
								id="customer_sku_code"
								name="customer_sku_code"
								placeholder="Yêu cầu nhập khi mã sku chưa có ..."
								v-bind:class="hasError('customer_sku_code') ? 'is-invalid' : ''"
								type="text"
							/>

							<span
								v-if="hasError('customer_sku_code')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_sku_code') }}</strong>
								<!-- <div v-for="(error, index) in getError('customer_sku_code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
						<div class="form-group">
							<label>Tên SKU khách hàng</label>
							<!-- <small class="text-danger"></small> -->
							<input
								v-model="sap_material_mapping.customer_sku_name"
								class="form-control"
								id="customer_sku_name"
								name="customer_sku_name"
								placeholder="Yêu cầu nhập khi tên sku chưa có..."
								v-bind:class="hasError('customer_sku_name') ? 'is-invalid' : ''"
								type="text"
							/>
							<span
								v-if="hasError('customer_sku_name')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_sku_name') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Đơn vị tính SKU khách hàng</label>
							<!-- <small class="text-danger"></small> -->
							<input
								v-model="sap_material_mapping.customer_sku_unit"
								class="form-control"
								id="customer_sku_unit"
								name="customer_sku_unit"
								placeholder="Yêu cầu nhập khi đơn vị tính SKU chưa có..."
								v-bind:class="hasError('customer_sku_unit') ? 'is-invalid' : ''"
								type="text"
							/>
							<span
								v-if="hasError('customer_sku_unit')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_sku_unit') }}</strong>
							</span>
						</div>
					</div>

					<div class="modal-footer justify-content-between">
						<button type="submit" title="Submit" class="btn btn-primary">
							{{ is_editing ? 'Cập nhật' : 'Tạo mới' }}
						</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Đóng
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- end tạo form -->
</template>

<script>
	import APIHandler from '../../ApiHandler';
	import { APIRequest } from '../../ApiHandler';
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogAddUpdateSapMapping',
		props: {
			is_editing: Boolean,
			editing_item: Object,
			refetchData: Function,
		},
		components: {
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				is_loading: false,
				errors: {},


				sap_material_mapping: {
					customer_group_id: null,
					customer_sku_code: '',
					customer_sku_name: '',
					customer_sku_unit: '',
					customer_material_id: null,
					sap_material_id: null,
                    sap_material_code: null,
					percentage: '',
				},
				customer_group_options: [],
				customer_options: [],
				sap_material_mappings: [],

				api_url: '/api/master/sap-material-mappings',
			};
		},
		created() {
  this.fetchOptionsData();
},
		methods: {
			async addSapMapping() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createSapMapping();
				} else {
					this.updateSapMapping();
				}
			},
			async createSapMapping() {
				try {
					this.is_loading = true;
					const data = await this.api_handler.post('/api/master/sap-material-mappings', {
						customer_material_id: parseInt(
							this.sap_material_mapping.customer_material_id,
						),
						sap_material_id: parseInt(this.sap_material_mapping.sap_material_id),
						percentage: this.sap_material_mapping.percentage,
						customer_group_id: this.sap_material_mapping.customer_group_id,
						customer_sku_code: this.sap_material_mapping.customer_sku_code,
						customer_sku_name: this.sap_material_mapping.customer_sku_name,
						customer_sku_unit: this.sap_material_mapping.customer_sku_unit,
					});
                    if(data.success) {
                        if (Array.isArray(data)) {
						this.sap_material_mappings.push(...data); // Add the new mappings to the end of the list
					}
                        this.showMessage('success', 'Thêm thành công');
                        this.closeDialog();
                        await this.refetchData(); // Load the data again after successful creation
                    }else{
                        this.errors = data.errors;
                        this.showMessage('error', 'Thêm không thành công');
                    }

				} catch (error) {
					this.showMessage('error', 'Thêm không thành công');
				} finally {
					this.is_loading = false;
				}
			},

			async updateSapMapping() {
				try {
					this.is_loading = true;
					const data = await this.api_handler.put(
						`${this.api_url}/${this.sap_material_mapping.id}`,
						this.sap_material_mapping,
					);

					// Xử lý dữ liệu trả về (nếu cần)
					if(data.success) {
                        if (Array.isArray(data)) {
						this.sap_material_mappings.push(...data); // Add the new mappings to the end of the list
					}
                        this.showMessage('success', 'Thêm thành công');
                        this.closeDialog();
                        await this.refetchData(); // Load the data again after successful creation
                    }else{
                        this.errors = data.errors;
                        this.showMessage('error', 'Thêm không thành công');
                    }

				} catch (error) {
					this.showMessage('error', 'Cập nhật không thành công');
				} finally {
					this.is_loading = false;
				}
			},

			async fetchOptionsData() {
				const [customer_group_options, customer_options] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-groups'),
						new APIRequest('get', '/api/master/customer-materials'),
					]);
				this.customer_group_options = customer_group_options;
				this.customer_options = customer_options.map((customer_material) => {
					return {
						id: customer_material.id,
						label: `(${customer_material.id}) ${customer_material.customer_sku_name}`,
					};
				});
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
							label: `(${item.sap_code}) (${item.unit.unit_code}) ${item.name}`,
						};
					});
					callback(null, options);
				}
			},
			normalizerOption(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
            closeDialog() {
				this.clearForm();
				this.clearErrors();
				$('#DialogAddUpdateSapMapping').modal('hide');
			},
			clearForm() {
				this.sap_material_mapping.sap_material_id = null;
				this.sap_material_mapping.customer_material_id = null;
				this.sap_material_mapping.percentage = null;
				this.sap_material_mapping.customer_group_id = null;
				this.sap_material_mapping.customer_sku_code = null;
				this.sap_material_mapping.customer_sku_name = null;
				this.sap_material_mapping.customer_sku_unit = null;
				this.sap_material_mapping.sap_material_code = null;
			},
			clearErrors() {
				this.errors = {};
			},

			showMessage(type, title, message) {
				if (!title) title = 'Information';
				toastr.options = {
					positionClass: 'toast-bottom-right',
					toastClass: this.getToastClassByType(type),
				};
				toastr[type](message, title);
			},
			hasError(fieldName) {
				return fieldName in this.errors;
			},
			getError(fieldName) {
				return this.errors[fieldName];
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
		watch: {
            is_editing (){
                if (!this.is_editing) {
                    this.clearForm();
                }
            },
			editing_item: function (item) {
                console.log(item);
					this.sap_material_mapping.customer_group_id = item.customer_material.customer_group_id;
					this.sap_material_mapping.customer_sku_code = item.customer_material.customer_sku_code;
					this.sap_material_mapping.customer_sku_name = item.customer_material.customer_sku_name;
					this.sap_material_mapping.customer_sku_unit = item.customer_material.customer_sku_unit;
					this.sap_material_mapping.customer_material_id = item.customer_material_id;
					this.sap_material_mapping.sap_material_id = item.sap_material_id;
					this.sap_material_mapping.percentage = item.percentage;
                    this.sap_material_mapping.sap_material_code = item.sap_material.sap_code;
                    this.sap_material_mapping.id = item.id;
				}
		},
		computed: {

			rows() {
				return this.sap_material_mappings.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
