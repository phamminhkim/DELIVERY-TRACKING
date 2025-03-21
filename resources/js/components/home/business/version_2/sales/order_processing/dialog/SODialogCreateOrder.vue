<template>
    <div class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo đơn hàng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <h3>Nhập đơn hàng</h3>

                        <!-- Chọn sản phẩm -->
                        <label>Chọn sản phẩm:</label>
                        <treeselect v-model="selectedProduct" :options="products" placeholder="Chọn sản phẩm"
                            @input="addProduct" />

                        <!-- Chọn khách hàng -->
                        <label>Chọn khách hàng:</label>
                        <treeselect v-model="selectedCustomers" :options="customerList" :multiple="true"
                            placeholder="Chọn khách hàng" />

                        <b-table :items="orders" :fields="tableFields" responsive>
                            <!-- Cột Sản phẩm -->
                            <template v-slot:cell(product)="data">
                                {{ getProductName(data.item.productId) }}
                            </template>

                            <!-- Cột Kho hàng -->
                            <template v-slot:cell(stock)="data">
                                {{ getStock(data.item.productId) }}
                            </template>

                            <!-- Cột Số lượng (Từng khách hàng) -->
                            <template v-slot:cell(quantities)="data">
                                <div v-for="(customer, index) in selectedCustomers" :key="customer.id">
                                    <label>{{ customer.label }}:</label>
                                    <input type="number" v-model="data.item.quantities[customer.id]" min="0"
                                        class="form-control form-control-sm" />
                                </div>
                            </template>

                            <!-- Cột Hành động -->
                            <template v-slot:cell(actions)="data">
                                <button class="btn btn-danger btn-sm" @click="removeProduct(data.index)">Xóa</button>
                            </template>
                        </b-table>

                        <button class="btn btn-primary" @click="submitOrder">Tạo đơn hàng</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";

export default {
    components: { Treeselect     },
    data() {
        return {
            selectedProduct: null,
            selectedCustomers: [],
            orders: [],
            products: [
                { id: "400026", label: "Bút chì gỗ Bizner BIZ-P02" },
                { id: "400027", label: "Bút sáp màu (10 màu)" },
                { id: "400138", label: "Thước thẳng 30cm SR-03" },
                { id: "400056", label: "Bút lông mỹ thuật AM-C002 12 màu" },
            ],
            customerList: [
                { id: "c1", label: "Công ty A" },
                { id: "c2", label: "Công ty B" },
                { id: "c3", label: "Công ty C" },
                { id: "c4", label: "Công ty D" },
            ],
            stockData: {
                "400026": { CAY_GO: 30 },
                "400027": { CU_CHI: 10 },
                "400138": { BMT: 10 },
                "400056": { BA_RIA_2: 3 },
            },
        };
    },
    computed: {
        tableFields() {
            return [
                { key: "product", label: "Sản phẩm" },
                { key: "stock", label: "Kho hàng" },
                { key: "quantities", label: "Số lượng theo khách hàng" },
                { key: "actions", label: "Hành động" },
            ];
        },
    },
    methods: {
        addProduct() {
            if (!this.selectedProduct) return;

            const exists = this.orders.some((o) => o.productId === this.selectedProduct);
            if (!exists) {
                this.orders.push({
                    productId: this.selectedProduct,
                    quantities: this.selectedCustomers.reduce((acc, customer) => {
                        acc[customer.id] = 0;
                        return acc;
                    }, {}),
                });
            }
            this.selectedProduct = null;
        },
        removeProduct(index) {
            this.orders.splice(index, 1);
        },
        getProductName(id) {
            const product = this.products.find((p) => p.id === id);
            return product ? product.label : "Không xác định";
        },
        getStock(productId) {
            return this.stockData[productId] ? JSON.stringify(this.stockData[productId]) : "N/A";
        },
        submitOrder() {
            console.log("Đơn hàng:", this.orders);
        },
    },
};
</script>

<style lang="scss" scoped>
/* .form-control-sm {
    width: 60px;
    display: inline-block;
    margin-left: 5px;
    text-align: center;
} */
</style>
