<template>
	<div
		class="modal fade"
		id="DialogRawSoHeaderInfo"
		tabindex="-1"
		role="dialog"
		ref="DialogRawSoHeaderInfo"
	>
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<b-overlay :show="is_loading" rounded="sm">
					<div class="modal-header">
						<h4 class="modal-title">
							<i class="fas fa-info-circle" /> Thông tin đơn hàng trích xuất
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="card-body">
						<div class="form-group row">
							<div class="col-lg-8">
								<div>
									<div class="row">
										<div class="form-group col-lg-6">
											<div class="row align-items-center">
												<div class="col-lg-6 p-0 text-lg-right">
													<label class="ml-lg-2 mr-lg-4"
														>Nhóm khách hàng</label
													>
												</div>
												<div class="col-lg-6 p-0">
													<input
														class="form-control"
														:value="raw_so_header?.customer?.group.name"
													/>
												</div>
											</div>
										</div>
										<div class="form-group col-lg-6">
											<div class="row align-items-center">
												<div class="col-lg-6 p-0 text-lg-right">
													<label class="ml-lg-2 mr-lg-4"
														>Mã khách hàng SAP</label
													>
												</div>
												<div class="col-lg-6 p-0">
													<input
														class="form-control"
														:value="raw_so_header?.customer?.code"
													/>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div>
									<div class="row">
										<div class="col-lg-6 form-group">
											<div class="row align-items-center">
												<div class="col-lg-6 p-0 text-lg-right">
													<label class="ml-lg-2 mr-lg-4">Số PO</label>
												</div>
												<div class="col-lg-6 p-0">
													<input
														class="form-control"
														type="text"
														:value="raw_so_header?.po_number"
													/>
												</div>
											</div>
										</div>
										<div class="col-lg-6 form-group">
											<div class="row align-items-center">
												<div class="col-lg-6 p-0 text-lg-right">
													<label class="ml-lg-2 mr-lg-4"
														>Ngày đặt hàng</label
													>
												</div>
												<div class="col-lg-6 p-0">
													<input
														class="form-control"
														:value="raw_so_header?.po_delivery_date"
													/>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group row align-items-center">
									<div class="col-lg-3 p-0 text-lg-right">
										<label class="ml-lg-2 mr-lg-4">Địa chỉ giao</label>
									</div>
									<div class="col-lg-9 p-0">
										<input
											class="form-control"
											:value="raw_so_header?.po_delivery_address"
										/>
									</div>
								</div>
								<div class="form-group row align-items-center">
									<div class="col-lg-3 p-0 text-lg-right">
										<label class="ml-lg-2 mr-lg-4">Ghi chú</label>
									</div>
									<div class="col-lg-9 p-0">
										<input class="form-control" :value="raw_so_header?.note" />
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<div class="row align-items-center">
										<div class="col-lg-6 p-0 text-lg-right">
											<label class="ml-lg-2 mr-lg-4">Người đặt hàng</label>
										</div>
										<div class="col-lg-6 p-0">
											<input
												class="form-control"
												:value="raw_so_header?.po_person"
											/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row align-items-center">
										<div class="col-lg-6 p-0 text-lg-right">
											<label class="ml-lg-2 mr-lg-4">Số điện thoại</label>
										</div>
										<div class="col-lg-6 p-0">
											<input
												class="form-control"
												:value="raw_so_header?.po_phone"
											/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row align-items-center">
										<div class="col-lg-6 p-0 text-lg-right">
											<label class="ml-lg-2 mr-lg-4">Email</label>
										</div>
										<div class="col-lg-6 p-0">
											<input
												class="form-control"
												:value="raw_so_header?.po_email"
											/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row align-items-center">
										<div class="col-lg-6 p-0 text-lg-right">
											<label class="ml-lg-2 mr-lg-4">Ngày giao</label>
										</div>
										<div class="col-lg-6 p-0">
											<input class="form-control" />
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="form-group">
							<button class="btn btn-sm btn-info mr-2 px-4 mb-1">
								Thêm dòng mới
							</button>
							<button class="btn btn-sm btn-info mr-2 px-4 mb-1">Lưu</button>
							<button class="btn btn-sm btn-info mr-2 px-4 mb-1">Coppy</button>
							<button class="btn btn-sm btn-secondary mr-2 px-4 mb-1">
								Download excel
							</button>
							<button class="btn btn-sm btn-secondary px-4 mb-1">Đồng bộ SAP</button>
						</div> -->

						<!-- ################### -->
						<form class="row">
							<div class="form-group col-8">
								<label>Thêm sản phẩm</label>
								<div class="row mb-3">
									<div class="col-md-8">
										<treeselect placeholder="Chọn sản phẩm.." required />
									</div>
									<div class="col-md-2">
										<input class="form-control" required type="number" />
									</div>
									<div class="col-md-2">
										<button class="btn btn-primary" type="submit">Thêm</button>
									</div>
								</div>
							</div>
						</form>
						<!-- ################# -->
						<div class="form-group">
							<b-table
								:items="raw_so_items"
								:fields="fields"
								responsive
								striped
								hover
								:current-page="pagination.current_page"
								:per-page="pagination.item_per_page"
								:tbody-tr-class="rowClass"
								show-empty
							>
								<template #cell(index)="data">
									{{
										data.index +
										(pagination.current_page - 1) * pagination.item_per_page +
										1
									}}
								</template>
								<template #cell(action)="data">
									<b-button variant="danger">
										<i class="fas fa-trash-alt"></i>
									</b-button>
								</template>
							</b-table>
						</div>
						<div class="row">
							<label
								class="col-form-label-sm col-md-2"
								style="text-align: left"
								for=""
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
				</b-overlay>
			</div>
		</div>
	</div>
</template>
<script>
	import APIHandler, { APIRequest } from '../../ApiHandler';
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	export default {
		components: {
			Treeselect,
		},
		props: {
			id: Number,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				raw_so_header: null,
				raw_so_items: [],
				is_loading: false,

				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},

				fields: [
					{
						key: 'index',
						label: '#',
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_item.customer_material.customer_sku_name',
						label: 'Tên Skus-PO',
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_item.quantity',
						label: 'Số lượng PO',
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_item.customer_material.customer_sku_unit',
						label: 'DVT - PO',
						class: 'text-nowrap',
					},
					{
						key: 'sap_material.sap_code',
						label: 'Skus - SAP',
						class: 'text-nowrap',
					},
					{
						key: 'quantity',
						label: 'Số lượng SAP',
						class: 'text-nowrap',
					},
					{
						key: 'sap_material.unit.unit_code',
						label: 'DVT - SAP',
						class: 'text-nowrap',
					},
					{
						key: 'percentage',
						label: 'Tỷ lệ',
						class: 'text-nowrap',
					},
					{
						key: 'action',
						label: 'Action',
						class: 'text-nowrap',
					},
				],
			};
		},
		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.get(`/api/raw-so-headers/${this.id}`);
					this.raw_so_header = data;
					this.raw_so_items = data.raw_so_items;
				} catch (e) {
					console.log(e);
				} finally {
					this.is_loading = false;
				}
			},
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
		},
		watch: {
			id(new_val, old_val) {
				if (!new_val) return;
				this.fetchData();
			},
		},
		computed: {
			rows() {
				return this.raw_so_items.length;
			},
		},
	};
</script>
<style lang=""></style>
