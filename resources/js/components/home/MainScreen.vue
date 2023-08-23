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
							<div class="col-12 col-sm-6 col-md-3">
								<div class="info-box">
									<span class="info-box-icon bg-info elevation-1"
										><i class="fas fa-cog"></i
									></span>
									<div class="info-box-content">
										<span class="info-box-text">CPU Traffic</span>
										<span class="info-box-number">
											10
											<small>%</small>
										</span>
									</div>
								</div>
							</div>

							<div class="col-12 col-sm-6 col-md-3">
								<div class="info-box mb-3">
									<span class="info-box-icon bg-danger elevation-1"
										><i class="fas fa-thumbs-up"></i
									></span>
									<div class="info-box-content">
										<span class="info-box-text">Likes</span>
										<span class="info-box-number">41,410</span>
									</div>
								</div>
							</div>

							<div class="clearfix hidden-md-up"></div>
							<div class="col-12 col-sm-6 col-md-3">
								<div class="info-box mb-3">
									<span class="info-box-icon bg-success elevation-1"
										><i class="fas fa-shopping-cart"></i
									></span>
									<div class="info-box-content">
										<span class="info-box-text">Sales</span>
										<span class="info-box-number">760</span>
									</div>
								</div>
							</div>

							<div class="col-12 col-sm-6 col-md-3">
								<div class="info-box mb-3">
									<span class="info-box-icon bg-warning elevation-1"
										><i class="fas fa-users"></i
									></span>
									<div class="info-box-content">
										<span class="info-box-text">New Members</span>
										<span class="info-box-number">2,000</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								<p class="text-center">
									<strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
								</p>
								<div class="chart">
									<div class="chartjs-size-monitor">
										<div class="chartjs-size-monitor-expand">
											<div class=""></div>
										</div>
										<div class="chartjs-size-monitor-shrink">
											<div class=""></div>
										</div>
									</div>

									<canvas
										id="salesChart"
										height="360"
										style="height: 180px; display: block; width: 485px"
										width="970"
										class="chartjs-render-monitor"
									></canvas>
								</div>
							</div>

							<div class="col-md-4">
								<p class="text-center">
									<strong>Goal Completion</strong>
								</p>
								<div class="progress-group">
									Số đơn trễ hạn / Số đơn đang giao
									<span class="float-right"
										><b>{{ dashboard.late_orders_count }}</b
										>/{{ dashboard.delivering_orders_count }}</span
									>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-primary"
											:style="`width: ${calculatePercent(
												dashboard.late_orders_count,
												dashboard.delivering_orders_count,
											)}%`"
										></div>
									</div>
								</div>

								<div class="progress-group">
									Số đơn chưa đánh giá / Số đơn đã nhận hàng
									<span class="float-right"
										><b>{{ dashboard.no_reviewed_orders_count }}</b
										>/{{ dashboard.received_orders_count }}</span
									>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-danger"
											:style="`width: ${calculatePercent(
												dashboard.no_reviewed_orders_count,
												dashboard.received_orders_count,
											)}%`"
										></div>
									</div>
								</div>

								<!-- <div class="progress-group">
									<span class="progress-text">Visit Premium Page</span>
									<span class="float-right"><b>480</b>/800</span>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-success"
											style="width: 60%"
										></div>
									</div>
								</div> -->

								<div class="progress-group">
									Số đơn chưa xác nhận / Số đơn đã giao
									<span class="float-right"
										><b>{{ dashboard.no_confirmed_orders_count }}</b
										>/{{ dashboard.delivered_orders_count }}</span
									>
									<div class="progress progress-sm">
										<div
											class="progress-bar bg-warning"
											:style="`width: ${calculatePercent(
												dashboard.no_confirmed_orders_count,
												dashboard.delivered_orders_count,
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
	import APIHandler from './ApiHandler';
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

				dashboard: {},
			};
		},
		async created() {
			await this.fetchcData();
		},
		methods: {
			async fetchcData() {
				const { data } = await this.api_handler.get('/api/dashboard');
				this.dashboard = data;
			},
			calculatePercent(amount, total) {
				return Math.floor((amount / total) * 100);
			},
		},
	};
</script>

<style></style>
