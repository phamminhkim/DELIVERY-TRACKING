<template>
	<div
		class="modal fade"
		id="DialogImportExcelToCreateSapMaterial"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form @submit.prevent="createNewSapMaterial">
					<div class="modal-header">
						<h4 class="modal-title">
							<span>Tạo dữ liệu mới từ excel</span>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<label>File</label>
							<small class="text-danger">(*)</small>
							<b-form-file
								v-model="form.file"
								:state="Boolean(form.file)"
								placeholder="Choose a file or drop it here..."
								drop-placeholder="Drop file here..."
								v-bind:class="hasError('file') ? 'is-invalid' : ''"
							></b-form-file>
							<span v-if="hasError('file')" class="text-danger">{{
								getError('file')
							}}</span>
							<div class="mt-3">
								Selected file: {{ form.file ? form.file.name : '' }}
							</div>
						</div>
						<div class="form-group">
							<a href="#" @click="downloadTemplate">Download template file mẫu</a>
						</div>
					</div>
					<!-- Hiển thị thông báo lỗi -->
					<div class="modal-body">
						<div v-if="errors.length > 0">
							<div class="alert alert-danger">
								<ul>
									<li v-for="error in errors" :key="error">{{ error }}</li>
								</ul>
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
						<button type="button" class="btn btn-secondary" @click="resetDialog">
							Reset
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
	import APIHandler from '../../../ApiHandler';
	import { APIRequest } from '../../../ApiHandler';
	import TextInputSearch from '../../../general/controls/TextInputSearch.vue';
	import axios from 'axios';

	export default {
		name: 'DialogImportExcelToCreateSapMaterial',
		components: {
			Vue,
			Treeselect,
			TextInputSearch,
		},
		props: {
			refetchData: Function,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_loading: false,

				form: {
					file: null,
				},
				errors: [],

				error_message: '',
				api_url: '/api/master/sap-materials',
			};
		},
		created() {
			// this.fetchMasterData();
		},

		methods: {
			async createNewSapMaterial() {
				try {
					this.errors = [];
					this.is_loading = true;

					const { data } = await this.api_handler
						.setHeaders({
							'Content-Type': 'multipart/form-data',
						})
						.post(
							'/api/master/sap-materials/excel',
							{},
							APIHandler.createFormData({
								file: this.form.file,
							}),
						);

					if (!data.success) {
						if (Array.isArray(data)) {
							this.sap_materials.push(...data);
						}
						this.showMessage('success', 'Thêm thành công');
						this.closeDialog();
						this.resetForm();
						await this.refetchData(); // Refetch the data
					} else {
						this.errors = data.errors;
						this.showMessage('error', 'Thêm không thành công');
						this.resetForm();
					}
				} catch (error) {
					this.showMessage('error', 'Thêm không thành công');
					this.resetForm();
				} finally {
					this.is_loading = false;
				}
			},
			downloadTemplate() {
				const filename = 'SapMaterial.xlsx';
				window.location.href = `/excel/${filename}`;
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
			clearErrors() {
				this.errors.splice(0, this.errors.length);
			},
			resetDialog() {
				this.clearErrors();
			},

			resetForm() {
				this.form = {
					file: null,
				};
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
			closeDialog() {
				this.resetForm();
				this.resetDialog();
				$('#DialogImportExcelToCreateSapMaterial').modal('hide');
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
