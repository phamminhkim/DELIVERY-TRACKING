<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateSapMaterial"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addSapMaterial">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>
								{{
									is_editing ? 'Cập nhật sản phẩm SAP' : 'Thêm mới sản phẩm SAP'
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
							<label>Mã sản phẩm SAP</label>
							<small class="text-danger">*</small>
							<input
								v-model="sap_material.sap_code"
								class="form-control"
								id="sap_code"
								name="sap_code"
								required
								placeholder="Yêu cầu nhập mã SAP..."
								v-bind:class="hasError('sap_code') ? 'is-invalid' : ''"
								type="text"
							/>
							<span v-if="hasError('sap_code')" class="invalid-feedback" role="alert">
								<strong>{{ getError('sap_code') }}</strong>
								<!-- <div v-for="(error, index) in getError('customer_sku_code')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
						<div class="form-group"  v-if="!editing_item || !editing_item.id">
							<label>Mã unit</label>
							<small class="text-danger">*</small>
							<treeselect
								v-model="sap_material.unit_id"
								:multiple="false"
								placeholder="Nhập unit.."
								required
								:load-options="loadOptions"
								v-bind:class="hasError('unit_id') ? 'is-invalid' : ''"
								:async="true"
							/>
							<span v-if="hasError('unit_id')" class="invalid-feedback" role="alert">
								<strong>{{ getError('unit_id') }}</strong>
							</span>
						</div>
                        <div class="form-group"  v-if="editing_item && editing_item.id">
							<label>Mã unit</label>
							<small class="text-danger">*</small>
							<treeselect
								v-model="sap_material.unit_code"
								:multiple="false"
								placeholder="Nhập unit.."
								required
								:load-options="loadOptions"
								v-bind:class="hasError('unit_id') ? 'is-invalid' : ''"
								:async="true"
							/>
							<span v-if="hasError('unit_id')" class="invalid-feedback" role="alert">
								<strong>{{ getError('unit_id') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Mã Barcode</label>
							<small class="text-danger">*</small>
							<input
								v-model="sap_material.bar_code"
								class="form-control"
								id="bar_code"
								name="bar_code"
								placeholder="Nhập mã Barcode(nếu có)..."
								v-bind:class="hasError('bar_code') ? 'is-invalid' : ''"
								type="text"
							/>
							<span v-if="hasError('bar_code')" class="invalid-feedback" role="alert">
								<strong>{{ getError('bar_code') }}</strong>
							</span>
						</div>
						<div class="form-group">
							<label>Tên sản phẩm SAP</label>
							<small class="text-danger">*</small>
							<input
								v-model="sap_material.name"
								class="form-control"
								id="name"
								name="name"
								placeholder="Yêu cầu nhập tên sản phẩm SAP..."
								v-bind:class="hasError('name') ? 'is-invalid' : ''"
								type="text"
							/>
							<span v-if="hasError('name')" class="invalid-feedback" role="alert">
								<strong>{{ getError('name') }}</strong>
							</span>
						</div>
					</div>

					<div class="modal-footer justify-content-between">
						<button type="submit" title="Submit" class="btn btn-primary">
							{{ is_editing ? 'Cập nhật' : 'Tạo mới' }}
						</button>
						<button type="button" class="btn btn-secondary" @click="resetDialog">
							Reset
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
	import Vue from 'vue';
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogAddUpdateSapMaterial',
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

				sap_material: {
					sap_code: '',
					unit_id: null,
					unit_code: null,
					bar_code: '',
					name: '',
				},
				unit_options: [],

				sap_materials: {
					data: [], // Mảng dữ liệu
					paginate: [], // Mảng thông tin phân trang
				},
				api_url: '/api/master/sap-materials',
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async addSapMaterial() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createSapMaterial();
				} else {
					this.updateSapMaterial();
				}
			},
			async createSapMaterial() {
				try {
					this.is_loading = true;
					const result = await this.api_handler.post('/api/master/sap-materials', {
						sap_code: this.sap_material.sap_code,
						unit_id: this.sap_material.unit_id,
						bar_code: this.sap_material.bar_code,
						name: this.sap_material.name,
					});
					console.log(result);

					if (result.success) {
						if (result.data && Array.isArray(result.data)) {
							this.sap_materials.data.unshift(result.data);
						}
						this.showMessage('success', 'Thêm thành công');
						this.closeDialog();
						await this.refetchData();
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
			async updateSapMaterial() {
				try {
					this.is_loading = true;
					const result = await this.api_handler.put(
						`${this.api_url}/${this.sap_material.id}`,
						this.sap_material,
					);

					// Xử lý dữ liệu trả về (nếu cần)
					if (!result.errors) {
						if (result.data && Array.isArray(result.data)) {
							this.sap_materials.data.push(result.data);
						}
						this.showMessage('success', 'Thêm thành công', result.message);
						this.closeDialog();
						await this.refetchData();
					} else {
						this.errors = result.errors;
						this.showMessage('error', 'Lỗi', result.message);
					}
				} catch (error) {
					this.showMessage('error', 'Cập nhật không thành công');
				}finally {
					this.is_loading = false;
				}
			},

			async fetchOptionsData() {
				try {
					this.is_loading = true;
					const [unit_options, sap_materials] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/master/sap-units'),
							new APIRequest('get', '/api/master/sap-materials'),
						]);
					this.sap_materials = sap_materials;
					this.unit_options = unit_options;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
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
			normalizerOption(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			closeDialog() {
				this.clearForm();
				this.clearErrors();
				$('#DialogAddUpdateSapMaterial').modal('hide');
			},
			resetDialog() {
				this.sap_material.sap_code = null;
				this.sap_material.unit_id = null;
				this.sap_material.unit_code = null;
				this.sap_material.bar_code = '';
				this.sap_material.name = '';
				this.clearErrors();
			},

			clearForm() {
				this.sap_material.sap_code = null;
				this.sap_material.unit_id = null;
				this.sap_material.unit_code= null;
				this.sap_material.bar_code = null;
				this.sap_material.name = null;
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
				this.sap_material.sap_code = item.sap_code;
				this.sap_material.unit_id = item.unit_id;
				this.sap_material.unit_code = item.unit.unit_code;
				this.sap_material.bar_code = item.bar_code;
				this.sap_material.name = item.name;
				this.sap_material.id = item.id;
			},
		},
		computed: {
			rows() {
				return this.sap_materials.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
