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
					// {
					// 	key: 'start_delivery_date',
					// 	label: 'Ngày bắt đầu giao',
					// 	sortable: true,
					// 	class: 'text-nowrap text-center',
					// },
					// {
					// 	key: 'complete_delivery_date',
					// 	label: 'Ngày giao đến',
					// 	sortable: true,
					// 	class: 'text-nowrap text-center',
					// },
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

				original_hashed_orders: '',
			};
		},
		watch: {
			async delivery_id() {
				if (this.delivery_id) {
					await this.getDeliveryInfo();
					this.order_items = structuredClone(this.delivery.orders);
				} else {
					this.delivery = {};
					this.order_items = [];
				}
			},
		},
		mounted() {
			$(this.$refs.DialogDeliveryInfo).on('show.bs.modal', this.onShownModal);
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
					sha256(this.order_items?.toString()).toString() != this.original_hashed_orders
				);
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
					this.original_hashed_orders = sha256(
						this.delivery.orders?.toString(),
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
						},
					);
					await this.getDeliveryInfo();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
				} finally {
					this.is_loading = false;
				}
			},
		},
	};
</script>
<style scoped>
	th {
		text-align: center;
	}
</style>
