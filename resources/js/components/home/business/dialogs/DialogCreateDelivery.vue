<template>
    <div class="modal fade " id="DialogCreateDelivery" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form @submit.prevent="addItem">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <span>
                                Tạo vận đơn mới
                            </span>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Chọn công ty</label>
                            <small class="text-danger">(*)</small>
                            <treeselect v-model="form.company" :multiple="false" :options="company_options"
                                placeholder="Chọn công ty.." />
                        </div>

                        <div class="form-group">
                            <label>Chọn đơn vị vận chuyển</label>
                            <small class="text-danger">(*)</small>
                            <treeselect v-model="form.delivery_partner" :multiple="false"
                                :options="delivery_partner_options" placeholder="Chọn đơn vị vận chuyển.." />

                        </div>

                        <div class="form-group">
                            <label>Danh sách đơn hàng đã chọn</label>
                            <small class="text-danger">(*)</small>
                            <div class="row">
                                <div class="col-md-5 ml-auto">
                                    <div class="input-group input-group-sm mt-1 mb-1">
                                        <input type="search" class="form-control -control-navbar" v-model="search_pattern"
                                            :placeholder="search_placeholder" aria-label="Search" />
                                        <div class="input-group-append">
                                            <button class="btn btn-default" style="background: #1b1a1a; color: white">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <b-table responsive hover striped :bordered="true" :current-page="pagination.current_page"
                                :per-page="pagination.item_per_page" :filter="search_pattern" :fields="fields"
                                :items="form.orders" :tbody-tr-class="rowClass">

                                <template #cell(warehouse)="data">
                                    <span>{{ data.value.name }}</span>
                                </template>

                                <template #cell(action)="data">
                                    <div class="margin">

                                        <button class="btn btn-xs mr-1" @click="removeOrder(data.index)">
                                            <i class="fas fa-trash text-red bigger-120" title="Delete"></i>
                                        </button>
                                    </div>
                                </template>
                            </b-table>
                            <div class="row">
                                <label class="col-form-label-sm col-md-2" style="text-align: left" for="">Số lượng mỗi
                                    trang:</label>
                                <div class="col-md-2">
                                    <b-form-select size="sm" v-model="pagination.item_per_page"
                                        :options="pagination.page_options">
                                    </b-form-select>
                                </div>
                                <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
                                <div class="col-md-3">
                                    <b-pagination v-model="pagination.current_page" :total-rows="rows"
                                        :per-page="pagination.item_per_page" size="sm" class="ml-1"></b-pagination>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="submit" title="Submit" class="btn btn-primary">
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
import Vue from 'vue';
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";
import APIHandler, { APIRequest } from "../../ApiHandler";

export default {
    name: 'DialogCreateDelivery',
    components: {
        Vue,
        Treeselect
    },
    props: {
        order_ids: Array
    },
    data() {
        return {
            api_handler: new APIHandler(window.Laravel.access_token),

            form: {
                company: null,
                delivery_partner: null,
                orders: []
            },
            company_options: [],
            delivery_partner_options: [],

            pagination: {
                item_per_page: 10,
                current_page: 1,
                page_options: [
                    10,
                    50,
                    100,
                    500,
                    { value: this.rows, text: "All" },
                ],
            },
            search_pattern: "",
            search_placeholder: "Tìm kiếm đơn hàng đã chọn..",

            fields: [
                {
                    key: "warehouse",
                    label: "Kho hàng",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "sap_so_number",
                    label: "SO",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "sap_do_number",
                    label: "DO",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
                {
                    key: "action",
                    label: "Hành động",
                    sortable: true,
                    class: "text-nowrap text-center",
                },
            ],

        }
    },
    created() {
        this.fetchMasterData();
    },
    watch: {
        order_ids: async function (new_order_ids) {
            const { data } = await this.api_handler.get('/api/admin/orders',
                {
                    ids: new_order_ids.length === 0 ? [null] : new_order_ids
                }
            );
            this.form.orders = data;
        }
    },
    methods: {
        async fetchMasterData() {
            try {
                const [
                    companies,
                    delivery_partners

                ] = await this.api_handler.handleMultipleRequest([
                    new APIRequest('get', '/api/master/companies'),
                    new APIRequest('get', '/api/master/delivery-partners')
                ]);

                this.company_options = [
                    ...companies.map(company => {
                        return {
                            id: company.code,
                            label: `(${company.code}) ${company.name}`
                        }
                    })
                ]

                this.delivery_partner_options = [
                    ...delivery_partners.map(delivery_partner => {
                        return {
                            id: delivery_partner.code,
                            label: `(${delivery_partner.code}) ${delivery_partner.name}`
                        }
                    })
                ]
            }
            catch (error) {
                console.log(error);
            }
        },
        rowClass(item, type) {
            if (!item || type !== "row") return;
            if (item.status === "awesome") return "table-success";
        },
        removeOrder(index) {
            this.form.orders.splice(index, 1);
        },
        addOrder(order) {
            this.form.orders.push(order);
        }
    },
    computed: {
        rows() {
            return this.form.orders.length;
        },
    },
}
</script>