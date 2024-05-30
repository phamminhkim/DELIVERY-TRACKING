<template>
    <div class="dropdown dropdown-sm mb-1" id="listCols">
        <button class="btn btn-default btn-sm dropdown-toggle" type="button" @click="showHide()" aria-expanded="true">
            <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu" style="overflow:auto;height:500px;display:block" v-if="display">

            <ul class="list-unstyled m-2" id="step1">
                <li v-for="(col, index) in fields" v-bind:key="index" class="todo-item d-flex" draggable="true"
                    @dragstart="dragStart(index, $event)" @dragenter="dragEnter" @dragend="dragEnd"
                    @drop="dragFinish(index, $event)" @dragover.prevent @dragleave="dragLeave">
                    <b-form-checkbox class="custom-b-form-checkbox" v-if="col.label !== ''" @change="changeData(col)"
                        v-model="col.isShow" :draggable="true" :value="true" :unchecked-value="false">
                        <i class="fas fa-grip-vertical" style="cursor:pointer;color:gray"></i>
                        <span style="cursor:pointer">{{ col.label }}</span>
                    </b-form-checkbox>


                </li>
            </ul>

        </div>
    </div>

</template>

<script>
export default {
    props: {

        columns: [],
        eventname: String,
    },
    data() {
        return {
            display: false,
            fields: [...this.columns],
            dragging: -1,
            //  current_step: "",
        }
    },
    watch: {

    },
    methods: {
        dragStart(which, ev) {

            ev.dataTransfer.setData('Text', ev.target.getAttribute('id'));
            ev.dataTransfer.dropEffect = 'move'
            this.dragging = which;
        },
        dragEnter(ev) {
            return true;
        },
        dragLeave(ev) {
        },
        dragEnd(ev) {
            this.dragging = -1
        },

        dragOver(ev) {
            return false;
        },
        dragFinish(to, ev) {
            this.moveItem(this.dragging, to);
            ev.target.style.marginTop = '2px'
            ev.target.style.marginBottom = '2px'
            this.eventUpdate();
        },
        moveItem(from, to, step) {
            if (to === -1) {
                this.removeItemAt(from);

            } else {
                this.fields.splice(to, 0, this.fields.splice(from, 1)[0]);

            }

        },
        removeItemAt(index) {
            this.fields.splice(index, 1);

        },
        eventUpdate() {
            this.$emit(this.eventname, [...this.fields]);
        },
        changeData(col) {
            var count = this.fields.filter(x => x.isShow == true).length;
            if (count > 0) {
                this.eventUpdate();
            } else {
                col.isShow = true;
            }

        },
        showHide(data) {
            this.display = !this.display;

        }
    }
}
</script>

<style lang="scss" scoped>
.todo-item {
    cursor: pointer;
}

::v-deep .custom-b-form-checkbox>.custom-control-label {
    display: flex;
    align-items: baseline;

    &>i {
        margin-right: 5px;
    }
}
</style>