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
        update_status_function: { type: Object, default: () => { } },
        position_order: { type: Object, default: () => { } },
        range_items: { type: Array, default: () => [] },
        hidden_columns: { type: Array, default: () => [] },
        range: { type: Object, default: () => { } },
        update_column: { type: Number, default: 0 },
        item_filters: { type: Array, default: () => [] },
        item_change_checked: { type: Array, default: () => [] },
        item_filter_backgrounds: { type: Array, default: () => [] },
        item_filter_texts: { type: Array, default: () => [] },
        selected_indexs: { type: Array, default: () => [] }
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
            query: "",
            theme_filter_colors: [
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
            theme_color_default: {
                name: 'none',
                color: '',
            },
            theme_colors: [
                {
                    name: 'black',
                    color: '#000000',
                },
                {
                    name: 'white',
                    color: '#FFFFFF',
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
            window_width: 0,
            window_height: 0,
            range_table: {
                indexs: [],
                items: [],
            },
            column: '',
            input_row: -1,
            item_selecteds: [],
            is_column_filter: {
                customer_name: false,
                sap_so_number: false,
                so_sap_note: false,
                barcode: false,
                sku_sap_code: false,
                sku_sap_name: false,
                quantity3_sap: false,
                sku_sap_unit: false,
                promotive: false,
                note: false,
                customer_code: false,
                customer_sku_code: false,
                customer_sku_name: false,
                customer_sku_unit: false,
                po: false,
                quantity1_po: false,
                promotive_name: false,
                inventory_quantity: false,
                quantity2_po: false,
                variant_quantity: false,
                price_po: false,
                company_price: false,
                amount_po: false,
                compliance: false,
                is_compliant: false,
                note1: false,
                level2: false,
                level3: false,
                level4: false,
                po_delivery_date: false,
                po_number: false,
                extra_offer: false,
            },
            selected_all: false,
            data_filters: [],
            colorsToFilter: [],
            is_editing: false,

        };
    },
    created() {
        this.tableData = this.filteredOrders;

    },
    async mounted() {
        await this.loadTable();

        // this.table.on("rangeAdded", function (range) {
        //     //range - range component for the selected range
        //     console.log('rangeAdded:', range);
        // });
        this.table.on("rangeChanged", (range) => {
            this.emitRangeChanged(range);
            this.$emit("emitGetRangesData", this.table.getRangesData(), this.table.getRanges().map(r => r.getRows().map(row => row.getPosition())));
        });
        this.table.on("rangeRemoved", (range) => {
            // this.$emit("emitRangeChanged", range);
            this.$emit("emitRangeRemoved", range);

        });
        this.table.on("cellEdited", (cell) => {
            this.$emit('cellEdited', cell);
        });
        this.table.on("cellEditing", (cell) => {
            console.log('Đang chỉnh sửa ô:', cell.getValue(), cell.isEdited());
            this.is_editing = true;
        });
        this.table.on("cellEditCancelled", (cell) => {
            console.log('cellEditCancelled:', cell.getValue());
            this.is_editing = false;
            // this.setSelectableRange();



        });

        // sự kiện khi clipboardPasted
        this.table.on("clipboardPasted", (clipboard, rowData, rows) => {
            this.$emit('clipboardPasted', rows);
        });
        this.table.on("columnResized", (column) => {
            this.$emit('columnResized', column);
        });
        // sự kiện movableColumns
        this.table.on("columnMoved", (column) => {
            this.$emit('columnMoved', this.table.getColumns().map(column => column.getField()));
            // lấy toàn bộ column trong bảng
            // console.log('this.table.getColumns():', this.table.getColumns().map(column => column.getField(), column.getDefinition()));
        });

        this.table.on("headerClick", (e, column) => {
            //  column.getTable().getRows().map(row => row.getPosition()));
            this.$emit('headerClick', column, this.table.getRanges());

        });

        this.table.on("popupOpened", (component) => {
            this.$emit('popupOpened', component.getField());
            this.column = component.getField();

            this.resetQuery();
            this.table.getColumns().forEach(column => {
                if (column.getField() == this.column) {
                    const headerElement = column.getDefinition().headerPopup;
                    this.$nextTick(() => {
                        // this.checkItemOpenPopup();
                        const div_foreach_item = headerElement.querySelector('.div_foreach_item');
                        if (div_foreach_item) {
                            const item_filters = this.renderItems();
                            div_foreach_item.replaceWith(item_filters);
                            this.checkSelectAllWithOpenPopup();
                        }
                        // tôi muốn update innerHTML của clear-filter-column
                        const clear_filter_column = headerElement.querySelector('.clear-filter-column');
                        if (clear_filter_column) {
                            clear_filter_column.innerHTML = "<u class='text-danger'>Clear</u> filter <u style='font-weight:500;'>dữ liệu</u>";
                        }



                        // console.log(headerElement);
                        // const item_filter_background = headerElement.querySelector('.div_parent_dropdown_filter_background');
                        // if (item_filter_background) {
                        //     const item_filter_backgrounds = this.itemFilterBackgrounds();
                        //     item_filter_background.replaceWith(item_filter_backgrounds);
                        // }
                    });

                }
            });
        });
        this.table.on("popupClosed", (component) => {
            this.$emit('popupOpened', component.getField());
            this.column = component.getField();
        });
        this.table.on("dataFiltering", (filters) => {
            setTimeout(() => {
                this.table.getColumns().forEach(column => {
                    if (column.getField() == this.column) {
                        const newIconHTML = this.headerPopupIcon(this.column);
                        const headerElement = column.getElement();
                        if (headerElement) {
                            const currentButton = headerElement.querySelector('button');
                            if (currentButton) {
                                const newButton = document.createElement('button');
                                newButton.innerHTML = newIconHTML; // Thêm HTML cho biểu tượng  
                                newButton.className = 'btn btn-light btn-sm text-xs p-0'; // Thêm class nếu cần  
                                newButton.title = 'Add/Remove Filter'; // Cập nhật title nếu cần  
                                currentButton.replaceWith(newButton); // Sử dụng replaceWith  

                            }
                        }
                    }
                });
            }, 100); // Delay of 0ms to allow DOM updates

        });
        this.table.on("rowDblClick", (e, row) => {
            let rowData = row.getData();
            let input = prompt("Di chuyển dòng " + rowData.order + " vào dòng: ", "Nhập số dòng mong muốn"); // Giá trị mặc định là `order` hiện tại
            if (input !== null && input !== "") {
                if (typeof this.convertIntoNumber(input) == 'number') {
                    this.input_row = this.convertIntoNumber(input);
                    this.$emit('rowDblClickMoveRow', row.getPosition(), input);

                } else {
                    alert('Vui lòng nhập số dòng hợp lệ');
                }
            }
        });
        // this.table.on("historyUndo", (action, component, data) => {
        //     console.log('historyUndo:', action, component, data);

        //     switch (action) {
        //         case "cellEdit":
        //             this.$emit('cellEdited', component, data);
        //             break;
        //         default:
        //             break;
        //     }
        // });
        // this.table.on("historyRedo", (action, component, data) => {
        //     //action - the action that has been redone
        //     //component - the Component object affected by the action (could be a row or cell component)
        //     //data - the data being changed
        //     console.log('historyRedo:', action, component, data);
        //     switch (action) {
        //         case "cellEdit":
        //             this.$emit('cellEdited', component, data);
        //             break;

        //         default:
        //             break;
        //     }
        // });

        window.addEventListener('resize', this.updateWindowDimensions);

    },

    watch: {
        // 'filterColumn': {
        //     handler: function (newVal, oldVal) {
        //         this.table.setColumns(newVal);
        //     },
        //     deep: true,
        // },
        filteredOrders: {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    this.tableData = newVal;
                }
            }, 10),
            deep: true,
        },
        'update_status_function.update_move_row': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    this.table.setData(this.filteredOrders);
                    var row = this.table.getRows()[this.convertIntoNumber(this.input_row) - 1];
                    var bottom_right = this.table.getRows()[this.convertIntoNumber(this.input_row) - 1].getCells().slice(-1)[0];
                    var row_header_cell = row.getCell("order");
                    this.table.addRange(row_header_cell, bottom_right);
                    var current_range = this.table.getRanges();
                    current_range[0].remove();
                    this.table.scrollToRow(this.convertIntoNumber(this.input_row) - 1, "top", false);
                    this.table.scrollToColumn('order', "center", false);
                }

            }, 10),
            deep: true,
        },
        'update_status_function.set_data': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    console.time('renderData');
                    this.setData();
                    console.timeEnd('renderData');
                }

            }, 10),
            deep: true,
        },
        'update_status_function.update_data': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    console.time('updateData');
                    this.updateData();
                    console.timeEnd('updateData');
                }
            }, 10),
            deep: true,
        },
        'update_status_function.color': {
            handler: _.debounce(function (newVal, oldVal) {
                this.table.updateData(this.filteredOrders);
            }, 10),
            deep: true,
        },
        'update_status_function.update_or_add': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    this.table.updateOrAddData(this.filteredOrders);
                }
            }, 10),
        },
        'update_status_function.add_row': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {

                    this.table.updateOrAddData(this.filteredOrders);
                    this.table.updateData(this.filteredOrders);
                }
            }, 10),
        },
        'update_status_function.delete_row': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    this.selected_indexs.forEach(position => {
                        try {
                            let row = this.table.getRowFromPosition(position); // Get the row using the position
                            this.table.deleteRow(row); // Delete the row
                            this.$emit('deleteRowSuccess');
                        } catch (error) {
                            console.error(`Delete Error - No matching row found at position: ${position}`, error);
                        }
                    });

                }
            }, 10),
        },
        'update_status_function.delete': {
            handler: _.debounce(function (newVal, oldVal) {
                if (newVal) {
                    // Get the positions of the rows to delete
                    let positions = this.table.getRanges()
                        .map(range => range.getRows().map(row => row.getPosition()))
                        .flat(); // Flatten the array if necessary

                    let uniquePositions = [...new Set(positions)].sort((a, b) => b - a);

                    uniquePositions.forEach(position => {
                        try {
                            let row = this.table.getRowFromPosition(position); // Get the row using the position
                            this.table.deleteRow(row); // Delete the row
                            this.table.updateData(this.filteredOrders);
                        } catch (error) {
                            console.error(`Delete Error - No matching row found at position: ${position}`, error);
                        }
                    });

                }
            }, 10),
        },
        'update_column': {
            handler: function (newVal, oldVal) {
                if (newVal) {
                    this.table.options.rowContextMenu[4].menu = this.updateContextMenu_4();
                }
            },
            deep: true,
        },
        'item_filters': {
            handler: function (newVal, oldVal) {
                if (newVal) {
                    this.table.getColumns().forEach(column => {
                        if (column.getField() == this.column) {
                            // column.getDefinition().headerPopup = this.headerPopupFormatter(this.column);
                        }
                    });
                }
            },
            deep: true,
        },

    },
    updated() {
        this.updateWindowDimensions();
    },
    methods: {
        convertIntoNumber(value) {
            return isNaN(value) ? value : Number(value);
        },
        async updateWindowDimensions() {
            this.window_width = window.innerWidth;
            this.window_height = window.innerHeight;
            if (this.table) {
                await this.table.setHeight(this.window_height - 280);
            }
        },
        setSelectableRange() {
            if (this.is_editing) {
                return false;
            } else {
                return true;
            }

        },
        setData() {
            this.updateWindowDimensions();
            this.table.clearData();
            this.table.setColumns(this.filterColumn());
            this.table.setData(this.filteredOrders);
            this.table.clearHistory();
            this.table.setSort([
                { column: "order", dir: "asc" },
            ]);

        },
        updateData() {
            this.table.updateData(this.filteredOrders);
            this.updateWindowDimensions();
            let positon = this.table.getRanges().map(range => range.getRows().map(row => row.getPosition()));
            let fields = this.table.getRanges().map(range => range.getColumns().map(column => column.getField()));
            if (positon[0][0] !== undefined) {
                this.table.scrollToRow(positon[0][0], "top", false);
                this.table.scrollToColumn(fields[0][0], "center", false);
            }

        },
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
        formatterSapSoNumber() {
            return (cell, formatterParams, onRendered) => {
                let ma_sap = cell.getValue();
                let promotive = cell.getRow().getData().promotive;

                let value_promotive = promotive ? `${promotive}` : '';
                return `${ma_sap}${value_promotive}`;
            };
        },
        async loadTable() {
            const Tabulator = this.$Tabulator; // Access Tabulator from Vue prototype
            this.table = await new Tabulator(this.$refs.table, {
                tabIndex: -1,
                index: "order",
                movableColumns: true,
                movableColumnsInitialDelay: 0,
                dataTreeFilter: true,
                // history: true,
                // scrollToRowPosition: "nearest",
                // scrollTo: false,
                // virtualScroll: true,  
                // layout:"fitColumns",
                // scrollToRowIfVisible: false, //prevent scrolling to a row if it is visible
                // movableRows: true,
                // movableRowsConnectedElements: "#drop-area", //element to receive rows
                debugInvalidComponentFuncs: false,
                // columnHeaderSortMulti: false,
                // headerFilterLiveFilterDelay: 800,
                // data: this.filteredOrders,
                data: this.tableData,
                column: this.filterColumn(),
                headerSortClickElement: "icon", //trigger sort on icon click
                rowContextMenu: this.rowMenu(), //add context menu to rows
                layout: "fitColumns",
                // placeholder:"Không có dữ liệu",

                rowHeader: {
                    // resizable: false,
                    frozen: true,
                    width: 20,
                    hozAlign: "center",
                    // formatter: "rownum", 
                    formatter: "order",
                    cssClass: "range-header-col",
                    // editor: false,
                    // field: "rownum",
                    field: "order",


                },
                initialSelection: false,
                //enable range selection
                selectableRange: this.setSelectableRange(),
                selectableRangeColumns: true,
                selectableRangeRows: true,
                selectableRangeClearCells: true,
                selectableRangeClearCellsValue: "",
                // scrollToColumnIfVisible: false,
                // scrollToColumnPosition: "left",
                // selectableRows:true, // Chọn Row
                editTriggerEvent: "dblclick", // dblClick Chỉnh sửa
                enterEdit: true, // Chỉnh sửa khi nhấn Enter
                clipboard: true,
                clipboardCopyStyled: false,
                clipboardCopyConfig: {
                    rowHeaders: false, //do not include row headers in clipboard output
                    columnHeaders: false, //do not include column headers in clipboard output
                },
                clipboardCopyRowRange: "range",
                clipboardPasteParser: "range",
                clipboardPasteAction: "range",
                // clipboardPasteAction:"replace",
                rowFormatter: (row) => {
                    const data = row.getData();
                    if (data.theme_color) {
                        const keys = Object.keys(data.theme_color.background);
                        // row.getElement().style.backgroundColor = "lightcoral";
                        keys.forEach(key => {
                            if (row.getCell(key)) {
                                row.getCell(key).getElement().style.backgroundColor = data.theme_color.background[key];
                                row.getCell(key).getElement().style.color = data.theme_color.text[key];
                            }
                            // switch (key) {
                            //     case 'price_po':
                            //         if (data.theme_color.text[key] == '' || data.theme_color.text[key] == null) {
                            //             row.getCell('price_po').getElement().style.color = 'red';
                            //         }
                            //         break;
                            //     // case 'inventory_quantity':
                            //     //     if (data.theme_color.background[key] == '' || data.theme_color.background[key] == null) {
                            //     //         // && data.inventory_quantity == null || data.inventory_quantity == '' || data.inventory_quantity == 0
                            //     //         if ((data.inventory_quantity == null || data.inventory_quantity == '' || data.inventory_quantity == 0 || data.inventory_quantity < data.quantity2_po)) {
                            //     //             row.getCell('inventory_quantity').getElement().style.background = 'red';
                            //     //         }
                            //     //     }
                            //     //     break;

                            //     default:
                            //         break;
                            // }


                        });
                    }
                    if (data.extra_offer == 'X') {
                        row.getCell('barcode').getElement().style.color = '##ffc107';
                        row.getCell('barcode').getElement().style.fontWeight = 'bold';
                        row.getCell('sku_sap_code').getElement().style.color = '##ffc107';
                        row.getCell('sku_sap_code').getElement().style.fontWeight = 'bold';
                    }
                    if (data.promotion_category == 'X') {
                        row.getCell('barcode').getElement().style.color = '#007bff';
                        row.getCell('barcode').getElement().style.fontWeight = 'bold';
                        row.getCell('sku_sap_code').getElement().style.color = '#007bff';
                        row.getCell('sku_sap_code').getElement().style.fontWeight = 'bold';

                    }
                    if (data.promotion_bundle == 'X') {
                        row.getCell('barcode').getElement().style.fontWeight = 'bold';
                        row.getCell('barcode').getElement().style.color = '#b40fcb';
                        row.getCell('sku_sap_code').getElement().style.color = '#b40fcb';
                        row.getCell('sku_sap_code').getElement().style.fontWeight = 'bold';
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

            // await this.updateWindowDimensions();

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
            let rowMenu = [
                // {
                //     label: "<i class='fas fa-user'></i> Change Name",
                //     action: function (e, row) {
                //         row.update({ name: "Steve Bobberson" });
                //     }
                // },
                {
                    label: "<i class='fas fa-fill text-black-50 mr-1'></i> Tô màu",
                    menu: [
                        {
                            label: "Nền",
                            menu: this.theme_colors.map(color => {
                                return {
                                    label: `<div style="background: ${color.color};width: 60px; height: 15px; border: 1px solid gray;"></div>`,
                                    action: (e, column) => {
                                        this.$emit('inputBackgroundColor', color);
                                    },
                                };
                            }),
                            cssClass: "color-menu",
                        },
                        {
                            label: "Chữ",
                            menu: this.theme_colors.map(color => {
                                return {
                                    label: `<div style="background: ${color.color};width: 60px; height: 15px; border: 1px solid gray"></div>`,
                                    action: (e, column) => {
                                        this.$emit('inputTextColor', color);
                                    },
                                };
                            }),
                        }
                    ]
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-eye-slash text-black-50 mr-1'></i> Ẩn cột",
                    action: (e, row) => {
                        this.$emit('toggleColumn', row.getPosition());
                        Object.keys(this.range_items[0]).forEach(key => {
                            this.table.hideColumn(key);
                            rowMenu[4].menu.push({
                                label: this.columns.find(column => column.field == key).title,
                                action: (e, row, colu) => {
                                    this.$emit('toggleColumnShow', row.getPosition(), key);
                                    this.table.showColumn(key);
                                    rowMenu[4].menu = rowMenu[4].menu.filter(item => item.label != this.columns.find(column => column.field == key).title);
                                    this.table.options.rowContextMenu = rowMenu;
                                },
                            });

                        });
                        const hidden_columns = this.table.getColumns().filter(column => !column.visible);
                        this.$emit('hiddenColumns', hidden_columns);
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-eye text-black-50 mr-1'></i> Hiện cột",
                    menu: this.hidden_columns.map((column, index) => {
                        return {
                            label: column.title,
                            action: (e, row, colu) => {
                                this.$emit('toggleColumnShow', row.getPosition(), column.field);
                                this.table.showColumn(column.field);
                                // xóa menu item đã chọn trong menu
                                rowMenu[4].menu = rowMenu[4].menu.filter(item => item.label != column.title);
                                // cập nhật lại menu
                                this.table.options.rowContextMenu = rowMenu;

                            },
                        };
                    }),
                },
                {
                    label: "<i class='fas fa-check-square text-black-50 mr-1'></i> Chọn dòng",
                    action: (e, row) => {
                        row.select();
                        this.$emit('rowSelectionChanged', row.getData(), true, this.table.getSelectedRows().map(row => row.getPosition()));
                    }
                },
                {
                    label: "<i class='fas fa-check-square text-black-50 mr-1'></i> Hủy chọn dòng",
                    action: (e, row) => {
                        row.deselect();
                        this.$emit('rowSelectionChanged', row.getData(), false, this.table.getSelectedRows().map(row => row.getPosition()));
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-plus text-black-50 mr-1'></i> Thêm dòng",
                    action: (e, row) => {
                        this.$emit('addRow', row.getPosition(), this.table.getRanges().map(r => r.getRows().map(row => row.getPosition())));
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
                    label: "<i class='fas fa-copy text-black-50 mr-1'></i> Copy",
                    action: (e, row) => {
                        // this.$emit('copyRow', row.getPosition(), row.getData());
                        // add this.range.items vào clipboard
                        // console.log('range', this.range);
                        this.table.copyToClipboard("range");

                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-paste text-black-50 mr-1'></i> Paste",
                    action: (e, row) => {
                        // this.$emit('pasteRow', row.getPosition());
                        // this.table.clipboardPasteParser("range");
                        console.log('Paste:', 'Chức năng Paste chưa hoàn thiện');
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-clone text-black-50 mr-1'></i> Duplicate",
                    action: (e, row) => {
                        // thêm dòng row mới vào bảng tương ứng với dòng row hiện tại
                        this.$emit('duplicateRow', row.getPosition(), row.getData(), this.table.getRanges().map(r => r.getRows().map(row => row.getPosition())));
                        // console.log('row.getPosition():', row.getPosition(), row.getData());
                        // row.move(row.getPosition() + 1, false);
                    }
                },
                {
                    separator: true,
                },
                {
                    label: "<i class='fas fa-trash text-black-50 mr-1'></i> Xóa dòng",
                    action: (e, row) => {
                        // this.$emit('deleteRow', row.getPosition(), row.getData());
                        this.$emit('deleteRow', this.table.getRanges().map(r => r.getRows().map(row => row.getPosition())));
                    }
                },


            ];
            return rowMenu;
        },
        emptyHeaderFilter() {
            return document.createElement("div");
        },
        createSearchInput(container) {
            var input = document.createElement("input");
            input.classList.add("form-control", "form-control-sm", "text-xs");
            input.placeholder = "Tìm kiếm...";
            let debounceTimer;
            input.addEventListener("keyup", (e) => {
                clearTimeout(debounceTimer);
                this.query = e.target.value;
                debounceTimer = setTimeout(() => {
                    this.updateRenderItems();
                }, 300); // Delay 300ms trước khi thực hiện việc lọc
            });
            return input;
        },
        createItemsContainer() {
            var itemsContainer = document.createElement("div");
            itemsContainer.className = "items-container";
            itemsContainer.style.marginTop = "10px"; // Thêm khoảng cách giữa input và danh sách
            return itemsContainer;
        },
        floatOrToLowerCase(value) {
            // convert null to empty string
            if (value === null) {
                return "";
            } else {
                return value.toLowerCase();
            }

            return isNaN(value) ? value.toLowerCase() : value;
            // return isNaN(value) ? value.toLowerCase() : value;

        },
        checkItemChange(checkbox, item) {
            if (this.item_change_checked.length > 0) {
                const isChecked = this.item_change_checked.some(item_checked => {
                    return item_checked.name === item.name &&
                        item_checked.promotive_name === item.promotive_name &&
                        item_checked.field === this.column;
                });

                if (isChecked) {
                    checkbox.setAttribute("checked", "true"); // Đặt thuộc tính checked
                } else {
                    checkbox.removeAttribute("checked"); // Bỏ thuộc tính checked
                }
            }
        },
        checkItemOpenPopup() {
            if (this.item_change_checked.length == 0) {
                this.item_selecteds = this.CompItemFilters.map(item => {
                    return { name: item.name, promotive_name: item.promotive_name, field: this.column }
                }
                );
                this.$emit('itemChangeChecked', this.item_selecteds, true);
            }

        },
        renderItems() {
            // itemsContainer.innerHTML = ""; // Xóa các mục hiện tại
            var div_foreach_item = document.createElement("div");
            div_foreach_item.className = "div_foreach_item ml-2";
            this.CompItemFilters.forEach(item => {
                var itemContainer = document.createElement("div");
                itemContainer.style.display = "flex";
                itemContainer.style.alignItems = "center";
                itemContainer.classList.add("text-xs");

                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.value = item.name + item.promotive_name;
                // checkbox.setAttribute("checked", false);


                var itemLabel = document.createElement("label");
                itemLabel.classList.add("text-black-50");
                itemLabel.style.marginBottom = "0";
                let value = '';
                switch (this.column) {
                    case 'sap_so_number':
                        value = item.name + item.promotive_name
                        break;
                    case 'so_sap_note':
                        value = item.name + item.promotive_name
                        break;
                    case 'promotive':
                        value = item.name
                        break;
                    case 'promotive_name':
                        value = item.name
                        break;
                    case 'note1':
                        value = item.name
                        break;
                    default:
                        value = item.name
                        break;
                }
                let value_default = value == null ? '<span>(Null)</span>' : value == '' ? '<span>(Empty)</span>' : value;
                itemLabel.innerHTML = value_default;


                itemLabel.style.marginLeft = "5px";
                itemLabel.style.fontSize = ".9em";
                itemLabel.style.fontWeight = "600";

                itemContainer.appendChild(checkbox);
                itemContainer.appendChild(itemLabel);
                div_foreach_item.appendChild(itemContainer);

                // So sánh với item_change_checked
                this.checkItemChange(checkbox, item);
                // Lắng nghe sự kiện thay đổi trạng thái của checkbox
                checkbox.addEventListener("change", (e) => {
                    if (checkbox.checked) {
                        // thêm một field vào mảng item_selecteds
                        this.item_selecteds.push({ name: item.name, promotive_name: item.promotive_name, field: this.column });
                    } else {
                        this.item_selecteds = this.item_selecteds.filter(item_selected =>
                            !(item_selected.name === item.name && item_selected.promotive_name === item.promotive_name));
                    }
                    const result = Object.groupBy(this.item_selecteds, ({ field }) => field);
                    var checkbox_selectall = document.querySelector(".checkbox-selectall");
                    if (result[this.column] !== undefined) {
                        result[this.column].length == this.CompItemFilters.length ? this.selected_all = true : this.selected_all = false;
                        checkbox_selectall.checked = this.selected_all;
                    }
                    this.checkDisableBtnSearch();
                    // checkbox.setAttribute("checked", checkbox.checked);

                    // this.$emit('itemChangeChecked', this.item_selecteds, checkbox.checked);
                });
            });
            return div_foreach_item;
        },
        createResetButton() {
            // Implement this.btnReset() logic here or call this.btnReset() directly if it's defined elsewhere
            return this.btnReset();
        },
        createUpdateButton(itemContainer, filter = "") {
            // Implement this.btnUpdate() logic here or call this.btnUpdate() directly if it's defined elsewhere
            return this.btnUpdate(itemContainer, filter = "");
        },
        createSearchButton(column) {
            // Implement this.btnSearch(column) logic here or call this.btnSearch(column) directly if it's defined elsewhere
            return this.btnSearch(column);
        },
        createDivSelectAll(itemsContainer) {
            var parent_selectall = document.createElement("div");
            parent_selectall.className = "d-flex justify-center align-items-center select-all ml-2";
            var checkbox_selectall = document.createElement("input");
            checkbox_selectall.className = "checkbox-selectall";
            checkbox_selectall.type = "checkbox";
            checkbox_selectall.value = "selectall";
            checkbox_selectall.style.cursor = "pointer";
            var label_selectall = document.createElement("label");
            label_selectall.innerHTML = "Select All";
            label_selectall.style.marginBottom = "0";
            label_selectall.style.marginLeft = "5px";
            label_selectall.style.fontSize = ".7em";
            label_selectall.style.fontWeight = "600";
            parent_selectall.appendChild(checkbox_selectall);
            parent_selectall.appendChild(label_selectall);
            // itemsContainer.appendChild(parent_selectall);

            // kiểm tra xem đã chọn tất cả các mục chưa
            this.selected_all ? checkbox_selectall.checked = true : checkbox_selectall.checked = false;
            checkbox_selectall.setAttribute("checked", this.selected_all);

            checkbox_selectall.addEventListener("change", (e) => {

                if (checkbox_selectall.checked) {
                    this.item_selecteds = this.CompItemFilters.map(item => {
                        return { name: item.name, promotive_name: item.promotive_name, field: this.column };
                    });
                    itemsContainer.querySelectorAll("input[type=checkbox]").forEach(checkbox => {
                        checkbox.checked = true;
                    });
                } else {
                    // this.item_selecteds = [];
                    this.item_selecteds = this.item_selecteds.filter(item_selected =>
                        !(item_selected.field === this.column));

                    itemsContainer.querySelectorAll("input[type=checkbox]").forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
                this.checkDisableBtnSearch();
                this.selected_all = checkbox_selectall.checked;
                checkbox_selectall.setAttribute("checked", this.selected_all);

                this.$emit('itemChangeChecked', this.item_selecteds, checkbox_selectall.checked);
            });
            return parent_selectall;
        },
        headerPopupFormatter(column) {
            var container = document.createElement("div");
            container.classList.add("set-popup");
            container.style.padding = "10px";
            container.style.height = "30em";
            container.style.overflow = "auto";
            container.style.width = "20em";

            // Tạo container cho các mục
            container.appendChild(this.createDivReset());
            container.appendChild(this.createDivClearFilterColumn());
            container.appendChild(this.createDivPriceDifference());
            container.appendChild(this.createDivPriceEqual());
            container.appendChild(this.createDivPromotion());
            container.appendChild(this.createDivCombo());
            container.appendChild(this.createBtnGroup());


            var container_filter_search = document.createElement("div");
            container_filter_search.className = "container-filter-search mt-2";
            container_filter_search.style.height = "10em";
            container_filter_search.style.overflow = "auto";
            container_filter_search.style.backgroundColor = "rgb(244, 244, 244)"

            var itemsContainer = this.createItemsContainer();
            container.appendChild(this.createSearchInput());
            ////
            itemsContainer.appendChild(this.createDivSelectAll(itemsContainer));
            itemsContainer.appendChild(this.renderItems());
            container_filter_search.appendChild(itemsContainer);

            // Render các mục ban đầu
            // this.$nextTick(() => {
            //     this.renderItems();
            // });


            var div_footer = document.createElement("div");
            div_footer.classList.add("text-xs", "text-right");
            // Thêm các nút vào container
            // container.appendChild(this.createBtnGroup());
            // div_footer.appendChild(this.createResetButton()); // Nút button reset 
            div_footer.appendChild(this.createSearchButton(column));
            div_footer.appendChild(this.createBtnCancel());
            // div_footer.appendChild(this.createUpdateButton(itemsContainer, "")); // Nút button cập nhật
            container.appendChild(container_filter_search);
            container.appendChild(div_footer);
            return container;
        },
        createDivClearFilterColumn() {
            var div_clear_filter_column = document.createElement("div");
            div_clear_filter_column.classList.add("text-xs", "dropdown-item", "cursor-pointer", "clear-filter-column");
            div_clear_filter_column.innerHTML = "Clear Filter from" + `"${this.column}"`;
            div_clear_filter_column.style.cursor = "pointer";
            div_clear_filter_column.classList.add("text-xs");
            div_clear_filter_column.addEventListener("click", () => {
                this.is_column_filter[this.column] = false;
                // this.table.clearFilter();
                this.removeOnlyFilter();
                this.removeOnlyDivClassSetPopup();
                console.log('this.column:', this.column);

            });
            return div_clear_filter_column;
        },
        removeOnlyFilter() {
            this.table.getFilters().forEach(filter => {
                if (typeof filter.field === 'function') {
                    switch (filter.field.name) {
                        case 'filterBackground':
                            this.table.removeFilter(filter.field);
                            this.is_column_filter[this.column] = false;
                            // this.colorsToFilter = [];
                            break;
                        case 'filterText':
                            this.table.removeFilter(filter.field);
                            this.is_column_filter[this.column] = false;
                            // this.colorsToFilter = [];
                            break;
                        default:
                            break;
                    }
                } else if (filter.field === this.column) {
                    this.table.removeFilter(filter.field, filter.type, filter.value);
                    this.updateRenderItems();
                    this.item_selecteds = this.item_selecteds.filter(item => item.field !== this.column);

                    this.$emit('itemChangeChecked', this.item_selecteds, false);

                }
            });
        },
        removeOnlyDivClassSetPopup() {
            document.querySelectorAll('.set-popup').forEach(item => {
                item.remove();
            });
        },
        createDivReset() {
            var div_reset = document.createElement("div");
            div_reset.classList.add("text-xs", "dropdown-item", "cursor-pointer");
            div_reset.innerHTML = "<u style='font-weight:500;'>Reset</u> all";
            div_reset.style.cursor = "pointer";
            div_reset.classList.add("text-xs");
            div_reset.addEventListener("click", () => {
                this.$emit('filterOrder', '', '', 'reset');
                // reset is_column_filter
                this.item_selecteds = [];
                this.table.clearFilter();
                this.resetIsColumnFilter();
                this.removeOnlyDivClassSetPopup();
                this.table.getColumns().forEach(column => {
                    // reset headerPopupIcon
                    const newIconHTML = this.headerPopupIcon(column.getField());
                    const headerElement = column.getElement();
                    if (headerElement) {
                        const currentButton = headerElement.querySelector('button');
                        if (currentButton) {
                            const newButton = document.createElement('button');
                            newButton.innerHTML = newIconHTML; // Thêm HTML cho biểu tượng  
                            newButton.className = 'btn btn-light btn-sm text-xs p-0'; // Thêm class nếu cần  
                            newButton.title = 'Add/Remove Filter'; // Cập nhật title nếu cần  
                            currentButton.replaceWith(newButton); // Sử dụng replaceWith  
                        }
                    }
                });
            });
            return div_reset;
        },
        createDivPromotion() {
            var div_promotion = document.createElement("div");
            div_promotion.classList.add("text-xs", "dropdown-item", "cursor-pointer");
            div_promotion.innerHTML = "Filter <u style='font-weight:500;'>khuyến mãi</u>";
            div_promotion.style.cursor = "pointer";
            div_promotion.classList.add("text-xs");
            div_promotion.addEventListener("click", () => {
                this.is_column_filter[this.column] = true;
                this.$emit('filterOrder', 'X', 'extra_offer', 'extra_offer');
                this.table.setFilter([
                    { field: "extra_offer", type: "like", value: "X" },
                ]);
                this.removeOnlyDivClassSetPopup();

            });
            return div_promotion;
        },
        createDivCombo() {
            var div_combo = document.createElement("div");
            div_combo.classList.add("text-xs", "dropdown-item", "cursor-pointer");
            div_combo.innerHTML = "Filter <u style='font-weight:500;'>combo</u>";
            div_combo.style.cursor = "pointer";
            div_combo.classList.add("text-xs");
            div_combo.addEventListener("click", () => {
                this.is_column_filter[this.column] = true;
                this.$emit('filterOrder', 'X', 'promotion_category', 'promotion_category');
                this.table.setFilter([
                    { field: "promotion_category", type: "like", value: "X" },
                ]);
                this.removeOnlyDivClassSetPopup();
            });
            return div_combo;
        },
        createDivPriceDifference() {
            var div_price = document.createElement("div");
            div_price.classList.add("text-xs", "dropdown-item", "cursor-pointer");
            div_price.innerHTML = "Filter giá <u style='font-weight:500;'>chênh lệch</u>";
            div_price.style.cursor = "pointer";
            div_price.classList.add("text-xs");
            div_price.addEventListener("click", () => {
                this.is_column_filter[this.column] = true;
                this.$emit('filterOrder', 'price_difference', 'difference', 'difference');
                this.table.setFilter([
                    { field: "difference", type: "=", value: "price_difference" },
                ]);
                this.removeOnlyDivClassSetPopup();
                console.log('this.table.getFilters():', this.table.getFilters());
                console.log('this.table.getData():', this.table.getData());
            });
            return div_price;
        },
        createDivPriceEqual() {
            var div_price = document.createElement("div");
            div_price.classList.add("text-xs", "dropdown-item", "cursor-pointer");
            div_price.innerHTML = "Filter giá <u style='font-weight:500;'>ngang nhau</u>";
            div_price.style.cursor = "pointer";
            div_price.classList.add("text-xs");
            div_price.addEventListener("click", () => {
                this.is_column_filter[this.column] = true;
                this.$emit('filterOrder', 'price_equal', 'difference', 'difference');
                this.table.setFilter([
                    { field: "difference", type: "like", value: "price_equal" },
                ]);
                this.removeOnlyDivClassSetPopup();

            });
            return div_price;
        },
        btnUpdate(itemContainer, filter = "") {
            var button_update = document.createElement("button");
            button_update.innerHTML = "Cập nhật";
            button_update.classList.add("btn", "btn-sm", "btn-light", "text-xs");
            button_update.style.marginTop = "10px";
            button_update.style.fontSize = ".7em";
            button_update.style.fontWeight = "600";
            button_update.addEventListener("click", () => {
                // this.$emit('resetItem');
                this.renderItems();

            });
            return button_update;
        },
        btnReset() {
            var button_reset = document.createElement("button");
            button_reset.innerHTML = "Reset";
            button_reset.classList.add("btn", "btn-sm", "btn-light", "text-xs");
            button_reset.style.marginTop = "10px";
            button_reset.style.fontSize = ".7em";
            button_reset.style.fontWeight = "600";
            button_reset.addEventListener("click", (e) => {
                this.item_selecteds = [];
                this.table.clearFilter();
                this.$emit('filterOrder', '', '');
            });
            return button_reset;
        },
        btnSearch(column) {
            var button_search = document.createElement("button");
            button_search.innerHTML = "Ok";
            button_search.classList.add("btn", "btn-sm", "btn-light", "text-xs", "btn-search-excel");
            button_search.style.marginTop = "10px";
            button_search.style.fontSize = ".7em";
            button_search.style.fontWeight = "600";


            button_search.addEventListener("click", (column) => {
                this.is_column_filter[this.column] = true;
                const result = Object.groupBy(this.item_selecteds, ({ field }) => field);

                // Lấy các giá trị trong cột
                let filterValues = result[this.column].map(item => item.name);
                // Kiểm tra giá trị đầu tiên trong cột để xác định kiểu dữ liệu
                let columnData = this.table.getData().map(row => row[this.column]);

                // Xử lý kiểu dữ liệu: null, số, chuỗi
                if (columnData.length > 0) {
                    filterValues = filterValues.map(value => {
                        if (value === null) {
                            return null; // Nếu giá trị là null thì giữ nguyên
                        }
                        //  else if (typeof columnData[0] === 'number') {
                        //     console.log('số', value);
                        //     return parseInt(value); // Nếu trong bảng là số, chuyển filter value thành số
                        // } else if (typeof columnData[0] === 'string' && value !== '') {
                        //     console.log('chuỗi', value);
                        //     return value.toString(); // Nếu trong bảng là chuỗi, giữ nguyên chuỗi
                        // } 
                        else if (value === '') {
                            return null; // Nếu giá trị là null thì giữ nguyên
                            // thiếu 1 trường hợp nếu là "false" thì chuyển thành false
                        } else if (value === 'false') {
                            return false;
                        } else if (value === 'true') {
                            return true;
                        }

                        return value;
                    });
                }
                // Áp dụng filter cho bảng
                if (this.table.getFilters().length == 0) {
                    this.table.addFilter(this.column, "in", filterValues);
                } else {
                    this.table.getFilters().forEach(filter => {
                        if (filter.field == this.column) {
                            this.table.removeFilter(filter.field, filter.type, filter.value);
                            this.table.addFilter(this.column, "in", filterValues);
                        } else {
                            this.table.addFilter(this.column, "in", filterValues);
                        }
                    });
                }

                // if (this.table.getFilters().length == 0) {
                //     this.table.addFilter(this.column, "in", result[this.column].map(item => item.name));
                // } else {
                //     this.table.getFilters().forEach(filter => {
                //         if (filter.field == this.column) {
                //             this.table.removeFilter(filter.field, filter.type, filter.value);
                //             this.table.addFilter(this.column, "in", result[this.column].map(item => item.name));
                //         } else {
                //             this.table.addFilter(this.column, "in", result[this.column].map(item => item.name));
                //         }
                //     });
                // }
                this.removeOnlyDivClassSetPopup();
                this.$emit('searchItem', this.column, 'search_item');
                this.$emit('itemChangeChecked', this.item_selecteds, false);

            });
            return button_search;
        },
        createBtnCancel() {
            var button_cancel = document.createElement("button");
            button_cancel.innerHTML = "Cancel";
            button_cancel.classList.add("btn", "btn-sm", "btn-light", "text-xs");
            button_cancel.style.marginTop = "10px";
            button_cancel.style.fontSize = ".7em";
            button_cancel.style.fontWeight = "600";
            button_cancel.addEventListener("click", () => {
                this.removeOnlyDivClassSetPopup();
            });
            return button_cancel;
        },
        createBtnGroup() {
            var btn_group = document.createElement("div");
            btn_group.classList.add("cursor-pointer", "btn-group", "btn-group-sm", "w-100");
            var btn = document.createElement("div");
            btn.style.cursor = "pointer";

            btn.classList.add("text-xs", "dropdown-toggle", "dropdown-item", "cursor-pointer");
            btn.setAttribute("data-toggle", "dropdown");
            btn.innerHTML = "Filter <u style='font-weight:500;'>màu</u>";

            btn_group.appendChild(btn);

            var dropdown_menu = document.createElement("div");
            var span_background = document.createElement("span");
            span_background.classList.add("ml-2", "font-weight-bold");
            span_background.innerHTML = "Filter màu nền";
            dropdown_menu.style.cursor = "pointer";
            dropdown_menu.classList.add("dropdown-menu", "text-xs");

            dropdown_menu.appendChild(span_background);


            btn.addEventListener("click", () => {
                dropdown_menu.classList.toggle("show");
            });

            btn_group.appendChild(dropdown_menu);
            btn_group.addEventListener("click", () => {
                for (let i = dropdown_menu.children.length - 1; i >= 0; i--) {
                    let child = dropdown_menu.children[i];
                    dropdown_menu.removeChild(child);
                }
                while (!dropdown_menu.contains(span_background)) {
                    dropdown_menu.appendChild(span_background);
                }
                this.itemFilterBackgrounds(dropdown_menu);
                this.itemFilterTexts(dropdown_menu);


            });
            return btn_group;
        },
        createDivIconCheck() {
            var div_icon_check = document.createElement("div");
            div_icon_check.innerHTML = '<i class="fas fa-check"></i>';
            div_icon_check.className = "text-xs text-black-50 div_icon_check";
            div_icon_check.style.display = 'none';
            return div_icon_check;
        },
        itemFilterBackgrounds(dropdown_menu) {
            var items = [... this.item_filter_backgrounds];
            var div_parent_dropdown_filter_background = document.createElement("div");
            div_parent_dropdown_filter_background.className = "div_parent_dropdown_filter_background";
            items.forEach(item => {
                var dropdown_item = document.createElement("div");
                dropdown_item.classList.add("dropdown-item", "d-flex", "justify-content-between", "item-filter-background");
                var div_item = document.createElement("div");
                switch (item.color) {
                    case null:
                        div_item.innerHTML = '<small>No fill</small>';
                        break;

                    default:
                        div_item.style.backgroundColor = item.color;
                        div_item.style.width = "60px";
                        div_item.style.height = "15px";
                        div_item.style.border = "1px solid gray";
                        div_item.style.borderRadius = "3px";
                        break;
                }
                dropdown_item.appendChild(div_item);

                dropdown_item.appendChild(this.createDivIconCheck());

                dropdown_item.addEventListener("click", () => {
                    // this.$emit('inputBackgroundColor', item);
                    this.is_column_filter[this.column] = true;
                    this.$emit('filterOrder', item.color, this.column, 'theme_color_bg');
                    dropdown_menu.classList.remove("show");
                    this.colorsToFilter.push({ color: item.color, column: this.column });
                    const filterBackground = (data) => {
                        return data.theme_color.background[this.column] == item.color;
                    };
                    this.table.setFilter(filterBackground, '', 'filterBackground');
                    // if (this.table.getFilters().length == 0) {
                    //     this.table.addFilter(filterBackground);
                    // } else {
                    //     this.table.getFilters().forEach(filter => {
                    //         if (filter.field.name === 'filterBackground') {
                    //             // this.table.removeFilter(filterBackground);
                    //             this.table.addFilter(filterBackground);
                    //         } else {
                    //             this.table.addFilter(filterBackground);
                    //         }
                    //     });
                    // }
                    this.removeOnlyDivClassSetPopup();


                });
                div_parent_dropdown_filter_background.appendChild(dropdown_item);
                dropdown_menu.appendChild(div_parent_dropdown_filter_background);
            });
            return dropdown_menu;
        },
        getValue(data, field) {
            return data.theme_color.background[field];
        },
        itemFilterTexts(dropdown_menu) {
            var span_text = document.createElement("span");
            span_text.innerHTML = "Filter màu chữ";
            span_text.classList.add("ml-2", "font-weight-bold");


            while (!dropdown_menu.contains(span_text)) {
                dropdown_menu.appendChild(span_text);
            }

            var items = [... this.item_filter_texts];
            items.forEach(item => {
                var dropdown_item = document.createElement("div");
                dropdown_item.classList.add("dropdown-item");

                var div_item = document.createElement("div");

                switch (item.color) {
                    case null:
                        div_item.innerHTML = '<small>No fill</small>';
                        break;
                    default:
                        div_item.style.backgroundColor = item.color;
                        div_item.style.width = "60px";
                        div_item.style.height = "15px";
                        div_item.style.border = "1px solid gray";
                        div_item.style.borderRadius = "3px";
                        break;
                }
                dropdown_item.appendChild(div_item);
                dropdown_item.addEventListener("click", () => {
                    // this.$emit('inputBackgroundColor', item);
                    dropdown_menu.classList.remove("show");
                    this.$emit('filterOrder', item.color, this.column, 'theme_color_txt');
                    this.is_column_filter[this.column] = true;

                    const filterText = (data) => {
                        return data.theme_color.text[this.column] == item.color;
                    };
                    this.table.setFilter(filterText, '', 'filterText');
                    // if (this.table.getFilters().length == 0) {
                    //     this.table.addFilter("filterText", "function", filterText);
                    // } else {
                    //     const existingFilter = this.table.getFilters().find(filter => filter.field === 'filterText');
                    //     if (existingFilter) {
                    //         // // this.table.removeFilter(filter.field, filter.type, filter.value);
                    //         this.table.addFilter("filterText", "function", filterText);
                    //     } else {
                    //         this.table.addFilter("filterText", "function", filterText);
                    //     }
                    // }
                    // console.log(this.table.getFilters());
                    this.removeOnlyDivClassSetPopup();

                });
                dropdown_menu.appendChild(dropdown_item);
            });


            return dropdown_menu;
        },
        headerPopupIcon(column) {
            if (this.is_column_filter[column]) {
                return "<button class='btn btn-light btn-sm text-xs p-0' style='width:15px;height:19px;' ><i class='fas fa-filter'></i></button>";
            } else {
                return "<button class='btn btn-light btn-sm text-xs p-0' style='width:15px;height:19px;' ><i class='fas fa-caret-down'></i></button>";
            }
            // return "<button class='btn btn-light btn-sm text-xs'><i class='fas fa-caret-down'></i></button>";
            // return "<button class='btn btn-light btn-sm text-xs py-0 px-1'><i class='fas fa-caret-down'></i></button>";

        },
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
                    width: column.width,
                    editor: column.field == "promotive" ? "list" : "input",
                    visible: column.visible,
                    headerPopup: this.headerPopupFormatter(''),
                    headerPopupIcon: this.headerPopupIcon(column.field),
                    // headerFilter: this.emptyHeaderFilter(),
                    // headerFilterFunc: "like",
                    editorParams: column.field == "promotive" ? {
                        values: this.material_category_types.map(item => item.name)
                    } : {
                        shiftEnterSubmit: true,
                        selectContents: true,
                        verticalNavigation: "table",
                    },
                    // headerMenu: this.headerMenu(),
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
        updateContextMenu_4() {
            let context_menu_4 = this.hidden_columns.map(column => {
                return {
                    label: column.title,
                    action: (e, row, colu) => {
                        this.$emit('toggleColumnShow', row.getPosition(), column.field);
                        this.table.showColumn(column.field);
                        // xóa menu item đã chọn trong menu
                        this.table.options.rowContextMenu[4].menu = this.table.options.rowContextMenu[4].menu.filter(item => item.label != column.title);
                        // cập nhật lại menu
                        this.table.options.rowContextMenu = this.table.options.rowContextMenu;

                    },
                }
            });
            return context_menu_4;

        },
        formatterSapSoNumberOrSoSapNote() {
            return (cell, formatterParams, onRendered) => {
                let value = cell.getValue(); // Giá trị của cột 'ma_sap'
                let promotive = cell.getRow().getData().promotive; // Giá trị của cột 'note'
                cell.getRow().getData().promotive_name = promotive;
                cell.getRow().getData().note1 = promotive;
                cell.getRow().getData().is_promotive = promotive !== '' ? true : false;

                let value_promotive = promotive ? `${promotive}` : '';
                let value_new = value == null ? '' : value;
                return `${value_new}${value_promotive}`;
            };
        },
        resetIsColumnFilter() {
            this.is_column_filter = {
                customer_name: false,
                sap_so_number: false,
                so_sap_note: false,
                barcode: false,
                sku_sap_code: false,
                sku_sap_name: false,
                quantity3_sap: false,
                sku_sap_unit: false,
                promotive: false,
                note: false,
                customer_code: false,
                customer_sku_code: false,
                customer_sku_name: false,
                customer_sku_unit: false,
                po: false,
                quantity1_po: false,
                promotive_name: false,
                inventory_quantity: false,
                quantity2_po: false,
                variant_quantity: false,
                price_po: false,
                company_price: false,
                amount_po: false,
                compliance: false,
                is_compliant: false,
                note1: false,
                level2: false,
                level3: false,
                level4: false,
                po_delivery_date: false,
                po_number: false,
            };
        },
        checkDisableBtnSearch() {
            var btn_search_excel = document.querySelector(".btn-search-excel");
            const result = Object.groupBy(this.item_selecteds, ({ field }) => field);
            if (result[this.column] == undefined) {
                // tìm button có class btn-search-excel 
                btn_search_excel.disabled = true;
            } else {
                btn_search_excel.disabled = false;
            }
        },
        updateRenderItems() {
            this.table.getColumns().forEach(column => {
                if (column.getField() == this.column) {
                    const headerElement = column.getDefinition().headerPopup;
                    this.$nextTick(() => {
                        const item_filters = this.renderItems();
                        const div_foreach_item = headerElement.querySelector('.div_foreach_item');
                        if (div_foreach_item) {
                            div_foreach_item.replaceWith(item_filters);
                        }
                    });
                }
            });
        },
        resetQuery() {
            this.query = '';
        },
        checkSelectAllWithOpenPopup() {
            const result = Object.groupBy(this.item_selecteds, ({ field }) => field);
            var checkbox_selectall = document.querySelector(".checkbox-selectall");
            if (result[this.column] !== undefined) {
                result[this.column].length == this.CompItemFilters.length ? this.selected_all = true : this.selected_all = false;
                checkbox_selectall.checked = this.selected_all;

            }
        },
    },
    computed: {
        // columns() {
        //     return this.filterColumn();
        // },
        // context_menu_4() {
        //     return this.updateContextMenu_4();
        // },
        // formatterSapSoNumberOrSoSapNote() {
        //     return this.formatterSapSoNumberOrSoSapNote();
        // },
        CompItemFilters() {
            // search query
            return this.item_filters.filter(item => this.floatOrToLowerCase(item.name).includes(this.floatOrToLowerCase(this.query)) ||
                this.floatOrToLowerCase(item.promotive_name).includes(this.floatOrToLowerCase(this.query)));
        },
    },


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



::v-deep .tabulator-range-only-cell-selected {
    border: 2px solid rgb(255, 28, 28) !important;
    // background-color: transparent !important;
}

::v-deep .tabulator-range-active {
    background-color: transparent !important;
    border: 2px solid red !important;
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

::v-deep .tabulator-menu-item {
    font-size: 0.8em !important;
    /* Kích thước văn bản nhỏ hơn */
}

::v-deep .tabulator-row {
    &:hover {
        background-color: rgb(232, 232, 232) !important;
        cursor: cell;
    }
}

::v-deep .tabulator-row .tabulator-cell.tabulator-range-selected:not(.tabulator-range-only-cell-selected):not(.tabulator-range-row-header) {
    background-color: #f3f3f3;
}

::v-deep .tabulator-col {
    direction: rtl !important;
}

.cursor-pointer {
    cursor: pointer !important;
}

::v-deep .tabulator-col-title {
    display: flex;
    justify-content: space-between;
}

::v-deep .tabulator-header-popup-button {
    padding: 0px !important;
}
</style>