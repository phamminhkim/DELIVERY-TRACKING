(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[5],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _CRUDPage_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CRUDPage.vue */ "./resources/js/components/home/general_components/crud_page/CRUDPage.vue");


/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    Vue: vue__WEBPACK_IMPORTED_MODULE_0___default.a,
    CRUDPage: _CRUDPage_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  data: function data() {
    return {
      page_structure: {}
    };
  },
  created: function created() {
    this.page_structure = {
      header: {
        title: 'Khách hàng',
        title_icon: 'fas fa-truck'
      },
      table: {
        table_fields: [{
          key: "name",
          label: "Tên người dùng",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "email",
          label: "Email",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "phone_number",
          label: "Số điện thoại",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "active",
          label: "Khả dụng",
          sortable: true,
          "class": "text-nowrap text-center"
        }, {
          key: "image",
          label: "Hình ảnh",
          sortable: true,
          "class": "text-nowrap text-center"
        }],
        table_cells: [
        //theo lý thuyết nên có đủ khai báo cho tất cả các cells
        // {...},
        // {...}
        {
          target_key: 'active',
          type: 'template' // 'text', 'bool', 'number', 'image', 'template'
        }, {
          target_key: 'image',
          type: 'image'
        }]
      },
      form: {
        form_name: 'khách hàng',
        form_fields: [{
          label: 'Tên người dùng',
          placeholder: 'Nhập tên người dùng..',
          key: 'name',
          type: 'text',
          //html input type
          required: true
        }, {
          label: 'Email',
          placeholder: 'Nhập email..',
          key: 'email',
          type: 'email',
          required: false
        }, {
          label: 'Số điện thoại',
          placeholder: 'Nhập số điện thoại..',
          key: 'phone_number',
          type: 'text',
          required: false
        }]
      },
      api_url: '/api/master/users'
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=template&id=97509038&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=template&id=97509038& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("CRUDPage", {
    attrs: {
      structure: _vm.page_structure
    },
    scopedSlots: _vm._u([{
      key: "cell(active)",
      fn: function fn(data) {
        return [data.item.active == 1 ? _c("span", {
          staticClass: "badge bg-success"
        }, [_vm._v("Đang hoạt động")]) : _vm._e(), _vm._v(" "), data.item.active == 0 ? _c("span", {
          staticClass: "badge bg-warning"
        }, [_vm._v("Ngưng hoạt động")]) : _vm._e()];
      }
    }])
  });
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue":
/*!******************************************************************************************!*\
  !*** ./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TestCRUDPage_Users_vue_vue_type_template_id_97509038___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TestCRUDPage_Users.vue?vue&type=template&id=97509038& */ "./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=template&id=97509038&");
/* harmony import */ var _TestCRUDPage_Users_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TestCRUDPage_Users.vue?vue&type=script&lang=js& */ "./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _TestCRUDPage_Users_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _TestCRUDPage_Users_vue_vue_type_template_id_97509038___WEBPACK_IMPORTED_MODULE_0__["render"],
  _TestCRUDPage_Users_vue_vue_type_template_id_97509038___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************!*\
  !*** ./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_Users_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./TestCRUDPage_Users.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_Users_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=template&id=97509038&":
/*!*************************************************************************************************************************!*\
  !*** ./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=template&id=97509038& ***!
  \*************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_Users_vue_vue_type_template_id_97509038___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./TestCRUDPage_Users.vue?vue&type=template&id=97509038& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage_Users.vue?vue&type=template&id=97509038&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_Users_vue_vue_type_template_id_97509038___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_Users_vue_vue_type_template_id_97509038___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);