<template>
	<div class="container-fluid">
		<b-tabs content-class="mt-3">
			<b-tab title="Thống kê" active>
				<div class="row">
					<div class="col-md-12">
						<div class="header d-flex">
							<p class="title" style="flex: 6">Thống kê</p>
							<div style="flex: 3">
								<treeselect
									placeholder="Chọn đơn vị vận chuyển.."
									v-model="filter_delivery_partner"
									:options="filter_delivery_partner_options"
								/>
							</div>
							<div style="flex: 1">
								<treeselect
									placeholder="Chọn tháng.."
									v-model="filter_time"
									:options="filter_time_options"
								/>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Thống kê giao hàng</h3>
										<div class="card-tools">
											<button
												type="button"
												class="btn btn-tool"
												data-card-widget="collapse"
											>
												<i class="fas fa-minus"></i>
											</button>
										</div>
									</div>
									<div class="card-body p-0" style="display: block">
										<ul class="nav nav-pills flex-column">
											<li class="nav-item statistic">
												<div class="progress-group">
													Số đơn đúng hạn / Số đơn đã giao
													<span class="float-right"
														><b>{{
															dashboard_statistic.ontime_orders_count
														}}</b
														>/{{
															dashboard_statistic.delivered_orders_count
														}}</span
													>
													<b-progress
														:max="
															dashboard_statistic.delivered_orders_count
														"
														show-progress
														height="1.5rem"
													>
														<b-progress-bar
															:style="`background-color: ${getProcessColor(
																dashboard_statistic.ontime_orders_count,
																dashboard_statistic.delivered_orders_count,
															)}`"
															:value="
																dashboard_statistic.ontime_orders_count
															"
															:label="`${calculatePercent(
																dashboard_statistic.ontime_orders_count,
																dashboard_statistic.delivered_orders_count,
															)}%`"
														></b-progress-bar
													></b-progress>
												</div>
											</li>
											<li class="nav-item statistic">
												<div class="progress-group">
													Số đơn đã xác nhận nhận hàng / Số đơn đã giao
													<span class="float-right"
														><b>{{
															dashboard_statistic.received_orders_count
														}}</b
														>/{{
															dashboard_statistic.delivered_orders_count
														}}</span
													>
													<b-progress
														:max="
															dashboard_statistic.delivered_orders_count
														"
														show-progress
														height="1.5rem"
													>
														<b-progress-bar
															:style="`background-color: ${getProcessColor(
																dashboard_statistic.received_orders_count,
																dashboard_statistic.delivered_orders_count,
															)}`"
															:value="
																dashboard_statistic.received_orders_count
															"
															:label="`${calculatePercent(
																dashboard_statistic.received_orders_count,
																dashboard_statistic.delivered_orders_count,
															)}%`"
														></b-progress-bar
													></b-progress>
												</div>
											</li>
											<li class="nav-item statistic">
												<div class="progress-group">
													Số đơn đã đánh giá / Số đơn đã nhận hàng
													<span class="float-right"
														><b>{{
															dashboard_statistic.reviewed_orders_count
														}}</b
														>/{{
															dashboard_statistic.received_orders_count
														}}</span
													>
													<b-progress
														:max="
															dashboard_statistic.received_orders_count
														"
														show-progress
														height="1.5rem"
													>
														<b-progress-bar
															:style="`background-color: ${getProcessColor(
																dashboard_statistic.reviewed_orders_count,
																dashboard_statistic.received_orders_count,
															)}`"
															:value="
																dashboard_statistic.reviewed_orders_count
															"
															:label="`${calculatePercent(
																dashboard_statistic.reviewed_orders_count,
																dashboard_statistic.received_orders_count,
															)}%`"
														></b-progress-bar
													></b-progress>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>

							<div class="col-md-8">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Thống kê theo tiêu chí đánh giá</h3>
										<div class="card-tools">
											<button
												type="button"
												class="btn btn-tool"
												data-card-widget="collapse"
											>
												<i class="fas fa-minus"></i>
											</button>
										</div>
									</div>
									<div class="card-body p-0" style="display: block">
										<ul class="nav nav-pills flex-column">
											<li
												v-for="(
													criteria_statistic, index
												) in criteria_statistics"
												:key="index"
												class="nav-item"
											>
												<a
													href="#"
													class="nav-link"
													v-b-toggle="`collapse-${index}`"
													@click="onOpenCriteria(criteria_statistic.id)"
												>
													<i class="fas fa-inbox"></i>
													{{ criteria_statistic.name }}
													<span class="badge bg-primary float-right">{{
														criteria_statistic.amount
													}}</span>
												</a>
												<b-collapse
													:id="`collapse-${index}`"
													accordion="my-accordion"
												>
													<div
														style="
															overflow-y: scroll;
															height: 125px;
															margin-left: 20px;
															margin-right: 20px;
															margin-bottom: 10px;
															border: 1px solid #e9ecef;
														"
													>
														<ul class="nav nav-pills flex-column">
															<li
																class="nav-item"
																v-for="(
																	order, index
																) in order_by_criterias"
																:key="index"
															>
																<a
																	href="#"
																	class="nav-link"
																	@click.prevent="
																		showOrderInfoDialog(
																			order.id,
																		)
																	"
																>
																	<i class="far fa-envelope"></i>
																	{{
																		`SO: ${order.sap_so_number} DO: ${order.sap_do_number}`
																	}}
																</a>
															</li>
														</ul>
													</div>
												</b-collapse>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<!-- <div class="card-footer" style="display: block">
					<div class="row">
						<div class="col-sm-3 col-6">
							<div class="description-block border-right">
								<span class="description-percentage text-success"
									><i class="fas fa-caret-up"></i> 17%</span
								>
								<h5 class="description-header">$35,210.43</h5>
								<span class="description-text">TOTAL REVENUE</span>
							</div>
						</div>

						<div class="col-sm-3 col-6">
							<div class="description-block border-right">
								<span class="description-percentage text-warning"
									><i class="fas fa-caret-left"></i> 0%</span
								>
								<h5 class="description-header">$10,390.90</h5>
								<span class="description-text">TOTAL COST</span>
							</div>
						</div>

						<div class="col-sm-3 col-6">
							<div class="description-block border-right">
								<span class="description-percentage text-success"
									><i class="fas fa-caret-up"></i> 20%</span
								>
								<h5 class="description-header">$24,813.53</h5>
								<span class="description-text">TOTAL PROFIT</span>
							</div>
						</div>

						<div class="col-sm-3 col-6">
							<div class="description-block">
								<span class="description-percentage text-danger"
									><i class="fas fa-caret-down"></i> 18%</span
								>
								<h5 class="description-header">1200</h5>
								<span class="description-text">GOAL COMPLETIONS</span>
							</div>
						</div>
					</div>
				</div>  -->
					</div>
				</div>
			</b-tab>
			<b-tab title="Báo cáo">
				<div class="row">
					<div class="col-md-12">
						<div class="header d-flex">
							<p class="title" style="flex: 6">Báo cáo chi tiết</p>
							<div>
								<div class="form-group row">
									<div class="btn-group">
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
									<button
										@click="filterData()"
										class="btn btn-secondary btn-xs ml-1"
									>
										<i class="fas fa-sync-alt" title="Tải lại"></i>
									</button>
								</div>
							</div>
						</div>
						<div>
							<!-- <div class="col-md-9">
								<div class="form-group row">
									<div class="btn-group">
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
									<button
										@click="filterData()"
										class="btn btn-secondary btn-xs ml-1"
									>
										<i class="fas fa-sync-alt" title="Tải lại"></i>
									</button>
								</div>
							</div> -->

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
														:options="
															form_filter_options.delivery_partner
														"
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
										<h6 class="text-center">
											Không có vận đơn nào để hiển thị
										</h6>
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
											(pagination.current_page - 1) *
												pagination.item_per_page +
											1
										}}
									</template>
									<template #cell(start_delivery_date)="data">
										{{
											data.item.deliveries[0]?.start_delivery_date?.split(
												' ',
											)[0]
										}}
									</template>
									<template #cell(complete_delivery_date)="data">
										<span>{{
											data.item.deliveries[0]?.complete_delivery_date?.split(
												' ',
											)[0]
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
					</div>
				</div>
			</b-tab>
		</b-tabs>

		<DialogOrderInfo :order="viewing_order" />
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from './ApiHandler';
	import DialogOrderInfo from './business/dialogs/DialogOrderInfo.vue';
	export default {
		components: {
			Treeselect,
			DialogOrderInfo,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				token: 'Bearer ' + window.Laravel.access_token,

				// filter_time: null,
				// filter_options: [
				// 	{ id: 1, label: 'Đơn hàng mới' },
				// 	{ id: 2, label: 'Đơn hàng đã xác nhận' },
				// 	{ id: 3, label: 'Đơn hàng đang giao' },
				// 	{ id: 4, label: 'Đơn hàng đã giao' },
				// 	{ id: 5, label: 'Đơn hàng đã hủy' },
				// ],

				filter_time: null,
				filter_time_options: [],

				filter_delivery_partner: null,
				filter_delivery_partner_options: [],

				dashboard_statistic: {},
				criteria_statistics: [],

				order_by_criterias: [],

				viewing_order: {},

				form_filter: {
					start_date: null,
					end_date: null,
					companies: [],
					customers: [],
					delivery_partner: [],
				},
				form_filter_options: {
					companies: [],
					// customers: [],
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
						label: 'Thòi gian giao hàng theo HĐ',
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
			this.generateFilterTimeOption();
			// await this.fetchcData();
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
			async fetchcData() {
				try {
					const [
						dashboard_statistic,
						criteria_statistics,
						filter_delivery_partner_options,
					] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/dashboard', {
							delivery_partner_ids: this.filter_delivery_partner
								? [this.filter_delivery_partner]
								: undefined,
							month_year: this.filter_time ? this.filter_time : undefined,
						}),
						new APIRequest('get', '/api/dashboard/criteria', {
							delivery_partner_ids: this.filter_delivery_partner
								? [this.filter_delivery_partner]
								: undefined,
							month_year: this.filter_time ? this.filter_time : undefined,
						}),
						new APIRequest('get', '/api/master/delivery-partners'),
					]);
					this.dashboard_statistic = dashboard_statistic;
					this.criteria_statistics = criteria_statistics;
					this.filter_delivery_partner_options = filter_delivery_partner_options.map(
						(delivery_partner) => {
							return {
								id: delivery_partner.id,
								label: delivery_partner.name,
							};
						},
					);
				} catch (error) {
					console.log(error);
				}
			},
			async fetchReportData() {
				try {
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

			generateFilterTimeOption() {
				const currentYear = new Date().getFullYear();
				const currentMonth = new Date().getMonth() + 1;
				const filter_time_options = [];
				for (let year = currentYear; year >= 2023; year--) {
					for (let month = 12; month >= 1; month--) {
						if (year === currentYear && month > currentMonth) {
							continue;
						}
						if (year === 2023 && month < 8) {
							break;
						}
						filter_time_options.push({
							id: `${month}-${year}`,
							label: `${month}/${year}`,
						});
					}
				}
				this.filter_time_options = filter_time_options;
				this.filter_time = this.filter_time_options[0].id;
			},

			calculatePercent(amount, total) {
				return Math.floor((amount / total) * 100);
			},
			getProcessColor(current_value, total_value) {
				const percent = this.calculatePercent(current_value, total_value);
				let red, green;

				if (percent < 50) {
					// Transition from red to yellow
					red = 255;
					green = (percent * 2 * 200) / 100;
				} else {
					// Transition from yellow to green
					red = 240 - ((percent - 50) * 2 * 255) / 100;
					green = 200;
				}

				return `rgb(${red}, ${green}, 0)`;
			},
			async onOpenCriteria(criteria_id) {
				try {
					this.order_by_criterias = [];
					const { data } = await this.api_handler.get('api/partner/orders/minified', {
						criteria_ids: [criteria_id],
						month_year: this.filter_time ? this.filter_time : undefined,
					});
					this.order_by_criterias = data;
				} catch (error) {
					console.log(error);
				}
			},
			async showOrderInfoDialog(order_id) {
				try {
					const { data } = await this.api_handler.get(
						`api/customer/orders/${order_id}/expanded`,
					);
					this.viewing_order = data;
					$('#DialogOrderInfo').modal('show');
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
		},
		watch: {
			filter_delivery_partner: async function (newVal, oldVal) {
				await this.fetchcData();
			},
			filter_time: async function (newVal, oldVal) {
				await this.fetchcData();
			},
		},
		computed: {
			rows() {
				return this.orders.length;
			},
		},
	};
</script>

<style>
	.title {
		font-size: 1.5rem;
		font-weight: 500;
	}
	.statistic {
		margin: 5px 10px 5px 10px !important;
	}
	.card-header {
		background-color: transparent;
	}
</style>
