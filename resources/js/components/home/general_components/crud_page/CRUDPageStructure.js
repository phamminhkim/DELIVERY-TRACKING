class PageHeader {
    constructor(header = {}) {
        Object.assign(
            this,
            {
                title: 'This is title',
                title_icon: 'fas fa-truck'
            },
            header
        )
    }
}
class TableField {
    constructor(table_field = {}) {
        Object.assign(
            this,
            {
                key: '',
                label: '',
                sortable: false,
                class: 'text-nowrap'
            },
            table_field
        )
    }
    static ArrayToTableFields(table_fields_array = []) {
        return table_fields_array.map(table_field => {
            return new TableField(table_field);
        });
    }
}

class TableCell{
    constructor(table_cell){
        Object.assign(
            this,
            {
                value: '',
                label: '',
                type: 'text'
            },
            table_cell
        )
    }
    static ArrayToTableCells(table_cells_array = []){
        return table_cells_array.map(table_item => {
            return new TableItem(table_item);
        });
    }
}

class Table {
    constructor(table) {

    }
}

class CRUDPageStructure {


}