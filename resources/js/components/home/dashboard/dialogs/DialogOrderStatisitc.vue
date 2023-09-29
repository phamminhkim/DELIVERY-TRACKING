<template>
	<div class="modal fade" id="DialogOrderStatistic" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<b-overlay rounded="sm">
					<div class="modal-header">
						<h4 class="modal-title">
							<i class="fas fa-info-circle" /> Danh sách đơn hàng
							{{ viewingStatistic.toLowerCase() }}
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body" style="margin-left: 10px; margin-right: 10px">
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
										<button class="btn btn-xs mr-1 text-info">
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
								<template
									#cell(delivery_info.delivery.estimate_delivery_date)="data"
								>
									{{ data.value | formatDate }}
								</template>
								<template
									#cell(delivery_info.delivery.complete_delivery_date)="data"
								>
									{{ data.value | formatDate }}
								</template>
								<template #cell(delivery_info.confirm_delivery_date)="data">
									{{ data.value | formatDate }}
								</template>
							</b-table>
						</div>
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
			orders: {
				type: Array,
				default: () => [],
			},
			viewingStatistic: {
				type: String,
				default: '',
			},
		},
		components: {},
		data() {
			return {
				is_loading: false,
				search_placeholder: 'Tìm kiếm..',
				search_pattern: '',
				pagination: {
					item_per_page: 10,
					current_page: 1,
					page_options: [10, 50, 100, 500, { value: this.rows, text: 'All' }],
				},
				fields: [
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
			};
		},
		mounted() {
			const viewportMeta = document.createElement('meta');
			viewportMeta.name = 'viewport';
			viewportMeta.content = 'width=device-width, initial-scale=1';
			document.head.appendChild(viewportMeta);
		},
		computed: {
			rows() {
				return this.orders.length;
			},
		},
		methods: {
			rowClass(item, type) {
				if (!item || type !== 'row') return;
				if (item.status === 'awesome') return 'table-success';
			},
			formatAddress(address) {
				if (!address) return '';
				const addr_arr = address.split(',');
				return [addr_arr[addr_arr.length - 2], addr_arr[addr_arr.length - 1]].join(', ');
			},
		},
	};
</script>
