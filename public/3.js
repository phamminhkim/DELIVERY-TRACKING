(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[3],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {};
  },
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var xlsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! xlsx */ "./node_modules/xlsx/xlsx.mjs");
/* harmony import */ var _ApiHandler__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../../ApiHandler */ "./resources/js/components/home/ApiHandler.js");
/* harmony import */ var _handsontable_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @handsontable/vue */ "./node_modules/@handsontable/vue/es/vue-handsontable.mjs");
/* harmony import */ var handsontable_plugins_contextMenu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! handsontable/plugins/contextMenu */ "./node_modules/handsontable/plugins/contextMenu/index.mjs");
/* harmony import */ var handsontable_registry__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! handsontable/registry */ "./node_modules/handsontable/registry.mjs");
/* harmony import */ var handsontable_dist_handsontable_full_css__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! handsontable/dist/handsontable.full.css */ "./node_modules/handsontable/dist/handsontable.full.css");
/* harmony import */ var handsontable_dist_handsontable_full_css__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(handsontable_dist_handsontable_full_css__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _dialog_SODialogCreateColumn_vue__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../dialog/SODialogCreateColumn.vue */ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue");
/* harmony import */ var handsontable_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! handsontable/i18n */ "./node_modules/handsontable/i18n/index.mjs");
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
function _toConsumableArray(r) {
  return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread();
}
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}
function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return _arrayLikeToArray(r);
}
function _slicedToArray(r, e) {
  return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest();
}
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return _arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0;
  }
}
function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}








