<template>
	<!-- container -->
	<div class="container-fluid">
		<div>
			<div class="row">
				<div class="col-md-9">
					<div class="form-group row">
						<!-- <button type="button" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>Tạo hợp đồng</button> -->
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
						<!-- <button type="button" :title="$t('form.filter')" onclick="location.reload(true)" class="btn btn-secondary  btn-xs ml-1" ><i class="fas fa-redo-alt" title="Refresh"></i></button> -->
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
							class="btn btn-success btn-sm mt-1"
							@click="showCreateDialog"
						>
							<strong>
								<i class="fas fa-truck-loading mr-1 text-bold" />Tạo vận đơn</strong
							>
						</button>
						<button
							type="button"
							class="btn btn-info btn-sm ml-1 mt-1"
							@click="printDeliveryQrCode"
						>
							<strong> <i class="fas fa-print mr-1 text-bold" />In vận đơn</strong>
						</button>

						<button
							type="button"
							class="btn btn-info btn-sm ml-1 mt-1"
							@click="showExcelDialog"
						>
							<strong>
								<i class="fas fa-upload mr-1 text-bold"></i>Import Excel</strong
							>
						</button>
					</div>
					<div class="row">
						<div class="col-md-3 align-center text-right">
							<span class="align-middle" style="font-weight: bold; font-size: 14px"
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
						<div class="col-md-5">
							<button
								type="button"
								class="btn btn-warning btn-sm"
								@click="showPrintSettingDialog"
							>
								<strong> <i class="fas fa-plus mr-1 text-bold" />Tạo</strong>
							</button>
							<button
								v-if="
									print_config_selected &&
									print_config_selected != print_config_default.id
								"
								class="btn btn-danger btn-sm"
								@click.prevent="deletePrintConfig(print_config_selected)"
							>
								<strong> <i class="fas fa-trash mr-1 text-bold" />Xóa</strong>
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
					show-empty
					:busy="is_loading"
					:bordered="true"
					:current-page="pagination.current_page"
					:per-page="pagination.item_per_page"
					:filter="search_pattern"
					:fields="fields"
					:items="deliveries"
					:tbody-tr-class="rowClass"
				>
					<template #empty="scope">
						<h6 class="text-center">Không có vận đơn nào để hiển thị</h6>
					</template>
					<template #table-busy>
						<div class="text-center text-primary my-2">
							<b-spinner class="align-middle" type="grow"></b-spinner>
							<strong>Đang tải dữ liệu...</strong>
						</div>
					</template>
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
                    <template #cell(province)="data"
						>{{ formatAddress(data.item.address) }}
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
					<template #cell(estimate_delivery_date)="data">
						<span>{{ data.value }}</span>
						<b-badge variant="danger" v-if="data.item.is_late_deadline"
							><i class="fas fa-fire text-white mr-1"></i>Trễ hạn
						</b-badge>
						<b-badge variant="warning" v-if="data.item.is_near_deadline"
							>Gần đến hạn
						</b-badge>
					</template>
					<template #cell(delivery_code)="data">
						<a href="#" @click="showInfoDialog(data.item)">
							{{ data.value }}
						</a>
						<b-badge variant="danger" v-if="data.item.is_new"
							><i class="fas fa-fire text-white"></i>Mới
						</b-badge>
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
		</div>

		<DialogCreatePrintQRSetting
			@onPrintQRSettingCreated="onPrintQRSettingCreated"
		></DialogCreatePrintQRSetting>

		<dialog-delivery-info :delivery_id="viewing_delivery_id" v-on:printQrCode="printQrCode" />

		<dialog-create-delivery ref="dialog_create" @onDeliveryCreated="onDeliveryCreated" />

		<dialog-import-excel-to-create-delivery />
	</div>
	<!-- end container -->
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import ApiHandler, { APIRequest } from '../ApiHandler';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import DialogDeliveryInfo from './dialogs/DialogDeliveryInfo.vue';
	import DialogCreateDelivery from './dialogs/DialogCreateDelivery.vue';
	import APIHandler from '../ApiHandler';
	import DialogCreatePrintQRSetting from './dialogs/DialogCreatePrintQRSetting.vue';
	import DialogImportExcelToCreateDelivery from './dialogs/DialogImportExcelToCreateDelivery.vue';

	export default {
		components: {
			DialogDeliveryInfo,
			DialogCreateDelivery,
			DialogCreatePrintQRSetting,
			Treeselect,
			DialogImportExcelToCreateDelivery,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',
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
					filter: [],
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
						key: 'delivery_code',
						label: 'Mã vận đơn',
						sortable: true,
						class: 'text-nowrap text-left',
					},
					{
						key: 'status',
						label: 'Trạng thái',
						sortable: true,
						class: 'text-nowrap text-center',
					},
					{
						key: 'estimate_delivery_date',
						label: 'Thời hạn giao hàng',
						sortable: true,
						class: 'text-nowrap text-left',
					},
					{
						key: 'customer.name',
						label: 'Khách hàng',
						sortable: true,
						class: 'text-nowrap text-left',
					},
                    {
						key: 'province',
						label: 'Tỉnh/Thành',
						sortable: true,
						class: 'text-nowrap text-left',
					},
					{
						key: 'partner.name',
						label: 'Đơn vị vận chuyển',
						sortable: true,
						class: 'text-nowrap text-left',
					},
					{
						key: 'created_at',
						label: 'Ngày tạo vận đơn',
						sortable: true,
						class: 'text-nowrap text-left',
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
						key: 'address',
						label: 'Địa chỉ giao hàng',
						sortable: true,
						class: 'text-nowrap text-left',
					},
					// {
					// 	key: 'action',
					// 	label: 'Hành động',
					// 	class: 'text-nowrap text-center',
					// },
				],

				deliveries: [],
				api_url_deliveries: '/api/partner/deliveries',
			};
		},

		async created() {
			//this.fetchData();
			this.fetchPrintQRConfigOptions();
			this.form_filter.start_date = this.formatDate(this.subtractDate(new Date(), 0, 1, 0));
			this.form_filter.end_date = this.formatDate(new Date());
			await Promise.all([this.fetchData(), this.fetchFilterOptions()]);
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
					}
				},
			},
		},
		methods: {
			async fetchData(query) {
				try {
					//if (this.is_loading) return;
					this.is_loading = true;
					const [deliveries] = await this.api_handler.handleMultipleRequest([
						new APIRequest('get', this.api_url_deliveries, {
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
					this.deliveries = deliveries;
				} catch (error) {
					this.$showMessage('error', 'Lỗi', error.message);
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
			async filterData() {
				try {
					if (this.is_loading) return;
					this.is_loading = true;

					const { data } = await this.api_handler.get(this.api_url_deliveries, {
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
					this.deliveries = data;
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
						'api/partner/deliveries/print-qrs',
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
			showExcelDialog() {
				$('#DialogImportExcelToCreateDelivery').modal('show');
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
            formatAddress(address) {
				if (!address) return '';
				const addr_arr = address.split(',');
				return [addr_arr[addr_arr.length - 2], addr_arr[addr_arr.length - 1]].join(', ');
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
					data.is_new = true;
					this.deliveries.unshift(data);
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
