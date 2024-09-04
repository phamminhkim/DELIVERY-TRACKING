<template>
	<div>
		<div class="row">
			<div class="col-md-8">
				<b-form class="row" @submit.prevent="onClickLoadConfig">
					<div class="col-md-4">
						<treeselect
							:multiple="false"
							id="method"
							placeholder="Chọn customer group.."
							v-model="load_config_form.customer_group_id"
							:options="customer_group_options"
							:normalizer="normalizer"
							required
						/>
					</div>
                    <div class="col-md-2">
						<treeselect
							:multiple="false"
							id="convert_file_type"
							placeholder="Chọn loại file.."
							v-model="load_config_form.convert_file_type_id"
							:options="convert_file_type_options"
							required
						/>
					</div>
					<div class="col-md-3">
						<b-form-select
							placeholder="Chọn cấu hình.."
							v-model="load_config_form.extract_order_id"
							:options="load_extract_order_config_options"
							required
						/>
					</div>
					<div class="col-md-3">
						<b-button variant="success" type="submit">Load cấu hình</b-button>
					</div>
				</b-form>
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
            <div class="col" style="text-align: right;">
                <b-button class="btn btn-info ml-1 mt-1" id="importConfig" @click="showImportOrderConfig"><i class="fas fa-upload mr-1"></i>Import cấu hình</b-button>
                <b-button class="btn btn-info ml-1 mt-1" id="exportConfig" @click="exportOrderConfig"><i class="fas fa-download mr-1"></i>Export cấu hình</b-button>
            </div>
        </div>
        <div class="row">
            <b-card no-body>
                <b-tabs card align="right"
                    active-nav-item-class="font-weight-bold text-primary">
                    <!-- CẤU HÌNH DATA -->
                    <b-tab title="Cấu hình data">
                    <b-card-text>
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

                                    <div class="form-group">
                                        <label>Specify only 1 table</label>
                                        <b-form-input
                                            type="number"
                                            v-model="extract_phase_form.specify_table_number"
                                            class="form-number-input"
                                        />
                                    </div>

                                    <div class="form-group d-flex flex-row">
                                        <label>Specify table areas</label>
                                        <b-form-checkbox
                                            v-model="extract_phase_form.is_specify_table_area"
                                            style="margin-left: 10px"
                                        />
                                    </div>
                                    <div v-if="extract_phase_form.is_specify_table_area" class="form-group">
                                        <label for="advancedSettings">Table area information</label>
                                        <small class="text-danger">*</small>
                                        <VueJsonEditor v-model="extract_phase_form.table_area_info" />
                                    </div>

                                    <div class="form-group d-flex flex-row">
                                        <label>Specify advanced settings</label>
                                        <b-form-checkbox
                                            v-model="extract_phase_form.is_specify_advanced_settings"
                                            style="margin-left: 10px"
                                        />
                                    </div>
                                    <div v-if="extract_phase_form.is_specify_advanced_settings" class="form-group">
                                        <label for="manualPattern">Advanced settings information</label>
                                        <small class="text-danger">*</small>
                                        <VueJsonEditor v-model="extract_phase_form.advanced_settings_info" />
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <b-button variant="success" @click="onClickNextPhaseInExtractPhase(data_config_type.DATA)"
                                            >Bước tiếp theo</b-button
                                        >
                                        <b-button variant="primary" @click="onClickQuicklyCheckExtractOrder(data_config_type.DATA)"
                                            >Kiểm tra nhanh</b-button>
                                        <b-button variant="primary" @click="onClickCheckExtractPhase(data_config_type.DATA)"
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
                                    <div v-if="convert_phase_form.method =='leaguecsv'" class="form-group d-flex flex-row">
                                        <label>Without Header</label>
                                        <b-form-checkbox
                                            v-model="convert_phase_form.is_without_header"
                                            style="margin-left: 10px"
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
                                        <b-button variant="success" @click="onClickNextPhaseInConvertPhase(data_config_type.DATA)"
                                            >Bước tiếp theo</b-button
                                        >
                                        <b-button variant="primary" @click="onClickCheckConvertPhase(data_config_type.DATA)"
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
                                        <b-button variant="success" @click="onClickUpdateConfig(data_config_type.DATA)"
                                            >Lưu cấu hình</b-button
                                        >
                                        <b-button variant="primary" @click="onClickCheckRestructurePhase(data_config_type.DATA)"
                                            >Kiểm tra</b-button
                                        >
                                    </div>
                                </b-card>
                            </div>
                        </div>
                    </b-card-text>
                    </b-tab>
                    <!-- CẤU HÌNH HEADER -->
                    <b-tab title="Cấu hình header">
                    <b-card-text>
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
                                        v-for="(table, index) in extract_header_phase_result"
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
                                <div class="form-group d-flex flex-row">
                                    <label>Enable convert header</label>
                                    <b-form-checkbox
                                        v-model="is_convert_header"
                                        style="margin-left: 10px"
                                    />
                                </div>
                                <b-card>
                                    <div class="form-group">
                                        <label for="method">Method</label>
                                        <small class="text-danger">*</small>
                                        <treeselect
                                            :multiple="false"
                                            id="method"
                                            placeholder="Chọn cách thức.."
                                            v-model="extract_header_phase_form.method"
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
                                            v-model="extract_header_phase_form.camelot_flavor"
                                            :options="extract_phase_options.camelot_flavors"
                                            required
                                        />
                                    </div>
                                    <div class="form-group d-flex flex-row">
                                        <label>Merge Pages</label>
                                        <b-form-checkbox
                                            v-model="extract_header_phase_form.is_merge_pages"
                                            style="margin-left: 10px"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Exclude head tables count</label>
                                        <b-form-input
                                            type="number"
                                            v-model="extract_header_phase_form.exclude_head_tables_count"
                                            class="form-number-input"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label>Exclude tail tables count</label>
                                        <b-form-input
                                            type="number"
                                            v-model="extract_header_phase_form.exclude_tail_tables_count"
                                            class="form-number-input"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label>Specify only 1 table</label>
                                        <b-form-input
                                            type="number"
                                            v-model="extract_header_phase_form.specify_table_number"
                                            class="form-number-input"
                                        />
                                    </div>

                                    <div class="form-group d-flex flex-row">
                                        <label>Specify table areas</label>
                                        <b-form-checkbox
                                            v-model="extract_header_phase_form.is_specify_table_area"
                                            style="margin-left: 10px"
                                        />
                                    </div>
                                    <div v-if="extract_header_phase_form.is_specify_table_area" class="form-group">
                                        <label for="manualPattern">Table area information</label>
                                        <small class="text-danger">*</small>
                                        <VueJsonEditor v-model="extract_header_phase_form.table_area_info" />
                                    </div>

                                    <div class="form-group d-flex flex-row">
                                        <label>Specify advanced settings</label>
                                        <b-form-checkbox
                                            v-model="extract_header_phase_form.is_specify_advanced_settings"
                                            style="margin-left: 10px"
                                        />
                                    </div>
                                    <div v-if="extract_header_phase_form.is_specify_advanced_settings" class="form-group">
                                        <label for="advancedSettings">Advanced settings information</label>
                                        <small class="text-danger">*</small>
                                        <VueJsonEditor v-model="extract_header_phase_form.advanced_settings_info" />
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <b-button variant="success" @click="onClickNextPhaseInExtractPhase(data_config_type.HEADER)"
                                            >Bước tiếp theo</b-button
                                        >
                                        <b-button variant="primary" @click="onClickQuicklyCheckExtractOrder(data_config_type.HEADER)"
                                            >Kiểm tra nhanh</b-button>
                                        <b-button variant="primary" @click="onClickCheckExtractPhase(data_config_type.HEADER)"
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
                                        :value="JSON.stringify(convert_header_phase_input)"
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
                                    <VueJsonEditor v-model="convert_header_phase_result" />
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
                                            v-model="convert_header_phase_form.method"
                                            :options="convert_phase_options.methods"
                                            required
                                        />
                                    </div>
                                    <div v-if="convert_header_phase_form.method =='leaguecsv'" class="form-group d-flex flex-row">
                                        <label>Without Header</label>
                                        <b-form-checkbox
                                            v-model="convert_header_phase_form.is_without_header"
                                            style="margin-left: 10px"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label for="manualPattern">Manual Patterns</label>
                                        <small class="text-danger">*</small>
                                        <VueJsonEditor v-model="convert_header_phase_form.manual_patterns" />
                                    </div>
                                    <div class="form-group">
                                        <label for="regexPattern">Regex Pattern</label>
                                        <input
                                            id="regexPattern"
                                            type="text"
                                            v-model="convert_header_phase_form.regex_pattern"
                                            placeholder="Nhập regex.."
                                            class="form-control"
                                        />
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <b-button variant="success" @click="onClickNextPhaseInConvertPhase(data_config_type.HEADER)"
                                            >Bước tiếp theo</b-button
                                        >
                                        <b-button variant="primary" @click="onClickCheckConvertPhase(data_config_type.HEADER)"
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
                                        :value="JSON.stringify(restructure_header_phase_input)"
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
                                    <VueJsonEditor v-model="restructure_header_phase_result" />
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
                                            v-model="restructure_header_phase_form.method"
                                            :options="restructure_header_phase_options.methods"
                                            required
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label for="manualPattern">Structure</label>
                                        <small class="text-danger">*</small>
                                        <VueJsonEditor v-model="restructure_header_phase_form.structure" />
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <b-button variant="success" @click="onClickUpdateConfig(data_config_type.HEADER)"
                                            >Lưu cấu hình</b-button
                                        >
                                        <b-button variant="primary" @click="onClickCheckRestructurePhase(data_config_type.HEADER)"
                                            >Kiểm tra</b-button
                                        >
                                    </div>
                                </b-card>
                            </div>
                        </div>
                    </b-card-text>
                    </b-tab>
                </b-tabs>
            </b-card>
            <b-card>
                <b-form @submit.prevent="onClickCreateConfig">
                    <div class="row">
                        <div class="col-md-4">
                            <b-form-input
                                type="text"
                                v-model="create_config_form.name"
                                placeholder="Nhập tên cấu hình.."
                                required
                            />
                        </div>
                        <div class="col-md-2">
                            <treeselect
                                :multiple="false"
                                id="method2"
                                placeholder="Chọn customer group.."
                                v-model="create_config_form.customer_group_id"
                                :options="customer_group_options"
                                :normalizer="normalizer"
                                required
                            />
                        </div>
                        <div class="col-md-2">
                            <treeselect
                                :multiple="false"
                                id="convert_file_type2"
                                placeholder="Chọn loại file.."
                                v-model="create_config_form.convert_file_type_id"
                                :options="convert_file_type_options"
                                required
                            />
                        </div>
                        <div class="col-md-2">
                            <treeselect
                                :multiple="false"
                                id="active"
                                placeholder="Hoạt động"
                                v-model="create_config_form.active"
                                :options="active_options"
                                required
                            />
                        </div>
                        <div class="col-md-2">
                            <b-button variant="primary" type="submit">Tạo cấu hình</b-button>
                        </div>
                    </div>
                </b-form>
            </b-card>
            <b-card>
                <div class="row">
                    <div class="col-lg-6">
                        <h5 class="text-dark font-weight-bold">
                            <i class="fa fa-list"></i> Danh sách cấu hình
                            <button
                            @click="reloadConfigList()"
                            class="btn btn-secondary  btn-xs ml-1">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </h5>
                    </div>
                    <div class="col-lg-6">
                        <InputFilter @inputFilter="inputFilter"></InputFilter>
                        <!-- Thanh tìm kiếm-->
                    </div>
                    <b-table
                        :items="convert_config_list"
                        :fields="fields"
                        small
                        responsive
                        hover
                        striped
                        head-variant="light"
                        :sticky-header="false"
                        :bordered="true"
                        :current-page="paginate.current_page"
                        :per-page="paginate.per_page"
                        :filter="filter"
                        :busy="loading"
                    >
                        <template #table-busy>
                            <div class="text-center text-secondary my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Loading...</strong>
                            </div>
                        </template>
                        <template #cell(index)="data">
                            {{ (paginate.current_page - 1) * paginate.per_page + data.index + 1 }}
                        </template>
                        <template #cell(customer_group_id)="data">
                            <span>{{ data.item.customer_group.name }}</span>
                        </template>
                        <!-- <template #cell(name)="data">
                            <span>{{ getConfigNameById(data.value) }}</span>
                        </template> -->
                        <template #cell(master_config_group_id)="data">
                            <span>{{ data.item.master_extract_order_config? data.item.master_extract_order_config.name : "" }}</span>
                        </template>
                        <template #cell(is_config_group)="data">
                            <span class="badge bg-success" v-if="data.value == true"><i class="fa fa-check"></i></span>
                        </template>
                        <template #cell(is_master_config_group)="data">
                            <span class="badge bg-success" v-if="data.value == true"><i class="fa fa-check"></i></span>
                        </template>
                        <template #cell(is_slave_config_group)="data">
                            <span class="badge bg-success" v-if="data.value == true"><i class="fa fa-check"></i></span>
                        </template>
                        <template #cell(active)="data">
                            <span class="badge bg-success" v-if="data.value == true">Hoạt động</span>
                            <span class="badge bg-warning" v-if="data.value == false">Ngưng hoạt động</span>
                        </template>
                        <template #cell(action)="data">
                            <div class="text-center">
                            <button @click="showConfigEdit(data.item)" class="btn btn-xs btn-warning">
                                <i class="fas fa-pen mr-1"></i>Sửa
                            </button>
                            <button @click="deleteConfig(data.item.id)" class="btn btn-xs btn-danger">
                                <i class="fas fa-trash-alt mr-1"></i>Xóa
                            </button>
                            </div>
                        </template>
                    </b-table>
                    <PaginateBottom
                        :rows="rows"
                        :paginate="paginate"
                        :pageOptions="pageOptions"
                    ></PaginateBottom>
                    <!-- Thanh phân trang-->
                </div>
            </b-card>
        </div>

        <div class="modal fade" id="importConfigDialog" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                <form
                    @submit.prevent="importOrderConfig"
                >
                    <div class="modal-header">
                        <h5 class="modal-title">Import cấu hình từ file</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <b-form-file
                                ref="jsonFile"
                                placeholder="Chọn file json để import..."
                                drop-placeholder="Drop file here..."
                            ></b-form-file>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary mr-auto" data-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit"
                            class="btn btn-primary">
                            OK
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Edit config dialog -->
        <div class="modal fade" id="convert_config_dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="editConvertConfig">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">
                                <span>Cập nhật cấu hình</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nhóm khách hàng</label><small class="text-danger">*</small>
                                <select v-model="convert_config.customer_group_id"
                                class="form-control"
                                id="customer_group_id"
                                name="customer_group_id"
                                v-bind:class="hasError('customer_group_id') ? 'is-invalid' : ''">
                                    <option v-for="customer in customer_group_options" :key="customer.id" :value="customer.id"> {{
                                    customer.name
                                    }} </option>
                                </select>
                                <span v-if="hasError('customer_group_id')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("customer_group_id") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Tên cấu hình</label
                                ><small class="text-danger font-italic"> (nhập tối đa 255 kí tự)*</small>
                                <input
                                v-model="convert_config.name"
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Nhập tên cấu hình"
                                id="convert_name"
                                name="convert_name"
                                v-bind:class="hasError('convert_name') ? 'is-invalid' : ''"
                                />
                                <span v-if="hasError('convert_name')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("convert_name") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Loại file</label><small class="text-danger">*</small>
                                <select v-model="convert_config.convert_file_type"
                                class="form-control"
                                id="convert_file_type"
                                name="convert_file_type"
                                v-bind:class="hasError('convert_file_type') ? 'is-invalid' : ''">
                                    <option v-for="file_type in convert_file_type_options" :key="file_type.id" :value="file_type.id"> {{
                                    file_type.label
                                    }} </option>
                                </select>
                                <span v-if="hasError('convert_file_type')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("convert_file_type") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Nhóm cấu hình?</label>
                                <select v-model="convert_config.is_config_group"
                                class="form-control"
                                id="is_config_group"
                                name="is_config_group"
                                v-bind:class="hasError('is_config_group') ? 'is-invalid' : ''">
                                    <option value="true">Có</option>
                                    <option value="false">Không</option>
                                </select>
                                <span v-if="hasError('is_config_group')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("is_config_group") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Nhóm chính?</label>
                                <select v-model="convert_config.is_master_config_group"
                                class="form-control"
                                id="is_master_config_group"
                                name="is_master_config_group"
                                v-bind:class="hasError('is_master_config_group') ? 'is-invalid' : ''">
                                    <option value="true">Có</option>
                                    <option value="false">Không</option>
                                </select>
                                <span v-if="hasError('is_master_config_group')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("is_master_config_group") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Nhóm phụ?</label>
                                <select v-model="convert_config.is_slave_config_group"
                                class="form-control"
                                id="is_slave_config_group"
                                name="is_slave_config_group"
                                v-bind:class="hasError('is_slave_config_group') ? 'is-invalid' : ''">
                                    <option value="true">Có</option>
                                    <option value="false">Không</option>
                                </select>
                                <span v-if="hasError('is_slave_config_group')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("is_slave_config_group") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Thuộc nhóm chính nào?</label>
                                <select v-model="convert_config.master_config_group_id"
                                class="form-control"
                                id="master_config_group_id"
                                name="master_config_group_id"
                                v-bind:class="hasError('master_config_group_id') ? 'is-invalid' : ''">
                                    <option v-for="config in convert_config_list.filter(item => item.is_master_config_group && item.customer_group_id == convert_config.customer_group_id)"
                                    :key="config.id" :value="config.id">
                                        {{config.id}} - {{config.name}}
                                    </option>
                                </select>
                                <span v-if="hasError('master_config_group_id')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("master_config_group_id") }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Trạng thái</label><small class="text-danger">*</small>
                                <select v-model="convert_config.active"
                                class="form-control"
                                id="config_active"
                                name="config_active"
                                v-bind:class="hasError('config_active') ? 'is-invalid' : ''">
                                    <option value=true>Hoạt động</option>
                                    <option value=false>Ngưng hoạt động</option>
                                </select>
                                <span v-if="hasError('config_active')" class="invalid-feedback" role="alert">
                                <strong>{{ getError("config_active") }}</strong>
                                </span>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary mr-auto" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit"
                                class="btn btn-primary">
                                OK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH, LOAD_ROOT_OPTIONS } from '@riophae/vue-treeselect';
	import APIHandler, { APIRequest } from '../ApiHandler';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import VueJsonEditor from 'vue-json-editor';
    import InputFilter from "../general/filters/InputFilter.vue";
    import PaginateBottom from "../general/paginations/PaginateBottom.vue";
	export default {
		components: {
			Treeselect,
			VueJsonEditor,
            InputFilter,
            PaginateBottom,
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
                    specify_table_number: 0,
                    is_specify_table_area: false,
                    table_area_info: [],
                    is_specify_advanced_settings: false,
                    advanced_settings_info: null,
				},
                extract_header_phase_form: {
					method: 'camelot',
					camelot_flavor: 'lattice',
					is_merge_pages: true,
					exclude_head_tables_count: 0,
					exclude_tail_tables_count: 0,
                    specify_table_number: 0,
                    is_specify_table_area: false,
                    table_area_info: [],
                    is_specify_advanced_settings: false,
                    advanced_settings_info: null,
				},

				extract_phase_result: [],
                extract_header_phase_result: [],
				extract_phase_options: {
					methods: [{ id: 'camelot', label: 'Camelot' }, { id: 'ai', label: 'AI' }],
					camelot_flavors: [
						{ id: 'stream', label: 'Stream' },
						{ id: 'lattice', label: 'Lattice' },
					],
				},

				is_loading_convert_phase: false,
				convert_phase_input: null,
                convert_header_phase_input: null,
				convert_phase_form: {
					method: 'leaguecsv',
					manual_patterns: [],
					regex_pattern: null,
                    is_without_header: false,
				},
                convert_header_phase_form: {
					method: 'leaguecsv',
					manual_patterns: [],
					regex_pattern: null,
                    is_without_header: false,
				},
				convert_phase_result: null,
                convert_header_phase_result: null,
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
                restructure_header_phase_input: null,
				restructure_phase_form: {
					method: 'arraymappingbyindex',
					structure: {},
				},
                restructure_header_phase_form: {
					method: 'arraymappingbymergeindex',
					structure: {},
				},
				restructure_phase_options: {
					methods: [
						{ id: 'arraymappingbyindex', label: 'Array Mapping By Index' },
						{ id: 'arraymappingbykey', label: 'Array Mapping By Key' },
					],
				},
                restructure_header_phase_options: {
                    methods: [
                        { id: 'arraymappingbymergeindex', label: 'Array Mapping By Merge Index' },
                        { id: 'arraymappingbysearchtext', label: 'Array Mapping By Search Text' },
                    ],
                },
				restructure_phase_result: null,
                restructure_header_phase_result: null,

				customer_group_options: [],
				load_extract_order_config_options: null,

				create_config_form: {
					customer_group_id: null,
					extract_data_config: null,
					convert_table_config: null,
					restructure_data_config: null,
                    extract_header_config: null,
                    convert_table_header_config: null,
                    restructure_header_config: null,
					name: null,
                    is_convert_header: false,
                    convert_file_type_id: null,
                    active: false,
				},

				load_config_form: {
					customer_group_id: null,
					extract_order_id: null,
                    convert_file_type_id: null,
				},
                data_config_type: Object.freeze({
                    DATA: 'data',
                    HEADER: 'header'
                }),
                is_convert_header: false,
                convert_file_type_options: null,
                active_options: [
                    { id: true, label: 'Hoạt động' },
                    { id: false, label: 'Không hoạt động' },
                ],
                loading: false,
                token: "",
                fields: [
                    { key: "index", label: "STT", sortable: true, class: "text-center" },
                    { key: "customer_group_id", label: "Nhóm khách hàng", sortable: true },
                    { key: "name", label: "Tên cấu hình", sortable: true },
                    { key: "convert_file_type", label: "Loại file", sortable: true },
                    { key: "is_config_group", label: "Nhóm cấu hình?", sortable: true, class: "text-center" },
                    { key: "is_master_config_group", label: "Nhóm chính?", sortable: true, class: "text-center" },
                    { key: "is_slave_config_group", label: "Nhóm phụ?", sortable: true, class: "text-center" },
                    { key: "master_config_group_id", label: "Thuộc nhóm chính", sortable: true },
                    { key: "active", label: "Hoạt động", class: "text-center" },
                    { key: "action", label: "Hành động", class: "text-center" },
                ],
                filter: "",
                paginate: {
                    total: 0,
                    per_page: 10,
                    from: 1,
                    to: 0,
                    current_page: 1,
                },
                pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],
                convert_config_list: [],
                convert_config: {
                    id: "",
                    customer_group_id: "",
                    name: "",
                    convert_file_type: "pdf",
                    is_config_group: false,
                    is_master_config_group: false,
                    is_slave_config_group: false,
                    master_config_group_id: "",
                    active: false,
                },
                errors: {},
                page_url_convert_config_list: "/api/ai/config/config-list",
                page_url_convert_config_destroy: "/api/ai/config/config-destroy",
                page_url_convert_config_update: "/api/ai/config/config-update",
			};
		},
		created() {
            this.token = "Bearer " + window.Laravel.access_token;
			this.fetchOptionsData();
            this.fetchConvertConfigList();
		},
		methods: {
            showConfigEdit(config) {
                this.convert_config = {
                    id: config.id,
                    customer_group_id: config.customer_group_id,
                    name: config.name,
                    convert_file_type: config.convert_file_type,
                    is_config_group: config.is_config_group,
                    is_master_config_group: config.is_master_config_group,
                    is_slave_config_group: config.is_slave_config_group,
                    master_config_group_id: config.master_config_group_id,
                    active: config.active,
                    };
                this.convert_file_type_options = [
                    { id: 'pdf', label: 'PDF' },
                    { id: 'excel', label: 'EXCEL' },
                ];
                $("#convert_config_dialog").modal("show");
            },
            async editConvertConfig() {
                try {
                    let page_url = this.page_url_convert_config_update + "/" + this.convert_config.id;
                    const { data } = await this.api_handler.put(
                        page_url,
                        {},
                        JSON.stringify(this.convert_config)
                    );
                    if (!data.errors) {
                        $("#convert_config_dialog").modal("hide");
                        this.resetConvertConfigList();
                        this.reloadConfigList();
                        toastr.success("Thông báo", "Lưu thành công");
                    } else {
                        this.errors = data.errors;
                        toastr.error(this.errors, "Lưu thất bại");
                    }
                } catch (error) {
                    console.log(error);
                    toastr.error(error, "Lỗi");
                }
            },
            deleteConfig(id) {
                this.$bvModal
                    .msgBoxConfirm("Xác nhận xóa?", {
                    title: "",
                    size: "sm",
                    buttonSize: "sm",
                    okVariant: "danger",
                    okTitle: "OK",
                    cancelTitle: "Cancel",
                    footerClass: "p-2",
                    hideHeaderClose: false,
                    centered: true,
                    })
                    .then((value) => {
                        if (value) {
                            let page_url = this.page_url_convert_config_destroy + "/" + id;
                            fetch(page_url, {
                                method: "DELETE",
                                headers: {
                                    Authorization: this.token,
                                    "content-type": "application/json",
                                },
                            })
                            .then((res) => res.json())
                            .then((res) => {
                                if (!res.errors) {
                                    toastr.success("Xóa thành công", "Thông báo");
                                    this.reloadConfigList();
                                } else {
                                    this.errors = res.data.errors;
                                    toastr.error(this.errors, "Xóa thất bại");
                                }
                            })
                            .catch((err) => {
                                console.log(err);
                                toastr.error(err, "Lỗi");
                            });
                        }
                    })
                    .catch((err) => {
                     console.log(err);
                    });
            },
            resetConvertConfigList() {
                this.convert_config = {
                    id: "",
                    customer_group_id: "",
                    name: "",
                    convert_file_type: "pdf",
                    is_config_group: false,
                    is_master_config_group: false,
                    is_slave_config_group: false,
                    master_config_group_id: "",
                    active: false,
                };
            },
            inputFilter(value) {
                this.filter = value;
            },
            reloadConfigList() {
                this.fetchConvertConfigList();
            },
            hasError(fieldName) {
             return fieldName in this.errors;
            },
            getError(fieldName) {
                return this.errors[fieldName][0];
            },
            clearError(event) {
                Vue.delete(this.errors, event.target.name);
            },

            fetchConvertConfigList() {
                this.loading = true;
                var page_url = this.page_url_convert_config_list;
                fetch(page_url, {
                    method: "GET",
                    headers: {
                    Authorization: this.token,
                    "content-type": "application/json",
                    },
                })
                    .then((res) => res.json())
                    .then((res) => {

                    this.convert_config_list = res.data;
                    // console.log("list", this.convert_config_list);
                    this.loading = false;
                    })
                    .catch((err) => {
                    console.log(err);
                    this.loading = false;
                    });
            },
			async fetchOptionsData() {
				const [customer_group_options] = await this.api_handler.handleMultipleRequest([
					new APIRequest('get', '/api/master/customer-groups'),
				]);
				this.customer_group_options = customer_group_options;
			},
			normalizer(node) {
				return {
					id: node.id,
					label: node.name,
				};
			},
			async onClickCheckExtractPhase(data_config_type) {
				try {
					if (this.is_loading_extract_phase) return;
					this.is_loading_extract_phase = true;

                    if (data_config_type === this.data_config_type.DATA) {
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
                                    table_area_info: JSON.stringify(
                                        this.extract_phase_form.table_area_info,
                                    ),
                                    advanced_settings_info: JSON.stringify(
                                        this.extract_phase_form.advanced_settings_info,
                                    ),
                                    convert_file_type: this.load_convert_file_type_id,
                                }),
                            )
                            .finally(() => {
                                this.is_loading_extract_phase = false;
                            });

                        this.extract_phase_result = data;
                    } else {
                        const { data } = await this.api_handler
                            .setHeaders({
                                'Content-Type': 'multipart/form-data',
                            })
                            .post(
                                '/api/ai/config/extract',
                                {},
                                APIHandler.createFormData({
                                    file: this.file,
                                    ...this.extract_header_phase_form,
                                    extract_method: this.extract_header_phase_form.method,
                                    table_area_info: JSON.stringify(
                                        this.extract_header_phase_form.table_area_info,
                                    ),
                                    advanced_settings_info: JSON.stringify(
                                        this.extract_header_phase_form.advanced_settings_info,
                                    ),
                                    convert_file_type: this.load_convert_file_type_id,
                                }),
                            )
                            .finally(() => {
                                this.is_loading_extract_phase = false;
                            });

                        this.extract_header_phase_result = data;
                    }

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			async onClickNextPhaseInExtractPhase(data_config_type) {
                switch (this.load_convert_file_type_id) {
                    case 'pdf':
                        if (data_config_type === this.data_config_type.DATA) {
                            this.convert_phase_input = this.extract_phase_result;
                            this.create_config_form.extract_data_config = this.extract_phase_form;
                        } else {
                            this.convert_header_phase_input = this.extract_header_phase_result;
                            this.create_config_form.extract_header_config = this.extract_header_phase_form;
                        }
                        break;
                    case 'excel':
                        if (data_config_type === this.data_config_type.DATA) {
                            this.restructure_phase_input = this.extract_phase_result;
                            this.create_config_form.extract_data_config = this.extract_phase_form;
                        } else {
                            this.restructure_header_phase_input = this.extract_header_phase_result;
                            this.create_config_form.extract_header_config = this.extract_header_phase_form;
                        }
                        break;

                    default:
                        this.$showMessage('error', 'Lỗi', 'Không tìm thấy loại file');
                        break;
                }
			},

			async onClickCheckConvertPhase(data_config_type) {
				try {
					if (this.is_loading_convert_phase) return;
					this.is_loading_convert_phase = true;
                    if (data_config_type === this.data_config_type.DATA) {
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
                                    is_without_header: this.convert_phase_form.is_without_header,
                                },
                            )
                            .finally(() => {
                                this.is_loading_convert_phase = false;
                            });

                        this.convert_phase_result = data;
                    } else {
                        const { data } = await this.api_handler
                            .post(
                                '/api/ai/config/convert',
                                {},
                                {
                                    raw_table_data: JSON.stringify(this.convert_header_phase_input),
                                    convert_method: this.convert_header_phase_form.method,
                                    manual_patterns: JSON.stringify(
                                        this.convert_header_phase_form.manual_patterns,
                                    ),
                                    regex_pattern: this.convert_header_phase_form.regex_pattern,
                                    is_without_header: this.convert_header_phase_form.is_without_header,
                                },
                            )
                            .finally(() => {
                                this.is_loading_convert_phase = false;
                            });

                        this.convert_header_phase_result = data;
                    }

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			async onClickNextPhaseInConvertPhase(data_config_type) {
                if (data_config_type === this.data_config_type.DATA) {
                    this.restructure_phase_input = this.convert_phase_result;
                    this.create_config_form.convert_table_config = this.convert_phase_form;
                } else {
                    this.restructure_header_phase_input = this.convert_header_phase_result;
                    this.create_config_form.convert_table_header_config = this.convert_header_phase_form;
                }
			},

			async onClickCheckRestructurePhase(data_config_type) {
				try {
					if (this.is_loading_restructure_phase) return;
					this.is_loading_restructure_phase = true;

                    if (data_config_type === this.data_config_type.DATA) {
                        const { data } = await this.api_handler
                            .post(
                                '/api/ai/config/restructure',
                                {},
                                {
                                    name: this.create_config_form.name,
                                    table_data: JSON.stringify(this.restructure_phase_input),
                                    restructure_method: this.restructure_phase_form.method,
                                    customer_group_id: this.load_config_form.customer_group_id,
                                    table_area_info: JSON.stringify(this.extract_phase_form.table_area_info),
                                    convert_file_type: this.load_convert_file_type_id,
                                    structure: JSON.stringify(this.restructure_phase_form.structure),
                                },
                            )
                            .finally(() => {
                                this.is_loading_restructure_phase = false;
                            });

                        this.restructure_phase_result = data;
                    } else {
                        const { data } = await this.api_handler
                            .post(
                                '/api/ai/config/restructure',
                                {},
                                {
                                    name: this.create_config_form.name,
                                    table_data: JSON.stringify(this.restructure_header_phase_input),
                                    restructure_method: this.restructure_header_phase_form.method,
                                    customer_group_id: this.load_config_form.customer_group_id,
                                    table_area_info: JSON.stringify(this.extract_phase_form.table_area_info),
                                    convert_file_type: this.load_convert_file_type_id,
                                    structure: JSON.stringify(this.restructure_header_phase_form.structure),
                                },
                            )
                            .finally(() => {
                                this.is_loading_restructure_phase = false;
                            });

                        this.restructure_header_phase_result = data;
                    }

					this.$showMessage('success', 'Gửi yêu cầu xử lý file thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			async onClickCreateConfig() {
                // this.create_config_form.restructure_data_config = this.restructure_phase_form;
                // this.create_config_form.extract_data_config = this.extract_phase_form;
                // this.create_config_form.convert_table_config = this.convert_phase_form;
                // this.create_config_form.restructure_header_config = this.restructure_header_phase_form;
                // this.create_config_form.extract_header_config = this.extract_header_phase_form;
                // this.create_config_form.convert_table_header_config = this.convert_header_phase_form;
                // this.create_config_form.is_convert_header = this.is_convert_header;
                this.create_config_form.restructure_data_config = this.getFormattedRestructData(this.restructure_phase_form);
                this.create_config_form.extract_data_config = this.getFormattedExtractData(this.extract_phase_form);
                this.create_config_form.convert_table_config = this.getFormattedConvertData(this.convert_phase_form);
                this.create_config_form.restructure_header_config = this.getFormattedRestructData(this.restructure_header_phase_form);
                this.create_config_form.extract_header_config = this.getFormattedExtractData(this.extract_header_phase_form);
                this.create_config_form.convert_table_header_config = this.getFormattedConvertData(this.convert_header_phase_form);
                this.create_config_form.is_convert_header = this.is_convert_header;
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
                            extract_header_config: this.create_config_form.extract_header_config,
                            convert_table_header_config: this.create_config_form.convert_table_header_config,
                            restructure_header_config: this.create_config_form.restructure_header_config,
							name: this.create_config_form.name,
                            is_convert_header: this.is_convert_header,
                            convert_file_type: this.create_config_form.convert_file_type_id,
                            active: this.create_config_form.active,
						},
					);
					this.create_config_form = {
						customer_group_id: null,
						extract_data_config: null,
						convert_table_config: null,
						restructure_data_config: null,
                        extract_header_config: null,
                        convert_table_header_config: null,
                        restructure_header_config: null,
						name: null,
                        is_convert_header: false,
                        convert_file_type_id: null,
                        active: false,
					};
					this.$showMessage('success', 'Tạo cấu hình thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},

			async onClickLoadConfig() {
				try {
                    this.resetDataForm();
                    this.create_config_form.customer_group_id =
                        this.load_config_form.customer_group_id;
                    const { data } = await this.api_handler.get(`/api/ai/config/customer-groups`, {
                        customer_group_ids: [this.load_config_form.customer_group_id],
                        extract_order_config_ids: [this.load_config_form.extract_order_id],
                    });
                    let extract_order_config_response = data[0];
                    let extract_response = extract_order_config_response.extract_data_config;
                    this.extract_phase_form = extract_response ? {
                            method: extract_response.method,
                            camelot_flavor: extract_response.camelot_flavor,
                            is_merge_pages: extract_response.is_merge_pages,
                            exclude_head_tables_count: extract_response.exclude_head_tables_count,
                            exclude_tail_tables_count: extract_response.exclude_tail_tables_count,
                            specify_table_number: extract_response.specify_table_number,
                            is_specify_table_area: extract_response.is_specify_table_area,
                            table_area_info: extract_response.table_area_info
                                ? JSON.parse(extract_response.table_area_info)
                                : null,
                            is_specify_advanced_settings: extract_response.is_specify_advanced_settings,
                            advanced_settings_info: extract_response.advanced_settings_info
                                ? JSON.parse(extract_response.advanced_settings_info)
                                : null,
                        } : this.extract_phase_form;

                    let extract_header_response = extract_order_config_response.extract_header_config;
                    this.extract_header_phase_form = extract_header_response ? {
                            method: extract_header_response.method,
                            camelot_flavor: extract_header_response.camelot_flavor,
                            is_merge_pages: extract_header_response.is_merge_pages,
                            exclude_head_tables_count: extract_header_response.exclude_head_tables_count,
                            exclude_tail_tables_count: extract_header_response.exclude_tail_tables_count,
                            specify_table_number: extract_header_response.specify_table_number,
                            is_specify_table_area: extract_header_response.is_specify_table_area,
                            table_area_info: extract_header_response.table_area_info
                                ? JSON.parse(extract_header_response.table_area_info)
                                : null,
                            is_specify_advanced_settings: extract_header_response.is_specify_advanced_settings,
                            advanced_settings_info: extract_header_response.advanced_settings_info
                                ? JSON.parse(extract_header_response.advanced_settings_info)
                                : null,
                        } : this.extract_header_phase_form;

                    let convert_response = extract_order_config_response.convert_table_config;
                    this.convert_phase_form = convert_response ? {
                            method: convert_response.method,
                            manual_patterns: convert_response.manual_patterns
                                ? JSON.parse(convert_response.manual_patterns)
                                : null,
                            regex_pattern: convert_response.regex_pattern,
                            is_without_header: convert_response.is_without_header,
                        } : this.convert_phase_form;

                    let convert_header_response = extract_order_config_response.convert_table_header_config;
                    this.convert_header_phase_form = convert_header_response ? {
                            method: convert_header_response.method,
                            manual_patterns: convert_header_response.manual_patterns
                                ? JSON.parse(convert_header_response.manual_patterns)
                                : null,
                            regex_pattern: convert_header_response.regex_pattern,
                            is_without_header: convert_header_response.is_without_header,
                        } : this.convert_header_phase_form;

                    let restructure_response =
                        extract_order_config_response.restructure_data_config;
                    this.restructure_phase_form = restructure_response ? {
                            method: restructure_response.method,
                            structure: restructure_response.structure
                                ? JSON.parse(restructure_response.structure)
                                : null,
                        } : this.restructure_phase_form;

                    let restructure_header_response =
                        extract_order_config_response.restructure_header_config;
                    this.restructure_header_phase_form = restructure_header_response ? {
                            method: restructure_header_response.method,
                            structure: restructure_header_response.structure
                                ? JSON.parse(restructure_header_response.structure)
                                : null,
                        } : this.restructure_header_phase_form;
                    this.is_convert_header = extract_order_config_response.is_convert_header;

					this.$showMessage('success', 'Tải cấu hình thành công');
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response?.data.message);
				}
			},

			async onClickUpdateConfig(data_config_type) {
				try {
                    let extract_data_form = this.getFormattedExtractData(this.extract_phase_form);
                    let convert_data_form = this.getFormattedConvertData(this.convert_phase_form);
                    let restructure_data_form = this.getFormattedRestructData(this.restructure_phase_form);

                    let extract_header_form = this.getFormattedExtractData(this.extract_header_phase_form);
                    let convert_header_form = this.getFormattedConvertData(this.convert_header_phase_form);
                    let restructure_header_form = this.getFormattedRestructData(this.restructure_header_phase_form);

                    let update_config_form = (data_config_type == this.data_config_type.DATA) ?
                    {
                        customer_group_id: this.load_config_form.customer_group_id,
                        extract_data_config: extract_data_form,
                        convert_table_config: convert_data_form,
                        restructure_data_config: restructure_data_form,
                        data_config_type: data_config_type,
                    } :
                    {
                        customer_group_id: this.load_config_form.customer_group_id,
                        extract_header_config: extract_header_form,
                        convert_table_header_config: convert_header_form,
                        restructure_header_config: restructure_header_form,
                        data_config_type: data_config_type,
                        is_convert_header: this.is_convert_header,
                    };

					const { data } = await this.api_handler.put(
						`/api/ai/config/${this.load_config_form.extract_order_id}`,
                        {},
                        update_config_form
					);
					this.create_config_form = {
						customer_group_id: null,
						extract_data_config: null,
						convert_table_config: null,
						restructure_data_config: null,
                        extract_header_config: null,
                        convert_table_header_config: null,
                        restructure_header_config: null,
						name: null,
                        is_convert_header: false,
                        convert_file_type_id: null,
					};
                    let msg = (data_config_type == this.data_config_type.DATA) ?
                        'Cập nhật cấu hình data thành công' : 'Cập nhật cấu hình header thành công';
					this.$showMessage('success', msg);
				} catch (error) {
					console.log(error);
					this.$showMessage('error', 'Lỗi', error.response.data.message);
				}
			},
            getFormattedExtractData(extract_form) {
                let formatted_data = extract_form ? {
                    method: extract_form.method,
                    camelot_flavor: extract_form.camelot_flavor,
                    is_merge_pages: extract_form.is_merge_pages,
                    exclude_head_tables_count: extract_form.exclude_head_tables_count,
                    exclude_tail_tables_count: extract_form.exclude_tail_tables_count,
                    specify_table_number: extract_form.specify_table_number,
                    is_specify_table_area: extract_form.is_specify_table_area,
                    table_area_info: JSON.stringify(extract_form.table_area_info),
                    is_specify_advanced_settings: extract_form.is_specify_advanced_settings,
                    advanced_settings_info: JSON.stringify(extract_form.advanced_settings_info),
                } : extract_form;
                return formatted_data;
            },
            getFormattedConvertData(convert_form) {
                let formatted_data = convert_form ? {
                    method: convert_form.method,
                    manual_patterns: JSON.stringify(convert_form.manual_patterns),
                    regex_pattern: convert_form.regex_pattern,
                    is_without_header: convert_form.is_without_header,
                } : convert_form;
                return formatted_data;
            },
            getFormattedRestructData(restructure_form) {
                let formatted_data = restructure_form ? {
                    method: restructure_form.method,
                    structure: JSON.stringify(restructure_form.structure),
                } : restructure_form;
                return formatted_data;
            },
            resetDataForm() {
                this.file =  null;
                this.extract_phase_form = {
                    method: 'camelot',
                    camelot_flavor: 'lattice',
                    is_merge_pages: true,
                    exclude_head_tables_count: 0,
                    exclude_tail_tables_count: 0,
                    specify_table_number: 0,
                    is_specify_table_area: false,
                    table_area_info: [],
                    is_specify_advanced_settings: false,
                    advanced_settings_info: null,
                };
                this.extract_header_phase_form = {
                    method: 'camelot',
                    camelot_flavor: 'lattice',
                    is_merge_pages: true,
                    exclude_head_tables_count: 0,
                    exclude_tail_tables_count: 0,
                    specify_table_number: 0,
                    is_specify_table_area: false,
                    table_area_info: [],
                    is_specify_advanced_settings: false,
                    advanced_settings_info: null,
                };

                this.extract_phase_result = [];
                this.extract_header_phase_result = [];
                this.extract_phase_options = {
                    methods: [{ id: 'camelot', label: 'Camelot' }, { id: 'ai', label: 'AI' }],
                    camelot_flavors: [
                        { id: 'stream', label: 'Stream' },
                        { id: 'lattice', label: 'Lattice' },
                    ]
                };

                this.convert_phase_input = null;
                this.convert_header_phase_input = null;
                this.convert_phase_form = {
                    method: 'leaguecsv',
                    manual_patterns: [],
                    regex_pattern: null,
                    is_without_header: false
                };
                this.convert_header_phase_form = {
                    method: 'leaguecsv',
                    manual_patterns: [],
                    regex_pattern: null,
                    is_without_header: false
                };
                this.convert_phase_result = null;
                this.convert_header_phase_result = null;

                this.restructure_phase_input = null;
                this.restructure_header_phase_input = null;
                this.restructure_phase_form = {
                    method: 'arraymappingbyindex',
                    structure: {}
                };
                this.restructure_header_phase_form = {
                    method: 'arraymappingbymergeindex',
                    structure: {}
                };

                this.restructure_phase_result = null;
                this.restructure_header_phase_result = null;

                this.create_config_form = {
                    customer_group_id: null,
                    extract_data_config: null,
                    convert_table_config: null,
                    restructure_data_config: null,
                    extract_header_config: null,
                    convert_table_header_config: null,
                    restructure_header_config: null,
                    name: null,
                    convert_file_type_id: null
                };
                this.is_convert_header = false;
                this.convert_file_type_options = [
                    { id: 'pdf', label: 'PDF' },
                    { id: 'excel', label: 'EXCEL' },
                ];
            },
            exportOrderConfig() {
                try {
                    let config = {
                        extract_data_config: this.extract_phase_form,
                        convert_table_config: this.convert_phase_form,
                        restructure_data_config: this.restructure_phase_form,
                        extract_header_config: this.extract_header_phase_form,
                        convert_table_header_config: this.convert_header_phase_form,
                        restructure_header_config: this.restructure_header_phase_form,
                        is_convert_header: this.is_convert_header,
                    };
                    let data_str = JSON.stringify(config);
                    let blob = new Blob([data_str], {type: 'application/json'});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;

                    let selecting_config_name = this.load_config_form.extract_order_id ? this.load_extract_order_config_options.find(
                        (extract_order_config) => {
                            return extract_order_config.value == this.load_config_form.extract_order_id;
                        },
                    )?.text : null;
                    let export_file_name = selecting_config_name ? selecting_config_name : 'order-config'
                        + '.json';

                    link.setAttribute('download', export_file_name);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    this.$showMessage('success', 'Export cấu hình thành công');
                } catch (error) {
                    this.$showMessage('error', 'Lỗi', error);
                }

            },
            showImportOrderConfig() {
                $('#importConfigDialog').modal('show');
            },
            importOrderConfig() {
                try {
                    let file = this.$refs.jsonFile.files[0];
                    let reader = new FileReader();
                    reader.readAsText(file);
                    reader.onload = () => {
                        try {
                            let data = JSON.parse(reader.result);
                            this.extract_phase_form = data.extract_data_config;
                            this.convert_phase_form = data.convert_table_config;
                            this.restructure_phase_form = data.restructure_data_config;
                            this.extract_header_phase_form = data.extract_header_config;
                            this.convert_header_phase_form = data.convert_table_header_config;
                            this.restructure_header_phase_form = data.restructure_header_config;
                            this.is_convert_header = data.is_convert_header;
                            $('#importConfigDialog').modal('hide');
                            this.$showMessage('success', 'Import cấu hình thành công');
                            this.onClickUpdateConfig(this.data_config_type.DATA);
                            this.onClickUpdateConfig(this.data_config_type.HEADER);
                        } catch (error) {
                            this.$showMessage('error', 'Lỗi', error);
                        }

                    };

                } catch (error) {
                    this.$showMessage('error', 'Lỗi', error);
                }
            },
            async onClickQuicklyCheckExtractOrder(data_config_type) {
                switch (this.load_convert_file_type_id) {
                    case 'pdf':
                        if (data_config_type === this.data_config_type.DATA) {
                            await this.onClickCheckExtractPhase(this.data_config_type.DATA);
                            await this.onClickNextPhaseInExtractPhase(this.data_config_type.DATA);
                            await this.onClickCheckConvertPhase(this.data_config_type.DATA);
                            await this.onClickNextPhaseInConvertPhase(this.data_config_type.DATA);
                            await this.onClickCheckRestructurePhase(this.data_config_type.DATA);
                        } else {
                            await this.onClickCheckExtractPhase(this.data_config_type.HEADER);
                            await this.onClickNextPhaseInExtractPhase(this.data_config_type.HEADER);
                            await this.onClickCheckConvertPhase(this.data_config_type.HEADER);
                            await this.onClickNextPhaseInConvertPhase(this.data_config_type.HEADER);
                            await this.onClickCheckRestructurePhase(this.data_config_type.HEADER);
                        }
                        break;
                    case 'excel':
                        if (data_config_type === this.data_config_type.DATA) {
                            await this.onClickCheckExtractPhase(this.data_config_type.DATA);
                            await this.onClickNextPhaseInExtractPhase(this.data_config_type.DATA);
                            await this.onClickCheckRestructurePhase(this.data_config_type.DATA);
                        } else {
                            await this.onClickCheckExtractPhase(this.data_config_type.HEADER);
                            await this.onClickNextPhaseInExtractPhase(this.data_config_type.HEADER);
                            await this.onClickCheckRestructurePhase(this.data_config_type.HEADER);
                        }
                        break;

                    default:
                        this.$showMessage('error', 'Lỗi', 'Chưa chọn loại file');
                        break;
                }
            },
		},

		watch: {
			load_customer_group_id() {
                this.convert_file_type_options = [
                    { id: 'pdf', label: 'PDF' },
                    { id: 'excel', label: 'EXCEL' },
                ];
                this.load_config_form.convert_file_type_id = null;
			},
            load_convert_file_type_id() {
                this.load_extract_order_config_options = [];
                let extract_order_configs = this.customer_group_options.find(
                    (customer_group) => {
                        return customer_group.id == this.load_customer_group_id;
                    },
                )?.admin_extract_order_configs;

                let load_extract_order_config_options = extract_order_configs.filter(
                    (extract_order_config) => {
                        return extract_order_config.convert_file_type == this.load_convert_file_type_id;
                    },
                );
                this.load_extract_order_config_options = load_extract_order_config_options
                    ? load_extract_order_config_options.map((extract_order_config) => {
                            return {
                                value: extract_order_config.id,
                                text: extract_order_config.name,
                            };
                        })
                    : [];
            },
        },
        computed: {
            load_customer_group_id() {
                return this.load_config_form.customer_group_id;
            },
            load_convert_file_type_id() {
                return this.load_config_form.convert_file_type_id;
            },
            rows() {
                return this.convert_config_list.length;
            },
        },
    };
</script>

<style >
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
		padding: 5px 10px 5px 10px;
		/* background-color: #fff; */
		/* border-radius: 10px; */
		border: solid 1px #999;
	}

	.convert-phase-result {
		height: 80vh;
		overflow-y: scroll;
		padding: 5px 10px 5px 10px;
		/* background-color: #fff; */
		/* border-radius: 10px; */
		border: solid 1px #999;
	}

	.form-check-input {
		margin-left: 30px;
	}
	.form-number-input {
		border: 1px solid #dbd5d5;
	}
</style>