Object(handsontable_registry__WEBPACK_IMPORTED_MODULE_4__["registerAllModules"])();
/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    HotTable: _handsontable_vue__WEBPACK_IMPORTED_MODULE_2__["HotTable"],
    HotColumn: _handsontable_vue__WEBPACK_IMPORTED_MODULE_2__["HotColumn"],
    SODialogCreateColumn: _dialog_SODialogCreateColumn_vue__WEBPACK_IMPORTED_MODULE_6__["default"]
  },
  props: {
    prop_items: {
      type: Array,
      "default": function _default() {
        return [];
      }
    },
    step: {
      type: Number,
      "default": 0
    }
  },
  watch: {
    step: {
      handler: function handler(val) {
        if (val === 3) {
          this.fetchData();
        }
      },
      immediate: true
    }
  },
  data: function data() {
    var _this = this;
    return {
      is_loading: false,
      api_handler: new _ApiHandler__WEBPACK_IMPORTED_MODULE_1__["default"](window.Laravel.access_token),
      is_show_modal_create_column: false,
      settings: {
        height: '500',
        width: '100%',
        manualColumnResize: true,
        autoWrapRow: true,
        autoWrapCol: true,
        rowHeaders: true,
        colHeaders: true,
        afterChange: this.handleAfterChange,
        stretchH: 'all',
        hiddenColumns: {
          columns: [9],
          // Chỉ số cột `is_specifications`
          indicators: false // Không hiển thị chỉ báo ẩn
        },
        cells: function cells(row, col, prop) {
          var cellProperties = {};
          // Kiểm tra nếu row là hàng 3 (chỉ số 2 do đếm từ 0)
          // const colorField = `${prop}_color`; // Tìm field lưu thông tin màu
          // const colorInfo = this.instance.getDataAtRowProp(row, colorField); // Lấy thông tin màu
          // const colorInfo = this.data_api.items[row][colorField]; // Lấy thông tin màu
          switch (row) {
            case 0:
              if (col >= 8) {
                cellProperties.className = "highlight-row-blue";
              }
              if (col === 7) {
                cellProperties.className = "text-danger";
              }
              break;
            case 1:
              if (col === 7) {
                cellProperties.className = "text-right font-weight-bold bg-yellow";
              }
              break;
            case 2:
              if (col >= 8) {
                cellProperties.className = "highlight-row-lightblue";
              }
              if (col === 7) {
                cellProperties.className = "bg-yellow";
              }
              break;
            case 3:
              if (col <= 8) {
                cellProperties.className = "highlight-row";
              }
              break;
            default:
              break;
          }
          if (row > 3) {
            switch (col) {
              case 0:
                cellProperties.type = 'dropdown'; // Đặt kiểu dữ liệu là dropdown
                cellProperties.source = _this.setting_categories.source;
                break;
              case 7:
                cellProperties.className = "text-success";
                break;
              case 8:
                cellProperties.className = "text-success";
                break;
              default:
                break;
            }
            // if (colorInfo !== undefined) {
            //     console.log(colorInfo, 'colorInfo');
            //     // cellProperties.style = `background-color: ${colorInfo.background}; color: ${colorInfo.text}`;
            //     // css cellProperties
            //     cellProperties.renderer = function (instance, td, row, col, prop, value) {
            //         console.log(instance, 'instance', td, row, col, prop, value);
            //         // this.instance = instance;
            //         td.style.backgroundColor = colorInfo.background || ""; // Áp dụng màu nền
            //         td.style.color = colorInfo.text || ""; // Áp dụng màu chữ
            //         // return td;
            //     };

            // }
          } else {
            if (row <= 2 && col <= 6) {
              cellProperties.className = "border-transparent";
            }
            if (col <= 8) {
              cellProperties.readOnly = true;
            }
          }
          return cellProperties;
        },
        afterRenderer: function afterRenderer(TD, row, col, prop, value, cellProperties) {
          // console.log(TD, row, col, prop, value, cellProperties);
          if (row > 3) {
            var _this$data_api$items$;
            var is_field_color = (_this$data_api$items$ = _this.data_api.items[row].color) !== null && _this$data_api$items$ !== void 0 ? _this$data_api$items$ : false;
            if (is_field_color) {
              // console.log('is_field_color', is_field_color); // Hàm này luôn luôn render
              var colorInfo = is_field_color.background[prop]; // Lấy thông tin màu
              var color_info_text = is_field_color.text[prop];
              if (colorInfo !== undefined) {
                TD.style.background = colorInfo;
              }
              if (color_info_text !== undefined) {
                TD.style.color = color_info_text == '' ? 'black' : color_info_text;
              }
            }
          }
        },
        // hiddenColumns: {
        //     columns: [0], // Chỉ số cột `is_specifications`
        //     indicators: false, // Không hiển thị chỉ báo ẩn
        // },
        dropdownMenu: {
          items: {
            filter_by_value: {
              name: 'Tìm kiếm'
            },
            filter_action_bar: {}
          }
        },
        filters: true,
        contextMenu: {
          items: {
            "export": {
              name: 'Xuất Excel',
              callback: function callback(key, options) {
                this.getPlugin('exportFile').downloadFile('csv', {
                  filename: 'Data xử lý'
                });
              }
            },
            row_above: {
              name: 'Thêm dòng phía trên'
            },
            row_below: {
              name: 'Thêm dòng phía dưới'
            },
            remove_row: {
              name: 'Xóa dòng'
            },
            separator: handsontable_plugins_contextMenu__WEBPACK_IMPORTED_MODULE_3__["ContextMenu"].SEPARATOR,
            // Custom menu item
            create_column: {
              name: 'Tạo cột',
              callback: function callback(key, options) {
                // this.addColumn();
                _this.showModal = true;
              }
            },
            // // Custom menu item
            // create_row: {
            //     name: 'Tạo dòng',
            //     callback: (key, options) => {
            //         this.addColumnDefault();
            //     },
            // },
            // Custom menu item Xòa cột tùy chỉnh
            remove_column: {
              name: 'Xóa cột',
              callback: function callback(key, options) {
                // this.addColumn();
                // this.showModal = true;
                // lấy vị trí cột đang selectRanges
                var selectedRange = _this.$refs.myHotTableSecond.hotInstance.getSelectedRange();
                selectedRange.map(function (range) {
                  var col = range.from.col;
                  var colEnd = range.to.col;
                  var field = Object.keys(_this.data_api.items[3])[col];
                  _this.data_api.items.map(function (item, index) {
                    delete item[field];
                  });
                  _this.loadDataHandsontable();
                });
                // console.log(selectedRange);
              }
            },
            color_background: {
              name: 'Tô màu nền',
              submenu: {
                items: this.getColorItems('color_background')
              }
            },
            color_text: {
              name: 'Tô màu chữ',
              submenu: {
                items: this.getColorItems('color_text')
              }
            }
          }
        },
        licenseKey: 'non-commercial-and-evaluation'
      },
      setting_types: {
        type: 'dropdown',
        source: ['_GK', '_Hộp'],
        strict: true
      },
      api: {
        so_processing_data: 'api/sales-order/so-processing-data',
        material_category: 'api/master/material-category'
      },
      error: {
        indexs: [],
        message: ''
      },
      data_api: {
        items: [{
          barcode: '',
          price_vat: '',
          promotion: '',
          sap_code: '',
          sap_name: '',
          specifications: '',
          sum_quantity: 'T_Tiền (-VAT)',
          tax: 'Số lượng NS',
          unit: ''
        }, {
          barcode: '',
          price_vat: '',
          promotion: '',
          sap_code: '',
          sap_name: '',
          specifications: '',
          sum_quantity: 'Ký hiệu ĐH',
          tax: '',
          unit: ''
        }, {
          barcode: 'Mã vạch',
          price_vat: 'Giá chưa VAT',
          promotion: 'Phân loại',
          sap_code: 'Mã SAP',
          sap_name: 'Tên sản phẩm',
          specifications: 'Quy cách',
          sum_quantity: 'Tổng SL',
          tax: 'Thuế',
          unit: ''
        }],
        headers: [],
        material_categories: []
      },
      setting_categories: {
        source: [],
        strict: true
      },
      showModal: false,
      newColumnName: "",
      cols: [{
        value: ""
      }],
      color_items: [{
        key: 'colors:red',
        name: '<div style="background: red;width: 50%;" >Red</div> ',
        callback: function callback(key, options) {
          _this.demoColor('red');
        }
      }, {
        key: 'colors:blue',
        name: 'Blue',
        callback: function callback(key, options) {
          _this.demoColor('blue');
        }
      }, {
        key: 'colors:green',
        name: 'Green',
        callback: function callback(key, options) {
          _this.demoColor('green');
        }
      }]
    };
  },
  created: function created() {
    this.fetchMaterialCategories();
  },
  methods: {
    getDemoClickModal: function getDemoClickModal() {
      console.log('click modal');
      this.$emit('click-modal');
    },
    getColorItems: function getColorItems(theme) {
      var _this2 = this;
      var colors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'gray'];
      return colors.map(function (color) {
        return {
          key: "".concat(theme, ":").concat(color),
          name: "<div style=\"background: ".concat(color, "; width: 100%; height: 15px;\"></div>"),
          callback: function callback() {
            return _this2.demoColor(color, theme);
          }
        };
      });
    },
    // demoColor(text_color, theme) {
    //     const selectedRange = this.$refs.myHotTableSecond.hotInstance.getSelectedRange();
    //     const field_color = theme == 'color_background' ? 'background' : 'text';
    //     if (selectedRange) {
    //         selectedRange.forEach((range) => {
    //             const startRow = range.from.row;
    //             const endRow = range.to.row;
    //             const startCol = range.from.col;
    //             const endCol = range.to.col;
    //             for (let row = startRow; row <= endRow; row++) {
    //                 for (let col = startCol; col <= endCol; col++) {
    //                     const field = Object.keys(this.data_api.items[row])[col];
    //                     console.log(field, 'field');
    //                     console.log(this.data_api.items, 'color', row);
    //                     this.data_api.items[row].color[field_color][field] = text_color;
    //                     console.log(this.data_api.items[row].color[field_color][field], 'color');
    //                 }
    //             }
    //         });
    //         this.loadDataHandsontable();
    //     }
    // },
    // demoColor(text_color, theme) {
    //     const selectedRange = this.$refs.myHotTableSecond.hotInstance.getSelectedRange();
    //     const sourceData = this.$refs.myHotTableSecond.hotInstance.getSourceData();
    //     const getData = this.$refs.myHotTableSecond.hotInstance.getData();
    //     const field_color = theme === 'color_background' ? 'background' : 'text';
    //     if (sourceData.length === getData.length) {
    //         if (selectedRange) {
    //             selectedRange.forEach((range) => {
    //                 const startRow = range.from.row;
    //                 const endRow = range.to.row;
    //                 const startCol = range.from.col;
    //                 const endCol = range.to.col;
    //                 for (let row = startRow; row <= endRow; row++) {
    //                     for (let col = startCol; col <= endCol; col++) {
    //                         const field = Object.keys(sourceData[row])[col];
    //                         if (!sourceData[row].color) {
    //                             this.$set(sourceData[row], 'color', { background: {}, text: {} });
    //                         }
    //                         this.$set(sourceData[row].color[field_color], field, text_color);
    //                     }
    //                 }
    //             });
    //             this.$refs.myHotTableSecond.hotInstance.render(); // Render lại Handsontable
    //         }
    //     } else {
    //         if (selectedRange) {
    //             selectedRange.forEach((range) => {
    //                 const startRow = range.from.row;
    //                 const endRow = range.to.row;
    //                 const startCol = range.from.col;
    //                 const endCol = range.to.col;
    //                 for (let row = startRow; row <= endRow; row++) {
    //                     for (let col = startCol; col <= endCol; col++) {
    //                         const field = Object.keys(getData[row])[col];
    //                         if (!getData[row].color) {
    //                             this.$set(getData[row], 'color', { background: {}, text: {} });
    //                         }
    //                         this.$set(getData[row].color[field_color], field, text_color);
    //                     }
    //                 }
    //             });
    //             this.$refs.myHotTableSecond.hotInstance.render(); // Render lại Handsontable
    //         }
    //     }
    // },
    demoColor: function demoColor(text_color, theme) {
      var _this3 = this;
      var hotInstance = this.$refs.myHotTableSecond.hotInstance;
      var selectedRange = hotInstance.getSelectedRange();
      var sourceData = hotInstance.getSourceData(); // Dữ liệu gốc
      var field_color = theme === 'color_background' ? 'background' : 'text';
      if (selectedRange) {
        selectedRange.forEach(function (range) {
          var startRow = range.from.row;
          var endRow = range.to.row;
          var startCol = range.from.col;
          var endCol = range.to.col;
          for (var row = startRow; row <= endRow; row++) {
            for (var col = startCol; col <= endCol; col++) {
              // Lấy tên cột từ vị trí cột hiển thị
              var field = hotInstance.colToProp(col);

              // Map từ dữ liệu hiển thị sang dữ liệu gốc
              var visualRowIndex = hotInstance.toPhysicalRow(row);
              console.log(visualRowIndex, 'visualRowIndex', row, 'row');
              if (visualRowIndex !== null && visualRowIndex !== undefined) {
                var rowData = sourceData[visualRowIndex];
                if (!rowData.color) {
                  _this3.$set(rowData, 'color', {
                    background: {},
                    text: {}
                  });
                }
                // Gán màu cho dữ liệu gốc
                _this3.$set(rowData.color[field_color], field, text_color);
                // Cập nhật màu hiển thị cho displayedData
                var displayedData = hotInstance.getDataAtRow(row); // Dữ liệu hiển thị (sau filter)
                // const displayRow = displayedData[row];
                if (!displayedData[9].color) {
                  _this3.$set(displayedData[9], 'color', {
                    background: {},
                    text: {}
                  });
                }
                _this3.$set(displayedData[9].color[field_color], field, text_color);
                console.log(displayedData, 'displayedData');
              }
            }
          }
        });

        // Render lại bảng để hiển thị màu
        hotInstance.render();
      }
    },
    handleAfterChange: function handleAfterChange(changes, source) {
      var _this4 = this;
      if (!changes) return;
      var calculator = {};
      var skipFields = this.skipFields();

      // Tạo object calculator cho từng cột được chỉnh sửa
      changes.forEach(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 4),
          row = _ref2[0],
          prop = _ref2[1],
          oldValue = _ref2[2],
          newValue = _ref2[3];
        if (!skipFields.includes(prop)) {
          calculator[prop] = {
            sum_price_vat: 0,
            sum_quantity: 0,
            index_sum: 0,
            total_price_vat: 0
          };
        }
      });

      // Tính toán lại giá trị cho các cột được chỉnh sửa
      Object.keys(calculator).forEach(function (column) {
        var sum_price_vat = 0;
        var sum_quantity = 0;
        var index_sum = 0;
        _this4.data_api.items.forEach(function (item, index) {
          if (index > 3 && item[column] != null) {
            sum_price_vat += parseFloat(item.price_vat) || 0;
            sum_quantity += parseFloat(item[column]) || 0;
            index_sum++;
            item.sum_quantity = Object.keys(item).filter(function (key) {
              return !skipFields.includes(key);
            }) // Loại bỏ các field không cần thiết
            .reduce(function (sum, key) {
              var value = parseFloat(item[key]) || 0; // Lấy giá trị số hoặc mặc định là 0
              return sum + value; // Cộng dồn các giá trị
            }, 0); // Bắt đầu từ 0
          }
        });
        calculator[column].sum_price_vat = sum_price_vat;
        calculator[column].sum_quantity = sum_quantity;
        calculator[column].index_sum = index_sum;
        calculator[column].total_price_vat = index_sum > 0 ? sum_price_vat * sum_quantity / index_sum : 0;

        // Cập nhật giá trị vào dòng đầu tiên của cột
        _this4.data_api.items[0][column] = calculator[column].total_price_vat;
      });

      // Tải lại Handsontable
      this.loadDataHandsontable();
    },
    skipFields: function skipFields() {
      return ['promotion', 'sap_code', 'sap_name', 'unit', 'price_vat', 'specifications', 'barcode', 'tax', 'sum_quantity'];
    },
    calculatorTotalPriceVat: function calculatorTotalPriceVat() {
      var _this5 = this;
      var calculator = {};

      // Lọc ra các key không nằm trong skipFields
      var filtered_keys = Object.keys(this.data_api.items[0]).filter(function (key) {
        return !_this5.skipFields().includes(key);
      });
      filtered_keys.forEach(function (key) {
        calculator[key] = {
          // Khởi tạo object cho từng key
          sum_price_vat: 0,
          sum_quantity: 0,
          index_sum: 0,
          total_price_vat: 0
        };
      });

      // Duyệt qua từng item sau dòng index > 3
      this.data_api.items.forEach(function (item, index) {
        if (index > 3) {
          filtered_keys.forEach(function (key) {
            if (item[key] != null) {
              calculator[key].sum_price_vat += parseFloat(item.price_vat); // Tính tổng price_vat cho từng key
              calculator[key].sum_quantity += parseFloat(item[key]); // Tính tổng quantity
              calculator[key].index_sum++; // Đếm số lần tính
              calculator[key].total_price_vat = calculator[key].sum_price_vat * calculator[key].sum_quantity / calculator[key].index_sum; // Tổng giá trị
            }
          });
        }
      });
      filtered_keys.forEach(function (key) {
        _this5.data_api.items[0][key] = calculator[key].total_price_vat;
      });
      this.loadDataHandsontable();
    },
    loadDataHandsontable: function loadDataHandsontable() {
      this.settings.data = _toConsumableArray(this.data_api.items);
      this.$refs.myHotTableSecond.hotInstance.loadData(this.data_api.items);
    },
    addColumnDefault: function addColumnDefault() {
      // lấy key của dòng 3 để thêm dòng mới
      var keys = Object.keys(this.data_api.items[3]);
      var new_item = {}; // gộp mảng keys với data_api.items[3] để tạo dòng mới
      keys.map(function (key) {
        new_item[key] = "";
      });
      this.data_api.items.push(new_item);
      this.loadDataHandsontable();
    },
    addNewColumns: function addNewColumns() {
      this.cols.push({
        value: ""
      });
    },
    closeModel: function closeModel() {
      this.showModal = false;
    },
    openModal: function openModal() {
      this.showModal = true;
    },
    addColumn: function addColumn() {
      var _this6 = this;
      var skipFields = this.skipFields();
      this.cols.map(function (col) {
        // this.newColumnName = col.value;
        _this6.data_api.items.map(function (item, index) {
          if (index === 3) {
            item[col.value] = col.value;
          } else {
            item[col.value] = "";
          }
        });
      });
      this.closeModel();
      var count_store = Object.keys(this.data_api.items[3]).filter(function (key) {
        return !skipFields.includes(key);
      }); // Loại bỏ các field không cần thiết
      console.log(count_store.length, count_store);
      this.data_api.items[1].tax = count_store.length;
      this.loadDataHandsontable();
    },
    mapSettingCategoryToSource: function mapSettingCategoryToSource(categories) {
      var source = [];
      categories.map(function (category) {
        source.push(category.name);
      });
      this.setting_categories.source = source;
      // console.log(this.setting_categories.source);
    },
    fetchMaterialCategories: function fetchMaterialCategories() {
      var _this7 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var _yield$_this7$api_han, data, success, errors, message;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _context.prev = 0;
              _context.next = 3;
              return _this7.api_handler.get(_this7.api.material_category);
            case 3:
              _yield$_this7$api_han = _context.sent;
              data = _yield$_this7$api_han.data;
              success = _yield$_this7$api_han.success;
              errors = _yield$_this7$api_han.errors;
              message = _yield$_this7$api_han.message;
              if (data.success) {
                _this7.mapSettingCategoryToSource(data.items);
                // this.data_api.material_categories = data;
              }
              _context.next = 15;
              break;
            case 11:
              _context.prev = 11;
              _context.t0 = _context["catch"](0);
              _this7.error.message = _context.t0.response.data.message;
              _this7.error.indexs = _context.t0.response.data.indexs;
            case 15:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[0, 11]]);
      }))();
    },
    // Map headers sang tiêu đề tiếng Việt
    mapHeadersToColHeaders: function mapHeadersToColHeaders(headers) {
      var headerMapping = {
        promotion: "Phân loại",
        sap_material_code: "Mã SAP",
        sap_material_name: "Tên SP",
        unit: "Đơn vị tính",
        price_vat: "Giá chưa VAT",
        specifications: "Quy cách",
        barcode: "Barcode",
        tax: "Thuế"
      };
      // Chuyển đổi headers
      return headers.map(function (header) {
        return headerMapping[header] || header;
      });
    },
    fetchData: function fetchData() {
      var _this8 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var _yield$_this8$api_han, data, success, errors, message;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              _this8.is_loading = true;
              _context2.prev = 1;
              _context2.next = 4;
              return _this8.api_handler.post(_this8.api.so_processing_data, {}, {
                items: _this8.prop_items
              });
            case 4:
              _yield$_this8$api_han = _context2.sent;
              data = _yield$_this8$api_han.data;
              success = _yield$_this8$api_han.success;
              errors = _yield$_this8$api_han.errors;
              message = _yield$_this8$api_han.message;
              if (success) {
                console.log(data, 'datasucess true', data.items);
                _this8.data_api.items = data.items;
                _this8.calculatorTotalPriceVat();
                // this.data_api.headers = data.headers;
                // const colHeaders = this.mapHeadersToColHeaders(this.data_api.headers);
                // Tạo cấu hình columns
                // const columns = this.data_api.headers.map((header) => {
                //     if (header === "promotion") {
                //         return {
                //             data: header,
                //             type: "dropdown",
                //             source: this.setting_categories.source,
                //             strict: this.setting_categories.strict,
                //         };
                //     }
                //     return { data: header }; // Cấu hình mặc định
                // });
                // this.settings.colHeaders = colHeaders;
                console.log(_this8.data_api.items);
                _this8.$refs.myHotTableSecond.hotInstance.loadData(_this8.data_api.items);
                // this.$refs.myHotTableSecond.hotInstance.updateSettings({
                //     colHeaders: colHeaders,
                //     columns: columns,

                // });
                _this8.$showMessage('success', 'Thông báo', 'Lấy dữ liệu thành công');
              }
              _context2.next = 16;
              break;
            case 12:
              _context2.prev = 12;
              _context2.t0 = _context2["catch"](1);
              _this8.error.message = _context2.t0.response.data.message;
              _this8.error.indexs = _context2.t0.response.data.indexs;
            case 16:
              _this8.is_loading = false;
            case 17:
            case "end":
              return _context2.stop();
          }
        }, _callee2, null, [[1, 12]]);
      }))();
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _vm._m(0);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "modal fade",
    attrs: {
      id: "SODialogCreateColumn",
      tabindex: "-1"
    }
  }, [_c("div", {
    staticClass: "modal-dialog modal-sm"
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_c("div", {
    staticClass: "modal-header"
  }, [_c("h5", {
    staticClass: "modal-title"
  }, [_vm._v("Modal title")]), _vm._v(" "), _c("button", {
    staticClass: "close",
    attrs: {
      type: "button",
      "data-dismiss": "modal",
      "aria-label": "Close"
    }
  }, [_c("span", {
    attrs: {
      "aria-hidden": "true"
    }
  }, [_vm._v("×")])])]), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_c("div", {
    staticClass: "form-group d-flex text-xs"
  }, [_c("div", {
    staticClass: "flex-shrink-0 text-xs"
  }, [_c("label", {
    attrs: {
      "for": ""
    }
  }, [_vm._v("Nhập Tiêu đề")])]), _vm._v(" "), _c("div", {
    staticClass: "flex-fill text-xs"
  }, [_c("textarea", {
    attrs: {
      name: "",
      id: "",
      size: "3"
    }
  })])])]), _vm._v(" "), _c("div", {
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
    }
  }, [_vm._v("Save changes")])])])])]);
}];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/babel-loader/lib??ref--11!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "row"
  }, [_c("div", {
    staticClass: "col-lg-12"
  }, [_c("div", {
    staticClass: "text-xs"
  }, [_c("button", {
    staticClass: "btn btn-sm btn-primary text-xs px-2",
    on: {
      click: function click($event) {
        return _vm.getDemoClickModal();
      }
    }
  }, [_c("i", {
    staticClass: "fas fa-plus mr-1"
  }), _vm._v(" Tạo đơn hàng\n                ")]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-sm btn-outline-info border border-info text-xs px-2",
    on: {
      click: function click($event) {
        return _vm.addColumnDefault();
      }
    }
  }, [_c("i", {
    staticClass: "fas fa-plus mr-1"
  }), _vm._v(" Thêm dòng\n                ")]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-sm btn-outline-info border border-info text-xs px-2",
    on: {
      click: function click($event) {
        return _vm.openModal();
      }
    }
  }, [_c("i", {
    staticClass: "fas fa-plus mr-1"
  }), _vm._v(" Thêm cột\n                ")]), _vm._v(" "), _vm._m(0), _vm._v(" "), _vm._m(1)])])]), _vm._v(" "), _c("hot-table", {
    ref: "myHotTableSecond",
    attrs: {
      data: _vm.data_api.items,
      settings: _vm.settings
    }
  }), _vm._v(" "), _c("b-modal", {
    attrs: {
      title: "THÊM CỘT MỚI",
      "button-size": "sm",
      "hide-footer": ""
    },
    model: {
      value: _vm.showModal,
      callback: function callback($$v) {
        _vm.showModal = $$v;
      },
      expression: "showModal"
    }
  }, [_c("label", {
    staticClass: "text-xs",
    attrs: {
      "for": ""
    }
  }, [_vm._v("Tên cột mới")]), _vm._v(" "), _vm._l(_vm.cols, function (col, index) {
    return _c("b-form-group", {
      key: index,
      attrs: {
        "label-size": "sm"
      }
    }, [_c("b-form-input", {
      attrs: {
        required: "",
        placeholder: "Nhập tên cột mới",
        "input-size": "sm"
      },
      model: {
        value: col.value,
        callback: function callback($$v) {
          _vm.$set(col, "value", $$v);
        },
        expression: "col.value"
      }
    }), _vm._v(" "), _c("b-form-invalid-feedback", {
      attrs: {
        state: col.value.length > 0 ? null : false
      }
    }, [_vm._v("Tên cột không được để\n                trống")])], 1);
  }), _vm._v(" "), _c("button", {
    staticClass: "btn btn-sm btn-info px-2 text-xs",
    on: {
      click: function click($event) {
        return _vm.addNewColumns();
      }
    }
  }, [_c("i", {
    staticClass: "fas fa-plus mr-1"
  }), _vm._v("Thêm cột")]), _vm._v(" "), _c("b-button", {
    attrs: {
      size: "sm",
      type: "button",
      variant: "success"
    },
    on: {
      click: function click($event) {
        return _vm.addColumn();
      }
    }
  }, [_vm._v("OK")]), _vm._v(" "), _c("b-button", {
    attrs: {
      variant: "secondary",
      size: "sm"
    },
    on: {
      click: function click($event) {
        return _vm.closeModel();
      }
    }
  }, [_vm._v("Hủy")])], 2)], 1);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("button", {
    staticClass: "btn btn-sm btn-success text-xs px-2"
  }, [_c("i", {
    staticClass: "fas fa-save mr-1"
  }), _vm._v(" Lưu đơn hàng\n                ")]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("button", {
    staticClass: "btn btn-sm btn-outline-warning text-xs px-4"
  }, [_c("i", {
    staticClass: "fas fa-boxes mr-1"
  }), _vm._v(" Check tồn\n                ")]);
}];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "@charset \"UTF-8\";\n[data-v-a32ac4b6] .highlight-row {\n  background-color: #ffc800 !important;\n  /* Màu vàng nhạt */\n  color: #000;\n  /* Màu chữ đen */\n  font-weight: bold;\n}\n[data-v-a32ac4b6] .highlight-row-blue {\n  background-color: #008cff !important;\n  /* Màu vàng nhạt */\n  color: white;\n  /* Màu chữ đen */\n  font-weight: bold;\n}\n[data-v-a32ac4b6] .highlight-row-lightblue {\n  background-color: rgba(0, 140, 255, 0.7137254902) !important;\n  /* Màu vàng nhạt */\n  color: black;\n  /* Màu chữ đen */\n  font-weight: bold;\n}\n[data-v-a32ac4b6] .bg-yellow {\n  background-color: #ffff00 !important;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../../node_modules/css-loader!../../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true&");

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

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue":
/*!****************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SODialogCreateColumn_vue_vue_type_template_id_7282e943_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true& */ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true&");
/* harmony import */ var _SODialogCreateColumn_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SODialogCreateColumn.vue?vue&type=script&lang=js& */ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SODialogCreateColumn_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SODialogCreateColumn_vue_vue_type_template_id_7282e943_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SODialogCreateColumn_vue_vue_type_template_id_7282e943_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "7282e943",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogCreateColumn_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SODialogCreateColumn.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogCreateColumn_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true&":
/*!***********************************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true& ***!
  \***********************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogCreateColumn_vue_vue_type_template_id_7282e943_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/dialog/SODialogCreateColumn.vue?vue&type=template&id=7282e943&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogCreateColumn_vue_vue_type_template_id_7282e943_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SODialogCreateColumn_vue_vue_type_template_id_7282e943_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SOHeaderSecond_vue_vue_type_template_id_a32ac4b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true& */ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true&");
