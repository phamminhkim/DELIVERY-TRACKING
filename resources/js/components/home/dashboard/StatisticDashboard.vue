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
							<h3 class="card-title">Chứng từ</h3>
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
							<div v-else>
								<ul class="nav nav-pills flex-column">
									<li
										class="nav-item"
										v-if="
											dashboard_statistic.pending_today_orders_count !==
											undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('pending_today_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-danger">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.pending_today_orders_count
															}}
														</h3>
														<p>DO mới trong ngày</p>
													</div>
													<div class="icon">
														<i class="fas fa-file-invoice"></i>
													</div>
												</div>
											</div>
										</div>
									</li>

									<li
										class="nav-item"
										v-if="
											dashboard_statistic.pending_orders_count !== undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('pending_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-danger">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.pending_orders_count
															}}
														</h3>
														<p>DO chưa có vận đơn</p>
													</div>
													<div class="icon">
														<i class="fas fa-file-alt"></i>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Vận chuyển</h3>
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
							<div v-else>
								<ul class="nav nav-pills flex-column">
									<li
										class="nav-item"
										v-if="
											dashboard_statistic.preparing_orders_count !== undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('preparing_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-warning">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.preparing_orders_count
															}}
														</h3>
														<p>DO vận chuyển nhận</p>
													</div>
													<div class="icon">
														<i class="fas fa-truck-loading"></i>
													</div>
												</div>
											</div>
										</div>
									</li>

									<li
										class="nav-item"
										v-if="
											dashboard_statistic.delivering_orders_count !==
											undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('delivering_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-info">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.delivering_orders_count
															}}
														</h3>
														<p>DO đang giao</p>
													</div>
													<div class="icon">
														<i class="fas fa-shipping-fast"></i>
													</div>
												</div>
											</div>
										</div>
									</li>

									<li
										class="nav-item"
										v-if="
											dashboard_statistic.delivered_orders_count !== undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('delivered_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-success">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.delivered_orders_count
															}}
														</h3>
														<p>DO đã giao</p>
													</div>
													<div class="icon">
														<i class="fas fa-shopping-cart"></i>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Đánh giá khách hàng</h3>
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
							<div v-else>
								<ul class="nav nav-pills flex-column">
									<li
										class="nav-item"
										v-if="
											dashboard_statistic.reviewed_orders_count !== undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('reviewed_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-success">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.reviewed_orders_count
															}}
														</h3>
														<p>DO khách hàng đã đánh giá</p>
													</div>
													<div class="icon">
														<i class="fas fa-comments"></i>
													</div>
												</div>
											</div>
										</div>
									</li>

									<li
										class="nav-item"
										v-if="
											dashboard_statistic.received_orders_count != undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('no_reviewed_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-warning">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.no_reviewed_orders_count
															}}
														</h3>
														<p>DO chưa đánh giá</p>
													</div>
													<div class="icon">
														<i class="fas fa-comment-slash"></i>
													</div>
												</div>
											</div>
										</div>
									</li>

									<li
										class="nav-item"
										v-if="
											dashboard_statistic.delivered_orders_count !=
												undefined &&
											dashboard_statistic.received_orders_count != undefined
										"
									>
										<div
											class="nav-link"
											@click="onClickOrdersStatistic('no_received_orders')"
										>
											<div class="col-lg-12 col-12">
												<div class="small-box bg-danger">
													<div class="inner">
														<h3>
															{{
																dashboard_statistic.no_received_orders_count
															}}
														</h3>
														<p>DO chưa xác nhận</p>
													</div>
													<div class="icon">
														<i class="fas fa-comment-slash"></i>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
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
							<div
								class="text-center text-primary my-2"
								v-if="is_loading"
								style="opacity: 0.5"
							>
								<b-spinner class="align-middle" type="grow"></b-spinner>
								<strong>Đang tải dữ liệu...</strong>
							</div>
							<div v-else style="padding: 15px">
								<BarChart
									:labels="criteria_statistics.map((criteria) => criteria.name)"
									:data="criteria_statistics.map((criteria) => criteria.amount)"
									@on-click="onClickBarchart"
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"></div>
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
		<DialogOrderStatisitcVue
			:orders="order_by_criterias"
			:viewingStatistic="viewing_statistic"
		/>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import DialogOrderInfo from '../business/dialogs/DialogOrderInfo.vue';
	import BarChart from './chart/BarChart.vue';
	import DialogOrderStatisitcVue from './dialogs/DialogOrderStatisitc.vue';
	export default {
		components: {
			Treeselect,
			DialogOrderInfo,
			BarChart,
			DialogOrderStatisitcVue,
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
				viewing_statistic: '',
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
						new APIRequest('get', '/api/master/warehouses/active'),
						new APIRequest('get', '/api/master/distribution-channels/active'),
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
			async onClickBarchart(event) {
				const { index } = event;
				try {
					this.order_by_criterias = [];
					this.viewing_statistic = '';
					const { data } = await this.api_handler.get('api/partner/orders/expanded', {
						criteria_ids: [this.criteria_statistics[index].id],
						month_year: this.filter_time ? this.filter_time : undefined,
					});
					this.order_by_criterias = data;
					this.viewing_statistic = this.criteria_statistics[index].name;
					$('#DialogOrderStatistic').modal('show');
				} catch (error) {
					console.log(error);
				}
			},
			async onClickOrdersStatistic(statistic) {
				try {
					this.order_by_criterias = [];
					this.viewing_statistic = '';
					const { data } = await this.api_handler.get('api/dashboard/orders', {
						month_year: this.filter_time ? this.filter_time : undefined,
						delivery_partner_ids: this.filter_delivery_partner
							? [this.filter_delivery_partner]
							: undefined,
						warehouse_ids: this.filter_warehouse ? [this.filter_warehouse] : undefined,
						distribution_channel_ids: this.filter_distribution_channel
							? [this.filter_distribution_channel]
							: undefined,
						order_statistics: [statistic],
					});
					const fields = [
						'reviewed_orders',
						'pending_today_orders',
						'pending_orders',
						'preparing_orders',
						'delivering_orders',
						'delivered_orders',
						'no_reviewed_orders',
						'no_received_orders',
					];

					fields.forEach((field) => {
                        if (data[field]) {
                            this.order_by_criterias.push(...data[field]);
                        }
                    });
					this.viewing_statistic = statistic;

					$('#DialogOrderStatistic').modal('show');
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
