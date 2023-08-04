<template>
	<!-- tạo form -->
	<div class="modal fade" :id="dialog_name" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addItem">
					<div class="modal-header">
						<h4 class="modal-title">
							<span>
								{{
									is_editing
										? `Cập nhật ${form_structure.form_name}`
										: `Thêm mới ${form_structure.form_name}`
								}}</span
							>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div
							class="form-group"
							v-for="(form_field, index) in form_structure.form_fields"
							:key="index"
						>
							<label>{{ form_field.label }}</label>
							<small v-if="form_field.required" class="text-danger">(*)</small>
							<input
								v-model="item[form_field.key]"
								class="form-control"
								:id="form_field.key"
								:name="form_field.key"
								:placeholder="form_field.placeholder"
								v-bind:class="hasError(form_field.key) ? 'is-invalid' : ''"
								:type="form_field.type"
								:required="form_field.required"
							/>
							<span
								v-if="hasError(form_field.key)"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError(form_field.key) }}</strong>
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
	import Vue from 'vue';
	export default {
		name: 'CrudDialog',
		components: {
			Vue,
		},
		props: {
			is_editing: Boolean,
			editing_item: Object,
			refetchData: Function,
			form_structure: Object,
			page_url_create: String,
			page_url_update: String,
			dialog_name: String,
			primary_key: String,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				is_loading: false,
				errors: {},
				item: {},
			};
		},
		created() {
			this.form_structure.form_fields.forEach((field) => {
				this.item[field.key] = '';
			});
		},
		methods: {
			async addItem() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createItem();
				} else {
					this.updateItem();
				}
			},
			async updateItem() {
				try {
					let data = await this.api_handler
						.put(
							`${this.page_url_update}/${this.item[this.primary_key]}`,
							{},
							this.item,
						)
						.finally(() => {
							this.is_loading = false;
						});

					this.$showMessage('success', 'Cập nhật thành công');
					this.closeDialog();

					this.refetchData();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
					this.errors = error.response.data.errors;
					this.$showMessage('error', 'Cập nhật không thành công', data.message);
					this.resetForm();
				}
			},
			async createItem() {
				try {
					console.log(this.item);
					let data = await this.api_handler
						.post(this.page_url_create, {}, this.item)
						.finally(() => {
							this.is_loading = false;
						});
					this.$showMessage('success', 'Thêm thành công');
					this.refetchData();
					this.closeDialog();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
					console.log(error);
					this.errors = error.response.data.errors;
					this.resetForm();
				}
			},
			closeDialog() {
				this.clearFormErrors();
				this.resetForm();
				$('#' + this.dialog_name).modal('hide');
			},
			hasError(fieldName) {
				return fieldName in this.errors;
			},
			getError(fieldName) {
				return this.errors[fieldName];
			},
			resetForm() {
				this.item = {};
			},
			clearFormErrors() {
				this.errors = {};
			},
		},
		watch: {
			editing_item: function (item) {
				this.errors = {};
				this.item = { ...item };
			},
		},
	};
</script>

<style lang="scss" scoped></style>
