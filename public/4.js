(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[4],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @riophae/vue-treeselect */ "./node_modules/@riophae/vue-treeselect/dist/vue-treeselect.cjs.js");
/* harmony import */ var _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _riophae_vue_treeselect_dist_vue_treeselect_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @riophae/vue-treeselect/dist/vue-treeselect.css */ "./node_modules/@riophae/vue-treeselect/dist/vue-treeselect.css");
/* harmony import */ var _riophae_vue_treeselect_dist_vue_treeselect_css__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_riophae_vue_treeselect_dist_vue_treeselect_css__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _review_SOReviewMail_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../review/SOReviewMail.vue */ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);
function _typeof(o) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, _typeof(o);
}
function _regeneratorRuntime() {
  "use strict";

  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */
  _regeneratorRuntime = function _regeneratorRuntime() {
    return e;
  };
  var t,
    e = {},
    r = Object.prototype,
    n = r.hasOwnProperty,
    o = Object.defineProperty || function (t, e, r) {
      t[e] = r.value;
    },
    i = "function" == typeof Symbol ? Symbol : {},
    a = i.iterator || "@@iterator",
    c = i.asyncIterator || "@@asyncIterator",
    u = i.toStringTag || "@@toStringTag";
  function define(t, e, r) {
    return Object.defineProperty(t, e, {
      value: r,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }), t[e];
  }
  try {
    define({}, "");
  } catch (t) {
    define = function define(t, e, r) {
      return t[e] = r;
    };
  }
  function wrap(t, e, r, n) {
    var i = e && e.prototype instanceof Generator ? e : Generator,
      a = Object.create(i.prototype),
      c = new Context(n || []);
    return o(a, "_invoke", {
      value: makeInvokeMethod(t, r, c)
    }), a;
  }
  function tryCatch(t, e, r) {
    try {
      return {
        type: "normal",
        arg: t.call(e, r)
      };
    } catch (t) {
      return {
        type: "throw",
        arg: t
      };
    }
  }
  e.wrap = wrap;
  var h = "suspendedStart",
    l = "suspendedYield",
    f = "executing",
    s = "completed",
    y = {};
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}
  var p = {};
  define(p, a, function () {
    return this;
  });
  var d = Object.getPrototypeOf,
    v = d && d(d(values([])));
  v && v !== r && n.call(v, a) && (p = v);
  var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p);
  function defineIteratorMethods(t) {
    ["next", "throw", "return"].forEach(function (e) {
      define(t, e, function (t) {
        return this._invoke(e, t);
      });
    });
  }
  function AsyncIterator(t, e) {
    function invoke(r, o, i, a) {
      var c = tryCatch(t[r], t, o);
      if ("throw" !== c.type) {
        var u = c.arg,
          h = u.value;
        return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) {
          invoke("next", t, i, a);
        }, function (t) {
          invoke("throw", t, i, a);
        }) : e.resolve(h).then(function (t) {
          u.value = t, i(u);
        }, function (t) {
          return invoke("throw", t, i, a);
        });
      }
      a(c.arg);
    }
    var r;
    o(this, "_invoke", {
      value: function value(t, n) {
        function callInvokeWithMethodAndArg() {
          return new e(function (e, r) {
            invoke(t, n, e, r);
          });
        }
        return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
      }
    });
  }
  function makeInvokeMethod(e, r, n) {
    var o = h;
    return function (i, a) {
      if (o === f) throw Error("Generator is already running");
      if (o === s) {
        if ("throw" === i) throw a;
        return {
          value: t,
          done: !0
        };
      }
      for (n.method = i, n.arg = a;;) {
        var c = n.delegate;
        if (c) {
          var u = maybeInvokeDelegate(c, n);
          if (u) {
            if (u === y) continue;
            return u;
          }
        }
        if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) {
          if (o === h) throw o = s, n.arg;
          n.dispatchException(n.arg);
        } else "return" === n.method && n.abrupt("return", n.arg);
        o = f;
        var p = tryCatch(e, r, n);
        if ("normal" === p.type) {
          if (o = n.done ? s : l, p.arg === y) continue;
          return {
            value: p.arg,
            done: n.done
          };
        }
        "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg);
      }
    };
  }
  function maybeInvokeDelegate(e, r) {
    var n = r.method,
      o = e.iterator[n];
    if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y;
    var i = tryCatch(o, e.iterator, r.arg);
    if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y;
    var a = i.arg;
    return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y);
  }
  function pushTryEntry(t) {
    var e = {
      tryLoc: t[0]
    };
    1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e);
  }
  function resetTryEntry(t) {
    var e = t.completion || {};
    e.type = "normal", delete e.arg, t.completion = e;
  }
  function Context(t) {
    this.tryEntries = [{
      tryLoc: "root"
    }], t.forEach(pushTryEntry, this), this.reset(!0);
  }
  function values(e) {
    if (e || "" === e) {
      var r = e[a];
      if (r) return r.call(e);
      if ("function" == typeof e.next) return e;
      if (!isNaN(e.length)) {
        var o = -1,
          i = function next() {
            for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next;
            return next.value = t, next.done = !0, next;
          };
        return i.next = i;
      }
    }
    throw new TypeError(_typeof(e) + " is not iterable");
  }
  return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", {
    value: GeneratorFunctionPrototype,
    configurable: !0
  }), o(GeneratorFunctionPrototype, "constructor", {
    value: GeneratorFunction,
    configurable: !0
  }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) {
    var e = "function" == typeof t && t.constructor;
    return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name));
  }, e.mark = function (t) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t;
  }, e.awrap = function (t) {
    return {
      __await: t
    };
  }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () {
    return this;
  }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) {
    void 0 === i && (i = Promise);
    var a = new AsyncIterator(wrap(t, r, n, o), i);
    return e.isGeneratorFunction(r) ? a : a.next().then(function (t) {
      return t.done ? t.value : a.next();
    });
  }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () {
    return this;
  }), define(g, "toString", function () {
    return "[object Generator]";
  }), e.keys = function (t) {
    var e = Object(t),
      r = [];
    for (var n in e) r.push(n);
    return r.reverse(), function next() {
      for (; r.length;) {
        var t = r.pop();
        if (t in e) return next.value = t, next.done = !1, next;
      }
      return next.done = !0, next;
    };
  }, e.values = values, Context.prototype = {
    constructor: Context,
    reset: function reset(e) {
      if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t);
    },
    stop: function stop() {
      this.done = !0;
      var t = this.tryEntries[0].completion;
      if ("throw" === t.type) throw t.arg;
      return this.rval;
    },
    dispatchException: function dispatchException(e) {
      if (this.done) throw e;
      var r = this;
      function handle(n, o) {
        return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o;
      }
      for (var o = this.tryEntries.length - 1; o >= 0; --o) {
        var i = this.tryEntries[o],
          a = i.completion;
        if ("root" === i.tryLoc) return handle("end");
        if (i.tryLoc <= this.prev) {
          var c = n.call(i, "catchLoc"),
            u = n.call(i, "finallyLoc");
          if (c && u) {
            if (this.prev < i.catchLoc) return handle(i.catchLoc, !0);
            if (this.prev < i.finallyLoc) return handle(i.finallyLoc);
          } else if (c) {
            if (this.prev < i.catchLoc) return handle(i.catchLoc, !0);
          } else {
            if (!u) throw Error("try statement without catch or finally");
            if (this.prev < i.finallyLoc) return handle(i.finallyLoc);
          }
        }
      }
    },
    abrupt: function abrupt(t, e) {
      for (var r = this.tryEntries.length - 1; r >= 0; --r) {
        var o = this.tryEntries[r];
        if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) {
          var i = o;
          break;
        }
      }
      i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null);
      var a = i ? i.completion : {};
      return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a);
    },
    complete: function complete(t, e) {
      if ("throw" === t.type) throw t.arg;
      return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y;
    },
    finish: function finish(t) {
      for (var e = this.tryEntries.length - 1; e >= 0; --e) {
        var r = this.tryEntries[e];
        if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y;
      }
    },
    "catch": function _catch(t) {
      for (var e = this.tryEntries.length - 1; e >= 0; --e) {
        var r = this.tryEntries[e];
        if (r.tryLoc === t) {
          var n = r.completion;
          if ("throw" === n.type) {
            var o = n.arg;
            resetTryEntry(r);
          }
          return o;
        }
      }
      throw Error("illegal catch attempt");
    },
    delegateYield: function delegateYield(e, r, n) {
      return this.delegate = {
        iterator: values(e),
        resultName: r,
        nextLoc: n
      }, "next" === this.method && (this.arg = t), y;
    }
  }, e;
}
function asyncGeneratorStep(n, t, e, r, o, a, c) {
  try {
    var i = n[a](c),
      u = i.value;
  } catch (n) {
    return void e(n);
  }
  i.done ? t(u) : Promise.resolve(u).then(r, o);
}
function _asyncToGenerator(n) {
  return function () {
    var t = this,
      e = arguments;
    return new Promise(function (r, o) {
      var a = n.apply(t, e);
      function _next(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "next", n);
      }
      function _throw(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "throw", n);
      }
      _next(void 0);
    });
  };
}





