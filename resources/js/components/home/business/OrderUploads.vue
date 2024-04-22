<template>
	<div>
		<div class="container">
			<div class="row">
				<div class="col-lg-6 mx-auto">
					<div class="form-group row">
						<div class="col-lg-4">
							<label for="customer_group_id" class="col-form-label">Nhóm khách hàng:</label>
						</div>
						<div class="col-lg-6">
							<treeselect :multiple="false" id="customer_group_id" placeholder="Chọn nhóm khách hàng.."
								v-model="load_config_form.customer_group_id" :options="customer_group_options"
								:normalizer="normalizer" required></treeselect>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-6 mx-auto">
					<div class="form-group row">
						<div class="col-lg-4">
							<label class="col-form-label">Chọn cấu hình:</label>
						</div>
						<div class="col-lg-6">
							<b-form-select placeholder="Chọn cấu hình.." v-model="load_config_form.config_id"
								:options="load_extract_order_config_options" required />
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 mx-auto">
					<div class="form-group row">
						<div class="col-lg-4">
							<label for="customer_id" class="col-form-label">Khách hàng:</label>
						</div>
						<div class="col-lg-6">
							<treeselect placeholder="Chọn khách hàng.." v-model="load_config_form.customer"
								:options="load_customer_id_options" required></treeselect>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 mx-auto">
					<div class="form-group row">
						<div class="col-lg-4">
							<label class="col-form-label">Công ty:</label>
						</div>
						<div class="col-lg-6">
							<treeselect :multiple="false" id="company" v-model="load_config_form.company"
								:options="company_options" placeholder="Chọn công ty.." :normalizer="normalizer"
								required></treeselect>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 mx-auto">
					<div class="form-group row">
						<div class="col-lg-4">
							<label class="col-form-label">Kho hàng:</label>
						</div>
						<div class="col-lg-6">
							<treeselect placeholder="Chọn kho hàng.." v-model="load_config_form.warehouse"
								:options="load_warehouse_id_options" required></treeselect>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 mx-auto">
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
							<button type="button" class="btn btn-primary" @click="onClickUploadFile"
								:disabled="loading">
								<span v-if="loading">Đang xử lý...</span>
								<span v-else>Upload</span>
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
			loading: false,
			customer_group_options: [],
			company_options: [],
			load_config_form: {
				customer: null,
				customer_group_id: null,
				config_id: null,
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
		async fetchOptionsData() {
			const [customer_group_options, companies] =
				await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
					new APIRequest('get', '/api/master/companies'),
				]);

			this.customer_group_options = customer_group_options;
			this.company_options = companies;
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

		async onClickUploadFile() {
			try {
				this.loading = true;
				const batch_data = {
					customer: this.load_config_form.customer,
					config_id: this.load_config_form.config_id,
					customer_group_id: this.load_config_form.customer_group_id,
					company: this.load_config_form.company,
					warehouse: this.load_config_form.warehouse,
				};
				let formData = new FormData();
				// Add each file to the form data
				for (let i = 0; i < this.files.length; i++) {
					formData.append(`file[]`, this.files[i]);
				}

				formData.append('config_id', this.load_config_form.config_id);

				const file_response = await this.api_handler
					.setHeaders({
						'Content-Type': 'multipart/form-data',
					})
					.post(
						'/api/sales-order/convert-pdf',
						{},
						formData,
					);
				this.$emit('convertFilePDF', file_response);
				// console.log(promises);
				// const file_responses = await Promise.all(file_response);
				// Xóa các giá trị và hiển thị thông báo thành công
				this.selected_batch_id = null;
				this.load_config_form.customer_group_id = null;
				this.load_config_form.config_id = null;
				this.load_config_form.customer = null;
				this.load_config_form.company = null;
				this.load_config_form.warehouse = null;
				this.files = [];
				toastr.success('Upload file thành công');
			} catch (error) {
				toastr.error('Đã xảy ra lỗi khi upload file');
			} finally {
				this.loading = false; // Ẩn thông báo "Đang xử lý..."
			}
		},
	},
	showMessage(type, title, message) {
		if (!title) {
			title = 'Information';
		}
		toastr.options.positionClass = 'toast-bottom-right';
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
				? load_extract_order_config_options.map((config_id) => {
					return {
						value: config_id.id,
						text: config_id.name,
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
				this.load_warehouse_id_options = load_compnay_options.warehouse.map((warehouse) => {
					return {
						id: warehouse.id,
						label: `(${warehouse.code}) ${warehouse.name}`,
					};
				});
			}
		},
	},
	computed: {
		load_customer_group_id() {
			return this.load_config_form.customer_group_id;
		},
		load_company_code() {
			return this.load_config_form.company;
		},
	},
};
</script>

<style lang="css">
.container {
	/* border: 1px solid #ccc; */
	padding: 10px;
	/* Add any additional styling for the container */
}
</style>
