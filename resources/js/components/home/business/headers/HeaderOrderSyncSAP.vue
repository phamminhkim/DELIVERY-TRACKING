<template>
    <div>
        <div class="row">
            <div class="col-lg-9">
                <div class="form-group">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-warning btn-sm" @click="is_show_search = !is_show_search"
                            v-b-toggle.collapse-1>
                            <span v-if="!is_show_search">Hiện tìm kiếm</span>
                            <span v-if="is_show_search">Ẩn tìm kiếm</span>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" @click="is_show_search = !is_show_search"
                            v-b-toggle.collapse-1>
                            <i v-if="is_show_search" class="fas fa-angle-up"></i>
                            <i v-else class="fas fa-angle-down"></i>
                        </button>
                    </div>
                    <button class="btn btn-secondary btn-sm ml-1">
                        <i class="fas fa-sync-alt" title="Tải lại"></i>
                    </button>
                </div>
            </div>
        </div>
        <b-collapse id="collapse-1">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Số SO SAP</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Từ ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input type="date" class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Đến ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input type="date" class="form-control form-control-sm" name="" id="" />
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Số PO</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Từ ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input type="date" class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Đến ngày</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input type="date" class="form-control form-control-sm" name="" id="" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Mã KH</label>
                                <div class="col-lg-3 mt-1 mb-1">
                                    <input class="form-control form-control-sm" name="" id="" />
                                </div>
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Tên KH</label>
                                <div class="col-lg-7 mt-1 mb-1">
                                    <select class="form-control form-control-sm" name="" id="">
                                        <option value="">Chọn đơn vị</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-sm col-lg-1 col-form-label text-left text-md-right mt-1"
                                    for="">Nhóm KH</label>
                                <div class="col-lg-11 mt-1 mb-1">
                                    <input class="form-control form-control-sm" name="" id="" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                <div><button type="submit" class="btn btn-warning btn-sm mt-1 mr-2 mb-1">
                                        <i class="fa fa-search"></i>
                                        Tìm kiếm
                                    </button></div>
                                <div><button type="reset" class="btn btn-secondary btn-sm mt-1 mb-1">
                                        <i class="fa fa-reset"></i>
                                        Xóa bộ lọc
                                    </button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </b-collapse>
    </div>
</template>
<script>
export default {
    data() {
        return {
            is_show_search: false,
        }
    },
    methods: {

    },
}
</script>
<style lang="scss" scoped></style>