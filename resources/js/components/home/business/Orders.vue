<template>
	<!-- <b-overlay :show="is_loading" rounded="sm"> -->
	<!-- container -->
	<div class="container-fluid">
		<div>
			<div class="row">
				<div class="col-md-9">
					<div class="form-group row">
						<div class="btn-group">
							<button
								type="button"
								class="btn btn-warning btn-xs"
								@click="is_show_search = !is_show_search"
								v-b-toggle.collapse-1
							>
								<span v-if="!is_show_search">Hiện tìm kiếm</span>
								<span v-if="is_show_search">Ẩn tìm kiếm</span>
							</button>
							<button
								type="button"
								class="btn btn-warning btn-xs"
								@click="is_show_search = !is_show_search"
								v-b-toggle.collapse-1
							>
								<i v-if="is_show_search" class="fas fa-angle-up"></i>
								<i v-else class="fas fa-angle-down"></i>
							</button>
						</div>
						<button @click="filterData()" class="btn btn-secondary btn-xs ml-1">
							<i class="fas fa-sync-alt" title="Tải lại"></i>
						</button>
					</div>
				</div>
				<div class="col-md-3">
					<div class="row"></div>
				</div>
			</div>
			<b-collapse class="row" id="collapse-1">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="form-group row">
								<label
									for="start_date"
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
									>Từ ngày</label
								>
								<div class="col-sm-4">
									<input
										type="date"
										v-model="form_filter.start_date"
										class="form-control form-control-sm mt-1"
									/>
								</div>
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
									for=""
									>Đến ngày</label
								>
								<div class="col-sm-4">
									<input
										type="date"
										v-model="form_filter.end_date"
										class="form-control form-control-sm mt-1"
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Trạng thái</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn trạng thái đơn hàng.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.statuses"
										:options="order_statuses"
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Khách hàng</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn khách hàng.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.customers"
										:async="true"
										:load-options="loadOptions"
										:normalizer="normalizerOption"
										searchPromptText="Nhập tên khách hàng để tìm kiếm.."
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Kho hàng</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn kho hàng.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.warehouses"
										:options="warehouse_options"
										:normalizer="normalizerOption"
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
									for=""
									>SO</label
								>
								<div class="col-sm-4">
									<input
										type="text"
										v-model="form_filter.sap_so_number"
										placeholder="Nhập SO.."
										class="form-control"
									/>
								</div>
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
									for=""
									>DO</label
								>
								<div class="col-sm-4">
									<input
										type="text"
										v-model="form_filter.sap_do_number"
										placeholder="Nhập DO.."
										class="form-control"
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Kênh</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn kênh.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.distribution_channels"
										:options="distribution_channel_options"
										:normalizer="normalizerOption"
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Nhà vận chuyển</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn nhà vận chuyển.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.delivery_partners"
										:options="delivery_partner_options"
										:normalizer="normalizerOption"
									/>
								</div>
							</div>
							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right mt-1"
									for=""
									>Đánh giá</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										placeholder="Chọn đánh giá.."
										:multiple="true"
										:disable-branch-nodes="false"
										v-model="form_filter.order_review_options"
										:options="order_review_option_options"
										:normalizer="normalizerOption"
									/>
								</div>
							</div>
							<div class="col-md-12" style="text-align: center">
								<button
									type="submit"
									class="btn btn-warning btn-sm mt-1 mb-1"
									@click.prevent="filterData()"
								>
									<i class="fa fa-search"></i>
									Tìm
								</button>
								<button
									type="reset"
									class="btn btn-secondary btn-sm mt-1 mb-1"
									@click.prevent="clearFilter()"
								>
									<i class="fa fa-reset"></i>
									Xóa bộ lọc
								</button>
							</div>
						</div>
					</div>
				</div>
			</b-collapse>

			<div class="row mb-1">
				<div class="col-md-9">
					<div class="form-group row">
						<button
							type="button"
							class="btn btn-success btn-sm ml-1 mt-1"
							@click="showCreateDialog"
						>
							<strong>
								<i class="fas fa-truck-loading mr-1 text-bold" />Tạo vận đơn</strong
							>
						</button>
						<button class="btn btn-info btn-sm ml-1 mt-1" @click="exportToExcel">
							<strong><i class="fas fa-file-excel mr-1" />Xuất file excel</strong>
						</button>
					</div>
				</div>
				<div class="col-md-3">
					<div class="input-group input-group-sm mt-1 mb-1">
						<input
							type="search"
							class="form-control -control-navbar"
							v-model="search_pattern"
							:placeholder="search_placeholder"
							aria-label="Search"
						/>
						<div class="input-group-append">
							<button
								class="btn btn-default"
								style="background: #1b1a1a; color: white"
							>
								<i class="fas fa-search"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<!-- tạo nút edit và delete -->

			<div class="row">
				<b-table
					responsive
					hover
					striped
					show-empty
					:busy="is_loading"
					:bordered="true"
					:current-page="pagination.current_page"
					:per-page="pagination.item_per_page"
					:filter="search_pattern"
					:fields="fields"
					:items="orders"
					:tbody-tr-class="rowClass"
				>
					<template #head(selection)>
						<b-form-checkbox
							class="ml-1"
							v-model="is_select_all"
							@change="selectAll"
						></b-form-checkbox>
					</template>
					<template v-slot:cell(selection)="data">
						<b-form-checkbox class="ml-1" :value="data.item.id" v-model="selected_ids">
						</b-form-checkbox>
					</template>
					<template #empty="scope">
						<h6 class="text-center">Không có đơn hàng nào để hiển thị</h6>
					</template>
					<template #table-busy>
						<div class="text-center text-primary my-2">
							<b-spinner class="align-middle" type="grow"></b-spinner>
							<strong>Đang tải dữ liệu...</strong>
						</div>
					</template>
					<template #cell(index)="data">
						{{
							data.index +
							(pagination.current_page - 1) * pagination.item_per_page +
							1
						}}
					</template>

					<template #cell(status.name)="data">
						<span :class="data.value.badge_class">{{ data.value }}</span>
					</template>

					<template #cell(sap_so_number)="data">
						<div class="margin">
							<button
								class="btn btn-xs mr-1 text-info"
								@click="showInfoDialog(data.item)"
							>
								{{ data.value }}
							</button>
						</div>
					</template>

					<template #cell(province)="data"
						>{{ formatAddress(data.item.detail?.delivery_address) }}
					</template>

					<template #cell(sap_so_created_date)="data">
						{{ data.value | formatDate }}
					</template>

					<template #cell(approved.sap_so_finance_approval_date)="data">
						{{ data.value | formatDate }}
					</template>
					<template #cell(delivery_info.delivery.start_delivery_date)="data">
						{{ data.value | formatDate }}
					</template>
					<template #cell(delivery_info.delivery.estimate_delivery_date)="data">
						{{ data.value | formatDate }}
					</template>
					<template #cell(delivery_info.delivery.complete_delivery_date)="data">
						{{ data.value | formatDate }}
					</template>
					<template #cell(delivery_info.confirm_delivery_date)="data">
						{{ data.value | formatDate }}
					</template>
				</b-table>
			</div>
			<!-- end tạo nút -->
			<!-- phân trang -->
			<div class="row">
				<label class="col-form-label-sm col-md-2" style="text-align: left" for=""
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
				<label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
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
			<!-- end phân trang -->

			<DialogOrderInfo :order="viewing_order" />
			<!-- tạo form -->
			<DialogCreateDelivery :order_ids="creating_delivery_order_ids" />
			<!-- end tạo form -->
		</div>
	</div>
	<!-- end container -->
	<!-- </b-overlay> -->
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import DialogCreateDelivery from './dialogs/DialogCreateDelivery.vue';
	import DialogOrderInfo from './dialogs/DialogOrderInfo.vue';
	import { saveExcel } from '@progress/kendo-vue-excel-export';
	import moment from 'moment';
	export default {
		components: {
			Treeselect,
			DialogCreateDelivery,
			DialogOrderInfo,
		},
		data() {
			return {
				api_handler: new ApiHandler(window.Laravel.access_token),

				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',

				is_select_all: false,
				selected_ids: [],
				creating_delivery_order_ids: [],

				is_editing: false,
				is_loading: false,
				editing_item: {},

				is_show_search: false,
				form_filter: {
					start_date: '',
					end_date: '',
					statuses: [],
					customers: [],
					warehouses: [],
					sap_so_number: undefined,
					sap_do_number: undefined,
					distribution_channels: [],
					delivery_partners: [],
					order_review_options: [],
				},
				customer_options: [],
				warehouse_options: [],
				distribution_channel_options: [],
				delivery_partner_options: [],
				order_review_option_options: [],

				order_statuses: [
					{ id: 10, label: 'Đang xử lí đơn hàng' },
					{ id: 20, label: 'Đã duyệt & đang soạn hàng' },
					{ id: 30, label: 'Đang vận chuyển' },
					{ id: 40, label: 'Đã giao một phần' },
					{ id: 100, label: 'Đã giao xong' },
					{ id: 200, label: 'Đã nhận hàng' },
				],

				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},

				fields: [
					{
						key: 'selection',
						label: 'All',
						stickyColumn: true,
					},
					{
						key: 'index',
						label: 'STT',
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
						key: 'sale.distribution_channel.name',
						label: 'Kênh',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'status.name',
						label: 'Trạng thái',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'sap_so_created_date',
						label: 'Ngày tạo đơn',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'approved.sap_so_finance_approval_date',
						label: 'Ngày duyệt',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_info.delivery.start_delivery_date',
						label: 'Ngày đi giao',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_info.delivery.estimate_delivery_date',
						label: 'Ngày dự kiến giao',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_info.delivery.complete_delivery_date',
						label: 'Ngày giao thực tế',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_info.confirm_delivery_date',
						label: 'Ngày KH xác nhận',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'customer_reviews[0]?.review_content',
						label: 'Đánh giá KH',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'province',
						label: 'Tỉnh/Thành',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'customer.code',
						label: 'Mã KH',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'receiver.receiver_name',
						label: 'Khách hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'warehouse.name',
						label: 'Kho hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_info.delivery.partner.name',
						label: 'Nhà vận chuyển',
						sortable: true,
						class: 'text-nowrap text-center',
					},
				],

				orders: [],

				viewing_order: {},
				api_url_orders: '/api/partner/orders',
			};
		},
		async created() {
			this.form_filter.start_date = this.formatDate(this.subtractDate(new Date(), 0, 1, 0));
			this.form_filter.end_date = this.formatDate(new Date());
			await Promise.all([this.fetchFilterOptions()]);
		},
		watch: {
			'$route.query': {
				immediate: true,
				handler(new_query, old_query) {
					if (new_query !== old_query && Object.keys(new_query).length > 0) {
						if (new_query.filter == 'undone') {
							this.form_filter.statuses = [10, 20, 30, 40];
							this.fetchData();
						}
						if (new_query.filter == 'delivering') {
							this.form_filter.statuses = [30, 40];
							this.fetchData();
						}
						if (new_query.filter == 'can-delivery') {
							this.form_filter.statuses = [10];
							this.fetchData();
						}
						if (new_query.filter == 'all') {
							this.form_filter.statuses = [10, 20, 30, 40, 100, 200];
							this.fetchData();
						}
						if (new_query.filter == 'preparing') {
							this.form_filter.statuses = [20];
							this.fetchData();
						}
					}
				},
			},
		},
		methods: {
			async fetchData(query) {
				try {
					this.is_loading = true;
					const [orders] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', `${this.api_url_orders}/expanded`, {
							from_date:
								this.form_filter.start_date.length == 0
									? undefined
									: this.form_filter.start_date,
							to_date:
								this.form_filter.end_date.length == 0
									? undefined
									: this.form_filter.end_date,
							status_ids: this.form_filter.statuses,
							customer_ids: this.form_filter.customers,
							warehouse_ids: this.form_filter.warehouses,
							sap_so_number: this.form_filter.sap_so_number,
							sap_do_number: this.form_filter.sap_do_number,
							distribution_channel_ids: this.form_filter.distribution_channels,
							delivery_partner_ids: this.form_filter.delivery_partners,
							order_review_option_ids: this.form_filter.order_review_options,
						}),
					]);

					this.orders = orders;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async fetchFilterOptions() {
				try {
					const [
						warehouses,
						distribution_channels,
						delivery_partners,
						order_review_options,
					] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', 'api/master/warehouses/minified'),
						new APIRequest('get', 'api/master/distribution-channels'),
						new APIRequest('get', 'api/master/delivery-partners'),
						new APIRequest('get', 'api/master/order-review-options'),
					]);
					this.warehouse_options = warehouses;
					this.distribution_channel_options = distribution_channels;
					this.delivery_partner_options = delivery_partners;
					this.order_review_option_options = order_review_options;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				}
			},
			selectAll() {
				this.selected_ids = [];
				if (this.is_select_all) {
					for (let i in this.orders) {
						this.selected_ids.push(this.orders[i].id);
					}
				}
			},
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
			showCreateDialog() {
				this.creating_delivery_order_ids = this.selected_ids;
				$('#DialogCreateDelivery').modal('show');
				this.selected_ids = [];
				this.is_select_all = false;
			},
			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler.get(`${this.api_url_orders}/expanded`, {
						from_date: this.form_filter.start_date,
						to_date: this.form_filter.end_date,
						status_ids: this.form_filter.statuses,
						customer_ids: this.form_filter.customers,
						warehouse_ids: this.form_filter.warehouses,
						sap_so_number: this.form_filter.sap_so_number,
						sap_do_number: this.form_filter.sap_do_number,
						distribution_channel_ids: this.form_filter.distribution_channels,
						delivery_partner_ids: this.form_filter.delivery_partners,
						order_review_option_ids: this.form_filter.order_review_options,
					});
					this.orders = data;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
			},
			async clearFilter() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					this.form_filter.start_date = this.formatDate(
						this.subtractDate(new Date(), 0, 1, 0),
					);
					this.form_filter.end_date = this.formatDate(new Date());
					this.form_filter.statuses = [];
					this.form_filter.customers = [];
					this.form_filter.warehouses = [];
					this.form_filter.sap_so_number = undefined;
					this.form_filter.sap_do_number = undefined;
					this.form_filter.distribution_channels = [];
					this.form_filter.delivery_partners = [];
					this.form_filter.order_review_options = [];
					await this.fetchData();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				} finally {
					this.is_loading = false;
				}
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
			showInfoDialog(order) {
				this.viewing_order = order;
				$('#DialogOrderInfo').modal('show');
			},
			subtractDate(date, sub_date = 0, sub_month = 0, sub_year = 0) {
				date.setDate(date.getDate() - sub_date);
				date.setMonth(date.getMonth() - sub_month);
				date.setFullYear(date.getFullYear() - sub_year);
				return date;
			},
			formatDate(date) {
				var d = new Date(date),
					month = '' + (d.getMonth() + 1),
					day = '' + d.getDate(),
					year = d.getFullYear();

				if (month.length < 2) month = '0' + month;
				if (day.length < 2) day = '0' + day;

				return [year, month, day].join('-');
			},
			formatDateTime(value) {
				if (value) {
					return moment(String(value)).format('DD/MM/YYYY');
				}
			},
			formatAddress(address) {
				if (!address) return '';
				const addr_arr = address.split(',');
				return [addr_arr[addr_arr.length - 2], addr_arr[addr_arr.length - 1]].join(', ');
			},
			exportToExcel() {
				let clone_fields = structuredClone(this.fields);
				clone_fields.splice(0, 2);
				let clone_orders = structuredClone(this.orders);
				clone_orders.forEach((order) => {
					order.sap_so_created_date = this.formatDateTime(order.sap_so_created_date);
					order.approved.sap_so_finance_approval_date = this.formatDateTime(
						order.approved.sap_so_finance_approval_date,
					);
					order.delivery_info.delivery.start_delivery_date = this.formatDateTime(
						order.delivery_info.delivery.start_delivery_date,
					);
					order.delivery_info.delivery.estimate_delivery_date = this.formatDateTime(
						order.delivery_info.delivery.estimate_delivery_date,
					);
					order.delivery_info.delivery.complete_delivery_date = this.formatDateTime(
						order.delivery_info.delivery.complete_delivery_date,
					);
					order.delivery_info.confirm_delivery_date = this.formatDateTime(
						order.delivery_info.confirm_delivery_date,
					);

					order.province = this.formatAddress(order.detail?.delivery_address);
				});
				saveExcel({
					data: clone_orders,
					fileName: 'orders_exported',
					columns: clone_fields.map((field) => ({
						field: field.key,
						title: field.label,
					})),
				});
			},
		},
		computed: {
			rows() {
				return this.orders.length;
			},
		},
	};
</script>
