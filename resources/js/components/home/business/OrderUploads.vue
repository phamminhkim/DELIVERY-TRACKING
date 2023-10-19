<template>
	<div>
		<div class="container">
			<div class="row">
				<div class="col-lg-3"></div>
				<div class="col-md-6">
					<div class="form-group row">
						<div class="col-lg-4">
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
							></treeselect>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-3"></div>
				<div class="col-md-6">
					<div class="form-group row">
						<div class="col-lg-4">
							<label class="col-form-label">Chọn cấu hình:</label>
						</div>
						<div class="col-lg-6">
							<b-form-select
								placeholder="Chọn cấu hình.."
								v-model="load_config_form.extract_order_config"
								:options="load_extract_order_config_options"
								required
							/>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3"></div>
				<div class="col-md-6">
					<div class="form-group row">
						<div class="col-lg-4">
							<label for="customer_id" class="col-form-label">Khách hàng:</label>
						</div>
						<div class="col-lg-6">
							<treeselect
								placeholder="Chọn khách hàng.."
								:multiple="true"
								:disable-branch-nodes="false"
								v-model="load_config_form.customers"
								:async="true"
								:load-options="loadOptions"
								:normalizer="normalizer"
								searchPromptText="Nhập tên khách hàng để tìm kiếm.."
							></treeselect>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3"></div>
				<div class="col-md-6">
					<div class="form-group row">
						<div class="col-lg-4">
							<label class="col-form-label">Công ty:</label>
						</div>
						<div class="col-lg-6">
							<treeselect
								v-model="load_config_form.company"
								:multiple="false"
								:options="company_options"
								placeholder="Chọn công ty.."
								required
							/>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3"></div>
				<div class="col-md-6">
					<div class="form-group row">
						<div class="col-lg-4">
							<label class="col-form-label">Chọn File:</label>
						</div>
						<div class="col-lg-6">
							<b-form-file v-model="files" multiple />
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-6"></div>
				<div class="col-md-6">
					<div class="form-group row">
						<div class="col-lg-6">
							<button
								type="button"
								class="btn btn-primary"
								@click="onClickUploadFile"
							>
								Upload
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';


	export default {
		components: {
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				customer_group_options: [],
				company_options: [],
				load_config_form: {
					customer_group_id: null,
					extract_order_config: null,
					customers: [],
					company: null,
				},
				files: [],
				selected_batch_id: null,
				load_extract_order_config_options: null,
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async fetchOptionsData() {
				const [customer_group_options, companies] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-groups'),
						new APIRequest('get', '/api/master/companies'),
					]);
				this.customer_group_options = customer_group_options;
				this.company_options = companies.map((company) => {
					return {
						id: company.code,
						label: `(${company.code}) ${company.name}`,
					};
				});
			},
			normalizer(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const { data } = await this.api_handler.get('api/master/customers/minified', {
						search: searchQuery,
					});
					const options = data;
					callback(null, options);
				}
			},

			async onClickUploadFile() {
				try {
					const batch_data = {
						customer: this.load_config_form.customers.join(','),
						extract_order_config: this.load_config_form.extract_order_config,
						customer_group_id: this.load_config_form.customer_group_id,
						company: this.load_config_form.company,
					};
					const batch_response = await this.api_handler.post(
						'/api/ai/file/batch',
						batch_data,
					);
					const selected_batch_id = batch_response.data;

					const promises = this.files.map((file) => {
						console.log(file);
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
					// console.log(promises);
					const file_responses = await Promise.all(promises);
					// Xóa các giá trị và hiển thị thông báo thành công
					this.selected_batch_id = null;
					this.load_config_form.customer_group_id = null;
					this.load_config_form.extract_order_config = null;
					this.load_config_form.customers = [];
					this.load_config_form.company = null;
                    this.files = [];
                    toastr.success('Upload file thành công');
                } catch (error) {
                    toastr.error('Đã xảy ra lỗi khi upload file');
				}
			},
		},
        showMessage(type, title, message) {
            if (!title) {
                title = "Information";
            }
            toastr.options.positionClass = "toast-bottom-right";
            toastr.options.toastClass = this.getToastClassByType(type);

            toastr[type](message, title);
        },

		formatFileSize(size) {
			if (size === 0) return '0 Bytes';
			const k = 1024;
			const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
			const i = Math.floor(Math.log(size) / Math.log(k));
			return parseFloat((size / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
		},
		watch: {
			load_customer_group_id() {
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
		},
		computed: {
			load_customer_group_id() {
				return this.load_config_form.customer_group_id;
			},
		},
	};
</script>

<style lang="css">
	.container {
		border: 1px solid #ccc;
		padding: 10px;
		/* Add any additional styling for the container */
	}
</style>
