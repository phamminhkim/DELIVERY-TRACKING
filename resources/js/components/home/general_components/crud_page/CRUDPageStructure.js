class Header {
    constructor(header) {
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
    constructor(table_field) {
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

class TableCell {
    constructor(table_cell) {
        Object.assign(
            this,
            {
                value: '',
                key: '',
                type: 'text' // 'text', 'bool', 'number', 'image'
            },
            table_cell
        )
    }
    static ArrayToTableCells(table_cells_array = []) {
        return table_cells_array.map(table_cell => {
            return new TableCell(table_cell);
        });
    }
}
class Table {
    constructor(table) {
        Object.assign(
            this,
            {
                table_fields: [],
                table_cells: []
            },
            {
                table_fields: TableField.ArrayToTableFields(table?.table_fields),
                table_cells: TableCell.ArrayToTableCells(table?.table_cells)
            }
        )
    }
}

class FormField {
    constructor(form_field){
        Object.assign(
            this,
            {
                label: '',
                type: '', //html input type
                key: '',
                required: true
            },
            form_field
        )
    }
    static ArrayToFormFields(form_fields_array = []){
        return form_fields_array.map(form_field => {
            return new FormField(form_field);
        })
    }
}
class Form {
    constructor(form) {
        Object.assign(
            this,
            {

            },
            form
        )
    }
}


class CRUDPageStructure {


}




export default CRUDPageStructure;