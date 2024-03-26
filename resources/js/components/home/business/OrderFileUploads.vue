<template lang="">
	<div class="container-fluid">
		<div class="p-3 border" style="border-radius: 10px">
			<div>
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="" for="">Nhóm khách hàng</label>
										</div>
									</div>
									<div class="col-md-8">
										<div class="mb-1">
											<treeselect
												:multiple="false"
												id="customer_group_id"
												placeholder="Chọn nhóm khách hàng.."
												v-model="form_filter.customer_group_id"
												:options="customer_group_options"
												:normalizer="normalizerOption"
											></treeselect>
										</div>
									</div>
								</div>
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class=" " for="">Khách hàng</label>
										</div>
									</div>
									<div class="col-md-8">
										<div class="mb-1">
											<treeselect
												placeholder="Chọn khách hàng.."
												:multiple="true"
												:disable-branch-nodes="false"
												v-model="form_filter.customers"
												:async="true"
												:load-options="loadOptions"
												:normalizer="normalizerOption"
												searchPromptText="Nhập tên khách hàng để tìm kiếm.."
											/>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="" for="">Từ ngày</label>
										</div>
									</div>
									<div class="col-md-8">
										<input
											type="date"
											v-model="form_filter.start_date"
											class="form-control form-control-sm"
										/>
									</div>
								</div>
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="" for="">Đến ngày</label>
										</div>
									</div>
									<div class="col-md-8">
										<input
											type="date"
											v-model="form_filter.end_date"
											class="form-control form-control-sm"
										/>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-lg-right">
											<label class="" for="">Trạng thái</label>
										</div>
									</div>
									<div class="col-md-8">
										<treeselect
											placeholder="Chọn trạng thái đơn hàng.."
											:multiple="true"
											:disable-branch-nodes="false"
											v-model="form_filter.statuses"
											:options="file_statuses"
										/>
									</div>
								</div>
								<div class="col-md-6 row align-items-center">
									<div class="col-md-4">
										<div class="text-md-right">
											<label class="" for="">PO khách hàng</label>
										</div>
									</div>
									<div class="col-md-8">
										<input
											type="text"
											v-model="form_filter.sap_so_number"
											placeholder="Nhập PO.."
											class="form-control form-control-sm"
										/>
									</div>
								</div>
							</div>

							<div class="col-md-12" style="text-align: center">
								<button
									type="submit"
									class="btn btn-warning btn-sm mt-1 mb-1"
									@click.prevent="filterData()"
								>
									<i class="fa fa-search"></i>
									Tìm
								</button>
								<button
									type="reset"
									class="btn btn-secondary btn-sm mt-1 mb-1"
									@click.prevent="clearFilter()"
								>
									<i class="fa fa-reset"></i>
									Xóa bộ lọc
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- <div class="row"></div> -->
			</div>
			<div class="form-group">
				<b-button variant="secondary" @click="syncFileSap">
					<strong>
						<i class="fas fa-globe-asia mr-1 text-bold"></i>
						Đồng bộ SAP
					</strong>
				</b-button>
				<b-button
					variant="secondary"
					@click="exportToExcel"
					class="btn-sm ml-1"
					style="height: 38px"
				>
					<strong> <i class="fas fa-download mr-1 text-bold"></i>Download Excel </strong>
				</b-button>
                <b-button variant="secondary" @click="mappingDataSap">
					<strong>
						<i class="fas fa-compass mr-1 text-bold"></i>
						Mapping dữ liệu
					</strong>
				</b-button>

                <b-button variant="secondary" @click="checkInventory">
					<strong>
						<i class="fas fa-check-square mr-1 text-bold"></i>
						Check tồn kho
					</strong>
				</b-button>
                <b-button variant="secondary" @click="orderUpload">
					<strong>
						<i class="fas fa-upload mr-1 text-bold"></i>
						Upload đơn hàng
					</strong>
				</b-button>
			</div>
			<div class="form-group">
				<div>
					<b-table
						:items="order_files"
						:fields="fields"
						responsive
						hover
						striped
						show-empty
						:busy="is_loading"
						:bordered="true"
						:current-page="pagination.current_page"
						:per-page="pagination.item_per_page"
						:tbody-tr-class="rowClass"
					>
						<template #table-busy>
							<div class="text-center text-primary my-2">
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
						</template>
						<template #head(selection)>
							<b-form-checkbox
								class="ml-1"
								v-model="is_select_all"
								@change="selectAll"
							></b-form-checkbox>
						</template>
						<template v-slot:cell(selection)="data">
							<b-form-checkbox
								class="ml-1"
								:value="data.item.id"
								v-model="selected_ids"
							>
							</b-form-checkbox>
						</template>
						<template #cell(action)="data">
							<div>
								<b-button
									variant="warning"
									@click="
										showAlert = true;
										selectedItem = data.item;
									"
								>
									<i class="fas fa-play"></i>
								</b-button>

								<b-modal
									v-model="showAlert"
									title="Thông báo"
									@ok="executeReconvert"
									@cancel="cancelReconvert"
								>
									<template v-slot:modal-title>
										<b-icon
											icon="exclamation-triangle-fill"
											variant="warning"
											class="blink-animation"
										></b-icon>
										Thông báo
									</template>
									<p>Bạn có chắc chắn muốn chuyển đổi lại tệp này?</p>
									<p>Mọi thay đổi sẽ bị mất và trở về dữ liệu gốc ban đầu.</p>
								</b-modal>
								<b-button variant="info" @click="data.toggleDetails"
									><i class="fas fa-info"></i
								></b-button>
								<b-button
									variant="danger"
									@click.prevent="
										showDeleteConfirmation = true;
										deleteItemId = data.item.id;
									"
								>
									<i class="fas fa-trash-alt"></i>
								</b-button>

								<b-modal
									v-model="showDeleteConfirmation"
									title="Xác nhận xóa"
									@ok="deleteFile(deleteItemId)"
									@cancel="showDeleteConfirmation = false"
								>
									<p>Bạn có chắc chắn muốn xóa dữ liệu này?</p>
								</b-modal>
							</div>
						</template>
						<template #row-details="data">
							<b-card>
								<div v-if="data.item.status.code == 40">
									<b-alert variant="danger" show>
										{{
											data.item.file_extract_error.extract_error.name
										}}</b-alert
									>
									<b-list-group style="max-height: 300px; overflow-y: scroll">
										<b-list-group-item
											v-for="(error, index) in JSON.parse(
												data.item.file_extract_error.log.log,
											)"
											:key="index"
											>{{ error }}</b-list-group-item
										>
									</b-list-group>
								</div>
								<b-table
									v-else
									:fields="child_fields"
									:items="data.item.raw_so_headers"
									responsive
									hover
									small
									head-variant="secondary"
								>
									<template #cell(action)="raw_so_header_data">
										<b-button
											variant="danger"
											@click.prevent="showDeleteConfirmation = true"
										>
											<i class="fas fa-trash-alt"></i>
										</b-button>

										<b-modal
											v-model="showDeleteConfirmation"
											title="Xác nhận xóa"
											@ok="
												deleteRawSoHeader(
													raw_so_header_data.item.id,
													data.item,
												)
											"
											@cancel="showDeleteConfirmation = false"
										>
											<p>Bạn có chắc chắn muốn xóa dữ liệu này?</p>
										</b-modal>
										<b-button
											variant="primary"
											v-if="!raw_so_header_data.item.is_promotive"
											@click.prevent="
												createPromoiveRawSoHeader(
													raw_so_header_data.item,
													data.item,
												)
											"
										>
											<i class="fas fa-copy"></i>
										</b-button>
									</template>
									<template #cell(created_at)="data">
										{{ data.value | formatDateTime }}
									</template>
									<template #cell(serial_number)="data">
										<a href="#" @click="showInfoDialog(data.item.id)">
											{{ data.value }}
										</a>
									</template>
								</b-table>
							</b-card>
						</template>
						<!-- <template #cell(path)="data">
							<a href="#"> {{ getFileName(data.value) }} </a>
						</template> -->
						<template #cell(path)="data">
							<a
								href="#"
								@click="downloadFile(data.item.id, getFileName(data.value))"
							>
								{{ getFileName(data.value) }}
							</a>
						</template>
						<template #cell(created_at)="data">
							{{ data.value | formatDateTime }}
						</template>
						<template #cell(status)="data">
							<div class="container">
								<span :class="data.value.badge_class">{{ data.value.name }}</span>
								<button @click="reloadStatus(data.item.id)">
									<i class="fas fa-sync-alt"></i>
								</button>
							</div>
						</template>
					</b-table>
				</div>
				<div class="row">
					<label class="col-form-label-sm col-md-2" style="text-align: left" for=""
						>Số lượng mỗi trang:</label
					>
					<div class="col-md-2">
						<b-form-select
							size="sm"
							v-model="pagination.item_per_page"
							:options="pagination.page_options"
						>
						</b-form-select>
					</div>
					<label
						class="col-form-label-sm col-md-1"
						style="text-align: left"
						for=""
					></label>
					<div class="col-md-3">
						<b-pagination
							v-model="pagination.current_page"
							:total-rows="rows"
							:per-page="pagination.item_per_page"
							size="sm"
							class="ml-1"
						></b-pagination>
					</div>
				</div>
			</div>
		</div>
		<DialogRawSoHeaderInfo :id="viewing_raw_so_header_id" :refetchData="fetchData" />
		<DialogToSAPOrderMapping :refetchData="fetchData" />
		<DialogOrderUpload :refetchData="fetchData" />
	</div>
