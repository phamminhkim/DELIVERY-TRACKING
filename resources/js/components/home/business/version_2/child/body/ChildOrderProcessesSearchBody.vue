<template>
    <div>
        <div class="row">
            <div class="col-lg-6">
                <div>
                    <input class="form-control" placeholder="Tìm kiếm..." @input="emitInputSearch($event)" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    methods: {
        emitInputSearch(event) {
            setTimeout(() => {
                this.$emit('inputSearch', event.target.value);
            }, 500);
        }
    },
    computed: {

    }
}
</script>