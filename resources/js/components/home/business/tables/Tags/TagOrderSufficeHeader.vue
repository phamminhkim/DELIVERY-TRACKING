<template>
    <div>
        <b-dropdown menu-class="form-dropdown" id="dropdown-1" size="sm" variant="light" ref="dropdown"
            @show="filterItems(column)" toggle-class="text-center rounded p-0 px-1 ml-1 mr-1">
            <template #button-content class="button">
                <i v-if="case_boolean.is_length_equal" class="fas fa-filter fa-sm"></i>
            </template>
            <b-dropdown-item @click="fieldColumnHeader(column, $event)"><u class="font-weigh-bold">C</u>opy
                all</b-dropdown-item>
            <b-dropdown-divider></b-dropdown-divider>
            <b-dropdown-group id="dropdown-group-1" header="Filter" header-classes="text-left">
                <b-dropdown-item-button @click="filterInventory()" button-class="px-5"><u
                        class="font-weigh-bold">H</u>àng
                    thiếu</b-dropdown-item-button>
                    <!-- <b-dropdown-item-button @click="filterCancleInventory()" button-class="px-5">Reset <u
                        class="font-weigh-bold">H</u>àng
                    thiếu</b-dropdown-item-button> -->
                    <b-dropdown-item-button @click="filterPriceDiffrences()" button-class="px-5">Giá <u
                        class="font-weigh-bold">chênh lệch</u></b-dropdown-item-button>
                        <b-dropdown-item-button @click="filterPriceEqual()" button-class="px-5">Giá <u
                            class="font-weigh-bold">ngang nhau</u></b-dropdown-item-button>
                    <b-dropdown-item-button @click="resetfilter()" button-class="px-5"><u class="font-weight-bold">Reset</u></b-dropdown-item-button>
                <b-dropdown-group id="dropdown-group-1" header="Color" header-classes="text-left px-5">
                    <b-dropdown-item-button @click="filterPromotionCategoryExtraOffer()" button-class="px-5 ml-3">
                        <div class="mr-2 rounded" style="background: rgb(255, 193, 7); height: 20px; width: 3rem;">
                        </div>
                    </b-dropdown-item-button>
                    <b-dropdown-item-button @click="filterPromotionCategoryCombo()" button-class="px-5 ml-3">
                        <div class="mr-2 rounded" style="background: rgb(0, 123, 255); height: 20px; width: 3rem;">
                        </div>
                    </b-dropdown-item-button>
                </b-dropdown-group>
            </b-dropdown-group>
            <b-dropdown-divider></b-dropdown-divider>
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
                            <span class="mb-0 font-weight-normal"> {{ order }}</span>
                            <span v-if="order === null">(Null)</span>
                            <span v-if="order === ''">(Blank)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group text-right mb-0 footer-fixed-bottom">
                <button @click="emitFilter(column)" :disabled="!case_boolean.is_ok" type="button"
                    class="btn btn-sm btn-light">Ok</button>
                <button @click="changeHide(column)" type="button" class="btn btn-sm btn-light">Đóng</button>
            </div>
        </b-dropdown>
    </div>
