(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[5],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************/
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
        title: 'Đối tác vận chuyển',
        title_icon: 'fas fa-truck'
      },
      table: {
        table_fields: [{
          key: "code",
          label: "Mã",
          sortable: true,
          "class": "text-nowrap text-center"
        }, {
          key: "name",
          label: "Tên nhà vận chuyển",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "api_url",
          label: "API Url",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "api_key",
          label: "API Key",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "api_secret",
          label: "API Secret",
          sortable: true,
          "class": "text-nowrap"
        }, {
          key: "is_external",
          label: "Phạm vị",
          sortable: true,
          "class": "text-nowrap text-center"
        }, {
          key: "is_active",
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
          target_key: 'is_external',
          type: 'template' // 'text', 'bool', 'number', 'image', 'template'
        }, {
          target_key: 'is_active',
          type: 'template' // 'text', 'bool', 'number', 'image', 'template'
        }, {
          target_key: 'image',
          type: 'image'
        }]
      },
      form: {
        form_name: 'đối tác vận chuyển',
        form_fields: [{
          label: 'Mã của nhà vận chuyển',
          placeholder: 'Nhập mã nhà vận chuyển..',
          key: 'code',
          type: 'text',
          //html input type
          required: true
        }, {
          label: 'Mã của nhà vận chuyển',
          placeholder: 'Nhập tên nhà vận chuyển',
          key: 'name',
          type: 'text',
          required: true
        }, {
          label: 'Api Url',
          placeholder: 'Chỉ nhập nếu có tích hợp API nhà vận chuyển',
          key: 'api_url',
          type: 'url',
          required: false
        }, {
          label: 'Api Key',
          placeholder: 'Chỉ nhập nếu có tích hợp API nhà vận chuyển',
          key: 'api_key',
          type: 'text',
          required: false
        }, {
          label: 'Api Secret',
          placeholder: 'Chỉ nhập nếu có tích hợp API nhà vận chuyển',
          key: 'api_secret',
          type: 'password',
          required: false
        }]
      },
      api_url: '/api/master/delivery-partners'
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=template&id=70090c8a&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=template&id=70090c8a& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
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
      key: "cell(is_external)",
      fn: function fn(data) {
        return [data.item.is_external ? _c("span", {
          staticClass: "badge bg-primary"
        }, [_vm._v("Đối tác ngoài")]) : _c("span", {
          staticClass: "badge bg-info"
        }, [_vm._v("Nội bộ")])];
      }
    }, {
      key: "cell(is_active)",
      fn: function fn(data) {
        return [data.item.is_active == 1 ? _c("span", {
          staticClass: "badge bg-success"
        }, [_vm._v("Đang hoạt động")]) : _vm._e(), _vm._v(" "), data.item.is_active == 0 ? _c("span", {
          staticClass: "badge bg-warning"
        }, [_vm._v("Ngưng hoạt động")]) : _vm._e()];
      }
    }])
  });
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue":
/*!************************************************************************************!*\
  !*** ./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TestCRUDPage_vue_vue_type_template_id_70090c8a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TestCRUDPage.vue?vue&type=template&id=70090c8a& */ "./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=template&id=70090c8a&");
/* harmony import */ var _TestCRUDPage_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TestCRUDPage.vue?vue&type=script&lang=js& */ "./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _TestCRUDPage_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _TestCRUDPage_vue_vue_type_template_id_70090c8a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _TestCRUDPage_vue_vue_type_template_id_70090c8a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/general_components/crud_page/TestCRUDPage.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************!*\
  !*** ./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./TestCRUDPage.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=template&id=70090c8a&":
/*!*******************************************************************************************************************!*\
  !*** ./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=template&id=70090c8a& ***!
  \*******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_vue_vue_type_template_id_70090c8a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./TestCRUDPage.vue?vue&type=template&id=70090c8a& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/general_components/crud_page/TestCRUDPage.vue?vue&type=template&id=70090c8a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_vue_vue_type_template_id_70090c8a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_TestCRUDPage_vue_vue_type_template_id_70090c8a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);