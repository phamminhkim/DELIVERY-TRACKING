<template>
    <div>
       <div class="d-flex">
          <div class="card border-0 mr-2 shadow-sm card-cursor" v-for="(file, index) in processing_files" :key="index"  >
                <div class="card-body border-0 text-center p-2 text-xs" @click="emitConvertFile(file)">
                    <i :class="['icon-font-size', file.icon, 'text-' + file.color]"></i>
                    <p class="mb-0">{{ file.name }}</p>
                    <p class="mb-0 font-italic text-black-50 note-font-size">{{ file.note }}</p>
                </div>
          </div>
       </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            order: {
                id: 1,
                name: 'Order 1',
                status: 'pending'
            },
            processing_files: [
                {
                    id: 1,
                    name: 'Excel',
                    icon: 'fas fa-file-excel',
                    color: 'success',
                    note: 'Convert Excel',
                    type: 'excel'
                },
                {
                    id: 2,
                    name: 'PDF',
                    icon: 'fas fa-file-pdf',
                    color: 'danger',
                    note: 'Convert PDF',
                    type: 'pdf'
                },
                // {
                //     id: 3,
                //     name: 'Import',
                //     icon: 'fas fa-file-import',
                //     color: 'info',
                //     note: 'Import to Excel',
                //     type: 'import'
                // }
            ]
        }
    },
    methods: {
        emitConvertFile(file) {
            this.$emit('convertFile', file);
        }
    }
}
</script>
<style lang="scss" scoped>
.icon-font-size{
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