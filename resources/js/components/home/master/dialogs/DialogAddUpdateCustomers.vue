<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateCustomer"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addCustomer">
					<div class="modal-header">
						<h4 class="modal-title">
							<span>
								{{
									is_editing ? 'Cập nhật khách hàng' : 'Thêm mới khách hàng'
								}}</span
							>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<label>Mã khách hàng</label>
							<small class="text-danger">(*)</small>
							<input
								v-model="customer.code"
								class="form-control"
								id="code"
								name="code"
								placeholder="Nhập mã khách hàng.."
								v-bind:class="hasError('code') ? 'is-invalid' : ''"
								type="text"
								required
							/>
							<span v-if="hasError('code')" class="invalid-feedback" role="alert">
								<strong>{{ getError('code') }}</strong>
								<!-- <div v-for="(error, index) in getError('code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
						<div class="form-group">
							<label>Tên khách hàng</label>
							<small class="text-danger">(*)</small>
							<input
								v-model="customer.name"
								class="form-control"
								id="name"
								name="name"
								placeholder="Nhập tên khách hàng.."
								v-bind:class="hasError('name') ? 'is-invalid' : ''"
								type="text"
								required
							/>
							<span v-if="hasError('name')" class="invalid-feedback" role="alert">
								<strong>{{ getError('name') }}</strong>
								<!-- <div v-for="(error, index) in getError('name')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
						<div class="form-group">
							<label>Email</label>
							<small class="text-danger"></small>
							<input
								v-model="customer.email"
								class="form-control"
								id="email"
								name="email"
								placeholder="Nhập Email..."
								v-bind:class="hasError('email') ? 'is-invalid' : ''"
								type="email"
								unique
							/>
							<span v-if="hasError('email')" class="invalid-feedback" role="alert">
								<strong>{{ getError('email') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Số điện thoại</label>
							<input
								v-model="customer.phone_number"
								class="form-control"
								id="phone_number"
								name="phone_number"
								placeholder="Nhập số điện thoại..."
								v-bind:class="hasError('phone_number') ? 'is-invalid' : ''"
								type="number"
							/>
							<span
								v-if="hasError('phone_number')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('phone_number') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Địa chỉ</label>
							<input
								v-model="customer.address"
								class="form-control"
								id="address"
								name="address"
								placeholder="Nhập địa chỉ..."
								v-bind:class="hasError('address') ? 'is-invalid' : ''"
								type="text"
							/>
							<span v-if="hasError('address')" class="invalid-feedback" role="alert">
								<strong>{{ getError('address') }}</strong>
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
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogAddUpdateCustomers',
		props: {
			is_editing: Boolean,
			editing_item: Object,
			refetchData: Function,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				is_loading: false,
				errors: {},
				customer: {
					id: '',
					code: '',
					name: '',
					email: '',
					phone_number: '',
					address: '',
					is_active: '',
				},

				page_url_create_customer: '/api/master/customers',
				page_url_update_customer: '/api/master/customers',
			};
		},
		methods: {
			async addCustomer() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createCustomer();
				} else {
					this.updateCustomer();
				}
			},
			async updateCustomer() {
				try {
					let data = await this.api_handler
						.put(`${this.page_url_update_customer}/${this.customer.id}`, this.customer)
						.finally(() => {
							this.is_loading = false;
						});

					this.showMessage('success', 'Cập nhật thành công');
					this.closeDialog();
					this.refetchData();
				} catch (error) {
					//this.showMessage("error", "Lỗi", error.message);
					this.errors = error.response.data.errors;
					this.showMessage('error', 'Cập nhật không thành công', data.message);
				}
			},
			async createCustomer() {
				try {
					let data = await this.api_handler
						.post(this.page_url_create_customer, this.customer)
						.finally(() => {
							this.is_loading = false;
						});

					this.showMessage('success', 'Thêm thành công');
					this.refetchData();
					this.closeDialog();
				} catch (error) {
					//this.showMessage("error", "Lỗi", error.message);
					this.errors = error.response.data.errors;
					this.showMessage('error', 'Thêm mới không thành công', data.message);
				}
			},
			closeDialog() {
				$('#DialogAddUpdateCustomer').modal('hide');
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
			editing_item: function (item) {
				this.errors = {};
				this.customer.id = item.id;
				this.customer.code = item.code;
				this.customer.name = item.name;
				this.customer.email = item.email;
				this.customer.phone_number = item.phone_number;
				this.customer.address = item.address;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
