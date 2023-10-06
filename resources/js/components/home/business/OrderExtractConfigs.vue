<template>
	<div>
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-4">
						<treeselect
							:multiple="false"
							id="method"
							placeholder="Chọn customer group.."
							v-model="load_config_form.customer_group_id"
							:options="customer_group_options"
							required
						/>
					</div>
					<div class="col-md-4">
						<b-button variant="success" @click="onClickLoadConfig"
							>Load cấu hình</b-button
						>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<b-form-file
						v-model="file"
						:state="Boolean(file)"
						placeholder="Chọn file để bắt đầu cấu hình..."
						drop-placeholder="Drop file here..."
					></b-form-file>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-8">
				<div
					class="extract-phase-result"
					style="display: flex; flex-direction: column; gap: 1rem"
				>
					<div
						class="text-center text-primary my-2"
						v-if="is_loading_extract_phase"
						style="opacity: 0.5"
					>
						<b-spinner class="align-middle" type="grow"></b-spinner>
						<strong>Đang tải dữ liệu...</strong>
					</div>
					<div
						class="card-rate"
						v-for="(table, index) in extract_phase_result"
						:key="index"
						header-tag="header"
						v-else
					>
						<div class="time">Bảng thứ {{ index + 1 }}</div>
						<div class="line"></div>
						<div class="container-rate">
							<div class="box-rate">
								<div class="review-content">
									{{ table }}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<b-card>
					<div class="form-group">
						<label for="method">Method</label>
						<small class="text-danger">*</small>
						<treeselect
							:multiple="false"
							id="method"
							placeholder="Chọn cách thức.."
							v-model="extract_phase_form.method"
							:options="extract_phase_options.methods"
							required
						/>
					</div>
					<div class="form-group">
						<label for="camelotFlavor">Camelot Flavor</label>
						<small class="text-danger">*</small>
						<treeselect
							id="camelotFlavor"
							placeholder="Chọn cấu hình.."
							v-model="extract_phase_form.camelot_flavor"
							:options="extract_phase_options.camelot_flavors"
							required
						/>
					</div>
					<div class="form-group d-flex flex-row">
						<label>Merge Pages</label>
						<b-form-checkbox
							v-model="extract_phase_form.is_merge_pages"
							style="margin-left: 10px"
						/>
					</div>
					<div class="form-group">
						<label>Exclude head tables count</label>
						<b-form-input
							type="number"
							v-model="extract_phase_form.exclude_head_tables_count"
							class="form-number-input"
						/>
					</div>

					<div class="form-group">
						<label>Exclude tail tables count</label>
						<b-form-input
							type="number"
							v-model="extract_phase_form.exclude_tail_tables_count"
							class="form-number-input"
						/>
					</div>
					<div class="d-flex justify-content-between">
						<b-button variant="success" @click="onClickNextPhaseInExtractPhase"
							>Bước tiếp theo</b-button
						>
						<b-button variant="primary" @click="onClickCheckExtractPhase"
							>Kiểm tra</b-button
						>
					</div>
				</b-card>
			</div>
			<!-- Phase 2 -->
		</div>

		<div class="row">
			<div class="col-md-8"></div>
			<div class="col-md-4">
				<div class="form-group">
					<b-form-input
						:value="JSON.stringify(convert_phase_input)"
						:disabled="true"
					></b-form-input>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-8">
				<div
					class="convert-phase-result"
					style="display: flex; flex-direction: column; gap: 1rem"
				>
					<div
						class="text-center text-primary my-2"
						v-if="is_loading_convert_phase"
						style="opacity: 0.5"
					>
						<b-spinner class="align-middle" type="grow"></b-spinner>
						<strong>Đang tải dữ liệu...</strong>
					</div>
					<VueJsonEditor v-model="convert_phase_result" />
				</div>
			</div>
			<div class="col-md-4">
				<b-card>
					<div class="form-group">
						<label for="method">Method</label>
						<small class="text-danger">*</small>
						<treeselect
							:multiple="false"
							id="method"
							placeholder="Chọn cách thức.."
							v-model="convert_phase_form.method"
							:options="convert_phase_options.methods"
							required
						/>
					</div>
					<div class="form-group">
						<label for="manualPattern">Manual Patterns</label>
						<small class="text-danger">*</small>
						<VueJsonEditor v-model="convert_phase_form.manual_patterns" />
					</div>
					<div class="form-group">
						<label for="regexPattern">Regex Pattern</label>
						<input
							id="regexPattern"
							type="text"
							v-model="convert_phase_form.regex_pattern"
							placeholder="Nhập regex.."
							class="form-control"
						/>
					</div>

					<div class="d-flex justify-content-between">
						<b-button variant="success" @click="onClickNextPhaseInConvertPhase"
							>Bước tiếp theo</b-button
						>
						<b-button variant="primary" @click="onClickCheckConvertPhase"
							>Kiểm tra</b-button
						>
					</div>
				</b-card>
			</div>
		</div>

		<div class="row">
			<div class="col-md-8"></div>
			<div class="col-md-4">
				<div class="form-group">
					<b-form-input
						:value="JSON.stringify(restructure_phase_input)"
						:disabled="true"
					></b-form-input>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-8">
				<div
					class="convert-phase-result"
					style="display: flex; flex-direction: column; gap: 1rem"
				>
					<div
						class="text-center text-primary my-2"
						v-if="is_loading_restructure_phase"
						style="opacity: 0.5"
					>
						<b-spinner class="align-middle" type="grow"></b-spinner>
						<strong>Đang tải dữ liệu...</strong>
					</div>
					<VueJsonEditor v-model="restructure_phase_result" />
				</div>
			</div>
			<div class="col-md-4 d-flex flex-column justify-content-between">
				<b-card>
					<div class="form-group">
						<label for="method">Method</label>
						<small class="text-danger">*</small>
						<treeselect
							:multiple="false"
							id="method"
							placeholder="Chọn cách thức.."
							v-model="restructure_phase_form.method"
							:options="restructure_phase_options.methods"
							required
						/>
					</div>
					<div class="form-group">
						<label for="manualPattern">Structure</label>
						<small class="text-danger">*</small>
						<VueJsonEditor v-model="restructure_phase_form.structure" />
					</div>

					<div class="d-flex justify-content-between">
						<div></div>
						<b-button variant="primary" @click="onClickCheckRestructurePhase"
							>Kiểm tra</b-button
						>
					</div>
				</b-card>

				<b-card>
					<div class="row">
						<div class="col-md-8">
							<treeselect
								:multiple="false"
								id="method"
								placeholder="Chọn customer group.."
								v-model="create_config_form.customer_group_id"
								:options="customer_group_options"
								required
							/>
						</div>
						<div class="col-md-4">
							<b-button variant="success" @click="onClickCreateConfig"
								>Lưu cấu hình</b-button
							>
						</div>
					</div>
				</b-card>
			</div>
		</div>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import VueJsonEditor from 'vue-json-editor';
	export default {
		components: {
			Treeselect,
			VueJsonEditor,
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				file: null,
				is_loading_convert_phase: false,
				is_loading_extract_phase: false,
				extract_phase_form: {
					method: 'camelot',
					camelot_flavor: 'lattice',
					is_merge_pages: true,
					exclude_head_tables_count: 0,
					exclude_tail_tables_count: 0,
				},

				extract_phase_result: [],
				extract_phase_options: {
					methods: [{ id: 'camelot', label: 'Camelot' }],
					camelot_flavors: [
						{ id: 'stream', label: 'Stream' },
						{ id: 'lattice', label: 'Lattice' },
					],
				},

				is_loading_convert_phase: false,
				convert_phase_input: null,
				convert_phase_form: {
					method: 'leaguecsv',
					manual_patterns: [],
					regex_pattern: null,
				},
				convert_phase_result: null,
				convert_phase_options: {
					methods: [
						{ id: 'manual', label: 'Manual' },
						{ id: 'leaguecsv', label: 'League CSV' },
						{ id: 'regexmatch', label: 'Regex Match' },
						{ id: 'regexsplit', label: 'Regex Split' },
					],
				},

				is_loading_restructure_phase: false,
				restructure_phase_input: null,
				restructure_phase_form: {
					method: 'arraymappingbyindex',
					structure: {},
				},
				restructure_phase_options: {
					methods: [
						{ id: 'arraymappingbyindex', label: 'Array Mapping By Index' },
						{ id: 'arraymappingbykey', label: 'Array Mapping By Key' },
					],
				},
				restructure_phase_result: null,

				customer_group_options: [],

				create_config_form: {
					customer_group_id: null,
					extract_data_config: null,
					convert_table_config: null,
					restructure_data_config: null,
				},

				load_config_form: {
					customer_group_id: null,
				},
			};
		},
		created() {
			this.fetchOptionsData();
		},
		methods: {
			async fetchOptionsData() {
				const [customer_group_options] = await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
				]);
				this.customer_group_options = customer_group_options.map((item) => ({
					id: item.id,
					label: item.name,
				}));
			},
			async onClickCheckExtractPhase() {
				try {
					if (this.is_loading_extract_phase) return;
					this.is_loading_extract_phase = true;

					const { data } = await this.api_handler
						.setHeaders({
							'Content-Type': 'multipart/form-data',
						})
						.post(
							'/api/ai/config/extract',
							{},
							APIHandler.createFormData({
								file: this.file,
								...this.extract_phase_form,
								extract_method: this.extract_phase_form.method,
							}),
						)
						.finally(() => {
							this.is_loading_extract_phase = false;
						});

					this.extract_phase_result = data;

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			onClickNextPhaseInExtractPhase() {
				this.convert_phase_input = this.extract_phase_result;
				this.create_config_form.extract_data_config = this.extract_phase_form;
			},

			async onClickCheckConvertPhase() {
				try {
					if (this.is_loading_convert_phase) return;
					this.is_loading_convert_phase = true;

					const { data } = await this.api_handler
						.post(
							'/api/ai/config/convert',
							{},
							{
								raw_table_data: JSON.stringify(this.convert_phase_input),
								convert_method: this.convert_phase_form.method,
								manual_patterns: JSON.stringify(
									this.convert_phase_form.manual_patterns,
								),
								regex_pattern: this.convert_phase_form.regex_pattern,
							},
						)
						.finally(() => {
							this.is_loading_convert_phase = false;
						});

					this.convert_phase_result = data;

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			onClickNextPhaseInConvertPhase() {
				this.restructure_phase_input = this.convert_phase_result;
				this.create_config_form.convert_table_config = this.convert_phase_form;
			},

			async onClickCheckRestructurePhase() {
				try {
					if (this.is_loading_restructure_phase) return;
					this.is_loading_restructure_phase = true;

					const { data } = await this.api_handler
						.post(
							'/api/ai/config/restructure',
							{},
							{
								table_data: JSON.stringify(this.restructure_phase_input),
								restructure_method: this.restructure_phase_form.method,
								structure: JSON.stringify(this.restructure_phase_form.structure),
							},
						)
						.finally(() => {
							this.is_loading_restructure_phase = false;
						});

					this.restructure_phase_result = data;

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			async onClickCreateConfig() {
				this.create_config_form.restructure_data_config = this.restructure_phase_form;

				try {
					const { data } = await this.api_handler.post(
						'/api/ai/config',
						{},
						{
							customer_group_id: this.create_config_form.customer_group_id,
							extract_data_config: this.create_config_form.extract_data_config,

							convert_table_config: this.create_config_form.convert_table_config,

							restructure_data_config:
								this.create_config_form.restructure_data_config,
						},
					);

					this.$showMessage('success', 'Tạo cấu hình thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			async onClickLoadConfig() {
				try {
					this.create_config_form.customer_group_id =
						this.load_config_form.customer_group_id;
					const { data } = await this.api_handler.get(
						`/api/ai/config/customer-groups/${this.load_config_form.customer_group_id}`,
					);
					console.log(data);
					let extract_response = data.extract_data_config;
					this.extract_phase_form = {
						method: extract_response.method,
						camelot_flavor: extract_response.camelot_flavor,
						is_merge_pages: extract_response.is_merge_pages,
						exclude_head_tables_count: extract_response.exclude_head_tables_count,
						exclude_tail_tables_count: extract_response.exclude_tail_tables_count,
					};

					let convert_response = data.convert_table_config;
					this.convert_phase_form = {
						method: convert_response.method,
						manual_patterns: convert_response.manual_patterns
							? JSON.parse(convert_response.manual_patterns)
							: null,
						regex_pattern: convert_response.regex_pattern,
					};

					let restructure_response = data.restructure_data_config;
					this.restructure_phase_form = {
						method: restructure_response.method,
						structure: restructure_response.structure
							? JSON.parse(restructure_response.structure)
							: null,
					};
					this.$showMessage('success', 'Tải cấu hình thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response?.data.message);
				}
			},
		},
	};
</script>

<style>
	.container-rate {
		width: 100%;
		padding: 1.5rem;
	}

	.card-rate {
		border-radius: 0.5rem;
		width: 100%;
		display: flex;
		gap: 1px;
		flex-direction: column;
		padding: 1rem 0rem;
		box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
		background-color: #fff;
	}

	.box-rate {
		display: flex;
		width: 100%;
		gap: 1px;
		margin-bottom: 1rem;
	}

	.review-content {
		width: 100%;
		box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
		padding: 0 0.5rem;
		border-radius: 0.5rem;
		height: fit-content;
		min-height: 5rem;
	}

	.rate {
		display: flex;
		gap: 0.5rem;
		flex-wrap: wrap;
		flex-direction: column;
		width: 30%;
	}
	.line {
		border: 1px solid black;
		opacity: 0.1;
	}

	.criteria {
		padding: 0.3rem;
		background-color: rgb(23, 162, 184);
		color: white;
		border-radius: 10px !important;
		margin-right: 10px !important;
		display: inline-block;
		font-size: 12px;
		font-weight: 700;
		text-align: center;
	}
	.time {
		color: #999;
		display: flex;
		justify-content: flex-start;
		font-size: 15px;
		margin-left: 0.5rem;
		align-items: center;
	}
	.box-images {
		display: flex;
		flex-wrap: wrap;
		width: 100%;
	}
	.extract-phase-result {
		height: 80vh;
		overflow-y: scroll;
		padding: 0 10px 0 10px;
		/* background-color: #fff; */
		border-radius: 10px;
		border: solid 1px #000;
	}

	.convert-phase-result {
		height: 80vh;
		overflow-y: scroll;
		padding: 0 10px 0 10px;
		/* background-color: #fff; */
		border-radius: 10px;
		border: solid 1px #000;
	}

	.form-check-input {
		margin-left: 30px;
	}
	.form-number-input {
		border: 1px solid #dbd5d5;
	}
</style>
