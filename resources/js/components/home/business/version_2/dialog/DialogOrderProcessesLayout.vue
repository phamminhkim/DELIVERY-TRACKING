<template>
    <div>
        <div class="modal fade" id="DialogOrderProcessesLayout" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Danh sách cấu hình Layout</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 ml-auto">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" placeholder="Tim kiem..." v-model="search">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2"><i
                                                class="fas fa-search"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-xs table-hover">
                                <thead class="bg-light font-weight-bold">
                                    <tr>
                                        <td class="text-center">Stt</td>
                                        <td>Tiêu đề</td>
                                        <td class="text-center">Ẩn/Hiện</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(column, index) in filteredColumns" :key="index">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ column.title }}</td>
                                        <td class="text-center">
                                            <span v-show="!column.visible" class="text-danger">Ẩn</span>
                                            <span v-show="column.visible" class="text-success">Hiện</span>
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
        columns: { type: Array, default: () => [] },
    },

    data() {
        return {
            search: '',
        }
    },
    methods: {

    },
    computed: {
        filteredColumns() {
            return this.columns.filter(column => {
                return column.title.toLowerCase().includes(this.search.toLowerCase());
            });
        }
    }


}
</script>
<style lang="scss" scoped></style>