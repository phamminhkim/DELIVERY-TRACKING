<template>
	<div>
		<div class="row">
			<div class="col-md-8"></div>
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
			<div
				class="col-8 "
			>
            <div class="first-phase-result" style="display: flex; flex-direction: column; gap: 1rem">
                <div
					class="text-center text-primary my-2"
					v-if="is_loading_first_phase"
					style="opacity: 0.5"
				>
					<b-spinner class="align-middle" type="grow"></b-spinner>
					<strong>Đang tải dữ liệu...</strong>
				</div>
				<div
					class="card-rate"
					v-for="(table, index) in first_phase_result"
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
							v-model="first_phase_form.method"
							:options="first_phase_options.methods"
							required
						/>
					</div>
					<div class="form-group">
						<label for="camelotFlavor">Camelot Flavor</label>
						<small class="text-danger">*</small>
						<treeselect
							id="camelotFlavor"
							placeholder="Chọn cấu hình.."
							v-model="first_phase_form.camelot_flavor"
							:options="first_phase_options.camelot_flavors"
							required
						/>
					</div>
					<div class="form-group d-flex flex-row">
						<label>Merge Pages</label>
						<b-form-checkbox
							v-model="first_phase_form.is_merge_pages"
							style="margin-left: 10px"
						/>
					</div>
					<div class="form-group">
						<label>Exclude head tables count</label>
						<b-form-input
							type="number"
							v-model="first_phase_form.exclude_head_tables_count"
							class="form-number-input"
						/>
					</div>

					<div class="form-group">
						<label>Exclude tail tables count</label>
						<b-form-input
							type="number"
							v-model="first_phase_form.exclude_tail_tables_count"
							class="form-number-input"
						/>
					</div>
					<div class="d-flex justify-content-between">
						<b-button variant="success">Bước tiếp theo</b-button>
						<b-button variant="primary" @click="onClickCheckFirstPhase"
							>Kiểm tra</b-button
						>
					</div>
				</b-card>
			</div>
            <!-- Phase 2 -->
	    </div>
        <div class="row">
			<div
				class="col-8"

			>
            <div class="convert-phase-result"	style="display: flex; flex-direction: column; gap: 1rem">
                <div
					class="text-center text-primary my-2"
					v-if="is_loading_convert_phase"
					style="opacity: 0.5"
				>
					<b-spinner class="align-middle" type="grow"></b-spinner>
					<strong>Đang tải dữ liệu...</strong>
				</div>
				<div
					class="card-rate"
					v-for="(table, index) in convert_phase_result"
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
							v-model="convert_phase_form.method"
							:options="convert_phase_options.methods"
							required
						/>
					</div>
					<div class="form-group">
						<label for="manualPattern">Manual Patterns</label>
						<small class="text-danger">*</small>
						<JsonEditorVue
                            v-model="convert_phase_form.manual_pattern"
                            v-bind="convert_phase_options"
                        />
					</div>
					<div class="form-group">
                        <label for="regexPattern">Regex Pattern</label>
                        <small class="text-danger">*</small>
                        <input
                            id="regexPattern"
                            type="text"
                            v-model="convert_phase_form.regex_pattern"
                            placeholder="Nhập mô hình.."
                            class="form-control"
                            required
                        />
                    </div>

					<div class="d-flex justify-content-between">
						<b-button variant="success">Bước tiếp theo</b-button>
						<b-button variant="primary" @click="onClickCheckConvertPhase"
							>Kiểm tra</b-button
						>
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
    //import JsonEditorVue from 'json-editor-vue';
	export default {
		components: {
			Treeselect,
            //JsonEditorVue
		},
		data() {
			return {
				api_handler: new APIHandler(window.Laravel.access_token),

				file: null,
                is_loading_convert_phase: false,
				is_loading_first_phase: false,
				first_phase_form: {
					method: 'camelot',
					camelot_flavor: 'lattice',
					is_merge_pages: true,
					exclude_head_tables_count: 0,
					exclude_tail_tables_count: 0,
				},

				first_phase_result: [],
				first_phase_options: {
					methods: [{ id: 'camelot', label: 'Camelot' }],
					camelot_flavors: [
						{ id: 'stream', label: 'Stream' },
						{ id: 'lattice', label: 'Lattice' },
					],
				},
                convert_phase_form: {
					method: [],
                    manual_pattern: {},
                    regex_pattern:undefined,
				},
                convert_phase_result: [],
                convert_phase_options: {
					methods: [
                        {id: 'manual', label: 'Manual',},
                        {id: 'leaguecsv', label: 'Leaguecsv'}
                    ],
                    manual_patterns: {
                        mode: 'name',
                        indentation: 4,
                        data: []
		            }
				},


			};
		},
		methods: {
			async onClickCheckFirstPhase() {
				try {
					if (this.is_loading_first_phase) return;
					this.is_loading_first_phase = true;

					const { data } = await this.api_handler
						.setHeaders({
							'Content-Type': 'multipart/form-data',
						})
						.post(
							'/api/ai/config/extract',
							{},
							APIHandler.createFormData({
								file: this.file,
								...this.first_phase_form,
							}),
						)
						.finally(() => {
							this.is_loading_first_phase = false;
						});

					this.first_phase_result = data;

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

            async onClickCheckConvertPhase() {
				try {
					if (this.is_loading_convert_phase) return;
					this.is_loading_convert_phase = true;

					const { data } = await this.api_handler
						.setHeaders({
							'Content-Type': 'multipart/form-data',
						})
						.post(
							'/api/ai/config/convert',
							{},
							APIHandler.createFormData({
								file: this.file,
								...this.convert_phase_form,
							}),
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
	.first-phase-result {
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
