(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[2],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* harmony default export */ __webpack_exports__["default"] = ({
  /*
   * The component's data.
   */
  data: function data() {
    return {
      accessToken: null,
      tokens: [],
      scopes: [],
      form: {
        name: '',
        scopes: [],
        errors: []
      }
    };
  },
  /**
   * Prepare the component (Vue 1.x).
   */
  ready: function ready() {
    this.prepareComponent();
  },
  /**
   * Prepare the component (Vue 2.x).
   */
  mounted: function mounted() {
    this.prepareComponent();
  },
  methods: {
    /**
     * Prepare the component.
     */
    prepareComponent: function prepareComponent() {
      this.getTokens();
      this.getScopes();
      $('#modal-create-token').on('shown.bs.modal', function () {
        $('#create-token-name').focus();
      });
    },
    /**
     * Get all of the personal access tokens for the user.
     */
    getTokens: function getTokens() {
      var _this = this;
      axios.get('/oauth/personal-access-tokens').then(function (response) {
        _this.tokens = response.data;
      });
    },
    /**
     * Get all of the available scopes.
     */
    getScopes: function getScopes() {
      var _this2 = this;
      axios.get('/oauth/scopes').then(function (response) {
        _this2.scopes = response.data;
      });
    },
    /**
     * Show the form for creating new tokens.
     */
    showCreateTokenForm: function showCreateTokenForm() {
      $('#modal-create-token').modal('show');
    },
    /**
     * Create a new personal access token.
     */
    store: function store() {
      var _this3 = this;
      this.accessToken = null;
      this.form.errors = [];
      axios.post('/oauth/personal-access-tokens', this.form).then(function (response) {
        _this3.form.name = '';
        _this3.form.scopes = [];
        _this3.form.errors = [];
        _this3.tokens.push(response.data.token);
        _this3.showAccessToken(response.data.accessToken);
      })["catch"](function (error) {
        if (_typeof(error.response.data) === 'object') {
          _this3.form.errors = _.flatten(_.toArray(error.response.data.errors));
        } else {
          _this3.form.errors = ['Something went wrong. Please try again.'];
        }
      });
    },
    /**
     * Toggle the given scope in the list of assigned scopes.
     */
    toggleScope: function toggleScope(scope) {
      if (this.scopeIsAssigned(scope)) {
        this.form.scopes = _.reject(this.form.scopes, function (s) {
          return s == scope;
        });
      } else {
        this.form.scopes.push(scope);
      }
    },
    /**
     * Determine if the given scope has been assigned to the token.
     */
    scopeIsAssigned: function scopeIsAssigned(scope) {
      return _.indexOf(this.form.scopes, scope) >= 0;
    },
    /**
     * Show the given access token to the user.
     */
    showAccessToken: function showAccessToken(accessToken) {
      $('#modal-create-token').modal('hide');
      this.accessToken = accessToken;
      $('#modal-access-token').modal('show');
    },
    /**
     * Revoke the given token.
     */
    revoke: function revoke(token) {
      var _this4 = this;
      axios["delete"]('/oauth/personal-access-tokens/' + token.id).then(function (response) {
        _this4.getTokens();
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true&":
/*!**************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", [_c("div", [_c("div", {
    staticClass: "card card-default"
  }, [_c("div", {
    staticClass: "card-header"
  }, [_c("div", {
    staticStyle: {
      display: "flex",
      "justify-content": "space-between",
      "align-items": "center"
    }
  }, [_c("span", [_vm._v("\n                        Personal Access Tokens\n                    ")]), _vm._v(" "), _c("a", {
    staticClass: "action-link",
    attrs: {
      tabindex: "-1"
    },
    on: {
      click: _vm.showCreateTokenForm
    }
  }, [_vm._v("\n                        Create New Token\n                    ")])])]), _vm._v(" "), _c("div", {
    staticClass: "card-body"
  }, [_vm.tokens.length === 0 ? _c("p", {
    staticClass: "mb-0"
  }, [_vm._v("\n                    You have not created any personal access tokens.\n                ")]) : _vm._e(), _vm._v(" "), _vm.tokens.length > 0 ? _c("table", {
    staticClass: "table table-responsive table-borderless mb-0"
  }, [_vm._m(0), _vm._v(" "), _c("tbody", _vm._l(_vm.tokens, function (token) {
    return _c("tr", [_c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_vm._v("\n                                " + _vm._s(token.name) + "\n                            ")]), _vm._v(" "), _c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_c("a", {
      staticClass: "action-link text-danger",
      on: {
        click: function click($event) {
          return _vm.revoke(token);
        }
      }
    }, [_vm._v("\n                                    Delete\n                                ")])])]);
  }), 0)]) : _vm._e()])])]), _vm._v(" "), _c("div", {
    staticClass: "modal fade",
    attrs: {
      id: "modal-create-token",
      tabindex: "-1",
      role: "dialog"
    }
  }, [_c("div", {
    staticClass: "modal-dialog"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_vm._m(1), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_vm.form.errors.length > 0 ? _c("div", {
    staticClass: "alert alert-danger"
  }, [_vm._m(2), _vm._v(" "), _c("br"), _vm._v(" "), _c("ul", _vm._l(_vm.form.errors, function (error) {
    return _c("li", [_vm._v("\n                                " + _vm._s(error) + "\n                            ")]);
  }), 0)]) : _vm._e(), _vm._v(" "), _c("form", {
    attrs: {
      role: "form"
    },
    on: {
      submit: function submit($event) {
        $event.preventDefault();
        return _vm.store.apply(null, arguments);
      }
    }
  }, [_c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-4 col-form-label"
  }, [_vm._v("Name")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-6"
  }, [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.form.name,
      expression: "form.name"
    }],
    staticClass: "form-control",
    attrs: {
      id: "create-token-name",
      type: "text",
      name: "name"
    },
    domProps: {
      value: _vm.form.name
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.form, "name", $event.target.value);
      }
    }
  })])]), _vm._v(" "), _vm.scopes.length > 0 ? _c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-4 col-form-label"
  }, [_vm._v("Scopes")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-6"
  }, _vm._l(_vm.scopes, function (scope) {
    return _c("div", [_c("div", {
      staticClass: "checkbox"
    }, [_c("label", [_c("input", {
      attrs: {
        type: "checkbox"
      },
      domProps: {
        checked: _vm.scopeIsAssigned(scope.id)
      },
      on: {
        click: function click($event) {
          return _vm.toggleScope(scope.id);
        }
      }
    }), _vm._v("\n\n                                                " + _vm._s(scope.id) + "\n                                        ")])])]);
  }), 0)]) : _vm._e()])]), _vm._v(" "), _c("div", {
    staticClass: "modal-footer"
  }, [_c("button", {
    staticClass: "btn btn-secondary",
    attrs: {
      type: "button",
      "data-dismiss": "modal"
    }
  }, [_vm._v("Close")]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-primary",
    attrs: {
      type: "button"
    },
    on: {
      click: _vm.store
    }
  }, [_vm._v("\n                        Create\n                    ")])])])])]), _vm._v(" "), _c("div", {
    staticClass: "modal fade",
    attrs: {
      id: "modal-access-token",
      tabindex: "-1",
      role: "dialog"
    }
  }, [_c("div", {
    staticClass: "modal-dialog"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_vm._m(3), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_c("p", [_vm._v("\n                        Here is your new personal access token. This is the only time it will be shown so don't lose it!\n                        You may now use this token to make API requests.\n                    ")]), _vm._v(" "), _c("textarea", {
    staticClass: "form-control",
    attrs: {
      rows: "10"
    }
  }, [_vm._v(_vm._s(_vm.accessToken))])]), _vm._v(" "), _vm._m(4)])])])]);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("thead", [_c("tr", [_c("th", [_vm._v("Name")]), _vm._v(" "), _c("th")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "modal-header"
  }, [_c("h4", {
    staticClass: "modal-title"
  }, [_vm._v("\n                        Create Token\n                    ")]), _vm._v(" "), _c("button", {
    staticClass: "close",
    attrs: {
      type: "button",
      "data-dismiss": "modal",
      "aria-hidden": "true"
    }
  }, [_vm._v("×")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("p", {
    staticClass: "mb-0"
  }, [_c("strong", [_vm._v("Whoops!")]), _vm._v(" Something went wrong!")]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "modal-header"
  }, [_c("h4", {
    staticClass: "modal-title"
  }, [_vm._v("\n                        Personal Access Token\n                    ")]), _vm._v(" "), _c("button", {
    staticClass: "close",
    attrs: {
      type: "button",
      "data-dismiss": "modal",
      "aria-hidden": "true"
    }
  }, [_vm._v("×")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "modal-footer"
  }, [_c("button", {
    staticClass: "btn btn-secondary",
    attrs: {
      type: "button",
      "data-dismiss": "modal"
    }
  }, [_vm._v("Close")])]);
}];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.action-link[data-v-49962cc0] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./resources/js/components/passport/PersonalAccessTokens.vue":
/*!*******************************************************************!*\
  !*** ./resources/js/components/passport/PersonalAccessTokens.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _PersonalAccessTokens_vue_vue_type_template_id_49962cc0_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true& */ "./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true&");
/* harmony import */ var _PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=script&lang=js& */ "./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _PersonalAccessTokens_vue_vue_type_style_index_0_id_49962cc0_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css& */ "./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _PersonalAccessTokens_vue_vue_type_template_id_49962cc0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _PersonalAccessTokens_vue_vue_type_template_id_49962cc0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "49962cc0",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/passport/PersonalAccessTokens.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css&":
/*!****************************************************************************************************************************!*\
  !*** ./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css& ***!
  \****************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_49962cc0_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=style&index=0&id=49962cc0&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_49962cc0_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_49962cc0_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_49962cc0_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_49962cc0_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true&":
/*!**************************************************************************************************************!*\
  !*** ./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true& ***!
  \**************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_49962cc0_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=49962cc0&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_49962cc0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_49962cc0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);