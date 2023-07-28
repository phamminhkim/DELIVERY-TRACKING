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
							<div class="col-6">
								<div class="form-group">
									<label>Người nhận</label>
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
									<label>Số lượng</label>
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
							<div class="col-6">
								<label class="mb-3"> Đánh giá </label>
								<b-card
									v-for="(review, index) in order.customer_reviews"
									:key="index"
									header-tag="header"
								>
									<template #header>
										<div
											v-for="(criteria, idx) in review.criterias"
											:key="`${index}_${idx}`"
											class="criteria"
										>
											{{ criteria.name }}
										</div>
									</template>
									<b-card-text>{{ review.review_content }}</b-card-text>
								</b-card>
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
	</div>
</template>

<script>
	export default {
		props: {
			order: Object,
		},
		methods: {
			formatDate(date) {
				if (!date) return '';
				let d = new Date(date);
				let month = '' + (d.getMonth() + 1);
				let day = '' + d.getDate();
				let year = d.getFullYear();

				if (month.length < 2) month = '0' + month;
				if (day.length < 2) day = '0' + day;

				const date_string = [year, month, day].join('-');

				let hour = '' + d.getHours();
				let min = '' + d.getMinutes();
				let sec = '' + d.getSeconds();

				if (hour.length < 2) hour = '0' + hour;
				if (min.length < 2) min = '0' + min;
				if (sec.length < 2) sec = '0' + sec;

				console.log([hour, min, sec]);
				const time_string = `${hour}:${min}:${sec}`;
				return date_string + ' ' + time_string;
			},
			formatValue(value) {
				if (!value) return '';
				return value + ' vnđ';
			},
			formatWeight(weight) {
				if (!weight) return '';
				return weight + ' kg';
			},
			formatOrder() {
				this.order.approved.sap_so_finance_approval_date = this.formatDate(
					this.order.approved.sap_so_finance_approval_date,
				);
				this.order.detail.total_value = this.formatValue(this.order.detail.total_value);
				this.order.detail.total_weight = this.formatWeight(this.order.detail.total_weight);
			},
		},
		watch: {
			order() {
				this.formatOrder();
			},
		},
	};
</script>

<style>
	.criteria {
		background-color: rgb(23, 162, 184);
		color: white;
		padding: 3px 10px 3px 10px;
		border-radius: 10px !important;
		margin-right: 10px !important;
		display: inline-block;
		font-size: 12px;
		font-weight: 700;
	}
</style>