/* harmony import */ var _SOHeaderSecond_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SOHeaderSecond.vue?vue&type=script&lang=js& */ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _SOHeaderSecond_vue_vue_type_style_index_0_id_a32ac4b6_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true& */ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _SOHeaderSecond_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SOHeaderSecond_vue_vue_type_template_id_a32ac4b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SOHeaderSecond_vue_vue_type_template_id_a32ac4b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "a32ac4b6",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOHeaderSecond.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true&":
/*!********************************************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true& ***!
  \********************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_style_index_0_id_a32ac4b6_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/style-loader!../../../../../../../../../node_modules/css-loader!../../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=style&index=0&id=a32ac4b6&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_style_index_0_id_a32ac4b6_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_style_index_0_id_a32ac4b6_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_style_index_0_id_a32ac4b6_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_style_index_0_id_a32ac4b6_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true&":
/*!*****************************************************************************************************************************************************!*\
  !*** ./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true& ***!
  \*****************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_template_id_a32ac4b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../node_modules/babel-loader/lib??ref--11!../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--7!../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/business/version_2/sales/order_processing/header/SOHeaderSecond.vue?vue&type=template&id=a32ac4b6&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_template_id_a32ac4b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_babel_loader_lib_index_js_ref_11_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_7_node_modules_vue_loader_lib_index_js_vue_loader_options_SOHeaderSecond_vue_vue_type_template_id_a32ac4b6_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);