<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateUser"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addUser">
					<div class="modal-header">
						<h4 class="modal-title">
							<span>
								{{
									is_editing ? 'Cập nhật người dùng' : 'Thêm mới người dùng'
								}}</span
							>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<label>Tên người dùng</label>
							<small class="text-danger">(*)</small>
							<input
								v-model="user.name"
								class="form-control"
								id="name"
								name="name"
								placeholder="Nhập tên người dùng.."
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
							<!-- <small class="text-danger">(*)</small> -->
							<input
								v-model="user.email"
								class="form-control"
								id="email"
								name="email"
								placeholder="Nhập email.."
								v-bind:class="hasError('email') ? 'is-invalid' : ''"
								type="email"
								unique
							/>
							<span v-if="hasError('email')" class="invalid-feedback" role="alert">
								<strong>{{ getError('email') }}</strong>
								<!-- <div v-for="(error, index) in getError('email')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
						<div class="form-group">
							<label>Số điện thoại</label>
							<!-- <small class="text-danger"></small> -->
							<input
								v-model="user.phone_number"
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
								<strong>{{ getError('email') }}</strong>
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
		name: 'DialogAddUpdateUsers',
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
				user: {
					id: '',
					name: '',
					email: '',
					phone_number: '',
					active: '',
				},

				page_url_create_user: '/api/master/users',
				page_url_update_user: '/api/master/users',
			};
		},
		methods: {
			async addUser() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createUser();
				} else {
					this.updateUser();
				}
			},
			async updateUser() {
				try {
					let data = await this.api_handler
						.put(`${this.page_url_update_user}/${this.user.id}`, this.user)
						.finally(() => {
							this.is_loading = false;
						});

					this.showMessage('success', 'Cập nhật thành công');
					this.closeDialog();
					this.refetchData();
				} catch (error) {
					this.errors = error.response.data.errors;
					this.showMessage('error', 'Cập nhật không thành công');
					this.refetchData();
				}
			},
			async createUser() {
				try {
					let data = await this.api_handler
						.post(this.page_url_create_user, this.user)
						.finally(() => {
							this.is_loading = false;
						});
					this.showMessage('success', 'Thêm thành công');
					this.refetchData();
					this.closeDialog();
				} catch (error) {
					this.errors = error.response.data.errors;
					this.showMessage('error', 'Thêm mới không thành công');
					this.refetchData();
				}
			},
			closeDialog() {
				$('#DialogAddUpdateUser').modal('hide');
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
				this.user.id = item.id;
				this.user.name = item.name;
				this.user.email = item.email;
				this.user.phone_number = item.phone_number;
				this.user.active = item.active;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
