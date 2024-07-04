<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateCustomerPromotion"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addCustomerPromotion">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>
								{{
									is_editing
										? 'Cập nhật khách hàng khuyến mãi'
										: 'Thêm mới khách hàng khuyến mãi'
								}}</span
							>
						</h5>

						<button
							type="button"
							class="close"
							data-dismiss="modal"
							aria-label="Close"
							@click="closeDialog"
						>
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group" v-if="!editing_item || !editing_item.id">
							<label>SKU SAP</label>
							<!-- <small class="text-danger"></small> -->
							<treeselect
								v-model="customer_promotion.sap_material_id"
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
								v-model="customer_promotion.sap_material_code"
								placeholder="Chọn sản phẩm.."
								required
								:load-options="loadOptions"
								:disabled="is_editing"
								:async="true"
							/>
							<span
								v-if="is_editing"
								class="readonly-text"
								style="color: red; font-style: italic"
								>Không được phép chỉnh sửa</span
							>
							<span
								v-if="hasError('sap_material_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('sap_material_id') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Nhóm khách hàng</label>
							<small class="text-danger">(*)</small>
							<treeselect
								v-model="customer_promotion.customer_group_id"
								:multiple="false"
								id="customer_group_id"
								placeholder="Chọn nhóm khách hàng.."
								required
								:options="customer_group_options"
								:normalizer="normalizer"
								v-bind:class="hasError('customer_group_id') ? 'is-invalid' : ''"
							></treeselect>
							<span
								v-if="hasError('customer_group_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_group_id') }}</strong>
							</span>
						</div>

						<div class="form-group">
							<label>Khách hàng</label>
							<small class="text-danger">(*)</small>
							<treeselect
								placeholder="Chọn khách hàng.."
								v-model="customer_promotion.customer_id"
								:options="load_customer_id_options"
								v-bind:class="hasError('customer_id') ? 'is-invalid' : ''"
								required
							></treeselect>

							<span
								v-if="hasError('customer_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_id') }}</strong>
							</span>
						</div>
						<!-- <div class="form-group">
							<label>Trạng thái</label>
							<input
								class="form-check-input"
								type="checkbox"
								id="is_active"
								v-model="is_active"
								:true-value="true"
								:false-value="false"
								@change="$emit('update:is_active', is_active)"
							/>
						</div> -->
					</div>

					<div class="modal-footer justify-content-between">
						<button type="submit" title="Submit" class="btn btn-primary">
							{{ is_editing ? 'Cập nhật' : 'Tạo mới' }}
						</button>
						<button type="button" class="btn btn-secondary" @click="resetDialog">
							Reset
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- end tạo form -->
</template>

<script>
	import APIHandler from '../../../ApiHandler';
	import { APIRequest } from '../../../ApiHandler';
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogAddUpdateCustomerPromotion',
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
				is_active: true,

				customer_promotion: {
					customer_id: null,
					customer_group_id: null,
					sap_material_id: null,
					sap_material_code: null,
					id: '',
				},
				customer_group_options: [],
				customer_promotions: [],

				load_customer_id_options: null,

				api_url: '/api/master/customer-promotions',
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async addCustomerPromotion() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createCustomerPromotion();
				} else {
					this.updateCustomerPromotion();
				}
			},
			async createCustomerPromotion() {
				try {
					console.log('createCustomerPromotion');
					this.is_loading = true;
					const data = await this.api_handler.post('/api/master/customer-promotions', {
						customer_group_id: this.customer_promotion.customer_group_id,
						customer_id: this.customer_promotion.customer_id,
						sap_material_id: parseInt(this.customer_promotion.sap_material_id),
					});
					if (data.success) {
						if (Array.isArray(data)) {
							this.customer_promotions.push(...data); // Add the new mappings to the end of the list
						}
						this.showMessage('success', 'Thêm thành công');
						this.closeDialog();
						this.clearForm();
						await this.refetchData(); // Load the data again after successful creation
					} else {
						this.errors = data.errors;
						this.showMessage('error', 'Thêm không thành công');
					}
				} catch (error) {
					this.showMessage('error', 'Thêm không thành công');
				} finally {
					this.is_loading = false;
				}
			},

			async updateCustomerPromotion() {
				try {
					this.is_loading = true;
					const data = await this.api_handler.put(
						`${this.api_url}/${this.customer_promotion.id}`,
						this.customer_promotion,
					);

					// Xử lý dữ liệu trả về (nếu cần)
					if (data.success) {
						if (Array.isArray(data)) {
							this.customer_promotions.push(...data); // Add the new mappings to the end of the list
						}
						this.showMessage('success', 'Thêm thành công');
						this.closeDialog();
						await this.refetchData(); // Load the data again after successful creation
					} else {
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
				const [customer_group_options,customer_id_options] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-groups'),
					]);
				this.customer_group_options = customer_group_options;
				this.customer_id_options = customer_id_options;
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
			async loadOptionsCustomer({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const { data } = await this.api_handler.get('api/master/customers/minified', {
						search: searchQuery,
					});
					const options = data;
					callback(null, options);
				}
			},

			normalizer(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			closeDialog() {
				// this.clearForm();
				this.clearErrors();
				$('#DialogAddUpdateCustomerPromotion').modal('hide');
			},
			resetDialog() {
				this.customer_promotion.sap_material_id = null;
				this.customer_promotion.customer_group_id = null;
				this.customer_promotion.customer_id = null;
				this.clearErrors();
			},

			clearForm() {
				this.customer_promotion.sap_material_id = null;
				this.customer_promotion.customer_group_id = null;
				this.customer_promotion.customer_id = null;
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
			is_editing() {
				if (!this.is_editing) {
					this.clearForm();
				}
			},
			editing_item: function (item) {
				console.log(item);
				this.customer_promotion.customer_group_id = item.customer_group_id;
				this.customer_promotion.customer_id = item.customer_id;
				this.customer_promotion.sap_material_id = item.sap_material_id;
				this.customer_promotion.sap_material_code = item.sap_material.sap_code;
				this.customer_promotion.id = item.id;
			},
			load_customer_group_id() {
				this.load_customer_id_options = [];
				let load_customer_id_options = this.customer_group_options.find(
					(customer_group) => {
						return customer_group.id == this.load_customer_group_id;
					},
				)?.customers;
				this.load_customer_id_options = load_customer_id_options
					? load_customer_id_options.map((customer) => {
							return {
								id: customer.id,
								label: `(${customer.code}) ${customer.name}`,
							};
					  })
					: [];

				// this.load_extract_order_config_options = [];
			},
		},
		computed: {
			load_customer_group_id() {
				return this.customer_promotion.customer_group_id;
			},
			rows() {
				return this.customer_promotions.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
