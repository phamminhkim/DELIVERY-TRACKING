<template>
    <div class="mb-1">
        <button @click="emitListLayout()" class="btn btn-sm btn-primary text-xs px-2">Danh sách cấu hình Layout</button>
        <!-- <div class="card border-0 mr-2 shadow-sm card-cursor" 
        @click="emitListLayout()">
            <div class="card-body border-0 text-center p-2 text-xs">
                <i class="fas fa-disease text-info icon-font-size"></i>
                <p class="mb-0">Layout</p>
                <p class="mb-0 font-italic text-black-50 note-font-size">Danh sách cấu hình Layout</p>
            </div>
        </div> -->
    </div>
</template>
<script>
export default {
   
    data() {
        return {

        }
    },
    methods: {
        emitListLayout() {
            this.$emit('listLayout');
        }
    },

}
</script>
<style lang="scss" scoped>
.icon-font-size {
    font-size: 1.5em;
}
.note-font-size {
    font-size: 0.7em;
}
.card-cursor {
    cursor: pointer;
    &:hover {
        background-color: #f8f9fa;
    }
}
</style>