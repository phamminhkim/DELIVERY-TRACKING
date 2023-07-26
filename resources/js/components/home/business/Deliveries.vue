<template>
	<b-overlay :show="is_loading" rounded="sm">
		<!-- container -->
		<div class="container-fluid">
			<div>
				<div class="row mb-1">
					<div class="col-md-9">
						<div class="form-group row">
							<button
								type="button"
								class="btn btn-success btn-sm ml-1 mt-1"
								@click="showCreateDialog"
							>
								<strong>
									<i class="fas fa-truck-loading mr-1 text-bold" />Tạo vận
									đơn</strong
								>
							</button>
							<button
								type="button"
								class="btn btn-info btn-sm ml-1 mt-1"
								@click="printDeliveryQrCode"
							>
								<strong>
									<i class="fas fa-print mr-1 text-bold" />In vận đơn</strong
								>
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
						:bordered="true"
						:current-page="pagination.current_page"
						:per-page="pagination.item_per_page"
						:filter="search_pattern"
						:fields="fields"
						:items="deliveries"
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
							<b-form-checkbox
								class="ml-1"
								:value="data.item.id"
								v-model="selected_ids"
							>
							</b-form-checkbox>
						</template>
						<template #cell(index)="data">
							{{
								data.index +
								(pagination.current_page - 1) * pagination.item_per_page +
								1
							}}
						</template>
						<template #cell(status)="data">
							<span :class="data.value.badge_class">{{ data.value.name }}</span>
						</template>
						<template #cell(action)="data">
							<div class="margin">
								<button
									class="btn btn-xs mr-1 text-info"
									@click="showInfoDialog(data.item)"
								>
									<i
										class="fas fa-info-circle mr-1"
										title="Xem thông tin chi tiết"
									/>Xem thông tin chi tiết
								</button>
							</div>
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
				<!-- end phân trang -->

				<!-- tạo form -->
				<!-- end tạo form -->
			</div>
		</div>
		<!-- end container -->
		<dialog-delivery-info :delivery_id="viewing_delivery_id" v-on:printQrCode="printQrCode" />

		<dialog-create-delivery ref="dialog_create" @onDeliveryCreated="onDeliveryCreated" />
	</b-overlay>
</template>

<script>
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import DialogDeliveryInfo from './dialogs/DialogDeliveryInfo.vue';
	import DialogCreateDelivery from './dialogs/DialogCreateDelivery.vue';
	import APIHandler from '../ApiHandler';

	export default {
		components: {
			DialogDeliveryInfo,
			DialogCreateDelivery,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',

				is_select_all: false,
				selected_ids: [],

				is_editing: false,
				is_loading: false,
				editing_item: {},
				viewing_delivery_id: null,

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
						width: '40px',
					},
					{
						key: 'index',
						label: 'STT',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'partner.name',
						label: 'Đơn vị vận chuyển',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'delivery_code',
						label: 'Mã vận đơn',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'created_at',
						label: 'Ngày tạo vận đơn',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'start_delivery_date',
						label: 'Ngày giao hàng',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'complete_delivery_date',
						label: 'Ngày hoàn thành',
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

				deliveries: [],
				api_url_deliveries: '/api/admin/deliveries',
			};
		},
		created() {
			this.fetchData();
		},
		watch: {
			'$route.query': {
				immediate: true,
				handler(new_query, old_query) {
					if (new_query !== old_query) {
						this.fetchData(new_query);
					}
				},
			},
		},
		methods: {
			async fetchData(query) {
				try {
					if (this.is_loading) return;
					this.is_loading = true;
					let result = await this.api_handler.get(this.api_url_deliveries, query);
					if (result.success) {
						this.deliveries = result.data;
					} else {
						this.$showMessage('error', 'Lỗi', result.message);
					}
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
				} finally {
					this.is_loading = false;
				}
			},
			async printDeliveryQrCode() {
				if (this.selected_ids.length === 0) {
					this.$showMessage('warning', 'Cảnh báo', 'Vui lòng chọn 1 vận đơn để in');
					return;
				}
				await this.printQrCode(this.selected_ids).finally(() => {
					this.selected_ids = [];
				});
			},
			async printQrCode(delivery_ids) {
				try {
					this.is_loading = true;
					// let result = await this.api_handler.post(
					// 	'api/admin/deliveries/' + delivery_id + '/print-qr',
					// );

					// const qr_codes = await this.api_handler.handleMultipleRequest(
					// 	delivery_ids.map((id) => {
					// 		return new APIRequest('post', `api/admin/deliveries/${id}/print-qr`);
					// 	}),
					// );
					// this.openPrintDialog(qr_codes);
					const { data } = await this.api_handler.post(
						'api/admin/deliveries/print-qrs',
						{},
						{
							delivery_ids: delivery_ids,
						},
					);
					const print_url = data;
					let print_window = window.open(print_url, '_blank');

					print_window.print();
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				} finally {
					this.is_loading = false;
				}
			},

			async openPrintDialog(image_datas) {
				const print_url = await this.api_handler.post(
					'/gen-qr-view',
					{},
					{
						qr_codes: image_datas,
					},
				);

				let print_window = window.open(print_url, '_blank');

				print_window.print();
			},
			showInfoDialog(delivery) {
				this.viewing_delivery_id = delivery.id;
				$('#DialogDeliveryInfo').modal('show');
			},
			selectAll() {
				this.selected_ids = [];
				if (this.is_select_all) {
					for (let i in this.deliveries) {
						this.selected_ids.push(this.deliveries[i].id);
					}
				}
			},
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
			showCreateDialog() {
				$('#DialogCreateDelivery').modal('show');
			},
			async onDeliveryCreated(delivery_response) {
				try {
					const { data } = await this.api_handler.get(
						`${this.api_url_deliveries}/${delivery_response.delivery_id}`,
					);
					this.deliveries.push(data);
				} catch (error) {
					console.log(error);
				}
			},
		},
		computed: {
			rows() {
				return this.deliveries.length;
			},
		},
	};
</script>
