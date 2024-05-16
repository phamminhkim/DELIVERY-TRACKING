<template>
    <div>
        <div class="row">
            <div class="col-lg-12 text-left ">
                <div class="btn-group">
                    <label class="col-form-label-sm text-nowrap mr-1">Per page: </label>
                    <b-form-select size="sm" @input="perPageChange(prop_per_page)" v-model="prop_per_page" :options="prop_page_options">
                    </b-form-select>
                    <b-pagination @input="pageChange(prop_current_page)" v-model="prop_current_page" :total-rows="rows" :per-page="prop_per_page" size="sm"
                        class="ml-1"></b-pagination>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
export default { props: {
        rows: {
            type: Number,
            default: 0
        },
        per_page: {
            type: Number,
            default: 10
        },
        current_page: {
            type: Number,
            default: 0
        },
        page_options: {
            type: Array,
            default: () => {
                return [10, 20, 50, 100, 200, 300, 500]
            }
        },

    },
    data() {
        return {
            prop_current_page: this.current_page,
            prop_per_page: this.per_page,
            prop_page_options: this.page_options
        }
    },
    methods: {
        pageChange() {
            this.$emit('pageChange', this.prop_current_page)
        },
        perPageChange() {
            this.$emit('perPageChange', this.prop_per_page)
        }
    },

}
</script>
<style lang="scss" scoped>
</style>