<template lang="">
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
										placeholder="Chọn trạng thái .."
										:multiple="true"
										:disable-branch-nodes="false"
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
									>Nhóm khách hàng</label
								>
								<div class="col-sm-10 mt-1 mb-1">
									<treeselect
										:multiple="false"
										id="customer_group_id"
										placeholder="Chọn nhóm khách hàng.."
										v-model="form_filter.customer_group_id"
										:options="customer_group_options"
										:normalizer="normalizerOption"
									></treeselect>
								</div>
							</div>

							<div class="form-group row">
								<label
									class="col-form-label-sm col-sm-2 col-form-label text-left text-md-right"
									for=""
									>PO khách hàng</label
								>
								<div class="col-sm-4">
									<input
										type="text"
										v-model="form_filter.sap_so_number"
										placeholder="Nhập PO.."
										class="form-control"
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




			<!-- <div class="row"></div> -->
		</div>
	</div>
</template>
<script>
    import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import ApiHandler, { APIRequest } from '../ApiHandler';
    import APIHandler from '../ApiHandler';
	export default {
        components: {
			Treeselect,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),
				is_show_search: false,
				form_filter: {
					start_date: '',
					end_date: '',
					// statuses: [],
					customers: [],
					customer_group_id: null,
				},
				customer_options: [],
				customer_group_options: [],

                //api_url_ais: '/api/ai/',
			};
		},
        created() {
			this.fetchOptionsData();
		},
		methods: {
			async fetchOptionsData() {
				const [customer_group_options] =
					await this.api_handler.handleMultipleRequest([
						new APIRequest('get', '/api/master/customer-groups'),
					]);
				this.customer_group_options = customer_group_options;
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

	}
    }
</script>
<style lang=""></style>