/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    is_show: {
      type: Boolean,
      "default": false
    },
    so_header: {
      type: Object,
      "default": function _default() {
        return {
          id: -1,
          title: '',
          central_branch: '',
          description: '',
          status: 'pending',
          user_receiver_mailes: [],
          user_receiver_maile_id: -1
        };
      }
    },
    api_handler: {
      type: Object,
      "default": function _default() {
        return {};
      }
    }
  },
  components: {
    Treeselect: _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_0___default.a,
    SOReviewMail: _review_SOReviewMail_vue__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  watch: {
    is_show: function is_show(val) {
      if (val) {
        $('#SODialogSaveHeader').modal('show');
      } else {
        $('#SODialogSaveHeader').modal('hide');
      }
    },
    so_header: {
      handler: function handler(val) {
        this.so_header_data = val;
      },
      deep: true
    }
  },
  data: function data() {
    return {
      is_loading: false,
      so_header_data: {
        id: -1,
        title: '',
        central_branch: '',
        description: '',
        status: 'pending',
        user_receiver_maile_id: -1
      },
      users: [],
      user: {},
      current_time: this.getCurrentTime(),
      // data_trees: [
      //     {
      //         id: 1,
      //         label: 'Phạm Quốc Khanh',
      //     },
      //     {
      //         id: 2,
      //         label: 'Quách Tuyền',

      //     },
      // ],
      api: {
        get_users_processing: '/api/master/users/processing'
      }
    };
  },
  created: function created() {
    this.fetchUsers();
  },
  mounted: function mounted() {
    var _this = this;
    this.timer = setInterval(function () {
      _this.current_time = _this.getCurrentTime(); // Cập nhật thời gian mỗi giây
    }, 1000);
  },
  beforeDestroy: function beforeDestroy() {
    clearInterval(this.timer); // Dọn dẹp interval khi component bị hủy
  },
  methods: {
    getCurrentTime: function getCurrentTime() {
      // let date = new Date();
      // let current_time = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
      // return current_time;
      var now = new Date();
      return now.toISOString().slice(0, 19).replace('T', ' ');
    },
    demoSelect: function demoSelect() {
      var user = this.findUserById(this.so_header_data.user_receiver_maile_id);
      this.user = user;
      console.log(user);
    },
    findUserById: function findUserById(id) {
      return Object(lodash__WEBPACK_IMPORTED_MODULE_4__["find"])(this.users, {
        id: id
      });
    },
    fetchUsers: function fetchUsers() {
      var _this2 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var _yield$_this2$api_han, data, success, errors, message;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _context.prev = 0;
              _this2.is_loading = true;
              _context.next = 4;
              return _this2.api_handler.get(_this2.api.get_users_processing, {}, {});
            case 4:
              _yield$_this2$api_han = _context.sent;
              data = _yield$_this2$api_han.data;
              success = _yield$_this2$api_han.success;
              errors = _yield$_this2$api_han.errors;
              message = _yield$_this2$api_han.message;
              if (success) {
                _this2.users = data;
              }
              _context.next = 16;
              break;
            case 12:
              _context.prev = 12;
              _context.t0 = _context["catch"](0);
              console.log(_context.t0);
              _this2.$showMessage('error', 'Lỗi', _context.t0);
            case 16:
              _context.prev = 16;
              _this2.is_loading = false;
              return _context.finish(16);
            case 19:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[0, 12, 16, 19]]);
      }))();
    },
    closeModal: function closeModal() {
      this.$emit('close-modal', false);
    },
    saveChanges: function saveChanges(status) {
      this.so_header_data.status = status;
      this.$emit('save-changes', false, this.so_header_data);
    },
    editSaveHeader: function editSaveHeader(status) {
      this.so_header_data.status = status;
      this.$emit('edit-save-header', false, this.so_header_data);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    data: {
      type: Object,
      "default": function _default() {
        return {};
      }
    },
    api_handler: {
      type: Object,
      "default": function _default() {
        return {};
      }
    },
    current_time: {
      type: String,
      "default": ''
    }
  },
  data: function data() {
    return {
      current_user: window.Laravel.current_user
    };
  },
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "modal fade",
    attrs: {
      id: "SODialogSaveHeader",
      "data-backdrop": "static",
      "data-keyboard": "false",
      tabindex: "-1"
    }
  }, [_c("div", {
    staticClass: "modal-dialog modal-xl"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_c("div", {
    staticClass: "modal-header",
    staticStyle: {
      background: "rgb(224 224 224 / 50%)"
    }
  }, [_c("h5", {
    staticClass: "modal-title font-weight-bold text-uppercase text-success"
  }, [_vm.so_header.id !== -1 ? _c("span", [_vm._v("Cập nhật đơn hàng")]) : _c("span", [_vm._v("Thêm mới đơn hàng")])]), _vm._v(" "), _c("button", {
    staticClass: "close",
    attrs: {
      type: "button",
      "data-dismiss": "modal",
      "aria-label": "Close"
    },
    on: {
      click: function click($event) {
        return _vm.closeModal();
      }
    }
  }, [_c("span", {
    attrs: {
      "aria-hidden": "true"
    }
  }, [_vm._v("×")])])]), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_c("div", {
    staticClass: "row"
  }, [_c("div", {
    staticClass: "col-lg-8"
  }, [_c("div", {
    staticClass: "form-group text-xs"
  }, [_c("div", {
    staticClass: "row text-xs"
  }, [_vm._m(0), _vm._v(" "), _c("div", {
    staticClass: "col-lg-10"
  }, [_c("textarea", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.so_header_data.title,
      expression: "so_header_data.title"
    }],
    staticClass: "form-control form-control-sm text-xs",
    attrs: {
      id: "inputSaveName",
      rows: "3"
    },
    domProps: {
      value: _vm.so_header_data.title
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.so_header_data, "title", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("small", {
    staticClass: "text-xs font-italic text-danger"
  }, [_vm._v("(*) không được bỏ trống")])])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group text-xs"
  }, [_c("div", {
    staticClass: "row text-xs"
  }, [_vm._m(1), _vm._v(" "), _c("div", {
    staticClass: "col-lg-10"
  }, [_c("textarea", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.so_header_data.central_branch,
      expression: "so_header_data.central_branch"
    }],
    staticClass: "form-control form-control-sm text-xs",
    attrs: {
      id: "inputSaveMain",
      rows: "3"
    },
    domProps: {
      value: _vm.so_header_data.central_branch
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.so_header_data, "central_branch", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("small", {
    staticClass: "text-xs font-italic text-black-50"
  }, [_vm._v("(có thể bỏ trống)")])])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group text-xs"
  }, [_c("div", {
    staticClass: "row"
  }, [_vm._m(2), _vm._v(" "), _c("div", {
    staticClass: "col-lg-10"
  }, [_c("textarea", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.so_header_data.description,
      expression: "so_header_data.description"
    }],
    staticClass: "form-control form-control-sm text-xs",
    attrs: {
      id: "inputSaveNote",
      rows: "3"
    },
    domProps: {
      value: _vm.so_header_data.description
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.so_header_data, "description", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("small", {
    staticClass: "text-xs font-italic text-black-50"
  }, [_vm._v("(có thể bỏ trống)")])])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group text-xs"
  }, [_c("div", {
    staticClass: "row"
  }, [_vm._m(3), _vm._v(" "), _c("div", {
    staticClass: "col-lg-10"
  }, [_c("select", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.so_header_data.user_receiver_maile_id,
      expression: "so_header_data.user_receiver_maile_id"
    }],
    staticClass: "form-control form-control-sm text-xs",
    on: {
      change: [function ($event) {
        var $$selectedVal = Array.prototype.filter.call($event.target.options, function (o) {
          return o.selected;
        }).map(function (o) {
          var val = "_value" in o ? o._value : o.value;
          return val;
        });
        _vm.$set(_vm.so_header_data, "user_receiver_maile_id", $event.target.multiple ? $$selectedVal : $$selectedVal[0]);
      }, function ($event) {
        return _vm.demoSelect();
      }]
    }
  }, _vm._l(_vm.users, function (user, index) {
    return _c("option", {
      key: index,
      domProps: {
        value: user.id
      }
    }, [_vm._v("\n                                            " + _vm._s(user.name) + " - (" + _vm._s(user.email) + ")\n                                        ")]);
  }), 0), _vm._v(" "), _c("small", {
    staticClass: "text-danger text-xs font-italic"
  }, [_vm._v("(user thuộc bộ phận điều\n                                        phối)")])])])])]), _vm._v(" "), _c("div", {
    staticClass: "col-lg-4 shadow"
  }, [_vm._m(4), _vm._v(" "), _c("div", {
    staticClass: "text-xs"
  }, [_c("span", {
    staticClass: "text-gray"
  }, [_vm._v("Subject: ")]), _vm._v(" "), _c("label", {
    attrs: {
      "for": ""
    }
  }, [_vm._v("\n                                ✨ (Mới) Đơn hàng #... - " + _vm._s(_vm.current_time) + "\n                                ")]), _c("br"), _vm._v(" "), _vm._m(5), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-gray"
  }, [_vm._v("To: '" + _vm._s(_vm.user.email) + "'"), _c("span")])]), _vm._v(" "), _c("div", {
    staticClass: "form-group text-xs h-75 border p-2 rounded"
  }, [_c("SOReviewMail", {
    attrs: {
      data: _vm.so_header_data,
      api_handler: _vm.api_handler,
      current_time: _vm.current_time
    }
  })], 1)])])]), _vm._v(" "), _c("div", {
    staticClass: "modal-footer"
  }, [_c("button", {
    staticClass: "btn btn-secondary btn-sm text-xs px-2",
    attrs: {
      type: "button",
      "data-dismiss": "modal"
    },
    on: {
      click: function click($event) {
        return _vm.closeModal();
      }
    }
  }, [_vm._v("Đóng")]), _vm._v(" "), _vm.so_header.id == -1 ? _c("div", [_c("button", {
    staticClass: "btn btn-success btn-sm text-xs px-4",
    attrs: {
      type: "button"
    },
    on: {
      click: function click($event) {
        return _vm.saveChanges("pending");
      }
    }
  }, [_vm._v("Lưu")]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-info btn-sm text-xs px-4",
    attrs: {
      type: "button"
    },
    on: {
      click: function click($event) {
        return _vm.saveChanges("sending");
      }
    }
  }, [_c("i", {
    staticClass: "fas fa-paper-plane mr-1"
  }), _vm._v("Lưu &\n                        gửi")])]) : _c("div", [_c("button", {
    staticClass: "btn btn-info btn-sm text-xs px-4",
    attrs: {
      type: "button"
    },
    on: {
      click: function click($event) {
        return _vm.editSaveHeader("pending");
      }
    }
  }, [_vm._v("Cập nhật")]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-success btn-sm text-xs px-4",
    attrs: {
      type: "button"
    },
    on: {
      click: function click($event) {
        return _vm.editSaveHeader("sending");
      }
    }
  }, [_c("i", {
    staticClass: "fas fa-paper-plane mr-1"
  }), _vm._v("Cập nhật\n                        &\n                        gửi")])])])])])]);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-lg-2"
  }, [_c("label", {
    staticClass: "col-form-label col-form-label-sm text-xs",
    attrs: {
      "for": "inputSaveName"
    }
  }, [_vm._v("Tiêu\n                                        đề")]), _vm._v(" "), _c("small", {
    staticClass: "text-danger"
  }, [_vm._v("(*)")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-lg-2"
  }, [_c("label", {
    staticClass: "col-form-label col-form-label-sm text-xs",
    attrs: {
      "for": "inputSaveMain"
    }
  }, [_vm._v("Trung\n                                        Tâm")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-lg-2"
  }, [_c("label", {
    staticClass: "col-form-label col-form-label-sm text-xs font-italic font-weight-normal",
    attrs: {
      "for": "inputSaveNote"
    }
  }, [_vm._v("Ghi\n                                        chú")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-lg-2"
  }, [_c("label", {
    staticClass: "col-form-label col-form-label-sm text-xs font-italic font-weight-normal",
    attrs: {
      "for": "inputSaveNote"
    }
  }, [_vm._v("Người\n                                        nhận mail")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "text-xs"
  }, [_c("label", {
    staticClass: "col-form-label col-form-label-sm text-xs font-italic font-weight-normal text-danger",
    attrs: {
      "for": "inputSaveStatus"
    }
  }, [_c("u", [_vm._v("Review thông tin Mail:")])])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("span", {
    staticClass: "text-gray"
  }, [_vm._v("Form: Example 'hello@example.com'"), _c("span")]);
}];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", [_vm._m(0), _vm._v(" "), _c("div", [_c("p", {
    staticClass: "text-primary custom-bd-bt"
  }, [_vm._v("THÔNG TIN ĐƠN HÀNG #... "), _c("small", {
    staticClass: "text-black-50"
  }, [_vm._v("(" + _vm._s(_vm.current_time) + ")")])])]), _vm._v(" "), _c("div", {
    staticClass: "form-group d-flex pl-2"
  }, [_c("div", {
    staticClass: "flex-1"
  }, [_c("label", {
    staticClass: "font-weight-bold"
  }, [_vm._v("Thông tin người gửi")]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-gray"
  }, [_vm._v(_vm._s(_vm.current_user.name))]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-primary"
  }, [_c("u", [_vm._v(_vm._s(_vm.current_user.email))])]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-gray"
  }, [_vm._v(_vm._s(_vm.current_user.phone_number))]), _c("br")]), _vm._v(" "), _c("div", {
    staticClass: "flex-fill ml-2"
  }, [_c("label", {
    staticClass: "font-weight-bold"
  }, [_vm._v("Nội dung")]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-gray"
  }, [_c("b", [_vm._v("Tiêu đề: ")]), _vm._v(_vm._s(_vm.data.title))]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-gray"
  }, [_c("b", [_vm._v("Trung tâm: ")]), _vm._v(_vm._s(_vm.data.central_branch))]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-gray"
  }, [_c("b", [_vm._v("Ghi chú: ")]), _vm._v(_vm._s(_vm.data.description))]), _c("br"), _vm._v(" "), _c("span", {
    staticClass: "text-xs"
  }, [_vm._v("Có gửi File đính kèm")])])]), _vm._v(" "), _vm._m(1)]);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("p", {
    staticClass: "font-weight-bold d-flex"
  }, [_c("img", {
    staticStyle: {
      "margin-right": "10px"
    },
    attrs: {
      src: "https://delivery.thienlong.vn/icon_tl.png",
      width: "5%"
    }
  }), _vm._v(" ĐƠN HÀNG TẠI HỆ\n        THỐNG DELIVERY TRACKING\n    ")]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticStyle: {
      "margin-top": "20px",
      "border-top": "1px solid #ddd",
      "padding-top": "10px"
    }
  }, [_c("p", {
    staticStyle: {
      "font-size": "14px",
      color: "#555"
    }
  }, [_vm._v("\n            Đây là email tự động, vui lòng không trả lời.\n        ")])]);
}];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".custom-bd-bt[data-v-2d1c2c08] {\n  border-bottom: 2px solid #007bff;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../../node_modules/css-loader!../../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue":
/*!**************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue ***!
  \**************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SODialogSaveHeader_vue_vue_type_template_id_3c031b0a_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true& */ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true&");
