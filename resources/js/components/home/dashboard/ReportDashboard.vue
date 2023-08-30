<template>
	<div class="row">
		<div class="col-md-12">
			<div class="header d-flex">
				<p class="title" style="flex: 6">Báo cáo chi tiết</p>
				<div>
					<div class="form-group row">
						<div class="btn-group">
							<button
								type="button"
								class="btn btn-info btn-xs mr-2"
								@click="exportToExcel"
							>
								<strong><i class="fas fa-file-excel mr-1" />Xuất file excel</strong>
							</button>
							<button
								type="button"
								class="btn btn-warning btn-xs"
								@click="is_show_search = !is_show_search"
								v-b-toggle.collapse-1
							>
								<span v-if="!is_show_search">Hiện tìm kiếm</span>
								<span v-if="is_show_search">Ẩn tìm kiếm</span>
							</button>
							<button
								type="button"
								class="btn btn-warning btn-xs"
								@click="is_show_search = !is_show_search"
								v-b-toggle.collapse-1
							>
								<i v-if="is_show_search" class="fas fa-angle-up"></i>
								<i v-else class="fas fa-angle-down"></i>
							</button>
						</div>
						<button @click="filterData()" class="btn btn-secondary btn-xs ml-1">
							<i class="fas fa-sync-alt" title="Tải lại"></i>
						</button>
					</div>
				</div>
			</div>
			<div>
				<b-collapse class="row" id="collapse-1">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-body">
								<div class="form-group row">
									<label
										for="start_date"
										class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
										>Từ ngày</label
									>
									<div class="col-sm-4">
										<input
											type="date"
											v-model="form_filter.start_date"
											class="form-control form-control-sm mt-1"
										/>
									</div>
									<label
										class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
										for=""
										>Đến ngày</label
									>
									<div class="col-sm-4">
										<input
											type="date"
											v-model="form_filter.end_date"
											class="form-control form-control-sm mt-1"
										/>
									</div>
								</div>
								<div class="form-group row">
									<label
										class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
										for=""
										>Công ty</label
									>
									<div class="col-sm-10 mt-1 mb-1">
										<treeselect
											placeholder="Chọn công ty.."
											:multiple="true"
											:disable-branch-nodes="false"
											v-model="form_filter.companies"
											:options="form_filter_options.companies"
										/>
									</div>
								</div>
								<div class="form-group row">
									<label
										class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
										for=""
										>Khách hàng</label
									>
									<div class="col-sm-10 mt-1 mb-1">
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
								<div class="form-group row">
									<label
										class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
										for=""
										>Nhà vận chuyển</label
									>
									<div class="col-sm-10 mt-1 mb-1">
										<treeselect
											placeholder="Chọn nhà vận chuyển.."
											:multiple="true"
											:disable-branch-nodes="false"
											v-model="form_filter.delivery_partner"
											:options="form_filter_options.delivery_partner"
											:normalizer="normalizerOption"
										/>
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
				</b-collapse>

				<div class="row">
					<b-table
						responsive
						hover
						striped
						show-empty
						:busy="is_loading"
						:bordered="true"
						:current-page="pagination.current_page"
						:per-page="pagination.item_per_page"
						:fields="fields"
						:items="orders"
					>
						<template #empty="scope">
							<h6 class="text-center">Không có vận đơn nào để hiển thị</h6>
						</template>
						<template #table-busy>
							<div class="text-center text-primary my-2">
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
						</template>

						<template #cell(index)="data">
							{{
								data.index +
								(pagination.current_page - 1) * pagination.item_per_page +
								1
							}}
						</template>
						<template #cell(start_delivery_date)="data">
							{{ data.item.deliveries[0]?.start_delivery_date?.split(' ')[0] }}
						</template>
						<template #cell(complete_delivery_date)="data">
							<span>{{
								data.item.deliveries[0]?.complete_delivery_date?.split(' ')[0]
							}}</span>
							<b-badge
								variant="danger"
								v-if="data.item.deliveries[0].is_late_deadline"
								><i class="fas fa-fire text-white mr-1"></i>Trễ hạn
							</b-badge>
							<b-badge
								variant="warning"
								v-if="data.item.deliveries[0].is_near_deadline"
								>Gần đến hạn
							</b-badge>
						</template>
						<template #cell(delivery_address)="data">
							{{ formatAddress(data.item.detail?.delivery_address) }}
						</template>
					</b-table>
				</div>
				<!-- end tạo nút -->
				<!-- phân trang -->
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
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import { saveExcel } from '@progress/kendo-vue-excel-export';
	export default {
		components: {
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				token: 'Bearer ' + window.Laravel.access_token,

				form_filter: {
					start_date: null,
					end_date: null,
					companies: [],
					customers: [],
					delivery_partner: [],
				},
				form_filter_options: {
					companies: [],
					delivery_partner: [],
				},
				is_show_search: false,
				is_loading: false,

				orders: [],
				fields: [
					{
						key: 'index',
						label: 'STT',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'start_delivery_date',
						label: 'Ngày xuất kho',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'sap_do_number',
						label: 'Phiếu xuất kho',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'customer.code',
						label: 'Mã khách hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'customer.name',
						label: 'Khách hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_address',
						label: 'Nơi đến',
						sortable: true,
						class: 'text-nowrap text-center',
						width: '50px',
					},
					{
						key: 'detail.total_item',
						label: 'Số thùng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'detail.total_weight',
						label: 'Trọng lượng (KG)',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'complete_delivery_date',
						label: 'Ngày NPP nhận',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'duration',
						label: 'Thời gian giao hàng theo HĐ',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'detail.note',
						label: 'Ghi chú',
						sortable: true,
						class: 'text-nowrap text-center',
					},
				],
				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
			};
		},
		async created() {
			(this.form_filter = {
				start_date: this.formatDate(this.subtractDate(new Date(), 0, 1, 0)),
				end_date: this.formatDate(new Date()),
				companies: [],
				customers: [],
				delivery_partner: [],
			}),
				await Promise.all([this.fetchReportData(), this.fetchOptionsData()]);
		},
		methods: {
			async fetchReportData() {
				try {
					this.is_loading = true;
					const { data } = await this.api_handler.get('api/dashboard/report', {
						from_date: this.form_filter.start_date,
						to_date: this.form_filter.end_date,
						customer_ids: this.form_filter.customers,
						delivery_partner_ids: this.form_filter.delivery_partner,
						company_codes: this.form_filter.companies,
					});
					this.orders = data;
				} catch (error) {
					console.log(error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchOptionsData() {
				try {
					const [company_options, delivery_partner_options] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/master/companies'),
							new APIRequest('get', '/api/master/delivery-partners'),
						]);
					this.form_filter_options.companies = company_options.map((company) => {
						return {
							id: company.code,
							label: company.name,
						};
					});
					this.form_filter_options.delivery_partner = delivery_partner_options;
				} catch (error) {
					console.log(error);
				}
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
			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler.get('api/dashboard/report', {
						from_date: this.form_filter.start_date,
						to_date: this.form_filter.end_date,
						customer_ids: this.form_filter.customers,
						delivery_partner_ids: this.form_filter.delivery_partner,
						company_codes: this.form_filter.companies,
					});
					this.orders = data;
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

					this.form_filter.start_date = this.formatDate(
						this.subtractDate(new Date(), 0, 1, 0),
					);
					this.form_filter.end_date = this.formatDate(new Date());
					this.form_filter.customers = [];
					this.form_filter.delivery_partner = [];
					this.form_filter.companies = [];
					await this.fetchReportData();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			subtractDate(date, sub_date = 0, sub_month = 0, sub_year = 0) {
				date.setDate(date.getDate() - sub_date);
				date.setMonth(date.getMonth() - sub_month);
				date.setFullYear(date.getFullYear() - sub_year);
				return date;
			},
			formatDate(date) {
				var d = new Date(date),
					month = '' + (d.getMonth() + 1),
					day = '' + d.getDate(),
					year = d.getFullYear();

				if (month.length < 2) month = '0' + month;
				if (day.length < 2) day = '0' + day;

				return [year, month, day].join('-');
			},
			formatAddress(address) {
				if (!address) return '';
				const addr_arr = address.split(',');
				return [addr_arr[addr_arr.length - 2], addr_arr[addr_arr.length - 1]].join(', ');
			},
			exportToExcel() {
				const clone_orders = structuredClone(this.orders);
				const data = clone_orders.map((order) => {
					order.deliveries[0].start_delivery_date = order.deliveries[0]
						.start_delivery_date
						? order.deliveries[0].start_delivery_date.split(' ')[0]
						: '';
					let expanded_string = '';
					if (order.deliveries[0].is_late_deadline) {
						expanded_string += '(Trễ hạn)';
					}
					if (order.deliveries[0].is_near_deadline) {
						expanded_string += '(Gần đến hạn)';
					}
					console.log(expanded_string);
					order.deliveries[0].complete_delivery_date = `${
						order.deliveries[0].complete_delivery_date
							? order.deliveries[0].complete_delivery_date?.split(' ')[0]
							: ''
					} ${expanded_string}`;

					order.delivery_address = this.formatAddress(order.detail?.delivery_address);
					return order;
				});

				saveExcel({
					data: data,
					fileName: 'orders_reported',
					columns: [
						{
							field: 'deliveries[0].start_delivery_date',
							title: 'Ngày xuất kho',
						},
						{
							field: 'sap_do_number',
							title: 'Phiếu xuất kho',
						},
						{
							field: 'customer.code',
							title: 'Mã khách hàng',
						},
						{
							field: 'customer.name',
							title: 'Khách hàng',
						},
						{
							field: 'delivery_address',
							title: 'Nơi đến',
						},
						{
							field: 'detail.total_item',
							title: 'Số thùng',
						},
						{
							field: 'detail.total_weight',
							title: 'Trọng lượng (KG)',
						},
						{
							field: 'deliveries[0].complete_delivery_date',
							title: 'Ngày NPP nhận',
						},
						{
							field: 'duration',
							title: 'Thời gian giao hàng theo HĐ',
						},
						{
							field: 'detail.note',
							title: 'Ghi chú',
						},
					],
				});
			},
		},
		computed: {
			rows() {
				return this.orders.length;
			},
		},
	};
</script>
