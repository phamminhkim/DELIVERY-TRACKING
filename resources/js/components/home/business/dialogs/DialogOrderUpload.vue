<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogOrderUpload"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="onClickUploadFile">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>Upload đơn hàng</span>
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
						<div class="row">
							<div class="col-lg-1"></div>
							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-lg-6">
										<label for="customer_group_id" class="col-form-label"
											>Nhóm khách hàng:</label
										>
									</div>
									<div class="col-lg-6">
										<treeselect
											:multiple="false"
											id="customer_group_id"
											placeholder="Chọn nhóm khách hàng.."
											v-model="load_config_form.customer_group_id"
											:options="customer_group_options"
											:normalizer="normalizer"
											required
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
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-1"></div>
							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-lg-6">
										<label class="col-form-label">Chọn cấu hình:</label>
									</div>
									<div class="col-lg-6">
										<b-form-select
											placeholder="Chọn cấu hình.."
											v-model="load_config_form.extract_order_config"
											:options="load_extract_order_config_options"
											required
                                            v-bind:class="hasError('extract_order_config') ? 'is-invalid' : ''"
										/>
                                        <span
											v-if="hasError('extract_order_config')"
											class="invalid-feedback"
											role="alert"
										>
											<strong>{{ getError('extract_order_config') }}</strong>

										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-1"></div>
							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-lg-6">
										<label for="customer_id" class="col-form-label"
											>Khách hàng:</label
										>
									</div>
									<div class="col-lg-6">
										<treeselect
											placeholder="Chọn khách hàng.."
											v-model="load_config_form.customer"
											:options="load_customer_id_options"
											required
                                            v-bind:class="hasError('customer') ? 'is-invalid' : ''"
										></treeselect>
                                        <span
											v-if="hasError('customer')"
											class="invalid-feedback"
											role="alert"
										>
											<strong>{{ getError('customer') }}</strong>

										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-1"></div>
							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-lg-6">
										<label class="col-form-label">Công ty:</label>
									</div>
									<div class="col-lg-6">
										<treeselect
											:multiple="false"
											id="company"
											v-model="load_config_form.company"
											:options="company_options"
											placeholder="Chọn công ty.."
											:normalizer="normalizer"
											required
                                            v-bind:class="hasError('company') ? 'is-invalid' : ''"
										></treeselect>
                                        <span
											v-if="hasError('company')"
											class="invalid-feedback"
											role="alert"
										>
											<strong>{{ getError('company') }}</strong>

										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-1"></div>
							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-lg-6">
										<label class="col-form-label">Kho hàng:</label>
									</div>
									<div class="col-lg-6">
										<treeselect
											placeholder="Chọn kho hàng.."
											v-model="load_config_form.warehouse"
											:options="load_warehouse_id_options"
											required
											v-bind:class="hasError('warehouse') ? 'is-invalid' : ''"
										></treeselect>
										<span
											v-if="hasError('warehouse')"
											class="invalid-feedback"
											role="alert"
										>
											<strong>{{ getError('warehouse') }}</strong>

										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-1"></div>
							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-lg-6">
										<label class="col-form-label">Chọn File:</label>
									</div>
									<div class="col-lg-6">
										<b-form-file v-model="files"
                                        multiple
                                        required
                                        v-bind:class="hasError('file') ? 'is-invalid' : ''"/>
                                        <span
											v-if="hasError('file')"
											class="invalid-feedback"
											role="alert"
										>
											<strong>{{ getError('file') }}</strong>

										</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6"></div>
						<div class="col-md-6">
							<div class="form-group row">
								<div class="col-lg-9">
									<button
										type="submit"
										title="Submit"
										class="btn btn-primary"
										id="submit-btn"
									>
										Upload file
									</button>
								</div>
							</div>
						</div>
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
	import Vue from 'vue';
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogOrderUpload',
		props: {
			is_editing: Boolean,
			editing_item: Object,
			refetchData: Function,
		},
		components: {
			Treeselect,
			Vue,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_loading: false,
                errors: {},
				customer_group_options: [],
				company_options: [],
				load_config_form: {
					customer: null,
					customer_group_id: null,
					extract_order_config: null,
					company: null,
					warehouse: null,
				},
				files: [],
				selected_batch_id: null,
				load_extract_order_config_options: null,
				load_customer_id_options: null,
				load_warehouse_id_options: null,
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async onClickUploadFile() {
				try {

					this.is_loading = true;
					const batch_data = {
						customer: this.load_config_form.customer,
						extract_order_config: this.load_config_form.extract_order_config,
						customer_group_id: this.load_config_form.customer_group_id,
						company: this.load_config_form.company,
						warehouse: this.load_config_form.warehouse,
					};
					const batch_response = await this.api_handler.post(
						'/api/ai/file/batch',
						batch_data,
					);
					const selected_batch_id = batch_response.data;

					const promises = this.files.map((file) => {
						return this.api_handler
							.setHeaders({
								'Content-Type': 'multipart/form-data',
							})
							.post(
								'/api/ai/file/upload',
								{},
								APIHandler.createFormData({
									batch_id: selected_batch_id,
									file: file,
								}),
							);
					});

					const file_responses = await Promise.all(promises);

					if (!batch_data.success) {
						this.$showMessage('success', 'Thêm thành công');
						this.closeDialog();
						await this.refetchData(); // Load the data again after successful creation
					} else {
						this.errors = batch_data.errors;
						this.$showMessage('error', 'Thêm không thành công');
					}
				} catch (error) {
					this.$showMessage('error', 'Đã xảy ra lỗi khi upload file');
				} finally {
					this.is_loading = false;
				}
			},

			async fetchOptionsData() {
				// this.loading = true;
				const [customer_group_options, companies] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-groups'),
						new APIRequest('get', '/api/master/companies'),
					]);

				this.customer_group_options = customer_group_options;
				this.company_options = companies;
			},
			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const params = {
						search: searchQuery,
					};
					const { data } = await this.api_handler.get('api/master/sap-units', params);
					let options = data.map((item) => {
						return {
							id: item.id,
							label: `(${item.id}) ${item.unit_code}`,
						};
					});
					callback(null, options);
				}
			},
			normalizer(node) {
				if (node.id !== null && node.id !== undefined) {
					return {
						id: node.id,
						label: node.name,
					};
				} else if (node.code !== null && node.code !== undefined) {
					return {
						id: node.code,
						label: `(${node.code}) ${node.name}`,
					};
				}
			},
			closeDialog() {
				this.resetForm();
				$('#DialogOrderUpload').modal('hide');
			},
			resetForm() {
				// Reset the form values
				this.selected_batch_id = null;
				this.load_config_form.customer_group_id = null;
				this.load_config_form.extract_order_config = null;
				this.load_config_form.customer = null;
				this.load_config_form.company = null;
				this.load_config_form.warehouse = null;
				this.files = [];
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

				this.load_extract_order_config_options = [];
				let load_extract_order_config_options = this.customer_group_options.find(
					(customer_group) => {
						return customer_group.id == this.load_customer_group_id;
					},
				)?.extract_order_configs;
				this.load_extract_order_config_options = load_extract_order_config_options
					? load_extract_order_config_options.map((extract_order_config) => {
							return {
								value: extract_order_config.id,
								text: extract_order_config.name,
							};
					  })
					: [];
			},
			load_company_code() {
				this.load_warehouse_id_options = [];
				const load_compnay_options = this.company_options.find((company) => {
					return company.code === this.load_config_form.company;
				});

				if (load_compnay_options && load_compnay_options.warehouse) {
					this.load_warehouse_id_options = load_compnay_options.warehouse.map(
						(warehouse) => {
							return {
								id: warehouse.id,
								label: `(${warehouse.code}) ${warehouse.name}`,
							};
						},
					);
				}
			},
		},
		computed: {
			rows() {
				return this.batch_data.length;
			},
			load_customer_group_id() {
				return this.load_config_form.customer_group_id;
			},
			load_company_code() {
				return this.load_config_form.company;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
