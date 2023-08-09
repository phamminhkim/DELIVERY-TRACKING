<template>
	<div class="modal fade" id="DialogDeliveryInfo" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<b-overlay :show="is_loading" rounded="sm">
					<div class="modal-header">
						<h4 class="modal-title">
							<i class="fas fa-info-circle" /> Thông tin vận đơn
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div v-if="delivery.orders" class="modal-body">
						<div class="row">
							<div class="col-7">
								<div class="form-group">
									<label>Đơn vị vận chuyển</label>
									<input
										type="text"
										class="form-control"
										:value="delivery.partner.name"
										readonly
									/>
								</div>
								<div class="form-group">
									<label>Mã vận đơn</label>
									<input
										type="text"
										class="form-control"
										:value="delivery.delivery_code"
										readonly
									/>
								</div>
								<div class="form-group">
									<label>Ngày tạo vận đơn</label>
									<div
										class="input-group date"
										id="delivery_created_at"
										data-target-input="nearest"
									>
										<div
											class="input-group-append"
											data-target="#delivery_created_at"
											data-toggle="datetimepicker"
										>
											<div class="input-group-text">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										<input
											type="text"
											class="form-control datetimepicker-input"
											data-target="#delivery_created_at"
											:value="delivery.created_at"
											readonly
										/>
									</div>
								</div>
								<div class="form-group">
									<label>Ngày bắt đầu vận chuyển</label>
									<div
										class="input-group date"
										id="delivery_start_date"
										data-target-input="nearest"
									>
										<div
											class="input-group-append"
											data-target="#delivery_start_date"
											data-toggle="datetimepicker"
										>
											<div class="input-group-text">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										<input
											type="text"
											class="form-control datetimepicker-input"
											data-target="#delivery_start_date"
											:value="delivery.start_delivery_date ?? 'Chưa bắt đầu'"
											readonly
										/>
									</div>
								</div>
								<div class="form-group">
									<label>Ngày hoàn thành vận chuyển</label>
									<div
										class="input-group date"
										id="delivery_completed_at"
										data-target-input="nearest"
									>
										<div
											class="input-group-append"
											data-target="#delivery_completed_at"
											data-toggle="datetimepicker"
										>
											<div class="input-group-text">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										<input
											type="text"
											class="form-control datetimepicker-input"
											data-target="#delivery_completed_at"
											:value="
												delivery.complete_delivery_date ?? 'Chưa hoàn thành'
											"
											readonly
										/>
									</div>
								</div>
								<div class="form-group">
									<label>Danh sách đơn hàng ({{ delivery.orders.length }})</label>
									<table class="table table-bordered">
										<thead>
											<tr>
												<th style="width: 10px">#</th>
												<th>SO</th>
												<th>DO</th>
												<th>Số thùng</th>
												<th>Trọng lượng</th>
												<th>Trạng thái</th>
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(order, index) in delivery.orders"
												:key="order.id"
											>
												<td>{{ index + 1 }}</td>
												<td>
													{{ order.sap_so_number }}
												</td>
												<td>
													{{ order.sap_do_number }}
												</td>
												<td>
													{{ order.detail.total_item }}
												</td>
												<td>{{ order.detail.total_weight }} kg</td>
												<td width="35px">
													<span :class="order.status.badge_class">{{
														order.status.name
													}}</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-5">
								<label class="mb-3"> Lịch sử </label>
								<div class="timeline">
									<template v-for="item in timelines">
										<div
											v-if="item.type == 'date'"
											v-bind:key="item.index"
											class="time-label"
										>
											<span class="bg-green">
												{{ item.day }}
											</span>
										</div>
										<div
											v-else-if="item.type == 'continue'"
											v-bind:key="item.index1"
										>
											<i
												class="fas fa-clock text-gray"
												style="
													margin-bottom: 15px;
													margin-right: 10px;
													position: relative;
												"
											>
											</i>
										</div>
										<div v-else v-bind:key="item.index2">
											<i v-if="item.icon" :class="item.icon"> </i>
											<i v-else class="fas fa-clock text-gray"> </i>
											<div class="timeline-item">
												<span class="time"
													><i class="far fa-clock"></i>
													{{ item.created_at | formatDateTime }}</span
												>
												<h3 class="timeline-header">
													{{ item.title }}
												</h3>
												<div class="timeline-body" v-if="item.content">
													{{ item.content }}
												</div>
											</div>
										</div>
									</template>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-info mr-auto" @click="printQrCode">
							<i class="fas fa-print" /> In vận đơn
						</button>
						<!-- <button type="button" class="btn btn-primary">
                            <i class="fas fa-edit" />
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fas fa-trash" />
                        </button> -->
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Đóng
						</button>
					</div>
				</b-overlay>
			</div>
		</div>
	</div>
</template>
<script>
	import ApiHandler from '../../ApiHandler';

	export default {
		props: {
			delivery_id: String,
		},
		data() {
			return {
				api_handler: new ApiHandler(window.Laravel.access_token),

				is_loading: false,
				delivery: {},
			};
		},
		watch: {
			delivery_id() {
				if (this.delivery_id) {
					this.getDeliveryInfo();
				} else {
					this.delivery = {};
				}
			},
		},
		computed: {
			timelines() {
				var current_day_string = null;

				var timelines = [];
				if (this.delivery.timelines) {
					this.delivery.timelines.forEach((timeline) => {
						var created_date = new Date(timeline.timestamp);

						var day_string =
							created_date.getUTCFullYear() +
							'-' +
							(created_date.getUTCMonth() + 1) +
							'-' +
							created_date.getUTCDate();

						var item = {
							type: 'event',
							title: timeline.event,
							icon: timeline.icon ?? 'fas fa-clock text-gray',
							created_at: timeline.timestamp,
							content: timeline.description,
						};
						timelines.unshift({ ...item });

						// Ngày mới
						if (current_day_string != day_string) {
							current_day_string = day_string;
							var current_day = {
								type: 'date',
								day: day_string,
							};
							timelines.unshift({ ...current_day });
						}
					});
				}

				return timelines;
			},
		},
		methods: {
			async getDeliveryInfo() {
				try {
					this.is_loading = true;
					let result = await this.api_handler.get(
						'api/partner/deliveries/' + this.delivery_id,
					);
					if (result.success) {
						this.delivery = result.data;
					} else {
						this.$showMessage('error', 'Lỗi', result.message);
					}
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
				} finally {
					this.is_loading = false;
				}
			},
			printQrCode() {
				this.$emit('printQrCode', [this.delivery_id]);
			},
		},
	};
</script>
<style scoped>
	th {
		text-align: center;
	}
</style>
