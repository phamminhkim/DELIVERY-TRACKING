<template>
    <div class="modal fade" id="DialogImportExcelToCreateMapping" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <form @submit.prevent="createNewMapping">
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
                <span v-if="hasError('file')" class="text-danger">{{ getError('file') }}</span>
                <div class="mt-3">
                  Selected file: {{ form.file ? form.file.name : '' }}
                </div>
              </div>
              <div class="form-group">
                <a href="#" @click="downloadTemplate">Download template file mẫu</a>
              </div>
            </div>

            <div class="modal-footer justify-content-between">
              <button type="submit" title="Submit" class="btn btn-primary" id="submit-btn">Tạo mới</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
          </form>

          <!-- Hiển thị thông báo lỗi -->
          <div v-if="errors.length > 0" class="modal-footer">
            <div class="alert alert-danger">
              <ul>
                <li v-for="error in errors" :key="error">{{ error }}</li>
              </ul>
            </div>
          </div>
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
				errors: [],

				error_message: '',
			};
		},
		created() {
			// this.fetchMasterData();
		},

		methods: {
			async createNewMapping() {
				try {
					this.errors = [];
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
						);

					if (data.success) {
						this.showMessage('success', 'Thêm thành công');
						this.closeDialog();
						await this.refetchData(); // Load the data again after successful creation
					} else {
						this.errors = data.errors; // Gán giá trị lỗi từ API vào biến errors
						this.showMessage('error', 'Thêm không thành công', this.errors);
					}
				} catch (error) {
					this.showMessage('error', 'Thêm không thành công');
				} finally {
					this.is_loading = false;
					this.resetForm();
				}
			},

			downloadTemplate() {
				const filename = 'Mapping.xlsx';
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
			resetForm() {
				this.form = {
					file: null,
				};

				this.error_message = '';
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
