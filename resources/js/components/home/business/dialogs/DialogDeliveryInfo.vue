<template>
	<div
		class="modal fade"
		id="DialogDeliveryInfo"
		tabindex="-1"
		role="dialog"
		ref="DialogDeliveryInfo"
	>
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
									<label>Khách hàng</label>
									<input
										type="text"
										class="form-control"
										:value="delivery.customer.name"
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
									<label>Ngày dự tính</label>
									<div
										class="input-group date"
										id="delivery_estimate_date"
										data-target-input="nearest"
									>
										<div
											class="input-group-append"
											data-target="#delivery_estimate_date"
											data-toggle="datetimepicker"
										>
											<div class="input-group-text">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										<input
											type="date"
											class="form-control datetimepicker-input"
											data-target="#delivery_estimate_date"
											:readonly="isImmutable"
											v-model="estimate_delivery_date"
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
						<div class="row">
							<div
								class="col-12 box-images"
								v-viewer="{
									inline: false,
									button: true,
									navbar: true,
									title: true,
									toolbar: true,
									tooltip: true,
									movable: true,
									zoomable: true,
									rotatable: true,
									scalable: true,
									transition: true,
									keyboard: true,
								}"
							>
								<!-- <div
									class="image-container"
									v-for="image_url in driver_confirm_image_urls"
									:key="image_url"
								>
									<expandable-image :src="`/${image_url}`" alt="" class="image" />
								</div> -->
								<img
									v-for="image_url in driver_confirm_image_urls"
									:key="image_url"
									:src="`/${image_url}`"
									class="image-container"
									style="
										object-fit: cover;
										border-radius: 0.5rem;
										overflow: hidden;
									"
								/>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-12">
								<label>Danh sách đơn hàng ({{ delivery.orders.length }})</label>
								<div class="row mb-3">
									<div class="col-md-10">
										<treeselect
											v-model="order_ids_waiting_to_add"
											:multiple="true"
											:options="order_options"
											placeholder="Chọn đơn hàng được vận chuyển.."
											:disabled="isImmutable"
										/>
									</div>
									<div class="col-md-2">
										<button
											id="add-waiting-list-btn"
											class="btn btn-primary"
											@click.prevent="pushWaitingOrdersToList"
											:disabled="isImmutable"
										>
											Thêm
										</button>
									</div>
								</div>
								<div class="row">
									<span class="text-danger col-12" v-if="isImmutable"
										>Vận đơn đã hoàn thành, không thể chỉnh sửa</span
									>
								</div>
								<b-table :items="order_items" :fields="fields">
									<template #cell(total_item)="{ item }">
										{{ item.detail.total_item }}
									</template>
									<template #cell(total_weight)="{ item }">
										{{ item.detail.total_weight }} kg
									</template>
									<template #cell(status)="{ item }">
										<span :class="item.status.badge_class">{{
											item.status.name
										}}</span>
									</template>
									<template #cell(action)="{ item }">
										<button
											class="btn btn-xs"
											@click="removeOrder(item.id)"
											:disabled="isImmutable"
										>
											<i
												class="fas fa-trash text-red bigger-120"
												title="Delete"
											></i>
										</button>
									</template>
									<!-- <template #cell(start_delivery_date)="{ item }">
										{{ item.delivery_info.start_delivery_date }}
									</template>
									<template #cell(complete_delivery_date)="{ item }">
										{{ item.delivery_info.complete_delivery_date }}
									</template> -->
									<template #cell(confirm_delivery_date)="{ item }">
										{{
											item.delivery_info?.confirm_delivery_date
												? item.delivery_info.confirm_delivery_date
												: '-'
										}}
									</template>
								</b-table>
							</div>
						</div>
						<div class="row">
							<div class="col-12 d-flex">
								<button
									type="button"
									class="btn btn-success ml-auto"
									@click="updateDelivery"
									:disabled="isImmutable"
									v-if="isEdited"
								>
									Lưu chỉnh sửa
								</button>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button
							v-if="!isOpenFromOrderDialog && !isExternalDelivery"
							type="button"
							class="btn btn-info mr-auto"
							@click="printQrCode"
						>
							<i class="fas fa-print" /> In vận đơn
						</button>

						<button
							v-if="!isOpenFromOrderDialog"
							type="button"
							class="btn btn-secondary"
							data-dismiss="modal"
						>
							Đóng
						</button>
						<button
							v-else
							class="btn btn-primary"
							data-bs-target="#DialogOrderInfo"
							data-bs-toggle="modal"
							@click="backToOrderDialog"
						>
							Quay lại đơn hàng
						</button>
					</div>
				</b-overlay>
			</div>
		</div>
	</div>
