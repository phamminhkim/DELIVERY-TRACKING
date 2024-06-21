<template>
    <div>
        <div class="modal fade  " id="modalGetConvertFile" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title font-weight-bold text-uppercase">Lỗi chuyển đổi File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gradient-light">
                        <div class="form-group">
                            <span class="text-info font-italic font-weight-bold">Có tồn tại file chưa hỗ trợ chuyển đổi. Có thể
                                tải các file dưới dạng Excel.</span>
                        </div>
                        <button @click="downloadCsvAll()"
                            class="btn btn-sm btn-light text-primary font-weight-bold shadow-btn border border-primary">
                            <i class="fas fa-file-download mr-2"></i>Download All</button>
                        <div v-if="!objectEmpty()" class="form-group">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr class="font-weight-bold bg-gray-light">
                                        <td>File</td>
                                        <td>Tải file excel</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(value, key) in csv_data" :key="key">
                                        <td>{{ key }}</td>
                                        <td>
                                            <button @click="downloadCsvItem(value, key)"
                                                class="btn btn-sm btn-light text-primary font-weight-bold shadow-btn border border-primary">
                                                <i class="fas fa-file-download mr-2"></i>Download
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        csv_data: {
            type: Object,
            default: () => { }
        }
    },
    data() {
        return {
            dialog: false,
        }
    },

    methods: {
        objectEmpty() {
            return Object.keys(this.csv_data).length === 0;
        },
        downloadCsvAll() {
            for (const [key, value] of Object.entries(this.csv_data)) {
                this.downloadCsvItem(value, key);
            }
        },
        downloadCsvItem(item, key) {
            const csvString = item.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = key + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        },


    }
}
</script>
<style lang="scss" scoped></style>