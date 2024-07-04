<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateCustomerGroupPivots"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addCustomerGroupPivot">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>
								{{
									is_editing
										? 'Cập nhật khách hàng liên kết'
										: 'Thêm mới khách hàng liên kết'
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
						<div class="form-group">
							<label>Nhóm khách hàng</label>
							<small class="text-danger">(*)</small>
							<treeselect
								v-model="customer_group_pivot.customer_group_id"
								:multiple="false"
								id="customer_group_id"
								placeholder="Chọn nhóm khách hàng.."
                                required
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
						<div class="form-group" >
							<label>Khách hàng</label>
							<small class="text-danger">*</small>
							<treeselect
								v-model="customer_group_pivot.customer_id"
								placeholder="Nhập khách hàng.."
								required
								:load-options="loadOptions"
                                v-bind:class="hasError('customer_id') ? 'is-invalid' : ''"
								:async="true"
							/>
							<span
								v-if="hasError('customer_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_id') }}</strong>
							</span>
						</div>
						<!-- <div class="form-group" v-if="editing_item && editing_item.code">
							<label>Khách hàng</label>
							<small class="text-danger">*</small>
							<treeselect
								v-model="customer_group_pivot.customer_code"
								placeholder="Chọn sản phẩm.."
								required
								:load-options="loadOptions"
								:async="true"
							/>
							<span
								v-if="hasError('customer_code')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_code') }}</strong>
							</span>
						</div> -->
					</div>

					<div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" @click="resetDialog">
							Reset
						</button>
						<button type="submit" title="Submit" class="btn btn-primary">
							{{ is_editing ? 'Cập nhật' : 'Tạo mới' }}
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
		name: 'DialogAddUpdateCustomerGroupPivots',
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

				customer_group_pivot: {
					customer_group_id: null,
					customer_id: null,
				},
				customer_group_options: [],
				customer_group_pivots: [],

				api_url: '/api/master/customer-group-pivots',
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async addCustomerGroupPivot() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createCustomerGroupPivot();
				} else {
					this.updateCustomerGroupPivot();
				}
			},
			async createCustomerGroupPivot() {
				try {
					this.is_loading = true;
					const data = await this.api_handler.post(this.api_url, {
						customer_group_id: this.customer_group_pivot.customer_group_id,
						customer_id: this.customer_group_pivot.customer_id,
					});

					if (data.success) {
						this.customer_group_pivots.push(data); // Add the new mapping to the list
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

			async updateCustomerGroupPivot() {
				try {
					this.is_loading = true;
					const data = await this.api_handler.put(
						`${this.api_url}/${this.customer_group_pivot.id}`,
						this.customer_group_pivot,
					);

					// Xử lý dữ liệu trả về (nếu cần)
					if (data.success) {
						if (Array.isArray(data)) {
							this.customer_group_pivots.push(...data); // Add the new mappings to the end of the list
						}
						this.showMessage('success', 'Cập nhật thành công');
						this.closeDialog();
						await this.refetchData(); // Load the data again after successful creation
					} else {
						this.errors = data.errors;
						this.showMessage('error', 'Cập nhật không thành công');
					}
				} catch (error) {
					this.showMessage('error', 'Cập nhật không thành công');
				} finally {
					this.is_loading = false;
				}
			},

			async fetchOptionsData() {
				const [customer_group_options] = await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
				]);
				this.customer_group_options = customer_group_options;
			},
			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const params = {
						search: searchQuery,
					};
					const { data } = await this.api_handler.get(
						'api/master/customers/minified',
						params,
					);
					let options = data.map((item) => {
						return {
							id: item.code,
							label: `(${item.code}) ${item.name}`,
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
				// this.clearForm();
				this.clearErrors();
				$('#DialogAddUpdateCustomerGroupPivots').modal('hide');
			},
			resetDialog() {
				this.customer_group_pivot.customer_id = null;
				this.customer_group_pivot.customer_group_id = null;
				this.clearErrors();
			},

			clearForm() {
				this.customer_group_pivot.customer_id = null;
				this.customer_group_pivot.customer_group_id = null;
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
				this.customer_group_pivot.customer_group_id = item.customer_group_id;
				this.customer_group_pivot.customer_id = item.customer_id;
				// this.customer_group_pivot.customer_code = item.customer_code;
				this.customer_group_pivot.id = item.id;
			},
		},
		computed: {
			rows() {
				return this.customer_group_pivots.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
