<template>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Monthly Recap Report</h5>

						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							</button>
						</div>
						<treeselect
							placeholder="Chọn bộ lọc.."
							:multiple="true"
							:disable-branch-nodes="false"
							v-model="filter_time"
							:options="filter_options"
						/>
					</div>

					<div class="card-body" style="display: block">
						<div class="row">
							<div class="col-md-8 d-flex flex-column">
								<div
									v-for="(criteria_statistic, index) in criteria_statistics"
									:key="index"
									class="info-box mb-3 bg-warning"
								>
									<span class="info-box-icon"><i class="fas fa-cog"></i></span>
									<div class="info-box-content">
										<span class="info-box-text">{{
											criteria_statistic.name
										}}</span>
										<span class="info-box-number">
											{{ criteria_statistic.amount }}
										</span>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<p class="text-center">
									<strong>Goal Completion</strong>
								</p>
								<div class="progress-group">
									Số đơn đúng hạn / Số đơn đang giao
									<span class="float-right"
										><b>{{ dashboard_statistic.ontime_orders_count }}</b
										>/{{ dashboard_statistic.delivering_orders_count }}</span
									>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-primary"
											:style="`width: ${calculatePercent(
												dashboard_statistic.ontime_orders_count,
												dashboard_statistic.delivering_orders_count,
											)}%`"
										></div>
									</div>
								</div>
								<div class="progress-group">
									Số đơn đã xác nhận nhận hàng / Số đơn đã giao
									<span class="float-right"
										><b>{{ dashboard_statistic.confirmed_orders_count }}</b
										>/{{ dashboard_statistic.delivered_orders_count }}</span
									>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-warning"
											:style="`width: ${calculatePercent(
												dashboard_statistic.confirmed_orders_count,
												dashboard_statistic.delivered_orders_count,
											)}%`"
										></div>
									</div>
								</div>
								<div class="progress-group">
									Số đơn đã đánh giá / Số đơn đã nhận hàng
									<span class="float-right"
										><b>{{ dashboard_statistic.reviewed_orders_count }}</b
										>/{{ dashboard_statistic.received_orders_count }}</span
									>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-danger"
											:style="`width: ${calculatePercent(
												dashboard_statistic.reviewed_orders_count,
												dashboard_statistic.received_orders_count,
											)}%`"
										></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card-footer" style="display: block">
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
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Treeselect from '@riophae/vue-treeselect';
	import APIHandler, { APIRequest } from './ApiHandler';
	export default {
		components: {
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				token: 'Bearer ' + window.Laravel.access_token,

				filter_time: null,
				filter_options: [
					{ id: 1, label: 'Đơn hàng mới' },
					{ id: 2, label: 'Đơn hàng đã xác nhận' },
					{ id: 3, label: 'Đơn hàng đang giao' },
					{ id: 4, label: 'Đơn hàng đã giao' },
					{ id: 5, label: 'Đơn hàng đã hủy' },
				],

				dashboard_statistic: {},
				criteria_statistics: [],
			};
		},
		async created() {
			await this.fetchcData();
		},
		methods: {
			async fetchcData() {
				const [dashboard_statistic, criteria_statistics] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/dashboard'),
						new APIRequest('get', '/api/dashboard/criteria'),
					]);
				this.dashboard_statistic = dashboard_statistic;
				this.criteria_statistics = criteria_statistics;
			},
			calculatePercent(amount, total) {
				return Math.floor((amount / total) * 100);
			},
		},
	};
</script>

<style></style>
