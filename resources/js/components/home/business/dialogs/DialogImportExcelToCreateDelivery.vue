<template>
	<div
		class="modal fade"
		id="DialogImportExcelToCreateDelivery"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form @submit.prevent="createNewDelivery">
					<div class="modal-header">
						<h4 class="modal-title">
							<span> Tạo vận đơn mới từ excel </span>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<label>Công ty</label>
							<small class="text-danger">(*)</small>
							<treeselect
								v-model="form.company"
								:multiple="false"
								:options="company_options"
								placeholder="Chọn công ty.."
								required
							/>
						</div>

						<div class="form-group">
							<label>Đơn vị vận chuyển</label>
							<small class="text-danger">(*)</small>
							<treeselect
								v-model="form.delivery_partner"
								:multiple="false"
								:options="delivery_partner_options"
								placeholder="Chọn đơn vị vận chuyển.."
								required
							/>
						</div>

						<div class="form-group">
							<label>File</label>
							<small class="text-danger">(*)</small>
							<b-form-file
								v-model="form.file"
								:state="Boolean(form.file)"
								placeholder="Choose a file or drop it here..."
								drop-placeholder="Drop file here..."
							></b-form-file>
							<div class="mt-3">
								Selected file: {{ form.file ? form.file.name : '' }}
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between">
						<button
							type="submit"
							title="Submit"
							class="btn btn-primary"
							id="submit-btn"
						>
							Tạo mới
						</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Đóng
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script>
	import Vue, { reactive } from 'vue';
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from '../../ApiHandler';
	import TextInputSearch from '../../general/controls/TextInputSearch.vue';
	import axios from 'axios';

	export default {
		name: 'DialogImportExcelToCreateDelivery',
		components: {
			Vue,
			Treeselect,
			TextInputSearch,
		},

		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_loading: false,

				form: {
					company: null,
					delivery_partner: null,
					file: null,
				},
				company_options: [],
				delivery_partner_options: [],

				error_message: '',
			};
		},
		created() {
			this.fetchMasterData();
		},

		methods: {
			async fetchMasterData() {
				try {
					const [companies, delivery_partners] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/master/companies'),
							new APIRequest('get', '/api/master/delivery-partners/external'),
						]);

					this.company_options = companies.map((company) => {
						return {
							id: company.code,
							label: `(${company.code}) ${company.name}`,
						};
					});

					this.delivery_partner_options = delivery_partners.map((delivery_partner) => {
						return {
							id: delivery_partner.id,
							label: `(${delivery_partner.code}) ${delivery_partner.name}`,
						};
					});
				} catch (error) {
					console.log(error);
				}
			},

			async createNewDelivery() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler
						.setHeaders({
							'Content-Type': 'multipart/form-data',
						})
						.post(
							'/api/partner/deliveries/excel',
							{},
							APIHandler.createFormData({
								company_code: this.form.company,
								delivery_partner: this.form.delivery_partner,
								file: this.form.file,
							}),
						)
						.finally(() => {
							this.resetForm();
							this.is_loading = false;
						});

					this.$showMessage('success', 'Tạo vận đơn thành công');
					this.closeDialog();
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
					this.error_message = error.response.data.message;
				}
			},
			resetForm() {
				this.form = {
					company: null,
					delivery_partner: null,
					file: null,
				};

				this.error_message = '';
			},
			closeDialog() {
				$('#DialogImportExcelToCreateDelivery').modal('hide');
			},
			addPropertyToObject(obj, key, value) {
				this.$set(obj, key, value);
			},
			removePropertyFromObject(obj, key) {
				this.$delete(obj, key);
			},
		},
	};
</script>
