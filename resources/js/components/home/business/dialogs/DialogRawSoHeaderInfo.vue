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
											:value="raw_so_header?.customer?.address"
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
												:value="raw_so_header?.customer?.phone_number"
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
												:value="raw_so_header?.customer?.email"
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
						<div class="form-group">
							<b-table
								:items="raw_so_items"
								:fields="fields"
								responsive
								striped
								hover
							>
								<template #cell(index)="data">
									<span> {{ data.index + 1 }} </span>
								</template>
								<template #cell(action)="data">
									<b-button variant="warning">
										<i class="fas fa-wrench"></i>
									</b-button>
									<b-button variant="danger">
										<i class="fas fa-trash-alt"></i>
									</b-button>
								</template>
							</b-table>
						</div>
					</div>
				</b-overlay>
			</div>
		</div>
	</div>
</template>
<script>
	import APIHandler, { APIRequest } from '../../ApiHandler';
	export default {
		props: {
			id: Number,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				raw_so_header: null,
				raw_so_items: [],
				is_loading: false,
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
		},
		watch: {
			id(new_val, old_val) {
				if (!new_val) return;
				this.fetchData();
			},
		},
	};
</script>
<style lang=""></style>
