<template>
    <div>
        <b-dropdown menu-class="form-dropdown" id="dropdown-1" size="sm" variant="light" ref="dropdown"
           @show="filterItems(column)"
            toggle-class="text-center rounded p-0 px-1 ml-1">
            <template #button-content class="button">
                <i v-if="case_boolean.is_length_equal" class="fas fa-filter fa-sm"></i>
            </template>
            <b-dropdown-item @click="fieldColumnHeader(column, $event)">Copy
                all</b-dropdown-item>
            <b-dropdown-item @click="filterInventory()">Filter hàng thiếu</b-dropdown-item>
            <div class="input-group input-group-sm mb-1 input-group-custom">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" v-model="case_filter.search" class="form-control" placeholder="Tìm kiếm...">
            </div>
            <div class="form-group filter-scroll" style="width: 260px;">
                <div class="mb-1 px-4 font-normal">
                    <label class="mb-0 d-flex align-items-center">
                        <input v-model="case_checkbox.select_all" type="checkbox" class="mr-1" />
                        <span class="mb-0"> (Select all)</span>

                    </label>
                    <div v-for="(order, index) in filterCaseFilterOrders" :key="index">
                        <label class="mb-0 d-flex align-items-center">
                            <input v-model="case_checkbox.items" :value="order" type="checkbox" class="mr-1" />
                            <span class="mb-0 font-weight-normal"> {{ order ? order : 'null' }}</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group text-right mb-0 footer-fixed-bottom">
                <button @click="emitFilter(column)" :disabled="!case_boolean.is_ok" type="button"
                    class="btn btn-sm btn-light">Ok</button>
                <button @click="changeHide()" type="button" class="btn btn-sm btn-light">Đóng</button>
            </div>
        </b-dropdown>
    </div>
</template>
<script>
export default {
    props: {
        column: String,
        orders: Array,
    },
    data() {
        return {
            case_checkbox: {
                items: [],
                select_all: false,
            },
            case_filter: {
                search: '',
            },
            case_boolean: {
                is_refesh: false,
                is_ok: false,
                is_length_equal: false,
            },
    
        }
    },
    watch: {
        'case_checkbox.select_all': function (val) {
            if (val) {
                if (this.case_checkbox.items.length <= 0 ) {
                    this.case_checkbox.items = this.orders;
                } 
                this.case_checkbox.items = this.orders;
            } 
            else {
                if(!this.case_boolean.is_refesh){
                    this.case_checkbox.items = [];
                }
            }
        },
        'case_checkbox.items': function (val) {
            this.isLengthItems();
            this.isLengthEqual();
        }
    },
    methods: {
        fieldColumnHeader(column, event) {
            this.$emit('fieldColumnHeader', column, event)
        },
        emitFilter(column) {
            this.case_boolean.is_length_equal = true;
            this.isLengthEqual();
            this.$emit('emitFilter', this.case_checkbox.items, column);
            this.changeHide();
        },
        filterItems(column) {
            if(this.case_checkbox.items.length > 0){
                this.case_checkbox.select_all = false;
            } else {
                this.case_checkbox.select_all = true;
            }
            if(this.case_checkbox.items.length == this.orders.length){
                this.case_checkbox.select_all = true;
            }
            this.$emit('filterItems', column);
        },
        changeHide() {
            this.$refs.dropdown.hide(true)
        },
        isLengthItems() {
            if (this.case_checkbox.items.length > 0) {
                this.case_boolean.is_ok = true;
            } else {
                this.case_boolean.is_ok = false;
            }
        },
        isLengthEqual() {
            if (this.case_checkbox.items.length == this.orders.length) {
                this.case_checkbox.select_all = true;
                this.case_boolean.is_length_equal = false;
            }
            else {
                this.case_boolean.is_refesh = true;
                this.case_checkbox.select_all = false;
            }
        },
        filterInventory() {
            this.case_boolean.is_length_equal = true;
            this.$emit('emitFilter', [], 'is_inventory', true);
        },
       

    },
    computed: {
        filterCaseFilterOrders() {
            return this.orders.filter((order) => {
                if (order != null && order != '') {
                    return order.toLowerCase().includes(this.case_filter.search.toLowerCase())
                }
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.filter-scroll {
    overflow-y: auto;
    height: 10rem;
    font-size: 10px;
}

.input-group-custom {
    padding: 0.25rem 1.5rem;
}

::v-deep .form-dropdown {
    overflow-y: scroll;
    top: 30px !important;
    border: 0 !important;
    transform: none !important;
    height: 340px !important;

}

.footer-fixed-bottom {
    position: fixed;
    bottom: 0;
    width: 100%;
}


</style>