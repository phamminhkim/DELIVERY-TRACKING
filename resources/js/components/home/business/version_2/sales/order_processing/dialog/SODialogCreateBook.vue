<template>
    <div class="modal fade" id="SODialogCreateBook" data-backdrop="static" data-keyboard="false"  tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-uppercase">Tool thêm nhà sách</h5>
                    <button @click="closeDialog()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <hot-table ref="myHotTableBook" :data="items" :settings="settings">
                        <!-- <hot-column title="STT" :renderer="sttRenderer"></hot-column> -->
                        <hot-column title="Tên_ns" data="key_customer">
                        </hot-column>
                        <hot-column title="Tên nhà sách" data="ten_ns">
                        </hot-column>

                    </hot-table>
                </div>
                <div class="modal-footer">
                    <button @click="closeDialog()" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import ApiHandler from '../../../../../ApiHandler';
import { HotTable, HotColumn } from '@handsontable/vue';
import { ContextMenu } from 'handsontable/plugins/contextMenu';
import { registerAllModules } from 'handsontable/registry';
import 'handsontable/dist/handsontable.full.css';
registerAllModules();

export default {
    components: {
        HotTable,
        HotColumn,
        ContextMenu
    },
    props: {
        is_show_hide_dialog: {
            type: Boolean,
            default: false
        },
        items: {
            type: Array,
            default: () => []
        },
        settings: {
            type: Object,
            default: () => ({
                contextMenu: true,
                // manualRowResize: true,
                // manualColumnResize: true,
                height: '500',
                width: '100%',
                autoWrapRow: true,
                autoWrapCol: true,
                rowHeaders: true,
                colWidths: [100, 250],
                // filters: true,
                // dropdownMenu: true,
                licenseKey: 'non-commercial-and-evaluation',
                columns: [
                    {
                        data: 'key_customer',
                        type: 'text',
                        readOnly: false
                    },
                    {
                        data: 'ten_ns',
                        type: 'text',
                        readOnly: false
                    }
                ],
                colHeaders: [
                    'Tên khách hàng',
                    'Tên nhà sách'
                ],
            })
        }
    },
    watch: {
        is_show_hide_dialog: function (val) {
            if (val) {
                $('#SODialogCreateBook').modal('show');
                this.binding_items = this.items;
                this.$refs.myHotTableBook.hotInstance.loadData(this.binding_items);
            } else {
                $('#SODialogCreateBook').modal('hide');
            }
        }
    },
    data() {
        return {
            dialog: false,
            binding_items: []
        }
    },
    methods: {
        openDialog() {
            this.dialog = true;
        },
        closeDialog() {
            this.$emit('close-dialog', false);
        }
    },
    computed: {
        sttRenderer() {
            return function (instance, td, row, col, prop, value, cellProperties) {
                td.innerHTML = row + 1;
                return td;
            }
        },

    }
}
</script>
<style scoped lang="scss"></style>
