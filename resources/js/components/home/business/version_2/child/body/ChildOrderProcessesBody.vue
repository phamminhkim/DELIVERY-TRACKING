<template>
    <div>
        <!-- <div class="text-right mr-1">
            <button class="btn btn-sm btn-success px-3"><i class="fas fa-save mr-2"></i>Lưu</button>
        </div> -->
        <!-- <ChildOrderProcessesSearchBody @inputSearch="handleEmittedInputSearch" /> -->
        <div ref="table"></div>
    </div>
</template>
<script>
import ChildOrderProcessesSearchBody from "../body/ChildOrderProcessesSearchBody.vue";

export default {
    props: {
        columns: { type: Array, default: () => [] },
        filteredOrders: { type: Array, default: () => [] },
        material_category_types: { type: Array, default: () => [] },
    },
    components: {
        ChildOrderProcessesSearchBody,
    },
    data() {
        return {
            table: null,
            tableData: [],
            selectedRangeStart: {},
            selectedRangeEnd: {},
            cell_values: [],
            theme_colors: [
                {
                    name: 'black',
                    color: '#000000',
                },
                {
                    name: 'red',
                    color: '#FF0000',
                },
                {
                    name: 'green',
                    color: '#00FF00',
                },
                {
                    name: 'blue',
                    color: '#0000FF',
                },
                {
                    name: 'yellow',
                    color: '#FFFF00',
                },
                {
                    name: 'gray',
                    color: '#808080',
                },
                {
                    name: 'orange',
                    color: '#FFA500',
                },
                {
                    name: 'purple',
                    color: '#800080',
                },
                {
                    name: 'light-blue',
                    color: '#87CEFA',
                }
            ],
        };
    },
    mounted() {

        this.loadTable();
        this.table.on("rangeChanged", (range) => {
            // this.$emit("emitRangeChanged", range);
            this.emitRangeChanged(range);

        });
        // sự kiện editOrParams ở header
        this.table.on("edit", (cell) => {
            console.log('edit:', cell);
        });
        this.table.on("rowSelectionChanged", (data, rows, selected, deselected) => {
            //rows - array of row components for the currently selected rows in order of selection
            //data - array of data objects for the currently selected rows in order of selection
            //selected - array of row components that were selected in the last action
            //deselected - array of row components that were deselected in the last action
            // console.log('rowSelectionChanged:', data, rows, selected, deselected);
            // this.$emit('rowSelectionChanged', data, rows, selected, deselected);
        });

    },
    watch: {
        filteredOrders: {
            handler: function (newVal, oldVal) {
                this.table.setData(newVal);
            },
            deep: true,
        },
        'filterColumn': {
            handler: function (newVal, oldVal) {
                this.table.setColumns(newVal);
            },
            deep: true,
        }

    },
    methods: {
        hasSignificantChange(newVal, oldVal) {
            // Kiểm tra xem hai mảng có cùng chiều dài không  
            if (newVal.length !== oldVal.length) {
                return true; // Có sự thay đổi về chiều dài  
            }

            // Duyệt qua từng phần tử trong hai mảng  
            for (let i = 0; i < newVal.length; i++) {
                // So sánh từng thuộc tính của mỗi phần tử  
                for (const key in newVal[i]) {
                    if (newVal[i][key] !== oldVal[i][key]) {
                        return true; // Có sự thay đổi về giá trị  
                    }
                }
            }

            // Không có sự thay đổi đáng kể  
            return false;
        },
        headerMenu() {
            var headerMenu = [
                // {
                //     label: "Ẩn cột",
                //     action: function (e, column) {
                //         column.hide();
                //     }
                // },
                {
                    label: "Reset",
                    action: (e, column) => {
                        this.$emit('filterOrder', '', '');
                    }
                },
                {
                    label: "Filter",
                    menu: [
                        {
                            label: "Giá",
                            menu: [
                                {
                                    label: "Chênh lệch",
                                    action: (e, column) => {
                                        this.$emit('filterOrder', 'price_difference', 'difference');
                                    }
                                },
                                {
                                    label: "Ngang nhau",
                                    action: (e, column) => {
                                        this.$emit('filterOrder', 'price_equal', 'difference');
                                    }
                                },
                            ],
                        },
                        {
                            label: "Khuyến mãi",
                            menu: [
                                {
                                    label: "Khuyến mãi",
                                    action: (e, column) => {
                                        this.$emit('filterOrder', 'X', 'extra_offer');
                                    }
                                },
                                {
                                    label: "Combo",
                                    action: (e, column) => {
                                        this.$emit('filterOrder', 'X', 'promotion_category');
                                    }
                                },
                            ],
                        },
                        {
                            label: "Màu",
                            menu: this.theme_colors.map(color => {
                                return {
                                    label: `<div style="background: ${color.color};width: 60px; height: 15px; border: 1px solid gray"></div>`,
                                    action: (e, column) => {
                                        this.$emit('filterOrder', color.color, column.getField(), 'theme_color');
                                    },
                                };
                            }),
                        },
                    ],
                },

            ];
            return headerMenu;
        },
        loadTable() {
            const Tabulator = this.$Tabulator; // Access Tabulator from Vue prototype
            this.table = new Tabulator(this.$refs.table, {
                debugInvalidComponentFuncs: false,
                columnHeaderSortMulti: false,
                // headerFilterLiveFilterDelay: 800,
                data: this.filteredOrders,
                rowContextMenu: this.rowMenu(), //add context menu to rows
                // layout: "fitDataFill",
                // layout: "fitColumns",
                rowHeader: { resizable: false, frozen: true, width: 20, hozAlign: "center", formatter: "rownum", cssClass: "range-header-col", editor: false },
                // selectableRange: true,
                //enable range selection
                selectableRange: 1,
                // selectableRangeColumns: true,
                selectableRangeRows: true,
                selectableRangeClearCells: true,
                scrollToColumnIfVisible: false,
                scrollToColumnPosition: "left",
                // selectableRows:true, // Chọn Row
                editTriggerEvent: "dblclick", // dblClick Chỉnh sửa
                clipboard: true,
                clipboardCopyStyled: false,
                clipboardCopyConfig: {
                    rowHeaders: false, //do not include row headers in clipboard output
                    columnHeaders: false, //do not include column headers in clipboard output
                },
                clipboardCopyRowRange: "range",
                clipboardPasteParser: "range",
                clipboardPasteAction: "range",
                rowFormatter: (row) => {
                    const data = row.getData();
                    if (data.theme_color) {
                        const keys = Object.keys(data.theme_color.background);
                        // row.getElement().style.backgroundColor = "lightcoral";
                        // lấy element tabulator-field="quantity3_sap" để thay đổi màu
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
                },
            });
        },
        handleEmittedInputSearch(value) {
            // switch (value) {
            //     case "":
            //         this.table.clearFilter();
            //         break;
            //     default:
            //         this.table.setFilter([
            //             { field: "sap_so_number", type: "like", value: value },
            //         ]);
            //         // lấy dữ liệu từ bảng đã lọc
            //         break;
            // }
            this.$emit("inputSearch", value);
        },
        emitRangeChanged(range) {
            this.$emit("emitRangeChanged", range);
        },
        rowMenu() {
            var rowMenu = [
                // {
                //     label: "<i class='fas fa-user'></i> Change Name",
                //     action: function (e, row) {
                //         row.update({ name: "Steve Bobberson" });
                //     }
                // },
                {
                    label: "<i class='fas fa-check-square'></i> Chọn dòng",
                    action: (e, row) => {
                        row.select();
                        this.$emit('rowSelectionChanged', row.getData(), true);
                    }
                },
                {
                    label: "<i class='fas fa-check-square'></i> Hủy chọn dòng",
                    action: (e, row) => {
                        row.deselect();
                        this.$emit('rowSelectionChanged', row.getData(), false);
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-plus'></i> Thêm dòng",
                    action: (e, row) => {
                        this.$emit('addRow', row.getPosition());
                    }
                },
                {
                    separator: true,
                },
                // {
                //     label: "<i class='fas fa-cut'></i> Cut",
                //     action: (e, row)  => {
                //         this.$emit('copyRow', row.getPosition(), row.getData());
                //     }
                // },
                {
                    label: "<i class='fas fa-copy'></i> Copy",
                    action: (e, row) => {
                        this.$emit('copyRow', row.getPosition(), row.getData());
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-paste'></i> Paste",
                    action: (e, row) => {
                        this.$emit('pasteRow', row.getPosition());
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-clone'></i> Duplicate",
                    action: (e, row) => {
                        this.$emit('duplicateRow', row.getPosition(), row.getData());
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-trash'></i> Xóa dòng",
                    action: (e, row) => {
                        this.$emit('deleteRow', row.getPosition());
                        row.delete();
                    }
                },
                // {
                //     label: "Admin Functions",
                //     menu: [

                //         {
                //             label: "<i class='fas fa-ban'></i> Disabled Option",
                //             disabled: true,
                //         },
                //     ]
                // }
            ];
            return rowMenu;
        },
        headerMenuV2() {
            var menu = [];
            var columns = this.columns;
            for (let column of columns) {

                //create checkbox element using font awesome icons
                let icon = document.createElement("i");
                icon.classList.add("fas");
                icon.classList.add(column.isVisible() ? "fa-check-square" : "fa-square");

                //build label
                let label = document.createElement("span");
                let title = document.createElement("span");

                title.textContent = " " + column.getDefinition().title;

                label.appendChild(icon);
                label.appendChild(title);

                //create menu item
                menu.push({
                    label: label,
                    action: function (e) {
                        //prevent menu closing
                        e.stopPropagation();

                        //toggle current column visibility
                        column.toggle();

                        //change menu item icon
                        if (column.isVisible()) {
                            icon.classList.remove("fa-square");
                            icon.classList.add("fa-check-square");
                        } else {
                            icon.classList.remove("fa-check-square");
                            icon.classList.add("fa-square");
                        }
                    }
                });
            }
            return menu;
        }
    },
    computed: {
        filterColumn() {
            if (!this.columns || !this.material_category_types) {
                console.error('columns or material_category_types is undefined');
                return [];
            }
            return this.columns.map(column => {
                return {
                    title: column.title,
                    field: column.field,
                    headerSort: false,
                    editor: column.field == "promotive" ? "list" : "textarea",
                    editorParams: column.field == "promotive" ? {
                        values: this.material_category_types.map(item => item.name)
                    } : {},
                    headerMenu: this.headerMenu(),
                    // headerMenu: this.headerMenuV2(),
                    cellEdited: (cell) => {
                        // cell - the CellComponent for the edited cell
                        console.log('Value selected:', cell.getValue());
                        console.log('Row data:', cell.getRow().getData());
                        console.log('Column data:', cell.getColumn().getField());
                        console.log('position:', cell.getRow().getPosition());
                    },
                    formatter: column.field == "sap_so_number" ? (cell, formatterParams, onRendered) => {
                        let ma_sap = cell.getValue(); // Giá trị của cột 'ma_sap'
                        let promotive = cell.getRow().getData().promotive; // Giá trị của cột 'note'
                        cell.getRow().getData().promotive_name = promotive;
                        cell.getRow().getData().note1 = promotive;
                        cell.getRow().getData().is_promotive = true;


                        let value_promotive = promotive ? `${promotive}` : '';
                        return `${ma_sap}${value_promotive}`;
                    } : undefined,

                };
            });
        }
    }
};
</script>
<style lang="scss" scoped>
.tabulator-page {
    background-color: aqua !important;
}

::v-deep .tabulator-header {
    font-size: 0.8em !important;
    /* Kích thước văn bản nhỏ hơn */
}


::v-deep .tabulator-cell {
    font-size: 0.8em !important;
    /* Kích thước văn bản nhỏ hơn */
}

::v-deep .tabulator-row {
    /* Kích thước văn bản nhỏ hơn */
    min-height: 0.2em !important;
}

::v-deep .tabulator-row-resize-guide {
    /* Kích thước văn bản nhỏ hơn */
    min-height: 0.2em !important;
}

::v-deep .tabulator-range-selected {
    background-color: lightgray !important;
}

::v-deep .tabulator-range-active {
    // background-color: lightgray !important;
    border: 1px solid rgb(251, 255, 0) !important;
}

::v-deep .highlighted {
    background-color: yellow !important;
    /* Change to any color you prefer */
}

.highlighted {
    background-color: yellow !important;
    /* Change to any color you prefer */
}

::v-deep .tabulator-selected {
    background-color: rgb(44, 238, 119) !important;
    font-weight: bold !important;
    font-style: italic !important;
}
</style>