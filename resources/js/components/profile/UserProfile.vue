<template>
	<div class="container">
		<div class="card-ps">
			<div class="card-header">
				<h2 class="text-center">Đổi mật khẩu</h2>
			</div>
			<div class="card-body">
				<form @submit.prevent="changePassword">
					<div class="form-group">
						<label for="email">Email</label>
						<input
							type="text"
							class="form-control"
							id="email"
							v-model="user.email"
							:class="{ 'is-invalid': hasError('email') }"
						/>
						<div v-if="hasError('email')" class="invalid-feedback">
							{{ getError('email') }}
						</div>
					</div>
					<div class="form-group">
						<label for="current_password">Mật khẩu hiện tại</label>
						<input
							v-model="user.current_password"
							type="password"
							class="form-control"
							id="current_password"
							:class="{ 'is-invalid': hasError('current_password') }"
						/>
						<div v-if="hasError('current_password')" class="invalid-feedback">
							{{ getError('current_password') }}
						</div>
					</div>
					<div class="form-group">
						<label for="new_password">Mật khẩu mới</label>
						<input
							v-model="user.new_password"
							type="password"
							class="form-control"
							id="new_password"
							:class="{ 'is-invalid': hasError('new_password') }"
						/>
						<div v-if="hasError('new_password')" class="invalid-feedback">
							{{ getError('new_password') }}
						</div>
					</div>
					<div class="form-group">
						<label for="password_confirm">Nhập lại mật khẩu mới</label>
						<input
							v-model="user.password_confirm"
							type="password"
							class="form-control"
							id="password_confirm"
							:class="{ 'is-invalid': hasError('password_confirm') }"
						/>
						<div v-if="hasError('password_confirm')" class="invalid-feedback">
							{{ getError('password_confirm') }}
						</div>
					</div>
					<div class="form-group text-center">
						<button type="submit" class="btn btn-primary" :disabled="is_loading">
							Đổi mật khẩu
						</button>
					</div>
				</form>
			</div>
			<router-view></router-view>
		</div>
	</div>
</template>

<script>
	import ApiHandler from '../home/ApiHandler';

	export default {
		name: 'UserProfile',
		data() {
			return {
				api_handler: new ApiHandler(window.Laravel.access_token),
				api_url: 'api/master/users',
				user: {
					email: '',
					current_password: '',
					new_password: '',
					password_confirm: '',
				},
				is_loading: false,
				errors: {},
			};
		},
		methods: {
			async changePassword() {
				try {
					this.is_loading = true;
					const result = await this.api_handler.post('/api/master/users/password', {
						email: this.user.email,
						current_password: this.user.current_password,
						new_password: this.user.new_password,
						password_confirm: this.user.password_confirm,
					});
					console.log(result);

					if (result.success) {
						if (result.data && Array.isArray(result.data)) {
							this.users.data(result.data);
						}
						this.showMessage('success', 'Đổi mật khẩu thành công');
                        this.clearErrors();
                        this.clearForm();
					} else {
						this.errors = result.errors;
						this.showMessage('error', 'Lỗi');
					}
				} catch (error) {
					this.showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
            clearForm() {
				this.user.email = null;
				this.user.current_password = null;
				this.user.new_password = null;
				this.user.password_confirm = null;
			},
			clearErrors() {
				this.errors = {};
			},
			hasError(fieldName) {
				return fieldName in this.errors;
			},
			getError(fieldName) {
				return this.errors[fieldName];
			},
            showMessage(type, title, message) {
				if (!title) title = 'Information';
				toastr.options = {
					positionClass: 'toast-bottom-right',
					toastClass: this.getToastClassByType(type),
				};
				toastr[type](message, title);
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
	};
</script>

<style lang="scss">
	.container {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
	}

	.card-ps {
		width: 800px;
		padding: 30px;
		border-radius: 4px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		background: #fff;
	}

	.card-header {
		background-color: #f8f9fa;
		padding: 10px;
		border-bottom: 1px solid #dee2e6;
		border-radius: 4px 4px 0 0;
		margin-bottom: 20px;
	}

	.form-group {
		margin-bottom: 20px;
	}

	label {
		font-weight: bold;
	}

	// button[type='submit'] {
	// 	width: 100%;
	// }
</style>
