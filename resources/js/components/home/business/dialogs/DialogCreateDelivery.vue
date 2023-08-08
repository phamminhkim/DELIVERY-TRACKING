<template>
	<div
		class="modal fade"
		id="DialogCreateDelivery"
		tabindex="-1"
		role="dialog"
		data-backdrop="static"
	>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form @submit.prevent="createNewDelivery">
					<div class="modal-header">
						<h4 class="modal-title">
							<span> Tạo vận đơn mới </span>
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<label>Công ty</label>
							<small class="text-danger">(*)</small>
							<treeselect
								v-model="form.company"
								:multiple="false"
								:options="company_options"
								placeholder="Chọn công ty.."
								required
							/>
						</div>
						<div class="form-group">
							<label>Khách hàng/Nhà phân phối</label>
							<small class="text-danger">(*)</small>
							<treeselect
								placeholder="Chọn khách hàng.."
								:disable-branch-nodes="false"
								v-model="customer_of_delivery"
								:async="true"
								:load-options="loadOptions"
								:normalizer="normalizerOption"
								searchPromptText="Nhập tên khách hàng để tìm kiếm.."
								@select="onSelectCustomer"
							/>
						</div>
						<div class="form-group">
							<label>Địa chỉ giao hàng</label>
							<small class="text-danger">(*)</small>
							<input
								type="text"
								v-model="form.address"
								placeholder="Nhập địa chỉ giao hàng..."
								required
								class="form-control"
							/>
						</div>
						<div class="form-group">
							<label>Đơn vị vận chuyển</label>
							<small class="text-danger">(*)</small>
							<treeselect
								v-model="form.delivery_partner"
								:multiple="false"
								:options="delivery_partner_options"
								placeholder="Chọn đơn vị vận chuyển.."
								required
							/>
						</div>
						<div class="form-group">
							<label>Thời hạn giao hàng</label>
							<!-- <small class="text-danger">(*)</small> -->
							<input
								type="date"
								v-model="form.estimate_delivery_date"
								class="form-control"
							/>
						</div>

						<div class="form-group">
							<label>Danh sách đơn hàng đã chọn</label>
							<small class="text-danger">(*)</small>
							<div class="row mb-3">
								<div class="col-md-10">
									<treeselect
										v-model="order_ids_waiting_to_add"
										:multiple="true"
										:options="renderedOnOptionOrderList"
										placeholder="Chọn đơn hàng được vận chuyển.."
									/>
								</div>
								<div class="col-md-2">
									<button
										id="add-waiting-list-btn"
										class="btn btn-primary"
										@click.prevent="pushWaitingOrdersToList"
									>
										Thêm
									</button>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									<span class="text-danger" role="alert">
										<strong> {{ error_message }} </strong>
									</span>
								</div>
							</div>
							<b-table
								responsive
								hover
								striped
								:bordered="true"
								:current-page="pagination.current_page"
								:per-page="pagination.item_per_page"
								:filter="search_pattern"
								:fields="fields"
								:items="renderedOnTableOrderList"
								:tbody-tr-class="rowClass"
							>
								<template #cell(warehouse)="data">
									<span>{{ data.value.name }}</span>
								</template>

								<template #cell(action)="data">
									<div class="margin">
										<button
											class="btn btn-xs mr-1"
											@click.prevent="removeOrder(data.item)"
											id="remove-order-btn"
										>
											<i
												class="fas fa-trash text-red bigger-120"
												title="Delete"
											></i>
										</button>
									</div>
								</template>
							</b-table>
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

					<div class="modal-footer justify-content-between">
						<button
							type="submit"
							title="Submit"
							class="btn btn-primary"
							id="submit-btn"
						>
							Tạo mới
						</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Đóng
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script>
	import Vue, { reactive } from 'vue';
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from '../../ApiHandler';

	export default {
		name: 'DialogCreateDelivery',
		components: {
			Vue,
			Treeselect,
		},
		props: {
			order_ids: Array,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_loading: false,

				form: {
					company: null,
					delivery_partner: null,
					orders: {}, // use plain object like a HashMap
					address: null,
					estimate_delivery_date: null,
				},
				customer_of_delivery: null,
				company_options: [],
				delivery_partner_options: [],

				selected_orders: {}, // use plain object like a HashMap,
				order_options: {}, //use plain object like a HashMap
				order_ids_waiting_to_add: [],

				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
				search_pattern: '',
				search_placeholder: 'Tìm kiếm đơn hàng đã chọn..',

				fields: [
					{
						key: 'warehouse',
						label: 'Kho hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
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
						key: 'action',
						label: 'Hành động',
						sortable: true,
						class: 'text-nowrap text-center',
					},
				],

				error_message: '',
			};
		},
		created() {
			this.fetchMasterData();
		},
		watch: {
			order_ids: async function (new_order_ids) {
				const { data } = await this.api_handler.get('/api/admin/orders', {
					ids: new_order_ids.length === 0 ? [null] : new_order_ids,
				});
				this.form.orders = {};
				this.selected_orders = {};
				data.forEach((order) => {
					this.form.orders[order.id] = order.id;
					this.selected_orders[order.id] = order;
				});
			},
		},
		methods: {
			async fetchMasterData() {
				try {
					const [companies, delivery_partners, deliveries, orders] =
						await this.api_handler.handleMultipleRequest([
							new APIRequest('get', '/api/master/companies'),
							new APIRequest('get', '/api/master/delivery-partners'),
							new APIRequest('get', '/api/admin/deliveries'),
							new APIRequest('get', '/api/admin/orders/minified', {
								filter: 'can-delivery',
							}),
						]);

					this.company_options = companies.map((company) => {
						return {
							id: company.code,
							label: `(${company.code}) ${company.name}`,
						};
					});

					this.delivery_partner_options = delivery_partners.map((delivery_partner) => {
						return {
							id: delivery_partner.code,
							label: `(${delivery_partner.code}) ${delivery_partner.name}`,
						};
					});

					orders.forEach((order) => {
						if (order.sap_so_number && order.sap_do_number) {
							this.addPropertyToObject(this.order_options, order.id, {
								id: order.id,
								label: `SO: (${order.sap_so_number}), DO: (${order.sap_do_number})`,
							});
						}
					});
				} catch (error) {
					console.log(error);
				}
			},
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
			removeOrder(order) {
				this.removePropertyFromObject(this.form.orders, order.id);
				this.removePropertyFromObject(this.selected_orders, order.id);
				this.addPropertyToObject(this.order_options, order.id, {
					id: order.id,
					label: `SO: (${order.sap_so_number}), DO: (${order.sap_do_number})`,
				});
			},
			async addOrders(order_ids) {
				try {
					const { data } = await this.api_handler.get('/api/admin/orders', {
						ids: order_ids.length === 0 ? [null] : order_ids,
					});
					data.forEach((order) => {
						console.log(order);
						this.addPropertyToObject(this.form.orders, order.id, order.id);
						this.addPropertyToObject(this.selected_orders, order.id, order);
						this.removePropertyFromObject(this.order_options, order.id);
					});
				} catch (err) {
					console.log(err);
				}
			},
			async pushWaitingOrdersToList() {
				if (this.order_ids_waiting_to_add.length === 0) return;
				await this.addOrders(this.order_ids_waiting_to_add);
				this.order_ids_waiting_to_add = [];
			},
			async createNewDelivery() {
				if (Object.keys(this.form.orders).length === 0) {
					this.error_message = 'Danh sách đơn hàng là bắt buộc.';
					return;
				}
				try {
					if (this.is_loading) return;
					this.is_loading = true;
					const { data } = await this.api_handler
						.post(
							'/api/admin/deliveries',
							{},
							{
								company_code: this.form.company,
								delivery_partner_code: this.form.delivery_partner,
								orders: Object.values(this.form.orders).map((order_id) => {
									return {
										id: order_id,
									};
								}),
								address: this.form.address,
								estimate_delivery_date: this.form.estimate_delivery_date,
							},
						)
						.finally(() => {
							this.resetForm();
							this.is_loading = false;
						});

					this.$emit('onDeliveryCreated', data);
					this.$showMessage('success', 'Tạo vận đơn thành công');
					this.closeDialog();
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
					this.error_message = error.response.data.message;
				}
			},
			resetForm() {
				this.form = {
					company: null,
					delivery_partner: null,
					orders: {},
				};
				this.selected_orders = {};
				this.error_message = '';
			},
			closeDialog() {
				$('#DialogCreateDelivery').modal('hide');
			},
			addPropertyToObject(obj, key, value) {
				this.$set(obj, key, value);
			},
			removePropertyFromObject(obj, key) {
				this.$delete(obj, key);
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
			async onSelectCustomer(node, instanceId) {
				try {
					const customer_id = node.id;
					const { data } = await this.api_handler.get(
						`api/master/customers/${customer_id}`,
					);
					this.form.address = data.address;
				} catch (err) {
					console.log(err);
				}
			},
		},
		computed: {
			rows() {
				return this.form.orders.length;
			},
			renderedOnTableOrderList() {
				return Object.values(this.selected_orders);
			},
			renderedOnOptionOrderList() {
				return Object.values(this.order_options);
			},
		},
	};
</script>
