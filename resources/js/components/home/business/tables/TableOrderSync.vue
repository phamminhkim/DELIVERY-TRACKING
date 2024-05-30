<template>
    <div>
        <b-table responsive small hover :fields="fields" :items="items" head-variant="light"
        :filter="query">
            <template #cell(index)="data">
                {{  data.index + 1 }}
            </template>
    </b-table>
    </div>
</template>
<script>
export default {
    props: {
        fields: Array,
        items: Array,
        query: String
    },
    data() {
        return {

        }
    },
    methods: {
    }
}
</script>
<style lang="scss" scoped>

</style>