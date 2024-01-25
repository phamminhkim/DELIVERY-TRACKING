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
														readonly
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
														readonly
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
														v-model="raw_so_header.po_number"
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
														class="form-control form-control-sm"
														type="date"
														data-date=""
														data-date-format="DD/MM/YYYY"
														v-model="po_date"
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
											v-model="raw_so_header.po_delivery_address"
										/>
									</div>
								</div>
								<div class="form-group row align-items-center">
									<div class="col-lg-3 p-0 text-lg-right">
										<label class="ml-lg-2 mr-lg-4">Ghi chú</label>
									</div>
									<div class="col-lg-9 p-0">
										<input class="form-control" v-model="raw_so_header.note" />
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
												v-model="raw_so_header.po_person"
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
												v-model="raw_so_header.po_phone"
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
												v-model="raw_so_header.po_email"
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
											<input
												class="form-control form-control-sm"
												type="date"
												data-date=""
												data-date-format="DD/MM/YYYY"
												v-model="po_delivery_date"
											/>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group d-flex">
							<!-- <button class="btn btn-sm btn-info mr-2 px-4 mb-1">
								Thêm dòng mới
							</button>
							<button class="btn btn-sm btn-info mr-2 px-4 mb-1">Lưu</button>
							<button class="btn btn-sm btn-info mr-2 px-4 mb-1">Coppy</button> -->
							<button class="btn btn-secondary mr-2 px-4 mb-1" @click="exportToExcel">
								Download excel
							</button>
							<button class="btn btn-secondary px-4 mb-1">Đồng bộ SAP</button>

							<div style="flex: 1"></div>
							<button class="btn btn-success px-4 mb-1" @click="updateRawSoHeader">
								Lưu thông tin
							</button>
						</div>
						<!-- ################### -->
						<form class="row">
							<div class="form-group col-8">
								<label>Thêm sản phẩm</label>
								<div class="row mb-3">
									<div class="col-md-8">
										<treeselect
											placeholder="Chọn sản phẩm.."
											required
											:load-options="loadOptions"
											:async="true"
											v-model="selected_sap_material_id"
										/>
									</div>
									<div class="col-md-2">
										<input
											class="form-control"
											required
											type="number"
											v-model="selected_quantity"
										/>
									</div>
									<div class="col-md-2">
										<button
											class="btn btn-primary"
											type="submit"
											@click.prevent="addRawSoItemToRawSoHeader"
										>
											Thêm
										</button>
									</div>
								</div>
							</div>
						</form>
						<!-- ################# -->
						<div class="form-group">
							<b-table
								:items="raw_so_header.raw_so_items"
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
								<template #cell(quantity)="data">
									<input
										type="number"
										class="form-control"
										v-model="data.item.quantity"
									/>
								</template>
								<template #cell(warehouse.name)="data">
									<div class="expanded-cell">
										<treeselect
											v-model="data.item.warehouse_id"
											:options="warehouse_options"
											:normalizer="normalizerOption"
										></treeselect>
									</div>
								</template>

								<template #cell(raw_extract_item_customer_sku_code)="data">
									<span
										v-if="
											data.item.raw_extract_item &&
											data.item.raw_extract_item.customer_material
										"
										>{{
											data.item.raw_extract_item.customer_material
												.customer_sku_code
										}}</span
									>
								</template>
								<template #cell(raw_extract_item_quantity)="data">
									<span v-if="data.item.raw_extract_item">{{
										data.item.raw_extract_item.quantity.toLocaleString(
											locale_format,
										)
									}}</span>
								</template>
								<template #cell(raw_extract_item_customer_sku_unit)="data">
									<span
										v-if="
											data.item.raw_extract_item &&
											data.item.raw_extract_item.customer_material
										"
										>{{
											data.item.raw_extract_item.customer_material
												.customer_sku_unit
										}}</span
									>
								</template>

								<template #cell(raw_extract_item_customer_sku_name)="data">
									<span
										v-if="
											data.item.raw_extract_item &&
											data.item.raw_extract_item.customer_material
										"
										>{{
											data.item.raw_extract_item.customer_material
												.customer_sku_name
										}}</span
									>
								</template>
								<template #cell(price)="data">
									<span v-if="data.item.raw_extract_item">{{
										data.item.raw_extract_item.price.toLocaleString(
											locale_format,
										)
									}}</span>
								</template>

								<template #cell(amount)="data">
									<span v-if="data.item.raw_extract_item">{{
										data.item.raw_extract_item.amount.toLocaleString(
											locale_format,
										)
									}}</span>
								</template>
								<template #cell(action)="data">
									<b-button
										variant="danger"
										@click="deleteRawSoItem(data.item.id)"
									>
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
	import sha256 from 'crypto-js/sha256';
	import { format } from 'path';
	import { saveExcel } from '@progress/kendo-vue-excel-export';

	export default {
		name: 'DialogRawSoHeaderInfo',
		components: {
			Treeselect,
		},
		props: {
			id: Number,
			refetchData: Function,
		},

		data() {
			return {
				locale_format: 'de-DE',
				api_handler: new APIHandler(window.Laravel.access_token),
				raw_so_header: {
					note: '',
					po_date: '',
					po_delivery_address: '',
					po_delivery_date: '',
					po_email: '',
					po_note: '',
					po_number: '',
					po_person: '',
					po_phone: '',
					raw_so_items: [],
					raw_so_items_deleted: [],
				},
				// raw_so_items: [],
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
						key: 'raw_extract_item_customer_sku_code',
						label: 'Mã Skus-PO',
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_item_customer_sku_name',
						label: 'Tên Skus-PO',
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_item_quantity',
						label: 'Số lượng PO',
						class: 'text-nowrap',
					},
					{
						key: 'raw_extract_item_customer_sku_unit',
						label: 'ĐVT - PO',
						class: 'text-nowrap',
					},
					{
						key: 'price',
						label: 'Đơn giá PO',
						class: 'text-nowrap',
					},
					{
						key: 'amount',
						label: 'Thành tiền PO',
						class: 'text-nowrap',
					},

					{
						key: 'sap_material.sap_code',
						label: 'Mã Sku SAP',
						class: 'text-nowrap',
					},
					{
						key: 'sap_material.name',
						label: 'Tên Sku SAP',
						class: 'text-nowrap',
					},
					{
						key: 'quantity',
						label: 'Số lượng SAP',
						class: 'text-nowrap',
					},
					{
						key: 'sap_material.unit.unit_code',
						label: 'ĐVT SAP',
						class: 'text-nowrap',
					},
					{
						key: 'percentage',
						label: 'Tỷ lệ',
						class: 'text-nowrap',
					},
					{
						key: 'warehouse.name',
						label: 'Kho hàng',
						class: 'text-nowrap',
					},
					{
						key: 'note',
						label: 'Ghi chú',
						class: 'text-nowrap',
					},
					{
						key: 'action',
						label: 'Action',
						class: 'text-nowrap',
					},
				],

				selected_sap_material_id: null,
				selected_quantity: null,
				warehouse_options: [],
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.get(`/api/raw-so-headers/${this.id}`);
					console.log(data);
					this.raw_so_header = data;
					this.raw_so_header.raw_so_items_deleted = [];
					// this.raw_so_items = structuredClone(data.raw_so_items);
					// this.$delete(this.raw_so_header, 'raw_so_items');
					// this.original_raw_so_header = structuredClone(this.raw_so_header);
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
			async loadOptions({ action, searchQuery, callback }) {
				if (action === ASYNC_SEARCH) {
					const params = {
						search: searchQuery,
					};
					const { data } = await this.api_handler.get(
						'api/master/sap-materials/minified',
						params,
					);
					let options = data.map((item) => {
						return {
							id: item.id,
							label: `(${item.sap_code}) (${item.unit.unit_code}) ${item.name}`,
						};
					});
					callback(null, options);
				}
			},
			async fetchOptionsData() {
				try {
					this.is_loading = true;
					const [warehouse_options] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/warehouses'),
					]);
					this.warehouse_options = warehouse_options;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			normalizerOption(node) {
				return {
					id: node.id,
					label: `(${node.code}) ${node.name}`,
				};
			},
			closeDialog() {
				$('#DialogRawSoHeaderInfo').modal('hide');
			},
			async addRawSoItemToRawSoHeader() {
				try {
					this.is_loading = true;
					// const { data } = await this.api_handler.post(
					// 	`/api/raw-so-headers/raw-so-items`,
					// 	{},
					// 	{
					// 		raw_so_header_id: this.id,
					// 		sap_material_id: this.selected_sap_material_id,
					// 		quantity: this.selected_quantity,
					// 		is_promotive: this.raw_so_header.is_promotive,
					// 	},
					// );
					//Lấy thông tin sản phẩm
					var res = await this.api_handler.get(
						'api/master/sap-materials?id=' + this.selected_sap_material_id,
					);

					var new_item = {
						sap_material_id: this.selected_sap_material_id,
						quantity: this.selected_quantity,
						sap_material: { ...res.data[0] },

						percentage: '100',
						// is_promotive: this.raw_so_header.is_promotive,
					};
					// this.raw_so_items.push(new_item);
					this.raw_so_header.raw_so_items.push({ ...new_item });

					// this.raw_so_items = [data, ...this.raw_so_items];
					this.selected_sap_material_id = this.selected_quantity = null;

					// this.$showMessage('success', 'Thêm thành công');
				} catch (e) {
					console.log(e);
					// this.$showMessage('danger', 'Thêm thất bại');
				} finally {
					this.is_loading = false;
				}
			},
			// async addRawSoItemToRawSoHeader() {
			// 	try {
			// 		this.is_loading = true;
			// 		const { data } = await this.api_handler.post(
			// 			`/api/raw-so-headers/raw-so-items`,
			// 			{},
			// 			{
			// 				raw_so_header_id: this.id,
			// 				sap_material_id: this.selected_sap_material_id,
			// 				quantity: this.selected_quantity,
			// 				is_promotive: this.raw_so_header.is_promotive,
			// 			},
			// 		);
			// 		this.raw_so_items = [data, ...this.raw_so_items];
			// 		this.selected_sap_material_id = this.selected_quantity = null;

			// 		this.$showMessage('success', 'Thêm thành công');
			// 	} catch (e) {
			// 		console.log(e);
			// 		this.$showMessage('danger', 'Thêm thất bại');
			// 	} finally {
			// 		this.is_loading = false;
			// 	}
			// },
			async deleteRawSoItem(id) {
				try {
					// this.is_loading = true;
					// const { data } = await this.api_handler.delete(
					// 	`/api/raw-so-headers/raw-so-items/${id}`,
					// 	{},
					// );
					this.raw_so_header.raw_so_items_deleted.push(
						this.raw_so_header.raw_so_items.find((item) => item.id === id),
					);
					//Xóa trong mảng hiện tại
					this.raw_so_header.raw_so_items = this.raw_so_header.raw_so_items.filter(
						(item) => item.id !== id,
					);
					//this.raw_so_items = this.raw_so_items.filter((item) => item.id !== id);
					// this.$showMessage('success', 'Xóa thành công');
				} catch (e) {
					console.log(e);
					// this.$showMessage('danger', 'Xóa thất bại');
				} finally {
					this.is_loading = false;
				}
			},
			// async deleteRawSoItem(id) {
			// 	try {
			// 		this.is_loading = true;
			// 		const { data } = await this.api_handler.delete(
			// 			`/api/raw-so-headers/raw-so-items/${id}`,
			// 			{},
			// 		);
			// 		this.raw_so_items = this.raw_so_items.filter((item) => item.id !== id);
			// 		this.$showMessage('success', 'Xóa thành công');
			// 	} catch (e) {
			// 		console.log(e);
			// 		this.$showMessage('danger', 'Xóa thất bại');
			// 	} finally {
			// 		this.is_loading = false;
			// 	}
			// },
			async updateRawSoHeader() {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.patch(
						`/api/raw-so-headers/${this.id}`,
						{},

						this.raw_so_header,
					);
					this.$showMessage('success', 'Cập nhật thành công');
					this.closeDialog();
					await this.refetchData();
				} catch (e) {
					console.log(e);
					this.$showMessage('danger', 'Cập nhật thất bại');
				} finally {
					this.is_loading = false;
				}
			},
			// async exportToExcel() {
			// 	try {
			// 		let clone_fields = structuredClone(this.fields);
			// 		clone_fields.splice(0, 2);
			// 		this.is_loading = true;

			// 		const { data } = await this.api_handler.get(`/api/raw-so-headers/${this.id}`);

			// 		saveExcel({
			// 			data: data.raw_so_items.map((item) => ({
			// 				'Số SO': data.customer.name,
			// 				'Mã Khách hàng': data.customer.code,
			// 				'Mã sản phẩm': item.sap_material.sap_code,
			// 				'Số lượng': item.quantity,
			// 				'Đơn vị tính': item.sap_material.unit.unit_code,
			// 			})),
			// 			fileName: 'Dữ liệu Đơn hàng',
			// 			columns: [
			// 				{
			// 					field: 'Số SO',
			// 					title: 'Số SO',
			// 				},
			// 				{
			// 					field: 'Mã Khách hàng',
			// 					title: 'Mã Khách hàng',
			// 				},
			// 				{
			// 					field: 'Mã sản phẩm',
			// 					title: 'Mã sản phẩm',
			// 				},
			// 				{
			// 					field: 'Số lượng',
			// 					title: 'Số lượng',
			// 				},
			// 				{
			// 					field: 'Đơn vị tính',
			// 					title: 'Đơn vị tính',
			// 				},
			// 				// Add additional columns if needed
			// 			],
			// 		});
			// 	} catch (error) {
			// 		toastr.error('Lỗi', error.response.data.message);
			// 	} finally {
			// 		this.is_loading = false;
			// 	}
			// },

			exportToExcel() {
				let clone_fields = structuredClone(this.fields);
				clone_fields.splice(0, 2);
				this.is_loading = true;

				this.api_handler
					.get(`/api/raw-so-headers/${this.id}`)
					.then((response) => {
						const { data } = response;
						console.log(data.po_person);

						const excelData = data.raw_so_items.map((item) => ({
							po_person: data.po_person,
							raw_extract_item_customer_sku_code:
								item.raw_extract_item.customer_material.customer_sku_code,
							raw_extract_item_customer_sku_name:
								item.raw_extract_item.customer_material.customer_sku_name,
							raw_extract_item_quantity: item.raw_extract_item.quantity,
							raw_extract_item_customer_sku_unit:
								item.raw_extract_item.customer_material.customer_sku_unit,
							price: item.price,
							amount: item.amount,
							sap_material_sap_code: item.sap_material.sap_code, // Thêm thông tin sap_code
							sap_material_name: item.sap_material.name, // Thêm thông tin name
							sap_material_unit_unit_code: item.sap_material.unit.unit_code, // Thêm thông tin unit_code
							quantity: item.quantity,
							percentage: item.percentage,
							warehouse_name: item.warehouse.name, // Thêm thông tin warehouse name
						}));
						saveExcel({
							data: excelData,
							fileName: 'order_data',
							columns: [
								{
									field: 'po_person',
									title: 'Mã khách hàng',
								},
								{
									field: 'raw_extract_item_customer_sku_code',
									title: 'Mã Skus-PO',
								},
								{
									field: 'raw_extract_item_customer_sku_name',
									title: 'Tên Skus-PO',
								},
								{
									field: 'raw_extract_item_quantity',
									title: 'Số lượng PO',
								},
								{
									field: 'raw_extract_item_customer_sku_unit',
									title: 'ĐVT - PO',
								},
								{
									field: 'price',
									title: 'Đơn giá PO',
								},
								{
									field: 'amount',
									title: 'Thành tiền PO',
								},
								{
									field: 'sap_material_sap_code',
									title: 'Mã Sku SAP',
								},
								{
									field: 'sap_material_name',
									title: 'Tên Sku SAP',
								},
								{
									field: 'quantity',
									title: 'Số lượng SAP',
								},
								{
									field: 'sap_material_unit_unit_code',
									title: 'ĐVT SAP',
								},
								{
									field: 'percentage',
									title: 'Tỷ lệ',
								},
								{
									field: 'warehouse_name', // Thêm cột warehouse name
									title: 'Tên Kho',
								},
							],
						});
					})
					.catch((error) => {
						// console.error('Lỗi', error);
						toastr.error('Đã xảy ra lỗi khi xuất Excel');
					})
					.finally(() => {
						this.is_loading = false;
					});
			},
			formatDateYMD(datetime) {
				if (!datetime) return '';
				const date = new Date(datetime);
				const year = date.getFullYear();
				const month = (date.getMonth() + 1).toString().padStart(2, '0');
				const day = date.getDate().toString().padStart(2, '0');
				return `${year}-${month}-${day}`;
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
				return this.raw_so_header.raw_so_items.length;
			},
			po_date: {
				get() {
					return this.formatDateYMD(this.raw_so_header.po_date);
				},
				set(value) {
					this.raw_so_header.po_date = value;
				},
			},
			po_delivery_date: {
				get() {
					return this.formatDateYMD(this.raw_so_header.po_delivery_date);
				},
				set(value) {
					this.raw_so_header.po_delivery_date = value;
				},
			},
		},
	};
</script>
<style lang="css">
	.expanded-cell {
		width: 200px; /* Đặt độ rộng tùy chỉnh cho ô "Kho hàng" */
	}
</style>
