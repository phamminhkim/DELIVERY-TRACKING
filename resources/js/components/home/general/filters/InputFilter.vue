<template>
    <div>
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <input v-model="filter" class="form-control form-control-sm" placeholder="Tìm kiếm..." aria-label="Username"
                aria-describedby="basic-addon1">
        </div>

    </div>
</template>
<script>
export default {

    data() {
        return {
            filter: '',
        }
    },
    watch: {
        filter(){
            this.inputFilter();
        }
    },
    methods: {
        inputFilter(){
            this.$emit('inputFilter', this.filter);
        }
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped></style>
