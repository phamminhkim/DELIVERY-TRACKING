<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateMaterialCombo"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addMaterialCombo">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>
								{{
									is_editing
										? 'Cập nhật khuyến mãi combo'
										: 'Thêm mới khuyến mãi combo'
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
							<small class="text-danger">*</small>
							<treeselect
								:multiple="false"
								id="customer_group_id"
								placeholder="Chọn nhóm khách hàng.."
								v-model="material_combo.customer_group_id"
								:options="customer_group_options"
								:normalizer="normalizer"
								required
							></treeselect>

							<span
								v-if="hasError('customer_group_id')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('customer_group_id') }}</strong>
								<!-- <div v-for="(error, index) in getError('customer_group_id')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>Mã sản phẩm</label>
							<small class="text-danger">*</small>
							<input
								v-model="material_combo.sap_code"
								class="form-control"
								id="sap_code"
								name="sap_code"
								placeholder="Yêu cầu nhập mã sản phẩm..."
								v-bind:class="hasError('sap_code') ? 'is-invalid' : ''"
								type="text"
								required
							/>

							<span v-if="hasError('sap_code')" class="invalid-feedback" role="alert">
								<strong>{{ getError('sap_code') }}</strong>
								<!-- <div v-for="(error, index) in getError('sap_code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>Tên sản phẩm</label>
							<small class="text-danger">*</small>
							<input
								v-model="material_combo.name"
								class="form-control"
								id="name"
								name="name"
								placeholder="Yêu cầu nhập tên sản phẩm..."
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
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>Mã Barcode</label>
							<small class="text-danger">*</small>
							<input
								v-model="material_combo.bar_code"
								class="form-control"
								id="bar_code"
								name="bar_code"
								placeholder="Nhập mã barcode..."
								v-bind:class="hasError('bar_code') ? 'is-invalid' : ''"
								type="text"
							/>

							<span v-if="hasError('bar_code')" class="invalid-feedback" role="alert">
								<strong>{{ getError('bar_code') }}</strong>
								<!-- <div v-for="(error, index) in getError('bar_code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="mr-5" >Trạng thái</label>
							<div class="form-check form-check-inline">
								<input
									v-model="material_combo.is_active"
									class="form-check-input"
									id="is_active"
									name="is_active"
									type="checkbox"
									@change="validateIsActive"
									v-bind:class="hasError('is_active') ? 'is-invalid' : ''"
								/>
							</div>
							<span
								v-if="hasError('is_active')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('is_active') }}</strong>
							</span>
						</div>
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
		name: 'DialogAddUpdateMaterialCombo',
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

				material_combo: {
					customer_group_id: null,
					sap_code: '',
					name: '',
					bar_code: '',
					is_active: false, // Giá trị mặc định là false
				},
				material_combos: {
					data: [], // Mảng dữ liệu
					paginate: [], // Mảng thông tin phân trang
				},
				customer_group_options: [],

				api_url: '/api/master/material-combos',
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			validateIsActive() {
				if (
					this.material_combo.is_active !== true &&
					this.material_combo.is_active !== false
				) {
					this.material_combo.is_active = false; // Giá trị mặc định là false nếu không hợp lệ
				}
			},
			async addMaterialCombo() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createMaterialCombo();
				} else {
					this.updateMaterialCombo();
				}
			},
			async createMaterialCombo() {
				try {
					console.log('createMaterialCombo');
					this.is_loading = true;
					const data = await this.api_handler.post('/api/master/material-combos', {
						customer_group_id: this.material_combo.customer_group_id,
						sap_code: this.material_combo.sap_code,
						bar_code: this.material_combo.bar_code,
						name: this.material_combo.name,
						is_active: this.material_combo.is_active ? 1 : 0, // Chuyển đổi giá trị boolean thành 0 hoặc 1
					});
					if (!data.errors) {
						if (data.data && Array.isArray(data.data)) {
							this.material_combos.data.unshift(data.data);
						}
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

			async updateMaterialCombo() {
				try {
					this.is_loading = true;
					const request = {
						customer_group_id: this.material_combo.customer_group_id,
						sap_code: this.material_combo.sap_code,
						bar_code: this.material_combo.bar_code,
						name: this.material_combo.name,
						is_active: this.material_combo.is_active ? 1 : 0, // Chuyển đổi giá trị boolean thành 0 hoặc 1
					};
					const data = await this.api_handler.put(
						`${this.api_url}/${this.material_combo.id}`,
						request,
					);

					// Xử lý dữ liệu trả về (nếu cần)
					if (!data.success) {
						if (data.data && Array.isArray(data.data)) {
							this.material_combos.data.push(data.data);
						}
						this.showMessage('success', 'Cập nhật thành công', data.message);
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
				const [material_combo_options, customer_group_options] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/material-combos'),
						new APIRequest('get', '/api/master/customer-groups'),
					]);
				this.material_combos = material_combo_options;
				this.customer_group_options = customer_group_options;
			},

			normalizer(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			closeDialog() {
				// this.clearForm();
				this.clearErrors();
				$('#DialogAddUpdateMaterialCombo').modal('hide');
			},
			resetDialog() {
				this.material_combo.customer_group_id = null;
				this.material_combo.name = null;
				this.material_combo.sap_code = null;
				this.material_combo.bar_code = null;
				this.material_combo.is_active = false;

				this.clearErrors();
			},

			clearForm() {
				this.material_combo.customer_group_id = null;
				this.material_combo.name = null;
				this.material_combo.sap_code = null;
				this.material_combo.bar_code = null;
				this.material_combo.is_active = false;
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
				this.material_combo.customer_group_id = item.customer_group_id;
				this.material_combo.name = item.name;
				this.material_combo.sap_code = item.sap_code;
				this.material_combo.bar_code = item.bar_code;
				this.material_combo.is_active = item.is_active;
				this.material_combo.id = item.id;
			},
		},
		computed: {
			rows() {
				return this.material_combos.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
