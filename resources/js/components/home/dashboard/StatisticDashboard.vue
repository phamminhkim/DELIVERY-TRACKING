<template>
	<div class="row">
		<div class="col-md-12">
			<div class="header d-flex">
				<p class="title" style="flex: 2">Thống kê</p>
				<div style="flex: 3">
					<treeselect
						placeholder="Chọn đơn vị vận chuyển.."
						v-model="filter_delivery_partner"
						:options="filter_delivery_partner_options"
						:disabled="
							filter_delivery_partner_options &&
							filter_delivery_partner_options.length == 1
						"
					/>
				</div>

				<div style="flex: 3">
					<treeselect
						placeholder="Chọn nhà kho.."
						v-model="filter_warehouse"
						:options="filter_warehouse_options"
					/>
				</div>

				<div style="flex: 3">
					<treeselect
						placeholder="Chọn kênh phân phối.."
						v-model="filter_distribution_channel"
						:options="filter_distribution_channel_options"
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
							<div
								class="text-center text-primary my-2"
								v-if="is_loading"
								style="opacity: 0.5"
							>
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
							<ul v-else class="nav nav-pills flex-column">
								<li class="nav-item statistic">
									<div class="progress-group">
										Số đơn trễ hạn / Số đơn đang giao
										<span class="float-right"
											><b>{{ dashboard_statistic.late_orders_count }}</b
											>/{{
												dashboard_statistic.delivering_orders_count
											}}</span
										>
										<b-progress
											:max="dashboard_statistic.delivering_orders_count"
											show-progress
											height="1.5rem"
										>
											<b-progress-bar
												:style="`background-color: ${getProcessColor(
													dashboard_statistic.late_orders_count,
													dashboard_statistic.delivering_orders_count,
													true,
												)}`"
												:value="dashboard_statistic.late_orders_count"
												:label="`${calculatePercent(
													dashboard_statistic.late_orders_count,
													dashboard_statistic.delivering_orders_count,
												)}%`"
											></b-progress-bar
										></b-progress>
									</div>
								</li>
								<li class="nav-item statistic">
									<div class="progress-group">
										Số đơn đúng hạn / Số đơn đã giao
										<span class="float-right"
											><b>{{ dashboard_statistic.ontime_orders_count }}</b
											>/{{ dashboard_statistic.delivered_orders_count }}</span
										>
										<b-progress
											:max="dashboard_statistic.delivered_orders_count"
											show-progress
											height="1.5rem"
										>
											<b-progress-bar
												:style="`background-color: ${getProcessColor(
													dashboard_statistic.ontime_orders_count,
													dashboard_statistic.delivered_orders_count,
												)}`"
												:value="dashboard_statistic.ontime_orders_count"
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
											><b>{{ dashboard_statistic.received_orders_count }}</b
											>/{{ dashboard_statistic.delivered_orders_count }}</span
										>
										<b-progress
											:max="dashboard_statistic.delivered_orders_count"
											show-progress
											height="1.5rem"
										>
											<b-progress-bar
												:style="`background-color: ${getProcessColor(
													dashboard_statistic.received_orders_count,
													dashboard_statistic.delivered_orders_count,
												)}`"
												:value="dashboard_statistic.received_orders_count"
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
											><b>{{ dashboard_statistic.reviewed_orders_count }}</b
											>/{{ dashboard_statistic.received_orders_count }}</span
										>
										<b-progress
											:max="dashboard_statistic.received_orders_count"
											show-progress
											height="1.5rem"
										>
											<b-progress-bar
												:style="`background-color: ${getProcessColor(
													dashboard_statistic.reviewed_orders_count,
													dashboard_statistic.received_orders_count,
												)}`"
												:value="dashboard_statistic.reviewed_orders_count"
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
						<!-- <div class="card-body p-0" style="display: block">
							<div
								class="text-center text-primary my-2"
								v-if="is_loading"
								style="opacity: 0.5"
							>
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
							<ul v-else class="nav nav-pills flex-column">
								<li
									v-for="(criteria_statistic, index) in criteria_statistics"
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
									<b-collapse :id="`collapse-${index}`" accordion="my-accordion">
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
													v-for="(order, index) in order_by_criterias"
													:key="index"
												>
													<a
														href="#"
														class="nav-link"
														@click.prevent="
															showOrderInfoDialog(order.id)
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
						</div> -->
						<div class="card-body p-0" style="display: block">
							<div
								class="text-center text-primary my-2"
								v-if="is_loading"
								style="opacity: 0.5"
							>
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
							<div v-else>
								<BarChart
									:labels="criteria_statistics.map((criteria) => criteria.name)"
									:data="criteria_statistics.map((criteria) => criteria.amount)"
								/>
							</div>
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
		<DialogOrderInfo :order="viewing_order" />
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import DialogOrderInfo from '../business/dialogs/DialogOrderInfo.vue';
	import BarChart from './chart/BarChart.vue';
	export default {
		components: {
			Treeselect,
			DialogOrderInfo,
			BarChart,
		},
		async created() {
			this.generateFilterTimeOption();
			await this.fetchDataOptions();
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				token: 'Bearer ' + window.Laravel.access_token,

				filter_time: null,
				filter_time_options: [],

				filter_delivery_partner: null,
				filter_delivery_partner_options: [],

				filter_warehouse: null,
				filter_warehouse_options: [],

				filter_distribution_channel: null,
				filter_distribution_channel_options: [],

				dashboard_statistic: {},
				criteria_statistics: [],

				order_by_criterias: [],

				viewing_order: {},

				is_loading: false,
			};
		},
		methods: {
			async fetchData() {
				try {
					this.is_loading = true;
					const [dashboard_statistic, criteria_statistics] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/dashboard', {
								delivery_partner_ids: this.filter_delivery_partner
									? [this.filter_delivery_partner]
									: undefined,
								warehouse_ids: this.filter_warehouse
									? [this.filter_warehouse]
									: undefined,
								month_year: this.filter_time ? this.filter_time : undefined,
								distribution_channel_ids: this.filter_distribution_channel
									? [this.filter_distribution_channel]
									: undefined,
							}),
							new APIRequest('get', '/api/dashboard/criteria', {
								delivery_partner_ids: this.filter_delivery_partner
									? [this.filter_delivery_partner]
									: undefined,
								warehouse_ids: this.filter_warehouse
									? [this.filter_warehouse]
									: undefined,
								month_year: this.filter_time ? this.filter_time : undefined,
								distribution_channel_ids: this.filter_distribution_channel
									? [this.filter_distribution_channel]
									: undefined,
							}),
						]);
					this.dashboard_statistic = dashboard_statistic;
					this.criteria_statistics = criteria_statistics;
				} catch (error) {
					console.log(error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchDataOptions() {
				try {
					this.is_loading = true;
					const [
						filter_delivery_partner_options,
						filter_warehouse_options,
						filter_distribution_channel_options,
					] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/delivery-partners'),
						new APIRequest('get', '/api/master/warehouses'),
						new APIRequest('get', '/api/master/distribution-channels'),
					]);
					this.filter_delivery_partner_options = filter_delivery_partner_options.map(
						(delivery_partner) => {
							return {
								id: delivery_partner.id,
								label: delivery_partner.name,
							};
						},
					);
					this.filter_warehouse_options = filter_warehouse_options.map((warehouse) => {
						return {
							id: warehouse.id,
							label: warehouse.name,
						};
					});
					this.filter_distribution_channel_options =
						filter_distribution_channel_options.map((distribution_channel) => {
							return {
								id: distribution_channel.id,
								label: distribution_channel.name,
							};
						});
					if (this.filter_delivery_partner_options.length == 1) {
						this.filter_delivery_partner = this.filter_delivery_partner_options[0].id;
					}
				} catch (error) {
					console.log(error);
				} finally {
					this.is_loading = false;
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
			getProcessColor(current_value, total_value, reverse = false) {
				let percent = this.calculatePercent(current_value, total_value);
				let red, green;

				if (reverse) {
					percent = 100 - percent;
				}

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
		},
		watch: {
			filter_delivery_partner: async function (newVal, oldVal) {
				await this.fetchData();
			},
			filter_time: async function (newVal, oldVal) {
				await this.fetchData();
			},
			filter_warehouse: async function (newVal, oldVal) {
				await this.fetchData();
			},
			filter_distribution_channel: async function (newVal, oldVal) {
				await this.fetchData();
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