</template>
<script>
export default {
    props: {
        column: String,
        orders: Array,
        count_reset_filter: Number,
    },
    data() {
        return {
            case_checkbox: {
                items: [],
                select_all: false,
            },
            case_filter: {
                search: '',
                column: '',
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
                if (this.case_checkbox.items.length <= 0) {
                    this.case_checkbox.items = this.filterCaseFilterOrders;
                }
                this.case_checkbox.items = this.filterCaseFilterOrders;
                if(this.case_checkbox.items.length != this.filterCaseFilterOrders.length){
                  thiss.case_checkbox.select_all = false;
                }
            }
            else {
                if (!this.case_boolean.is_refesh) {
                    this.case_checkbox.items = [];
                }
                if (this.case_checkbox.items.length == this.filterCaseFilterOrders.length) {
                    this.case_checkbox.items = [];
                }
                this.case_checkbox.items = [];
            }
        },
        'case_checkbox.items': function (val) {
            this.isLengthItems();
            this.isLengthEqual();
        },
        // count_reset_filter() {
        //     this.resetCaseCheckbox();
        // }
    },
    // hook kiểm tra b-dropdown đang mở hay đóng
    // nếu đang mở thì gửi emit là true
    // nếu đóng thì gửi emit là false
    mounted() {
        this.$refs.dropdown.$on('show', () => {
            this.$emit('showHideDropdown', true);

        });
        this.$refs.dropdown.$on('hide', () => {
            this.$emit('showHideDropdown', false);
        });
    },
    created() {
    },

    methods: {
        fieldColumnHeader(column, event) {
            this.$emit('fieldColumnHeader', column, event)
        },
        emitFilter(column) {
            this.case_boolean.is_length_equal = true;
            this.isLengthEqual();
            if(this.case_filter.search !== '') {
                this.case_checkbox.items = this.filterCaseFilterOrders;
            }
            this.$emit('emitFilter', this.case_checkbox.items, column);
            this.changeHide(column);
        },
        filterItems(column) {
            this.refeshSerach();
            if (this.case_checkbox.items.length > 0) {
                this.case_checkbox.select_all = false;
            } else {
                this.case_checkbox.select_all = true;
            }
            if (this.case_checkbox.items.length == this.filterCaseFilterOrders.length) {
                this.case_checkbox.select_all = true;
            }
            this.$emit('filterItems', column);
            this.$emit('showHideDropdown', true);
        },
        changeHide(column) {
            this.$refs.dropdown.hide(true);
            this.$emit('showHideDropdown', false);

        },
        isLengthItems() {
            if (this.case_checkbox.items.length > 0) {
                this.case_boolean.is_ok = true;
            } else {
                this.case_boolean.is_ok = false;
            }
        },
        isLengthEqual() {
            if (this.case_checkbox.items.length == this.filterCaseFilterOrders.length) {
                this.case_checkbox.select_all = true;
                this.case_boolean.is_length_equal = false;
                // this.case_checkbox.items = this.filterCaseFilterOrders;
            }
            else {
                this.case_boolean.is_refesh = true;
                this.case_checkbox.select_all = false;
            }
        },
        filterInventory() {
            this.case_boolean.is_length_equal = true;
            this.$emit('emitFilter', [true], 'is_inventory', false);
        },
        filterCancleInventory() {
            this.case_boolean.is_length_equal = true;
            this.$emit('emitFilter', ['false'], 'is_inventory', false);
        },
        filterPriceDiffrences() {
            this.case_boolean.is_length_equal = true;
            this.$emit('emitFilter', ['price_difference'], 'difference', false);
        },
        filterPriceEqual() {
            this.case_boolean.is_length_equal = true;
            this.$emit('emitFilter', ['price_equal'], 'difference', false);
        },
        resetfilter() {
            this.case_boolean.is_length_equal = false;
            this.$emit('emitFilter', ['X'], 'reset', false);
        },
        filterPromotionCategoryExtraOffer() {
            this.case_boolean.is_length_equal = true;
            let extra_offers = ['X'];
            this.$emit('emitFilter', extra_offers, 'extra_offer', false);
        },
        filterPromotionCategoryCombo() {
            this.case_boolean.is_length_equal = true;
            let combos = ['X'];
            this.$emit('emitFilter', combos, 'promotion_category', false);
        },
        resetCaseCheckbox() {
            this.case_checkbox.items = [];
            this.case_checkbox.select_all = false;
            this.case_boolean.is_length_equal = false;
            this.$emit('emitResetFilter');
        },
        refeshSerach() {
            // this.case_filter.search = '';
        }

    },
    computed: {
        filterCaseFilterOrders() {
            let news = [...this.orders];
            return news.filter((order) => {
                if (order === null) {
                    return true;
                }
                if (order === '') {
                    return true;
                }
                if (order === false) {
                    return true;
                }
                if (order !== '' && order !== undefined) {
                    order = order.toString();
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
</style>
// .footer-fixed-bottom {
// position: fixed;
// bottom: 0;
// width: 100%;
// }