</template>
<script>
	import ApiHandler, { APIRequest } from '../../ApiHandler';
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import sha256 from 'crypto-js/sha256';
	export default {
		components: {
			Treeselect,
		},
		props: {
			delivery_id: String,
			isOpenFromOrderDialog: Boolean,
		},
		data() {
			return {
				api_handler: new ApiHandler(window.Laravel.access_token),

				is_loading: false,
				delivery: {},

				order_items: [],
				fields: [
					{
						key: 'sap_so_number',
						label: 'SO',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'sap_do_number',
						label: 'DO',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'total_item',
						label: 'Số thùng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'total_weight',
						label: 'Trọng lượng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'confirm_delivery_date',
						label: 'Ngày xác nhận',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'status',
						label: 'Trạng thái',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'action',
						label: 'Hành động',
						class: 'text-nowrap text-center',
					},
				],

				order_ids_waiting_to_add: [],
				order_options: [],
				order_options_backup: [],

				original_hashed: '',

				estimate_delivery_date: '',
			};
		},
		watch: {
			async delivery_id() {
				if (this.delivery_id) {
					await this.getDeliveryInfo();
					this.order_items = structuredClone(this.delivery.orders);
					this.estimate_delivery_date = structuredClone(
						this.delivery.estimate_delivery_date,
					);
				} else {
					this.delivery = {};
					this.order_items = [];
					this.estimate_delivery_date = '';
				}
			},
		},
		mounted() {
			$(this.$refs.DialogDeliveryInfo).on('show.bs.modal', this.onShownModal);

			const viewportMeta = document.createElement('meta');
			viewportMeta.name = 'viewport';
			viewportMeta.content = 'width=device-width, initial-scale=1';
			document.head.appendChild(viewportMeta);
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
			isImmutable() {
				return this.delivery?.status?.id >= 100;
			},
			isEdited() {
				return (
					sha256(this.order_items?.toString() + this.estimate_delivery_date).toString() !=
					this.original_hashed
				);
			},
			isExternalDelivery() {
				return this.delivery?.partner?.is_external;
			},
			driver_confirm_image_urls() {
				let image_urls = [];
				this.delivery.orders.forEach((order) => {
					order.driver_confirms.forEach((confirm) => {
						confirm.images.forEach((image) => {
							image_urls.push(image.url);
						});
					});
				});
				return image_urls;
			},
		},
		methods: {
			async getDeliveryInfo() {
				try {
					this.is_loading = true;
					let result = await this.api_handler.get(
						'api/partner/deliveries/' + this.delivery_id,
					);
					this.delivery = result.data;
					const { data } = await this.api_handler.get('api/partner/orders/minified', {
						customer_id: this.delivery.customer.id,
						status_ids: [10],
					});
					this.order_options = data.map((order) => {
						return {
							id: order.id,
							label: `SO (${order.sap_so_number}), DO (${order.sap_do_number})`,
						};
					});
					this.order_options_backup = structuredClone(this.order_options);
					this.original_hashed = sha256(
						this.delivery.orders?.toString() + this.delivery.estimate_delivery_date,
					).toString();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
				} finally {
					this.is_loading = false;
				}
			},
			printQrCode() {
				this.$emit('printQrCode', [this.delivery_id]);
			},
			removeOrder(order_id) {
				for (let index in this.order_items) {
					if (order_id === this.order_items[index].id) {
						const order = this.order_items[index];
						this.order_options.push({
							id: order.id,
							label: `SO (${order.sap_so_number}), DO (${order.sap_do_number})`,
						});
						this.order_items.splice(index, 1);

						return;
					}
				}
			},
			onShownModal() {
				this.order_items = structuredClone(this.delivery.orders);
				this.order_options = structuredClone(this.order_options_backup);
				this.estimate_delivery_date = structuredClone(this.delivery.estimate_delivery_date);
			},
			async pushWaitingOrdersToList() {
				if (this.order_ids_waiting_to_add.length === 0) return;
				const order_ids = this.order_ids_waiting_to_add;
				const { data } = await this.api_handler.get('api/partner/orders', {
					ids: order_ids.length === 0 ? [null] : order_ids,
				});
				this.order_items.push(...data);

				this.order_options = this.order_options.filter(
					(order_option) => !this.order_ids_waiting_to_add.includes(order_option.id),
				);
				this.order_ids_waiting_to_add = [];
			},
			async updateDelivery() {
				try {
					this.is_loading = true;
					const update_result = await this.api_handler.patch(
						`api/partner/deliveries/${this.delivery.id}`,
						{},
						{
							orders: this.order_items,
							estimate_delivery_date: this.estimate_delivery_date,
						},
					);
					await this.getDeliveryInfo();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
				} finally {
					this.is_loading = false;
				}
			},
			backToOrderDialog() {
				$('#DialogDeliveryInfo').modal('hide');
			},
		},
	};
</script>
<style scoped>
	th {
		text-align: center;
	}
	.box-images {
		display: flex;
		flex-wrap: wrap;
		width: 100%;
	}
	.image-container {
		width: 13%;
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
</style>
