class Header {
    constructor(header) {
        Object.assign(
            this,
            {
                title: 'This is title',
                title_icon: 'fas fa-truck'
            },
            {
                ...header
            }
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
            {
                ...table_field
            }
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
                target_key: '',
                type: 'text' // 'text', 'bool', 'number', 'image', 'template'
            },
            {
                ...table_cell
            }
        )
    }
    static ArrayToTableCells(table_cells_array = []) {
        return table_cells_array?.map(table_cell => {
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
                ...table,
                table_fields: [
                    new TableCell({
                        key: "index",
                        label: "STT",
                        sortable: true,
                        class: "text-nowrap text-center",
                    }), 
                    ...TableField.ArrayToTableFields(table?.table_fields), 
                    new TableCell({
                        key: "action",
                        label: "Hành động",
                        sortable: true,
                        class: "text-nowrap text-center",
                    })
                ],
                table_cells: TableCell.ArrayToTableCells(table?.table_cells)
            }
        )
    }
}

class FormField {
    constructor(form_field) {
        Object.assign(
            this,
            {
                label: '',
                type: 'text', //html input type
                key: '',
                required: true
            },
            {
                ...form_field,
            }
        )
    }
    static ArrayToFormFields(form_fields_array = []) {
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
                form_name: '',
                form_fields: []
            },
            {
                ...form,
                form_fields: FormField.ArrayToFormFields(form?.form_fields)
            }
        )
    }
}


class CRUDPageStructure {
    constructor(crud_page_structure) {
        Object.assign(
            this,
            {
                header: new Header(),
                table: new Table(),
                form: new Form(),
                api_url: '',
            },
            {
                ...crud_page_structure,
                header: new Header(crud_page_structure?.header),
                table: new Table(crud_page_structure?.table),
                form: new Form(crud_page_structure?.form),
            }
        )
    }

}


export default CRUDPageStructure;