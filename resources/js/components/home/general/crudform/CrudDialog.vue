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
								v-if="form_field.type != 'treeselect'"
							/>
							<treeselect
								v-else
								:placeholder="form_field.placeholder"
								:multiple="form_field.treeselect.multiple"
								v-model="item[form_field.key]"
								:options="options_for_treeselect_fields[form_field.key]"
								:required="form_field.required"
								v-bind:class="hasError(form_field.key) ? 'is-invalid' : ''"
								:normalizer="
									(node) => {
										return {
											id: node[form_field.treeselect.option_id_key],
											label: node[form_field.treeselect.option_label_key],
										};
									}
								"
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
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';

	export default {
		name: 'CrudDialog',
		components: {
			Treeselect,
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

				options_for_treeselect_fields: {},
			};
		},
		async created() {
			this.form_structure.form_fields.forEach(async (field) => {
				if (field.type != 'treeselect') {
					this.item[field.key] = '';
				} else {
					if (field.treeselect.multiple) {
						this.item[field.key] = [];
					} else {
						this.item[field.key] = null;
					}
					const [options] = await this.api_handler.handleMultipleRequest([
						field.treeselect.api_for_options_request,
					]);
					this.options_for_treeselect_fields[field.key] = options;
				}
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
