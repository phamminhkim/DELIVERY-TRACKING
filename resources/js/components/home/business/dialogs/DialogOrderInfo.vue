<template>
	<div class="modal fade" id="DialogOrderInfo" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<b-overlay rounded="sm">
					<div class="modal-header">
						<h4 class="modal-title">
							<i class="fas fa-info-circle" /> Thông tin đơn hàng
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div v-if="Object.values(order).length !== 0" class="modal-body">
						<div class="row">
							<div class="col-4">
								<div class="form-group">
									<label class="d-flex justify-content-between">
										Người nhận
										<a
											@click="
												showDeliveryInfoDialog(
													order?.delivery_info?.delivery,
												)
											"
											href="#"
										>
											{{ order?.delivery_info?.delivery?.delivery_code }}
										</a>
									</label>
									<input
										type="text"
										class="form-control"
										:value="order.receiver.receiver_name"
										readonly
									/>
								</div>
								<div class="form-group">
									<label>Ngày duyệt</label>
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
											:value="order.approved.sap_so_finance_approval_date"
											readonly
										/>
									</div>
								</div>

								<!-- <div class="form-group">
									<label>Sale</label>
									<input
										type="text"
										class="form-control"
										:value="order.sale.name"
										readonly
									/>
								</div> -->

								<div class="form-group">
									<label>Địa chỉ giao hàng</label>
									<input
										type="text"
										class="form-control"
										:value="order.detail.delivery_address"
										readonly
									/>
								</div>

								<div class="form-group">
									<label>Ghi chú</label>
									<textarea
										class="form-control"
										row="5"
										:value="order.detail.note"
										readonly
									></textarea>
								</div>

								<div class="form-group">
									<label>Số thùng</label>
									<input
										type="text"
										class="form-control"
										:value="order.detail.total_item"
										readonly
									/>
								</div>

								<div class="form-group">
									<label>Thành tiền</label>
									<input
										type="text"
										class="form-control"
										:value="order.detail.total_value"
										readonly
									/>
								</div>

								<div class="form-group">
									<label>Trọng lượng</label>
									<input
										type="text"
										class="form-control"
										:value="order.detail.total_weight"
										readonly
									/>
								</div>
							</div>
							<div
								class="col-4"
								style="display: flex; flex-direction: column; gap: 1rem"
							>
								<label class="mb-3"> Đánh giá </label>
								<div
									class="card-rate"
									v-for="(review, index) in order.customer_reviews"
									:key="index"
									header-tag="header"
								>
									<div class="time">
										<i class="far fa-clock"></i>
										{{ formatDate(review.created_at) }}
									</div>
									<div class="line"></div>
									<div class="container-rate">
										<div class="box-rate">
											<div class="rate">
												<div
													v-for="(criteria, idx) in review.criterias"
													:key="`${index}_${idx}`"
													class="criteria"
												>
													{{ criteria.name }}
												</div>
											</div>
											<div class="review-content">
												{{ review.review_content }}
											</div>
										</div>
										<div class="box-images">
											<div
												class="image-container"
												v-for="image in review.images"
												:key="image.url"
											>
												<expandable-image
													:src="`/${image.url}`"
													alt=""
													class="image"
												/>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-4">
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
													{{ item.created_at }}</span
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
								<!-- <div class="d-flex justify-content-center">
									<a
										@click="
											showDeliveryInfoDialog(order?.delivery_info?.delivery)
										"
										href="#"
									>
										{{ order?.delivery_info?.delivery?.delivery_code }}
									</a>
								</div> -->
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Đóng
						</button>
					</div>
				</b-overlay>
			</div>
		</div>
		<dialog-delivery-info :delivery_id="viewing_delivery_id" isOpenFromOrderDialog />
	</div>
</template>

