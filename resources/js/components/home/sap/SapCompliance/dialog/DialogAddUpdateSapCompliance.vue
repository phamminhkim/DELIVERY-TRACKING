<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateSapCompliance"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addSapCompliance">
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
								v-model="sap_compliance.sap_code"
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
						<div class="form-group">
							<label>Mã unit</label>
							<small class="text-danger">*</small>
							<treeselect
								v-model="sap_compliance.unit_id"
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
						<!-- <div class="form-group">
							<label>Mã Barcode</label>
							<small class="text-danger">*</small>
							<input
								v-model="sap_compliance.bar_code"
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
						</div> -->
						<div class="form-group">
							<label>Tên sản phẩm SAP</label>
							<small class="text-danger">*</small>
							<input
								v-model="sap_compliance.name"
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
						<div class="form-group">
							<label>Quy cách</label>
							<!-- <small class="text-danger">*</small> -->
							<input
								v-model="sap_compliance.compliance"
								class="form-control"
								id="compliance"
								name="compliance"
								placeholder="Nhập số lượng quy cách..."
								v-bind:class="hasError('compliance') ? 'is-invalid' : ''"
								type="text"
							/>
							<span v-if="hasError('compliance')" class="invalid-feedback" role="alert">
								<strong>{{ getError('compliance') }}</strong>
							</span>
						</div>
						<div class="modal-body">
						<div class="form-group">
							<label class="mr-5" >Trạng thái</label>
							<div class="form-check form-check-inline">
								<input
									v-model="sap_compliance.check_qc"
									class="form-check-input"
									id="check_qc"
									name="check_qc"
									type="checkbox"
									@change="validateIsActive"
									v-bind:class="hasError('check_qc') ? 'is-invalid' : ''"
								/>
							</div>
							<span
								v-if="hasError('check_qc')"
								class="invalid-feedback"
								role="alert"
							>
								<strong>{{ getError('check_qc') }}</strong>
							</span>
						</div>
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
	import Vue from 'vue';
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogAddUpdateSapCompliance',
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

				sap_compliance: {
					sap_code: '',
					unit_id: null,
					// bar_code: '',
					name: '',
					compliance: '',
					check_qc: false,
				},
				unit_options: [],

				sap_compliances: {
					data: [], // Mảng dữ liệu
					paginate: [], // Mảng thông tin phân trang
				},
				api_url: '/api/master/sap-compliances',
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
            validateIsActive() {
				if (
					this.sap_compliance.check_qc !== true &&
					this.sap_compliance.check_qc !== false
				) {
					this.sap_compliance.check_qc = false; // Giá trị mặc định là false nếu không hợp lệ
				}
			},
			async addSapCompliance() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createSapCompliance();
				} else {
					this.updateSapCompliance();
				}
			},
			async createSapCompliance() {
				try {
					this.is_loading = true;
					const result = await this.api_handler.post('/api/master/sap-compliances', {
						sap_code: this.sap_compliance.sap_code,
						unit_id: this.sap_compliance.unit_id,
						// bar_code: this.sap_compliance.bar_code,
						name: this.sap_compliance.name,
						compliance: this.sap_compliance.compliance,
						check_qc: this.sap_compliance.check_qc ? 1 : 0,
					});
					console.log(result);

					if (!result.errors) {
						if (result.data && Array.isArray(result.data)) {
							this.sap_compliances.data.unshift(result.data);
						}
						this.showMessage('success', 'Thêm thành công', result.message);
						this.closeDialog();
						await this.refetchData();
					} else {
						this.errors = result.errors;
						this.showMessage('error', 'Thêm không thành công');
					}
				} catch (error) {
					this.showMessage('error', 'Lỗi', error);
				}finally {
					this.is_loading = false;
				}
			},
			async updateSapCompliance() {
				try {
					this.is_loading = true;
                    const request = {
						sap_code: this.sap_compliance.sap_code,
						unit_id: this.sap_compliance.unit_id,
						// bar_code: this.sap_compliance.bar_code,
						name: this.sap_compliance.name,
						compliance: this.sap_compliance.compliance,
						check_qc: this.sap_compliance.check_qc ? 1 : 0,
					};
					const data = await this.api_handler.put(
						`${this.api_url}/${this.sap_compliance.id}`,
						request,
					);
					// Xử lý dữ liệu trả về (nếu cần)
					if (!data.success) {
						if (data.data && Array.isArray(data.data)) {
							this.sap_compliances.data.push(data.data);
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
				}finally {
					this.is_loading = false;
				}
			},

			async fetchOptionsData() {
				try {
					this.is_loading = true;
					const [unit_options, sap_compliances] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/master/sap-units'),
							new APIRequest('get', '/api/master/sap-compliances'),
						]);
					this.sap_compliances = sap_compliances;
					this.unit_options = unit_options.map((unit) => {
						return {
							id: unit.id,
							label: `(${unit.id}) ${unit.unit_code}`,
						};
					});
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
				$('#DialogAddUpdateSapCompliance').modal('hide');
			},
			resetDialog() {
				this.sap_compliance.sap_code = null;
				this.sap_compliance.unit_id = null;
				// this.sap_compliance.bar_code = '';
				this.sap_compliance.name = '';
				this.sap_compliance.compliance = '';
				this.sap_compliance.check_qc = '';
				this.clearErrors();
			},

			clearForm() {
				this.sap_compliance.sap_code = null;
				this.sap_compliance.unit_id = null;
				// this.sap_compliance.bar_code = null;
				this.sap_compliance.name = null;
				this.sap_compliance.compliance = '';
				this.sap_compliance.check_qc = '';
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
			'sap_compliance.check_qc': function (newVal) {
				this.sap_compliance.check_qc = newVal;
			},

			editing_item: function (item) {
				console.log(item);
				this.sap_compliance.sap_code = item.sap_code;
				this.sap_compliance.unit_id = item.unit_id;
				// this.sap_compliance.bar_code = item.bar_code;
				this.sap_compliance.name = item.name;
				this.sap_compliance.compliance = item.compliance;
				this.sap_compliance.check_qc = item.check_qc;
				this.sap_compliance.id = item.id;
			},
		},
		computed: {
			rows() {
				return this.sap_compliances.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
