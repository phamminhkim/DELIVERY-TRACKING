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
						<div class="row">
							<div class="col-md-2 align-center text-right">
								<span
									class="align-middle"
									style="font-weight: bold; font-size: 14px"
									>Cấu hình in:</span
								>
							</div>
							<div class="col-md-4">
								<treeselect
									v-model="print_config_selected"
									:multiple="false"
									:options="print_config_options"
									placeholder="Chọn cấu hình in"
									required
								/>
							</div>
							<div class="col-md-6">
								<button
									type="button"
									class="btn btn-warning btn-sm"
									@click="showPrintSettingDialog"
								>
									<strong>
										<i class="fas fa-plus mr-1 text-bold" />Tạo cấu hình
										in</strong
									>
								</button>
								<button
									v-if="print_config_selected != print_config_default.id"
									class="btn btn-danger btn-sm"
									@click.prevent="deletePrintConfig(print_config_selected)"
								>
									<strong>
										<i class="fas fa-trash mr-1 text-bold" />Xóa cấu hình
										in</strong
									>
								</button>
							</div>
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
			</div>
		</div>
		<!-- end container -->

		<DialogCreatePrintQRSetting
			@onPrintQRSettingCreated="onPrintQRSettingCreated"
		></DialogCreatePrintQRSetting>

		<dialog-delivery-info :delivery_id="viewing_delivery_id" v-on:printQrCode="printQrCode" />

		<dialog-create-delivery ref="dialog_create" @onDeliveryCreated="onDeliveryCreated" />
	</b-overlay>
</template>

<script>
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import Treeselect from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import DialogDeliveryInfo from './dialogs/DialogDeliveryInfo.vue';
	import DialogCreateDelivery from './dialogs/DialogCreateDelivery.vue';
	import APIHandler from '../ApiHandler';
	import DialogCreatePrintQRSetting from './dialogs/DialogCreatePrintQRSetting.vue';

	export default {
		components: {
			DialogDeliveryInfo,
			DialogCreateDelivery,
			DialogCreatePrintQRSetting,
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',

				is_select_all: false,
				selected_ids: [],
				print_config_default: {
					id: 'default',
					name: 'Mặc định',
					config: {
						dimension: {
							width: '5cm',
							height: '3cm',
						},
						DO: {
							left: '1.243541666666651cm',
							top: '0cm',
						},
						SO: {
							left: '-0.79374999999999cm',
							top: '1.164166666666652cm',
						},
						qr_code: {
							left: '1.534583333333314cm',
							top: '0.661458333333325cm',
						},
					},
				},
				print_configs: [],
				print_config_options: [],
				print_config_selected: null,

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
			this.fetchPrintQRConfigOptions();
		},
		watch: {
			'$route.query': {
				immediate: true,
				handler(new_query, old_query) {
					if (new_query !== old_query && Object.keys(new_query).length > 0) {
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
			async fetchPrintQRConfigOptions() {
				try {
					const { data } = await this.api_handler.get(
						`${this.api_url_deliveries}/print-configs`,
					);
					this.print_configs = [
						this.print_config_default,
						...data.map((print_config_option) => {
							let opt = print_config_option;
							opt.config = JSON.parse(opt.config);
							return opt;
						}),
					];

					this.print_config_options = this.print_configs.map((print_config) => {
						return {
							id: print_config.id,
							label: print_config.name,
						};
					});
					this.print_config_selected = this.print_config_default.id;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error);
				}
			},
			onPrintQRSettingCreated(print_config_created) {
				this.print_configs.push(print_config_created);
				this.print_config_options.push({
					id: print_config_created.id,
					label: print_config_created.name,
				});
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

					const config = this.print_config_selected
						? this.print_configs.filter((print_config) => {
								return print_config.id === this.print_config_selected;
						  })[0].config
						: this.print_config_default.config;

					const { data } = await this.api_handler.post(
						'api/admin/deliveries/print-qrs',
						{},
						{
							delivery_ids: delivery_ids,
							print_config: config,
						},
					);
					const print_url = data;
					this.openPrintDialog(print_url);
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				} finally {
					this.is_loading = false;
				}
			},

			openPrintDialog(print_url) {
				window.open(print_url, '_blank');
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
			showPrintSettingDialog() {
				$('#DialogCreatePrintQRSetting').modal('show');
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
			async deletePrintConfig(print_config_id) {
				try {
					await this.api_handler.delete(
						`${this.api_url_deliveries}/print-configs/${print_config_id}`,
					);
					for (let i in this.print_configs) {
						if (this.print_configs[i].id == print_config_id) {
							this.print_configs.splice(i, 1);
							this.print_config_options.splice(i, 1);
							this.print_config_selected = this.print_config_default.id;
							break;
						}
					}
					this.$showMessage('success', 'Xóa thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error);
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
