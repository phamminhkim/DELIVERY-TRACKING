<template>
    <div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text border-0 font-weight-bold">Xử lý loại đơn hàng</span>
            </div>
            <select class="form-control" v-model="change_file.type" @change="onChangeFile()">
                <option v-for="(type_file, index) in type_files" :key="index" :value="type_file.name"> {{ type_file.name
                    }}</option>
            </select>
        </div>
    </div>
</template>
<script>
export default {
   
    data() {
        return {
            type_files: [
                {
                    name: 'Excel',
                },
                {
                    name: 'PDF'
                }
            ],
            change_file: {
                type: 'Excel'
            }
        }
    },
    methods: {
        onChangeFile() {
            this.$emit('changeFile', this.change_file);
        }
    }
}
</script>
<style lang="scss" scoped></style>