<template>
    <div>
        <div class="form-group mb-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex">
                        <div class="d-flex">
                            <b-dropdown size="sm" split text="Chọn màu nền" class="m-2" variant="light">
                                <div class="form-group">
                                    <div class="color-box mr-2 ml-2 mb-1 d-inline-block float-left"
                                        v-for="(color, index) in theme_colors" :key="index" :style="{
                                            backgroundColor: color.color,
                                            border: color.color == '#FFFFFF' ? '1px solid #000' : '',
                                            width: '15px', height: '15px'
                                        }" @click="emitBackgroundColor(color)">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center float-left mt-3 w-100"
                                    @click="emitBackgroundColor(theme_color_default)" style="cursor: pointer;">
                                    <div class="color-box mr-2 ml-2 mb-1 " :style="{
                                            backgroundColor: '#dddddd',
                                            border: '4px solid #000',
                                            width: '25px', height: '25px',
                                            opacity: 0.3,

                                        }"></div>
                                    <div class="text-black-50">
                                        <small><u>None</u></small>
                                    </div>
                                </div>
                            </b-dropdown>
                        </div>
                        <div class="d-flex">
                            <b-dropdown size="sm" split text="Chọn màu text" class="m-2" variant="light">
                                <div class="form-group">
                                    <div class="color-box mr-2 ml-2 mb-1 d-inline-block float-left"
                                        v-for="(color, index) in theme_colors" :key="index" :style="{
                                            backgroundColor: color.color,
                                            border: color.color == '#FFFFFF' ? '1px solid #000' : '',
                                            width: '15px', height: '15px'
                                        }" @click="emitTextColor(color)"></div>
                                </div>
                                <div class="d-flex align-items-center float-left mt-3 w-100"
                                    @click="emitTextColor(theme_color_default)" style="cursor: pointer;">
                                    <div class="color-box mr-2 ml-2 mb-1 " :style="{
                                            backgroundColor: '#dddddd',
                                            border: '4px solid #000',
                                            width: '25px', height: '25px',
                                            opacity: 0.3,

                                        }"></div>
                                    <div class="text-black-50">
                                        <small><u>None</u></small>
                                    </div>
                                </div>
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
            theme_color_default: {
                name: 'none',
                color: '',
            },
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