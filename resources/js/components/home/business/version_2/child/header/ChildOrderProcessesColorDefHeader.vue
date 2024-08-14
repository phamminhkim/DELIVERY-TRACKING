<template>
    <div>
        <div class="form-group mb-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex">
                        <div class="d-flex">
                            <b-dropdown size="sm" split text="Chọn màu nền" class="m-2" variant="light">
                                <div class="color-box mr-2 ml-2 mb-1 d-inline-block float-left"
                                    v-for="(color, index) in theme_colors" :key="index" :style="{
                                        backgroundColor: color.color,
                                        border: color.color == '#FFFFFF' ? '1px solid #000' : '',
                                        width: '15px', height: '15px'
                                    }" @click="emitBackgroundColor(color)"></div>
                            </b-dropdown>
                        </div>
                        <div class="d-flex">
                            <b-dropdown size="sm" split text="Chọn màu text" class="m-2" variant="light">
                                <div class="color-box mr-2 ml-2 mb-1 d-inline-block float-left" v-for="(color, index) in theme_colors" :key="index" :style="{
                                        backgroundColor: color.color,
                                        border: color.color == '#FFFFFF' ? '1px solid #000' : '',
                                        width: '15px', height: '15px'
                                    }" @click="emitTextColor(color)"></div>
                            </b-dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            theme_colors: [
                {
                    name: 'black',
                    color: '#000000',
                },
                {
                    name: 'white',
                    color: '#FFFFFF',
                },
                {
                    name: 'red',
                    color: '#FF0000',
                },
                {
                    name: 'green',
                    color: '#00FF00',
                },
                {
                    name: 'blue',
                    color: '#0000FF',
                },
                {
                    name: 'yellow',
                    color: '#FFFF00',
                },
                {
                    name: 'gray',
                    color: '#808080',
                },
                {
                    name: 'orange',
                    color: '#FFA500',
                },
                {
                    name: 'purple',
                    color: '#800080',
                },
                {
                    name: 'light-blue',
                    color: '#87CEFA',
                }
            ],
        }
    },
    methods: {
        emitBackgroundColor(color) {
            this.$emit('inputBackgroundColor', color);
        },
        emitTextColor(color) {
            this.$emit('inputTextColor', color);
        },
    }
}
</script>
<style lang="scss" scoped>
.color-box {
    &:hover {
        cursor: pointer;
        border: 1px solid #000;
    }
}
</style>