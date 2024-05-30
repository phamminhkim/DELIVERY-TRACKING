<template>
    <div>
        <b-table responsive small hover :fields="fields" :items="items" head-variant="light"
        :filter="query">
            <template #cell(index)="data">
                {{  data.index + 1 }}
            </template>
            <template #cell(sap_so)="data">
                <a class="link-item" @click="getUrl(data.item)" >{{ data.item.sap_so }}</a>
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
        getUrl(item) {
            const url = window.location.origin + this.$route.path + '#' + item.id + '?so_key=' + item.so_key;
            window.open(url, '_blank');
        },
    }
}
</script>
<style lang="scss" scoped>

</style>