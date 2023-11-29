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
							<label>Mã khách hàng SAP</label>

                            <treeselect
										placeholder="Mã/tên sản phẩm khách hàng.."
										:multiple="true"
										id="customer_material_id"
										:disable-branch-nodes="false"
										v-model="customer_promotion.customer_material_id"
										:options="customer_options"
                                        v-bind:class="hasError('customer_material_id') ? 'is-invalid' : ''"
									></treeselect>

							<span
								v-if="hasError('customer_material_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_material_id') }}</strong>
								<!-- <div v-for="(error, index) in getError('customer_sku_code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>

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
	import APIHandler from '../../ApiHandler';
	import { APIRequest } from '../../ApiHandler';
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

				customer_promotion: {
					// customer_sku_code: '',
					// customer_sku_name: '',
					customer_material_id: null,
					sap_material_id: null,
					sap_material_code: null,
				},
				customer_options: [],
				customer_promotions: [],

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
					this.is_loading = true;
					const data = await this.api_handler.post('/api/master/customer-promotions', {
						customer_material_id: parseInt(
							this.customer_promotion.customer_material_id,
						),
						sap_material_id: parseInt(this.customer_promotion.sap_material_id),
					});
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
				const [ customer_options] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-materials'),
					]);
				this.customer_options = customer_options.map((customer_material) => {
					return {
						id: customer_material.id,
						label: `(${customer_material.customer_sku_code}) ${customer_material.customer_sku_name}`,
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
				$('#DialogAddUpdateCustomerPromotion').modal('hide');
			},
			resetDialog() {
				this.customer_promotion.sap_material_id = null;
				this.customer_promotion.customer_material_id = null;
				// this.customer_promotion.customer_sku_code = '';
				// this.customer_promotion.customer_sku_name = '';
				// this.customer_promotion.sap_material_code = null;
				this.clearErrors();
			},

			clearForm() {
				this.customer_promotion.sap_material_id = null;
				this.customer_promotion.customer_material_id = null;
				// this.customer_promotion.customer_sku_code = null;
				// this.customer_promotion.customer_sku_name = null;
				// this.customer_promotion.sap_material_code = null;
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
				// this.customer_promotion.customer_sku_code =
				// 	item.customer_material.customer_sku_code;
				// this.customer_promotion.customer_sku_name =
				// 	item.customer_material.customer_sku_name;
				// this.customer_promotion.customer_sku_unit =
				// 	item.customer_material.customer_sku_unit;
				this.customer_promotion.customer_material_id = item.customer_material_id;
				this.customer_promotion.sap_material_id = item.sap_material_id;
				// this.customer_promotion.sap_material_code = item.sap_material.sap_code;
				this.customer_promotion.id = item.id;
			},
		},
		computed: {
			rows() {
				return this.customer_promotions.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
