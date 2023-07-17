(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[3],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/Clients.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
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
      clients: [],
      clientSecret: null,
      createForm: {
        errors: [],
        name: '',
        redirect: '',
        confidential: true
      },
      editForm: {
        errors: [],
        name: '',
        redirect: ''
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
      this.getClients();
      $('#modal-create-client').on('shown.bs.modal', function () {
        $('#create-client-name').focus();
      });
      $('#modal-edit-client').on('shown.bs.modal', function () {
        $('#edit-client-name').focus();
      });
    },
    /**
     * Get all of the OAuth clients for the user.
     */
    getClients: function getClients() {
      var _this = this;
      axios.get('/oauth/clients').then(function (response) {
        _this.clients = response.data;
      });
    },
    /**
     * Show the form for creating new clients.
     */
    showCreateClientForm: function showCreateClientForm() {
      $('#modal-create-client').modal('show');
    },
    /**
     * Create a new OAuth client for the user.
     */
    store: function store() {
      this.persistClient('post', '/oauth/clients', this.createForm, '#modal-create-client');
    },
    /**
     * Edit the given client.
     */
    edit: function edit(client) {
      this.editForm.id = client.id;
      this.editForm.name = client.name;
      this.editForm.redirect = client.redirect;
      $('#modal-edit-client').modal('show');
    },
    /**
     * Update the client being edited.
     */
    update: function update() {
      this.persistClient('put', '/oauth/clients/' + this.editForm.id, this.editForm, '#modal-edit-client');
    },
    /**
     * Persist the client to storage using the given form.
     */
    persistClient: function persistClient(method, uri, form, modal) {
      var _this2 = this;
      form.errors = [];
      axios[method](uri, form).then(function (response) {
        _this2.getClients();
        form.name = '';
        form.redirect = '';
        form.errors = [];
        $(modal).modal('hide');
        if (response.data.plainSecret) {
          _this2.showClientSecret(response.data.plainSecret);
        }
      })["catch"](function (error) {
        if (_typeof(error.response.data) === 'object') {
          form.errors = _.flatten(_.toArray(error.response.data.errors));
        } else {
          form.errors = ['Something went wrong. Please try again.'];
        }
      });
    },
    /**
     * Show the given client secret to the user.
     */
    showClientSecret: function showClientSecret(clientSecret) {
      this.clientSecret = clientSecret;
      $('#modal-client-secret').modal('show');
    },
    /**
     * Destroy the given client.
     */
    destroy: function destroy(client) {
      var _this3 = this;
      axios["delete"]('/oauth/clients/' + client.id).then(function (response) {
        _this3.getClients();
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=template&id=1552a5b6&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/Clients.vue?vue&type=template&id=1552a5b6&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", [_c("div", {
    staticClass: "card card-default"
  }, [_c("div", {
    staticClass: "card-header"
  }, [_c("div", {
    staticStyle: {
      display: "flex",
      "justify-content": "space-between",
      "align-items": "center"
    }
  }, [_c("span", [_vm._v("\n                    OAuth Clients\n                ")]), _vm._v(" "), _c("a", {
    staticClass: "action-link",
    attrs: {
      tabindex: "-1"
    },
    on: {
      click: _vm.showCreateClientForm
    }
  }, [_vm._v("\n                    Create New Client\n                ")])])]), _vm._v(" "), _c("div", {
    staticClass: "card-body"
  }, [_vm.clients.length === 0 ? _c("p", {
    staticClass: "mb-0"
  }, [_vm._v("\n                You have not created any OAuth clients.\n            ")]) : _vm._e(), _vm._v(" "), _vm.clients.length > 0 ? _c("table", {
    staticClass: "table table-responsive table-borderless mb-0"
  }, [_vm._m(0), _vm._v(" "), _c("tbody", _vm._l(_vm.clients, function (client) {
    return _c("tr", [_c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_vm._v("\n                            " + _vm._s(client.id) + "\n                        ")]), _vm._v(" "), _c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_vm._v("\n                            " + _vm._s(client.name) + "\n                        ")]), _vm._v(" "), _c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_c("code", [_vm._v(_vm._s(client.secret ? client.secret : "-"))])]), _vm._v(" "), _c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_c("a", {
      staticClass: "action-link",
      attrs: {
        tabindex: "-1"
      },
      on: {
        click: function click($event) {
          return _vm.edit(client);
        }
      }
    }, [_vm._v("\n                                Edit\n                            ")])]), _vm._v(" "), _c("td", {
      staticStyle: {
        "vertical-align": "middle"
      }
    }, [_c("a", {
      staticClass: "action-link text-danger",
      on: {
        click: function click($event) {
          return _vm.destroy(client);
        }
      }
    }, [_vm._v("\n                                Delete\n                            ")])])]);
  }), 0)]) : _vm._e()])]), _vm._v(" "), _c("div", {
    staticClass: "modal fade",
    attrs: {
      id: "modal-create-client",
      tabindex: "-1",
      role: "dialog"
    }
  }, [_c("div", {
    staticClass: "modal-dialog"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_vm._m(1), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_vm.createForm.errors.length > 0 ? _c("div", {
    staticClass: "alert alert-danger"
  }, [_vm._m(2), _vm._v(" "), _c("br"), _vm._v(" "), _c("ul", _vm._l(_vm.createForm.errors, function (error) {
    return _c("li", [_vm._v("\n                                " + _vm._s(error) + "\n                            ")]);
  }), 0)]) : _vm._e(), _vm._v(" "), _c("form", {
    attrs: {
      role: "form"
    }
  }, [_c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-3 col-form-label"
  }, [_vm._v("Name")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-9"
  }, [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.createForm.name,
      expression: "createForm.name"
    }],
    staticClass: "form-control",
    attrs: {
      id: "create-client-name",
      type: "text"
    },
    domProps: {
      value: _vm.createForm.name
    },
    on: {
      keyup: function keyup($event) {
        if (!$event.type.indexOf("key") && _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")) return null;
        return _vm.store.apply(null, arguments);
      },
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.createForm, "name", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "form-text text-muted"
  }, [_vm._v("\n                                    Something your users will recognize and trust.\n                                ")])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-3 col-form-label"
  }, [_vm._v("Redirect URL")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-9"
  }, [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.createForm.redirect,
      expression: "createForm.redirect"
    }],
    staticClass: "form-control",
    attrs: {
      type: "text",
      name: "redirect"
    },
    domProps: {
      value: _vm.createForm.redirect
    },
    on: {
      keyup: function keyup($event) {
        if (!$event.type.indexOf("key") && _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")) return null;
        return _vm.store.apply(null, arguments);
      },
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.createForm, "redirect", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "form-text text-muted"
  }, [_vm._v("\n                                    Your application's authorization callback URL.\n                                ")])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-3 col-form-label"
  }, [_vm._v("Confidential")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-9"
  }, [_c("div", {
    staticClass: "checkbox"
  }, [_c("label", [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.createForm.confidential,
      expression: "createForm.confidential"
    }],
    attrs: {
      type: "checkbox"
    },
    domProps: {
      checked: Array.isArray(_vm.createForm.confidential) ? _vm._i(_vm.createForm.confidential, null) > -1 : _vm.createForm.confidential
    },
    on: {
      change: function change($event) {
        var $$a = _vm.createForm.confidential,
          $$el = $event.target,
          $$c = $$el.checked ? true : false;
        if (Array.isArray($$a)) {
          var $$v = null,
            $$i = _vm._i($$a, $$v);
          if ($$el.checked) {
            $$i < 0 && _vm.$set(_vm.createForm, "confidential", $$a.concat([$$v]));
          } else {
            $$i > -1 && _vm.$set(_vm.createForm, "confidential", $$a.slice(0, $$i).concat($$a.slice($$i + 1)));
          }
        } else {
          _vm.$set(_vm.createForm, "confidential", $$c);
        }
      }
    }
  })])]), _vm._v(" "), _c("span", {
    staticClass: "form-text text-muted"
  }, [_vm._v("\n                                    Require the client to authenticate with a secret. Confidential clients can hold credentials in a secure way without exposing them to unauthorized parties. Public applications, such as native desktop or JavaScript SPA applications, are unable to hold secrets securely.\n                                ")])])])])]), _vm._v(" "), _c("div", {
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
      id: "modal-edit-client",
      tabindex: "-1",
      role: "dialog"
    }
  }, [_c("div", {
    staticClass: "modal-dialog"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_vm._m(3), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_vm.editForm.errors.length > 0 ? _c("div", {
    staticClass: "alert alert-danger"
  }, [_vm._m(4), _vm._v(" "), _c("br"), _vm._v(" "), _c("ul", _vm._l(_vm.editForm.errors, function (error) {
    return _c("li", [_vm._v("\n                                " + _vm._s(error) + "\n                            ")]);
  }), 0)]) : _vm._e(), _vm._v(" "), _c("form", {
    attrs: {
      role: "form"
    }
  }, [_c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-3 col-form-label"
  }, [_vm._v("Name")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-9"
  }, [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.editForm.name,
      expression: "editForm.name"
    }],
    staticClass: "form-control",
    attrs: {
      id: "edit-client-name",
      type: "text"
    },
    domProps: {
      value: _vm.editForm.name
    },
    on: {
      keyup: function keyup($event) {
        if (!$event.type.indexOf("key") && _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")) return null;
        return _vm.update.apply(null, arguments);
      },
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.editForm, "name", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "form-text text-muted"
  }, [_vm._v("\n                                    Something your users will recognize and trust.\n                                ")])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group row"
  }, [_c("label", {
    staticClass: "col-md-3 col-form-label"
  }, [_vm._v("Redirect URL")]), _vm._v(" "), _c("div", {
    staticClass: "col-md-9"
  }, [_c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.editForm.redirect,
      expression: "editForm.redirect"
    }],
    staticClass: "form-control",
    attrs: {
      type: "text",
      name: "redirect"
    },
    domProps: {
      value: _vm.editForm.redirect
    },
    on: {
      keyup: function keyup($event) {
        if (!$event.type.indexOf("key") && _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")) return null;
        return _vm.update.apply(null, arguments);
      },
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.editForm, "redirect", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "form-text text-muted"
  }, [_vm._v("\n                                    Your application's authorization callback URL.\n                                ")])])])])]), _vm._v(" "), _c("div", {
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
      click: _vm.update
    }
  }, [_vm._v("\n                        Save Changes\n                    ")])])])])]), _vm._v(" "), _c("div", {
    staticClass: "modal fade",
    attrs: {
      id: "modal-client-secret",
      tabindex: "-1",
      role: "dialog"
    }
  }, [_c("div", {
    staticClass: "modal-dialog"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_vm._m(5), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_c("p", [_vm._v("\n                        Here is your new client secret. This is the only time it will be shown so don't lose it!\n                        You may now use this secret to make API requests.\n                    ")]), _vm._v(" "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.clientSecret,
      expression: "clientSecret"
    }],
    staticClass: "form-control",
    attrs: {
      type: "text"
    },
    domProps: {
      value: _vm.clientSecret
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.clientSecret = $event.target.value;
      }
    }
  })]), _vm._v(" "), _vm._m(6)])])])]);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("thead", [_c("tr", [_c("th", [_vm._v("Client ID")]), _vm._v(" "), _c("th", [_vm._v("Name")]), _vm._v(" "), _c("th", [_vm._v("Secret")]), _vm._v(" "), _c("th"), _vm._v(" "), _c("th")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "modal-header"
  }, [_c("h4", {
    staticClass: "modal-title"
  }, [_vm._v("\n                        Create Client\n                    ")]), _vm._v(" "), _c("button", {
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
  }, [_vm._v("\n                        Edit Client\n                    ")]), _vm._v(" "), _c("button", {
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
  }, [_vm._v("\n                        Client Secret\n                    ")]), _vm._v(" "), _c("button", {
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

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.action-link[data-v-1552a5b6] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css&");

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

/***/ "./resources/js/components/passport/Clients.vue":
/*!******************************************************!*\
  !*** ./resources/js/components/passport/Clients.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Clients_vue_vue_type_template_id_1552a5b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Clients.vue?vue&type=template&id=1552a5b6&scoped=true& */ "./resources/js/components/passport/Clients.vue?vue&type=template&id=1552a5b6&scoped=true&");
/* harmony import */ var _Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Clients.vue?vue&type=script&lang=js& */ "./resources/js/components/passport/Clients.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Clients_vue_vue_type_style_index_0_id_1552a5b6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css& */ "./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Clients_vue_vue_type_template_id_1552a5b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Clients_vue_vue_type_template_id_1552a5b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "1552a5b6",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/passport/Clients.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/passport/Clients.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/components/passport/Clients.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css&":
/*!***************************************************************************************************************!*\
  !*** ./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_1552a5b6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=style&index=0&id=1552a5b6&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_1552a5b6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_1552a5b6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_1552a5b6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_1552a5b6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/components/passport/Clients.vue?vue&type=template&id=1552a5b6&scoped=true&":
/*!*************************************************************************************************!*\
  !*** ./resources/js/components/passport/Clients.vue?vue&type=template&id=1552a5b6&scoped=true& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_1552a5b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=template&id=1552a5b6&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/passport/Clients.vue?vue&type=template&id=1552a5b6&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_1552a5b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_1552a5b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);