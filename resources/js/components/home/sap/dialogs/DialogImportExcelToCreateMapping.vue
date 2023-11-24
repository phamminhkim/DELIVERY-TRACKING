<template>
	<div
		class="modal fade"
		id="DialogImportExcelToCreateMapping"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form @submit.prevent="createNewMapping">
					<div class="modal-header">
						<h4 class="modal-title">
							<span> Tạo dữ liệu mới từ excel </span>
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
							></b-form-file>
							<div class="mt-3">
								Selected file: {{ form.file ? form.file.name : '' }}
							</div>
						</div>
						<div class="form-group">
							<a href="#" @click="downloadTemplate">Download template file mẫu</a>
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
		name: 'DialogImportExcelToCreateMapping',
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
					file: null,
				},

				error_message: '',
			};
		},
		created() {
			// this.fetchMasterData();
		},

		methods: {
			async createNewMapping() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler
						.setHeaders({
							'Content-Type': 'multipart/form-data',
						})
						.post(
							'/api/master/sap-material-mappings/excel',
							{},
							APIHandler.createFormData({
								file: this.form.file,
							}),
						)
						.finally(() => {
							this.resetForm();
							this.is_loading = false;
						});

					this.$showMessage('success', 'Tạo dữ liệu mapping thành công');
					this.closeDialog();
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
					this.error_message = error.response.data.message;
				}
			},
			downloadTemplate() {
				const filename = 'Mapping.xlsx';
				window.location.href = `/excel/${filename}`;
			},
			resetForm() {
				this.form = {
					file: null,
				};

				this.error_message = '';
			},
			closeDialog() {
				$('#DialogImportExcelToCreateMapping').modal('hide');
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
