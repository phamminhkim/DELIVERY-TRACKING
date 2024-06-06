<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-form-label-sm col-md-4" style="text-align: left" for="">Per page:</label>
                    <div class="col-md-3">
                        <b-form-select size="sm" v-model="paginate.per_page" :options="pageOptions"></b-form-select>
                    </div>
                    <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
                    <div class="col-md-3">
                        <b-pagination v-model="paginate.current_page" :total-rows="rows" :per-page="paginate.per_page"
                            align="fill" size="sm" class="my-0"></b-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        rows: Number,
        paginate: Object,
        pageOptions: Array,
    },
}
</script>
<style lang="scss" scoped></style>
