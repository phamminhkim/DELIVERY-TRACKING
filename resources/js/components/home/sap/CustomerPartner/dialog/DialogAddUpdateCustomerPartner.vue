<template>
	<!-- tạo form -->
	<div
		class="modal fade"
		id="DialogAddUpdateCustomerPartner"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="addCustomerPartner">
					<div class="modal-header">
						<h5 class="modal-title">
							<span>
								{{
									is_editing
										? 'Cập nhật khách hàng đối tác'
										: 'Thêm mới khách hàng đối tác'
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
							<treeselect
								:multiple="false"
								id="customer_group_id"
								placeholder="Chọn nhóm khách hàng.."
								v-model="customer_partner.customer_group_id"
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
							<label>Khách hàng Key</label>
							<input
								v-model="customer_partner.name"
								class="form-control"
								id="name"
								name="name"
								placeholder="Nhập tên nhóm khách hàng..."
								v-bind:class="hasError('name') ? 'is-invalid' : ''"
								type="text"
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
							<label>Mã khách hàng</label>
							<input
								v-model="customer_partner.code"
								class="form-control"
								id="code"
								name="code"
								placeholder="Yêu cầu nhập mã khách hàng..."
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
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>Ghi chú</label>
							<input
								v-model="customer_partner.note"
								class="form-control"
								id="note"
								name="note"
								placeholder="Nhập ghi chú..."
								v-bind:class="hasError('note') ? 'is-invalid' : ''"
								type="text"
								required
							/>

							<span v-if="hasError('note')" class="invalid-feedback" role="alert">
								<strong>{{ getError('note') }}</strong>
								<!-- <div v-for="(error, index) in getError('note')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>LV2</label>
							<input
								v-model="customer_partner.LV2"
								class="form-control"
								id="LV2"
								name="LV2"
								placeholder="Nhập LV2..."
								v-bind:class="hasError('LV2') ? 'is-invalid' : ''"
								type="text"
							/>

							<span v-if="hasError('LV2')" class="invalid-feedback" role="alert">
								<strong>{{ getError('LV2') }}</strong>
								<!-- <div v-for="(error, index) in getError('LV2')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>LV3</label>
							<input
								v-model="customer_partner.LV3"
								class="form-control"
								id="LV3"
								name="LV3"
								placeholder="Nhập LV3..."
								v-bind:class="hasError('LV3') ? 'is-invalid' : ''"
								type="text"
							/>

							<span v-if="hasError('LV3')" class="invalid-feedback" role="alert">
								<strong>{{ getError('LV3') }}</strong>
								<!-- <div v-for="(error, index) in getError('LV3')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
							</span>
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label>LV4</label>
							<input
								v-model="customer_partner.LV4"
								class="form-control"
								id="LV4"
								name="LV4"
								placeholder="Nhập LV4..."
								v-bind:class="hasError('LV4') ? 'is-invalid' : ''"
								type="text"
							/>

							<span v-if="hasError('LV4')" class="invalid-feedback" role="alert">
								<strong>{{ getError('LV4') }}</strong>
								<!-- <div v-for="(error, index) in getError('LV4')" :key="index">
                                    <strong>{{ error }}</strong>
                                    <br />
                                </div> -->
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
	import toastr from 'toastr';
	import 'toastr/toastr.scss';

	export default {
		name: 'DialogAddUpdateCustomerPromotion',
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
				is_active: true,

				customer_partner: {
					customer_group_id: null,
					name: '',
					code: '',
					note: '',
					LV2: '',
					LV3: '',
					LV4: '',
					id: '',
				},
				customer_partners: {
                    data: [], // Mảng dữ liệu
					paginate: [], // Mảng thông tin phân trang
                },
				customer_group_options: [],

				api_url: '/api/master/customer-partners',
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async addCustomerPartner() {
				if (this.is_loading) return;
				this.is_loading = true;

				if (this.is_editing === false) {
					this.createCustomerPartner();
				} else {
					this.updateCustomerPartner();
				}
			},
			async createCustomerPartner() {
				try {
					console.log('createCustomerPartner');
					this.is_loading = true;
					const result = await this.api_handler.post('/api/master/customer-partners', {
						customer_group_id: this.customer_partner.customer_group_id,
						name: this.customer_partner.name,
						code: this.customer_partner.code,
						note: this.customer_partner.note,
						LV2: this.customer_partner.LV2,
						LV3: this.customer_partner.LV3,
						LV4: this.customer_partner.LV4,
					});
					if (result.success) {
						if (result.data && Array.isArray(result.data)) {
							this.customer_partners.data.unshift(result.data);
						}
						this.showMessage('success', 'Thêm thành công', result.message);
						this.closeDialog();
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

			async updateCustomerPartner() {
				try {
					this.is_loading = true;
					const result = await this.api_handler.put(
						`${this.api_url}/${this.customer_partner.id}`,
						this.customer_partner,
					);

					// Xử lý dữ liệu trả về (nếu cần)
					if (result.success) {
						if (result.data && Array.isArray(result.data)) {
							this.customer_partners.data.push(result.data);
						}
						this.showMessage('success', 'Thêm thành công', result.message);
						this.closeDialog();
						await this.refetchData(); // Load the data again after successful creation
					} else {
						this.errors = data.errors;
						this.showMessage('error', 'Thêm không thành công');
					}
				} catch (error) {
					this.showMessage('error', 'Cập nhật không thành công');
				} finally {
					this.is_loading = false;
				}
			},

			async fetchOptionsData() {
				const [customer_partner_options, customer_group_options] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-partners'),
						new APIRequest('get', '/api/master/customer-groups'),
					]);
				this.customer_partners = customer_partner_options;
				this.customer_group_options = customer_group_options;
			},

			normalizer(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			closeDialog() {
				this.clearForm();
				this.clearErrors();
				$('#DialogAddUpdateCustomerPartner').modal('hide');
			},
			resetDialog() {
				this.customer_partner.customer_group_id = null;
				this.customer_partner.name = null;
				this.customer_partner.code = null;
				this.customer_partner.note = null;
				this.customer_partner.LV2 = null;
				this.customer_partner.LV3 = null;
				this.customer_partner.LV4 = null;
				this.clearErrors();
			},

			clearForm() {
				this.customer_partner.customer_group_id = null;
				this.customer_partner.name = null;
				this.customer_partner.code = null;
				this.customer_partner.note = null;
				this.customer_partner.LV2 = null;
				this.customer_partner.LV3 = null;
				this.customer_partner.LV4 = null;
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
                this.customer_partner.customer_group_id = item.customer_group_id;
				this.customer_partner.name = item.name;
				this.customer_partner.code = item.code;
				this.customer_partner.note = item.note;
				this.customer_partner.LV2 = item.LV2;
				this.customer_partner.LV3 = item.LV3;
				this.customer_partner.LV4 = item.LV4;
				this.customer_partner.id = item.id;
			},
		},
		computed: {
			rows() {
				return this.customer_partners.length;
			},
		},
	};
</script>

<style lang="scss" scoped></style>