</template>
<!-- <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.2/dist/FileSaver.min.js"></script> -->
<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import DialogRawSoHeaderInfo from './dialogs/DialogRawSoHeaderInfo.vue';
	import DialogOrderUpload from './dialogs/DialogOrderUpload.vue';
	import DialogToSAPOrderMapping from './dialogs/DialogToSAPOrderMapping.vue';
	import { saveExcel } from '@progress/kendo-vue-excel-export';
	// import { saveAs } from 'file-saver';
	export default {

		components: {
			Treeselect,
			DialogRawSoHeaderInfo,
            DialogToSAPOrderMapping,
            DialogOrderUpload,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_loading: false,

				is_select_all: false,
				selected_ids: [],
				showAlert: false,
				selectedItem: null,
				showDeleteConfirmation: false,
				deleteItemId: null,

				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
				fields: [
					{
						key: 'selection',
						label: 'All',
						stickyColumn: true,
					},
					{
						key: 'created_at',
						label: 'Ngày tạo',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'batch.customer.code',
						label: 'Mã khách hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'path',
						label: 'Tên file PDF',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'batch.customer.group.name',
						label: 'Nhóm khách hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_header.po_number',
						label: 'PO khách hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'status',
						label: 'Trạng thái',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'action',
						label: 'Action',
						sortable: true,
						class: 'text-nowrap',
					},
				],
				child_fields: [
					{
						key: 'created_at',
						label: 'Ngày tạo',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'serial_number',
						label: 'Số đơn hàng',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'po_note',
						label: 'Ghi chú',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'sap_so_number',
						label: 'Đồng bộ SAP',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'sap_so_number',
						label: 'SAP SO',
						sortable: true,
						class: 'text-nowrap',
					},
					{
						key: 'action',
						label: 'Action',
						sortable: true,
						class: 'text-nowrap text-center',
					},
				],

				is_show_search: false,
				form_filter: {
					start_date: '',
					end_date: '',
					statuses: [],
					customers: [],
					customer_group_id: null,
				},
				file_statuses: [
					{ id: 10, label: 'Mới' },
					{ id: 20, label: 'Đang xử lý' },
					{ id: 30, label: 'Hoàn thành' },
					{ id: 40, label: 'Lỗi' },
					{ id: 50, label: 'Xử lý lại' },
					{ id: 60, label: 'Đã chuyển đổi' },
					{ id: 70, label: 'Đang đồng bộ' },
				],
				customer_options: [],
				customer_group_options: [],
				order_files: [],
				viewing_raw_so_header_id: null,
				api_url_order_file: '/api/ai/file',
			};
		},
		created() {
			this.fetchOptionsData();
			this.fetchData();
		},
		methods: {
			async fetchOptionsData() {
				const [customer_group_options] = await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
				]);
				this.customer_group_options = customer_group_options;
			},
			normalizerOption(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const { data } = await this.api_handler.get('api/master/customers/minified', {
						search: searchQuery,
					});
					const options = data;
					callback(null, options);
				}
			},
			async fetchData() {
				try {
					this.is_loading = true;
					const [order_files] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', this.api_url_order_file, {
							from_date:
								this.form_filter.start_date.length == 0
									? undefined
									: this.form_filter.start_date,
							to_date:
								this.form_filter.end_date.length == 0
									? undefined
									: this.form_filter.end_date,
							status_ids: this.form_filter.statuses,
							customer_group_ids: this.form_filter.customer_group_id,
							customer_ids: this.form_filter.customers,
						}),
					]);
					this.order_files = order_files;
					toastr.success('Lấy dữ liệu thành công');
				} catch (error) {
					toastr.error('Lỗi');
				} finally {
					this.is_loading = false;
				}
			},
			async downloadFile(id, fileName) {
				try {
					const response = await this.api_handler.get(
						`api/ai/file/download/${id}`,
						{},
						'blob',
					);
					const blobData = new Blob([response]);

					const url = window.URL.createObjectURL(blobData);
					const link = document.createElement('a');
					link.href = url;
					link.setAttribute('download', fileName);
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);

					// Giải phóng URL đã tạo ra
					window.URL.revokeObjectURL(url);
				} catch (error) {
					// Xử lý lỗi khi không thể tải xuống file
					console.error(error);
				}
			},
			getFileName(path) {
				let name = path.split('/')[1].split('_');
				name.pop();
				return name.join('') + '.pdf';
			},

			showInfoDialog(raw_so_header_id) {
				this.viewing_raw_so_header_id = raw_so_header_id;
				$('#DialogRawSoHeaderInfo').modal('show');
			},
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
			async deleteFile(id) {
				try {
					this.is_loading = true;
					await this.api_handler.delete(this.api_url_order_file + '/' + id);
					this.order_files = this.order_files.filter((item) => item.id !== id);
					toastr.success('Xóa dữ liệu thành công');
				} catch (error) {
					toastr.error('Lỗi');
				} finally {
					this.is_loading = false;
					this.showDeleteConfirmation = false; // Đóng hộp thoại sau khi xác nhận
				}
			},
			async deleteRawSoHeader(id, file_item) {
				try {
					this.is_loading = true;
					await this.api_handler.delete('/api/raw-so-headers/' + id);
					file_item.raw_so_headers = file_item.raw_so_headers.filter(
						(item) => item.id !== id,
					);
					toastr.success('Xóa dữ liệu thành công');
				} catch (error) {
					toastr.error('Lỗi');
				} finally {
					this.is_loading = false;
					this.showDeleteConfirmation = false; // Đóng hộp thoại sau khi xác nhận
				}
			},
			async createPromoiveRawSoHeader(raw_so_header, file_item) {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.post(
						'/api/raw-so-headers/promotive',
						{},
						{
							raw_so_header: raw_so_header.id,
						},
					);
					file_item.raw_so_headers.push(data);
					toastr.success('Tạo đơn hàng khuyến mãi thành công');
				} catch (error) {
					console.log(error, this.order_files);
					toastr.error('Lỗi');
				} finally {
					this.is_loading = false;
				}
			},

			executeReconvert() {
				// Thực hiện chuyển đổi lại tệp
				// Gọi hàm reconvertFile(this.selectedItem) ở đây
				this.reconvertFile(this.selectedItem);
				// Đóng modal
				this.showAlert = false;
			},
			cancelReconvert() {
				// Hủy chuyển đổi lại tệp
				this.showAlert = false;
			},
			async reloadStatus() {
				this.fetchData();
			},

			async reconvertFile(file) {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.post(
						'/api/ai/extract-order/reconvert/' + file.id,
					);
					let index = this.order_files.findIndex((item) => item.id === file.id);
					this.order_files.splice(index, 1, data);
					toastr.success('Đã gửi yêu cầu chuyển đổi lại file');
				} catch (error) {
					toastr.error('Lỗi');
				} finally {
					this.is_loading = false;
				}
			},
			selectAll() {
				this.selected_ids = [];
				if (this.is_select_all) {
					for (let i in this.order_files) {
						this.selected_ids.push(this.order_files[i].id);
					}
				}
			},
			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler.get(this.api_url_order_file, {
						from_date: this.form_filter.start_date,
						to_date: this.form_filter.end_date,
						status_ids: this.form_filter.statuses,
						customer_group_ids: this.form_filter.customer_group_id,
						customer_ids: this.form_filter.customers,
					});

					this.order_files = data;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async clearFilter() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					this.form_filter.start_date = null;
					this.form_filter.end_date = null;
					this.form_filter.statuses = null;
					this.form_filter.customer_group_id = null;
					this.form_filter.customers = [];
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async syncFileSap() {
				try {
					this.is_loading = true;
					if (this.selected_ids.length == 0) {
						toastr.error('Vui lòng chọn ít nhất 1 file');
						return;
					}
					await this.api_handler.patch(
						'/api/raw-so-headers/waiting-sync',
						{},
						{
							waiting_sync_files: this.selected_ids,
						},
					);
					this.selected_ids = [];
					await this.fetchData();
					toastr.success('Đã gửi yêu cầu đồng bộ file');
				} catch (error) {
					toastr.error('Lỗi', error.response.data.message);
				} finally {
					this.is_loading = false;
				}
			},

			async exportToExcel() {
				try {
					this.is_loading = true;

					if (this.selected_ids.length === 0) {
						toastr.error('Vui lòng chọn ít nhất 1 file');
						return;
					}

					const filePromises = this.selected_ids.map((fileId) =>
						this.api_handler.get(`/api/ai/file/${fileId}`),
					);

					const responses = await Promise.all(filePromises);
					const excelData = [];

					for (const response of responses) {
						if (response && response.data) {
							const data = response.data;
							excelData.push(...data);
						}
					}

					// Export to Excel
					this.exportExcel(excelData);
				} catch (error) {
					toastr.error('Lỗi', error.response.data.message);
				} finally {
					this.is_loading = false;
				}
			},
            ////
            mappingDataSap(){
                // this.is_loading = true;
					if (this.selected_ids.length == 0) {
						toastr.error('Vui lòng chọn ít nhất 1 file');
						return;
					}
                $('#DialogToSAPOrderMapping').modal('show');
            },
            checkInventory(){},
            ////

            orderUpload(){

                $('#DialogOrderUpload').modal('show');
            },
			exportExcel(data) {
				const columns = [
					{ field: 'Số SO', title: 'Số SO' },
					{ field: 'Mã Khách hàng', title: 'Mã Khách hàng' },
					{ field: 'Mã sản phẩm', title: 'Mã sản phẩm' },
					{ field: 'Số lượng', title: 'Số lượng', format: '#,##0' }, // Number format
					{ field: 'Đơn vị tính', title: 'Đơn vị tính' },
				];

				saveExcel({
					data: data,
					fileName: 'SAP_SO',
					columns: columns,
				});
			},
		},
		computed: {
			rows() {
				return this.order_files.length;
			},
		},
	};
</script>
<style lang="scss">
	.blink-animation {
		animation: blink 1s infinite;
	}

	@keyframes blink {
		0% {
			opacity: 1;
		}
		50% {
			opacity: 0;
		}
		100% {
			opacity: 1;
		}
	}
	.container {
		position: relative;
		border: none;
		right: 20px;
	}

	.container i.fas.fa-sync-alt {
		position: absolute;
		top: 25px;
		right: 0;
		transform: translate(50%, -50%);
		border: 1px solid black;
		padding: 5px;
	}
</style>
