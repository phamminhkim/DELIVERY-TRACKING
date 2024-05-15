<template>
    <div>
        <b-dropdown id="dropdown-left" dropright size="sm" variant="light" name="kim" class="form-dropdown"
            toggle-class="text-center rounded p-0 px-1 border">
            <template #button-content>
                <i class="fas fa-grip-vertical fa-sm"></i>
            </template>
            <b-dropdown-group class="menu">
                <b-dropdown-item @click="btnCopyDeleteRow(index, item)"><i
                        class="fas fa-cut fa-rotate-270 fa-sm mr-2 text-black-50"></i>
                    <u>Cut</u>
                </b-dropdown-item>
                <b-dropdown-item @click="btnCopy(index, item)"><i class="fas fa-copy fa-sm mr-2 text-black-50"></i>
                    <u>Copy</u>
                </b-dropdown-item>
                <b-dropdown-item @click="btnParseCreateRow(index)" v-if="copy"><i
                        class="fas fa-paste fa-sm mr-2 text-black-50"></i>
                    <u>Paste</u>
                </b-dropdown-item>
                <b-dropdown-item @click="btnDuplicateRow(index, item)"><i
                        class="fas fa-clone fa-sm mr-2 text-black-50"></i>
                    <u>Duplicate</u>
                </b-dropdown-item>
            </b-dropdown-group>
        </b-dropdown>
    </div>
</template>
<script>
export default {
    props: {
        index: Number,
        item: Object,
        copy: Boolean,
    },
    data() {
        return {

        }
    },
    methods: {
        btnCopyDeleteRow(index, item) {
            this.$emit('btnCopyDeleteRow', index, item)
        },
        btnCopy(index, item) {
            this.$emit('btnCopy', index, item)
        },
        btnParseCreateRow(index) {
            this.$emit('btnParseCreateRow', index)
        },
        btnDuplicateRow(index, item) {
            this.$emit('btnDuplicateRow', index, item)
        },
    }
}
</script>