<script>
	import DialogDeliveryInfo from './DialogDeliveryInfo.vue';
	export default {
		props: {
			order: Object,
		},
		components: {
			DialogDeliveryInfo,
		},
		data() {
			return {
				viewing_delivery_id: null,
			};
		},
		methods: {
			formatDate(date) {
				if (!date) return '';
				let d = new Date(date);
				// return d.toLocaleDateString('en-GB') + ' ' + d.toLocaleTimeString('en-GB');
				let month = '' + (d.getUTCMonth() + 1);
				let day = '' + d.getUTCDate();
				let year = d.getUTCFullYear();

				if (month.length < 2) month = '0' + month;
				if (day.length < 2) day = '0' + day;

				const date_string = [year, month, day].join('-');

				let hour = '' + d.getUTCHours();
				let min = '' + d.getUTCMinutes();
				let sec = '' + d.getUTCSeconds();

				if (hour.length < 2) hour = '0' + hour;
				if (min.length < 2) min = '0' + min;
				if (sec.length < 2) sec = '0' + sec;

				const time_string = `${hour}:${min}:${sec}`;
				return date_string + ' ' + time_string;
			},
			formatValue(value) {
				if (!value) return '';
				if (value.includes('vnđ')) return value;
				return value + ' vnđ';
			},
			formatWeight(weight) {
				if (!weight) return '';
				if (weight.includes('kg')) return weight;
				return weight + ' kg';
			},
			formatOrder() {
				this.order.approved.sap_so_finance_approval_date = this.formatDate(
					this.order.approved.sap_so_finance_approval_date,
				);
				this.order.detail.total_value = this.formatValue(this.order.detail.total_value);
				this.order.detail.total_weight = this.formatWeight(this.order.detail.total_weight);
			},
			showDeliveryInfoDialog(delivery) {
				this.viewing_delivery_id = delivery.id;
				$('#DialogDeliveryInfo').modal('show');
			},
		},
		computed: {
			timelines() {
				var delivery_timelines = this.order?.delivery_info?.delivery?.timelines;
				var current_day_string = null;

				var timelines = [];
				if (delivery_timelines) {
					delivery_timelines.forEach((timeline) => {
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
		watch: {
			order() {
				this.formatOrder();
			},
		},
		mounted() {
			const viewportMeta = document.createElement('meta');
			viewportMeta.name = 'viewport';
			viewportMeta.content = 'width=device-width, initial-scale=1';
			document.head.appendChild(viewportMeta);
		},
	};
</script>

<style>
	.container-rate {
		width: 100%;
		padding: 1.5rem;
	}

	.card-rate {
		border-radius: 0.5rem;
		width: 100%;
		display: flex;
		gap: 1px;
		flex-direction: column;
		padding: 1rem 0rem;
		box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
	}

	.box-rate {
		display: flex;
		width: 100%;
		gap: 1px;
		margin-bottom: 1rem;
	}

	.review-content {
		width: 69%;
		box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
		padding: 0 0.5rem;
		border-radius: 0.5rem;
		height: fit-content;
		min-height: 5rem;
	}

	.rate {
		display: flex;
		gap: 0.5rem;
		flex-wrap: wrap;
		flex-direction: column;
		width: 30%;
	}
	.line {
		border: 1px solid black;
		opacity: 0.1;
	}

	.criteria {
		padding: 0.3rem;
		background-color: rgb(23, 162, 184);
		color: white;
		border-radius: 10px !important;
		margin-right: 10px !important;
		display: inline-block;
		font-size: 12px;
		font-weight: 700;
		text-align: center;
	}
	.time {
		color: #999;
		display: flex;
		justify-content: flex-end;
		font-size: 12px;
		margin-right: 0.5rem;
		align-items: center;
	}
	.box-images {
		display: flex;
		flex-wrap: wrap;
		width: 100%;
	}
	.image-container {
		width: 46.7%;
		height: 250px;
		margin-right: 1rem;
		margin-bottom: 1rem;
	}
	.image-container > .image {
		width: 100%;
		height: 100%;
		object-fit: cover;
		border-radius: 0.5rem;
		overflow: hidden;
	}
	.image-container > .image > img {
		height: 100%;
	}
	@media (min-width: 1200px) {
		.modal-xl {
			max-width: 1800px;
		}
	}
</style>
