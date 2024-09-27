<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesHistoryDelete" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl m-1">
                <div class="modal-content">
                    <div class="modal-header p-3 bg-gradient-light">
                        <h5 class="modal-title font-weight-bold text-uppercase "
                            id="exampleModalLabel">Đã xóa gần đây
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div ref="table"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm text-xs px-2" data-dismiss="modal">Đóng</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        orders_delete: { type: Array, default: () => [] },
        columns: { type: Array, default: () => [] },
        update_status_function: { type: Object, default: () => { } },

    },
    data() {
        return {
            table: null,
            window_width: 0,
            window_height: 0,
        }
    },
    async mounted() {
        await this.loadTable();

     
        this.table.on("dataSorted", (sorters, rows) => {
            this.$emit('sortOrdersHistory', sorters);
        });

        window.addEventListener('resize', this.updateWindowDimensions);

    },
    watch: {
        'update_status_function.order_history_delete': {
            handler: function (newVal, oldVal) {
                if (newVal) {
                    this.table.clearData();
                    this.updateWindowDimensions();
                    this.table.setColumns(this.filterColumn());
                    this.table.setData(this.orders_delete);
                }
            },
            deep: true,
        },
    },
    methods: {
        async updateWindowDimensions() {
            this.window_width = window.innerWidth;
            this.window_height = window.innerHeight;
            if (this.table) {
                await this.table.setHeight(this.window_height - 200);
            }
        },
        emitDeleteOrdersHistory() {
            this.$emit('deleteOrdersHistory');
        },
        async loadTable() {
            const Tabulator = this.$Tabulator; 
            this.table = new Tabulator(this.$refs.table, {
                resizableRows: true,
                debugInvalidComponentFuncs: false,
                rowHeader: {
                    frozen: true,
                    width: 20,
                    hozAlign: "center",
                    formatter: "order",
                    cssClass: "range-header-col",
                    field: "order",
                },
                rowContextMenu: this.rowMenu(),
                rowFormatter: (row) => {
                    const data = row.getData();
                    if (data.theme_color) {
                        const keys = Object.keys(data.theme_color.background);
                        keys.forEach(key => {
                            if (row.getCell(key)) {
                                row.getCell(key).getElement().style.backgroundColor = data.theme_color.background[key];
                                row.getCell(key).getElement().style.color = data.theme_color.text[key];
                            }
                        });
                    }
                    if (data.extra_offer == 'X') {
                        row.getCell('barcode').getElement().style.backgroundColor = '#ffc107';
                        row.getCell('barcode').getElement().style.color = '#212529';
                        row.getCell('sku_sap_code').getElement().style.backgroundColor = '#ffc107';
                        row.getCell('sku_sap_code').getElement().style.color = '#212529';
                    }
                    if (data.promotion_category == 'X') {
                        row.getCell('barcode').getElement().style.backgroundColor = 'rgb(0, 123, 255)';
                        row.getCell('barcode').getElement().style.color = '#212529';
                        row.getCell('sku_sap_code').getElement().style.backgroundColor = 'rgb(0, 123, 255)';
                        row.getCell('sku_sap_code').getElement().style.color = '#212529';
                    }
                    // hiển thị dữ liệu của cột 'sap_so_number' và 'promotive' trong cùng một ô
                    const cell_sap_so_number = row.getCell('sap_so_number');
                    const cell_so_sap_note = row.getCell('so_sap_note');
                    if (cell_sap_so_number) {
                        const value1 = data.sap_so_number == null ? '' : data.sap_so_number;
                        const value2 = data.promotive == null ? '' : data.promotive;
                        // Kết hợp hai giá trị và cập nhật nội dung của ô
                        cell_sap_so_number.getElement().innerHTML = `<span>${value1}</span><span>${value2}</span>`;
                    }
                    if (cell_so_sap_note) {
                        const value1 = data.so_sap_note == null ? '' : data.so_sap_note;
                        const value2 = data.promotive == null ? '' : data.promotive;
                        cell_so_sap_note.getElement().innerHTML = `<span>${value1}</span><span>${value2}</span>`;
                    }

                },
            });
        },
        rowMenu() {
            let rowMenu = [
                {
                    label: "<i class='fas fa-undo text-black-50 mr-1'></i> Phục hồi",
                    action: (e, row) => {
                        this.$emit('restoreOrder', row.getData(), row.getPosition());
                        console.log('restoreOrder',row.getData(), row.getPosition());
                        // this.$emit('deleteRow', row.getPosition(), row.getData());
                        // this.$emit('deleteRow', this.table.getRanges().map(r => r.getRows().map(row => row.getPosition())));
                    }
                },


            ];
            return rowMenu;
        },
        filterColumn() {
            if (!this.columns) {
                console.error('columns or material_category_types is undefined');
                return [];
            }
            return this.columns.map(column => {
                return {
                    title: column.title,
                    field: column.field,
                    headerSort: false,
                    width: column.width,
                    // editor: column.field == "promotive" ? "list" : "input",
                    visible: column.visible,
                    formatter: (column.field == "sap_so_number" || column.field == "so_sap_note") ? (cell, formatterParams, onRendered) => {
                        let value = cell.getValue(); // Giá trị của cột 'ma_sap'
                        let promotive = cell.getRow().getData().promotive; // Giá trị của cột 'note'
                        cell.getRow().getData().promotive_name = promotive;
                        cell.getRow().getData().note1 = promotive;
                        cell.getRow().getData().is_promotive = promotive !== '' ? true : false

                        let value_promotive = promotive ? `${promotive}` : '';
                        let value_new = value == null ? '' : value;
                        return `${value_new}${value_promotive}`;
                    } : '',

                };
            });
        },
    }
}
</script>
<style lang="scss" scoped>
::v-deep .b-simple-sticky-action {
    position: sticky;
    left: 0;
    cursor: pointer;

    &:hover {
        background-color: #f8f9fa;
    }

    // z-index: 1;
}

.custom-font-size {
    font-size: 0.8em !important;
}

::v-deep .tabulator-header {
    font-size: 0.8em !important;
    /* Kích thước văn bản nhỏ hơn */
}


::v-deep .tabulator-cell {
    font-size: 0.8em !important;
    /* Kích thước văn bản nhỏ hơn */
}
</style>