/* harmony import */ var _SODialogSaveHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SODialogSaveHeader.vue?vue&type=script&lang=js& */ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SODialogSaveHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SODialogSaveHeader_vue_vue_type_template_id_3c031b0a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SODialogSaveHeader_vue_vue_type_template_id_3c031b0a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "3c031b0a",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogSaveHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SODialogSaveHeader.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogSaveHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true&":
/*!*********************************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true& ***!
  \*********************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogSaveHeader_vue_vue_type_template_id_3c031b0a_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogSaveHeader.vue?vue&type=template&id=3c031b0a&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogSaveHeader_vue_vue_type_template_id_3c031b0a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogSaveHeader_vue_vue_type_template_id_3c031b0a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue":
/*!********************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue ***!
  \********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SOReviewMail_vue_vue_type_template_id_2d1c2c08_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true& */ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true&");
/* harmony import */ var _SOReviewMail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SOReviewMail.vue?vue&type=script&lang=js& */ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _SOReviewMail_vue_vue_type_style_index_0_id_2d1c2c08_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true& */ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _SOReviewMail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SOReviewMail_vue_vue_type_template_id_2d1c2c08_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SOReviewMail_vue_vue_type_template_id_2d1c2c08_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "2d1c2c08",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOReviewMail.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true&":
/*!******************************************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true& ***!
  \******************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_style_index_0_id_2d1c2c08_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/style-loader!../../../../../../../../../node_modules/css-loader!../../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=style&index=0&id=2d1c2c08&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_style_index_0_id_2d1c2c08_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_style_index_0_id_2d1c2c08_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_style_index_0_id_2d1c2c08_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_style_index_0_id_2d1c2c08_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true&":
/*!***************************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true& ***!
  \***************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_template_id_2d1c2c08_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/review/SOReviewMail.vue?vue&type=template&id=2d1c2c08&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_template_id_2d1c2c08_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SOReviewMail_vue_vue_type_template_id_2d1c2c08_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);