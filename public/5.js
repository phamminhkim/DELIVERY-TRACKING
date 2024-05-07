(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[5],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ApiHandler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../ApiHandler */ "./resources/js/components/home/ApiHandler.js");
/* harmony import */ var _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @riophae/vue-treeselect */ "./node_modules/@riophae/vue-treeselect/dist/vue-treeselect.cjs.js");
/* harmony import */ var _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _riophae_vue_treeselect_dist_vue_treeselect_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @riophae/vue-treeselect/dist/vue-treeselect.css */ "./node_modules/@riophae/vue-treeselect/dist/vue-treeselect.css");
/* harmony import */ var _riophae_vue_treeselect_dist_vue_treeselect_css__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_riophae_vue_treeselect_dist_vue_treeselect_css__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var toastr__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! toastr */ "./node_modules/toastr/toastr.js");
/* harmony import */ var toastr__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(toastr__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var toastr_toastr_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! toastr/toastr.scss */ "./node_modules/toastr/toastr.scss");
/* harmony import */ var toastr_toastr_scss__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(toastr_toastr_scss__WEBPACK_IMPORTED_MODULE_5__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }







/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'DialogAddUpdateSapMaterial',
  props: {
    is_editing: Boolean,
    editing_item: Object,
    refetchData: Function
  },
  components: {
    Treeselect: _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_1___default.a,
    Vue: vue__WEBPACK_IMPORTED_MODULE_3___default.a
  },
  data: function data() {
    return {
      api_handler: new _ApiHandler__WEBPACK_IMPORTED_MODULE_0__["default"](window.Laravel.access_token),
      is_loading: false,
      errors: {},
      sap_material: {
        sap_code: '',
        unit_id: null,
        bar_code: '',
        name: ''
      },
      unit_options: [],
      sap_materials: [],
      api_url: '/api/master/sap-materials'
    };
  },
  created: function created() {
    this.fetchOptionsData();
  },
  methods: {
    addSapMaterial: function addSapMaterial() {
      var _this = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              if (!_this.is_loading) {
                _context.next = 2;
                break;
              }
              return _context.abrupt("return");
            case 2:
              _this.is_loading = true;
              if (_this.is_editing === false) {
                _this.createSapMaterial();
              } else {
                _this.updateSapMaterial();
              }
            case 4:
            case "end":
              return _context.stop();
          }
        }, _callee);
      }))();
    },
    createSapMaterial: function createSapMaterial() {
      var _this2 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var data, _this2$sap_materials;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              _context2.prev = 0;
              _this2.is_loading = true;
              _context2.next = 4;
              return _this2.api_handler.post('/api/master/sap-materials', {
                sap_code: _this2.sap_material.sap_code,
                unit_id: _this2.sap_material.unit_id,
                bar_code: _this2.sap_material.bar_code,
                name: _this2.sap_material.name
              });
            case 4:
              data = _context2.sent;
              if (!data.success) {
                _context2.next = 13;
                break;
              }
              if (Array.isArray(data)) {
                (_this2$sap_materials = _this2.sap_materials).push.apply(_this2$sap_materials, _toConsumableArray(data)); // Add the new mappings to the end of the list
              }

              _this2.showMessage('success', 'Thêm thành công');
              _this2.closeDialog();
              _context2.next = 11;
              return _this2.refetchData();
            case 11:
              _context2.next = 15;
              break;
            case 13:
              _this2.errors = data.errors;
              _this2.showMessage('error', 'Thêm không thành công');
            case 15:
              _context2.next = 20;
              break;
            case 17:
              _context2.prev = 17;
              _context2.t0 = _context2["catch"](0);
              _this2.showMessage('error', 'Thêm không thành công');
            case 20:
              _context2.prev = 20;
              _this2.is_loading = false;
              return _context2.finish(20);
            case 23:
            case "end":
              return _context2.stop();
          }
        }, _callee2, null, [[0, 17, 20, 23]]);
      }))();
    },
    updateSapMaterial: function updateSapMaterial() {
      var _this3 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
        var data, _this3$sap_materials;
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              _context3.prev = 0;
              _this3.is_loading = true;
              _context3.next = 4;
              return _this3.api_handler.put("".concat(_this3.api_url, "/").concat(_this3.sap_material.id), _this3.sap_material);
            case 4:
              data = _context3.sent;
              if (!data.success) {
                _context3.next = 13;
                break;
              }
              if (Array.isArray(data)) {
                (_this3$sap_materials = _this3.sap_materials).push.apply(_this3$sap_materials, _toConsumableArray(data)); // Add the new mappings to the end of the list
              }

              _this3.showMessage('success', 'Cập nhật thành công');
              _this3.closeDialog();
              _context3.next = 11;
              return _this3.refetchData();
            case 11:
              _context3.next = 15;
              break;
            case 13:
              _this3.errors = data.errors;
              _this3.showMessage('error', 'Cập nhật không thành công');
            case 15:
              _context3.next = 20;
              break;
            case 17:
              _context3.prev = 17;
              _context3.t0 = _context3["catch"](0);
              _this3.showMessage('error', 'Cập nhật không thành công');
            case 20:
              _context3.prev = 20;
              _this3.is_loading = false;
              return _context3.finish(20);
            case 23:
            case "end":
              return _context3.stop();
          }
        }, _callee3, null, [[0, 17, 20, 23]]);
      }))();
    },
    fetchOptionsData: function fetchOptionsData() {
      var _this4 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
        var _yield$_this4$api_han, _yield$_this4$api_han2, unit_options, sap_materials;
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              _context4.prev = 0;
              _this4.is_loading = true;
              _context4.next = 4;
              return _this4.api_handler.handleMultipleRequest([new _ApiHandler__WEBPACK_IMPORTED_MODULE_0__["APIRequest"]('get', '/api/master/sap-units')
              // new APIRequest('get', '/api/master/sap-materials'),
              ]);
            case 4:
              _yield$_this4$api_han = _context4.sent;
              _yield$_this4$api_han2 = _slicedToArray(_yield$_this4$api_han, 2);
              unit_options = _yield$_this4$api_han2[0];
              sap_materials = _yield$_this4$api_han2[1];
              _this4.sap_materials = sap_materials;
              _this4.unit_options = unit_options.map(function (unit) {
                return {
                  id: unit.id,
                  label: "(".concat(unit.id, ") ").concat(unit.unit_code)
                };
              });
              _context4.next = 15;
              break;
            case 12:
              _context4.prev = 12;
              _context4.t0 = _context4["catch"](0);
              _this4.$showMessage('error', 'Lỗi', _context4.t0);
            case 15:
              _context4.prev = 15;
              _this4.is_loading = false;
              return _context4.finish(15);
            case 18:
            case "end":
              return _context4.stop();
          }
        }, _callee4, null, [[0, 12, 15, 18]]);
      }))();
    },
    loadOptions: function loadOptions(_ref) {
      var _this5 = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee5() {
        var action, searchQuery, callback, params, _yield$_this5$api_han, data, options;
        return _regeneratorRuntime().wrap(function _callee5$(_context5) {
          while (1) switch (_context5.prev = _context5.next) {
            case 0:
              action = _ref.action, searchQuery = _ref.searchQuery, callback = _ref.callback;
              if (!(action === _riophae_vue_treeselect__WEBPACK_IMPORTED_MODULE_1__["ASYNC_SEARCH"])) {
                _context5.next = 9;
                break;
              }
              params = {
                search: searchQuery
              };
              _context5.next = 5;
              return _this5.api_handler.get('api/master/sap-units', params);
            case 5:
              _yield$_this5$api_han = _context5.sent;
              data = _yield$_this5$api_han.data;
              options = data.map(function (item) {
                return {
                  id: item.id,
                  label: "(".concat(item.id, ") ").concat(item.unit_code)
                };
              });
              callback(null, options);
            case 9:
            case "end":
              return _context5.stop();
          }
        }, _callee5);
      }))();
    },
    normalizerOption: function normalizerOption(node) {
      return {
        id: node.id,
        label: node.name
      };
    },
    closeDialog: function closeDialog() {
      this.clearForm();
      this.clearErrors();
      $('#DialogAddUpdateSapMaterial').modal('hide');
    },
    resetDialog: function resetDialog() {
      this.sap_material.sap_code = null;
      this.sap_material.unit_id = null;
      this.sap_material.bar_code = '';
      this.sap_material.name = '';
      this.clearErrors();
    },
    clearForm: function clearForm() {
      this.sap_material.sap_code = null;
      this.sap_material.unit_id = null;
      this.sap_material.bar_code = null;
      this.sap_material.name = null;
    },
    clearErrors: function clearErrors() {
      this.errors = {};
    },
    showMessage: function showMessage(type, title, message) {
      if (!title) title = 'Information';
      toastr__WEBPACK_IMPORTED_MODULE_4___default.a.options = {
        positionClass: 'toast-bottom-right',
        toastClass: this.getToastClassByType(type)
      };
      toastr__WEBPACK_IMPORTED_MODULE_4___default.a[type](message, title);
    },
    hasError: function hasError(fieldName) {
      return fieldName in this.errors;
    },
    getError: function getError(fieldName) {
      return this.errors[fieldName];
    },
    getToastClassByType: function getToastClassByType(type) {
      switch (type) {
        case 'success':
          return 'toastr-bg-green';
        case 'error':
          return 'toastr-bg-red';
        case 'warning':
          return 'toastr-bg-yellow';
        default:
          return '';
      }
    }
  },
  watch: {
    is_editing: function is_editing() {
      if (!this.is_editing) {
        this.clearForm();
      }
    },
    editing_item: function editing_item(item) {
      console.log(item);
      this.sap_material.sap_code = item.sap_code;
      this.sap_material.unit_id = item.unit_id;
      this.sap_material.bar_code = item.bar_code;
      this.sap_material.name = item.name;
      this.sap_material.id = item.id;
    }
  },
  computed: {
    rows: function rows() {
      return this.sap_materials.length;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************/
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
      id: "DialogAddUpdateSapMaterial",
      tabindex: "-1",
      role: "dialog",
      "data-backdrop": "static"
    }
  }, [_c("div", {
    staticClass: "modal-dialog",
    attrs: {
      role: "document"
    }
  }, [_c("div", {
    staticClass: "modal-content"
  }, [_c("form", {
    on: {
      submit: function submit($event) {
        $event.preventDefault();
        return _vm.addSapMaterial.apply(null, arguments);
      }
    }
  }, [_c("div", {
    staticClass: "modal-header"
  }, [_c("h5", {
    staticClass: "modal-title"
  }, [_c("span", [_vm._v("\n\t\t\t\t\t\t\t\t" + _vm._s(_vm.is_editing ? "Cập nhật sản phẩm SAP" : "Thêm mới sản phẩm SAP"))])]), _vm._v(" "), _c("button", {
    staticClass: "close",
    attrs: {
      type: "button",
      "data-dismiss": "modal",
      "aria-label": "Close"
    },
    on: {
      click: _vm.closeDialog
    }
  }, [_c("span", {
    attrs: {
      "aria-hidden": "true"
    }
  }, [_vm._v("×")])])]), _vm._v(" "), _c("div", {
    staticClass: "modal-body"
  }, [_c("div", {
    staticClass: "form-group"
  }, [_c("label", [_vm._v("Mã sản phẩm SAP")]), _vm._v(" "), _c("small", {
    staticClass: "text-danger"
  }, [_vm._v("*")]), _vm._v(" "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.sap_material.sap_code,
      expression: "sap_material.sap_code"
    }],
    staticClass: "form-control",
    "class": _vm.hasError("sap_code") ? "is-invalid" : "",
    attrs: {
      id: "sap_code",
      name: "sap_code",
      required: "",
      placeholder: "Yêu cầu nhập mã SAP...",
      type: "text"
    },
    domProps: {
      value: _vm.sap_material.sap_code
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.sap_material, "sap_code", $event.target.value);
      }
    }
  }), _vm._v(" "), _vm.hasError("sap_code") ? _c("span", {
    staticClass: "invalid-feedback",
    attrs: {
      role: "alert"
    }
  }, [_c("strong", [_vm._v(_vm._s(_vm.getError("sap_code")))])]) : _vm._e()]), _vm._v(" "), _c("div", {
    staticClass: "form-group"
  }, [_c("label", [_vm._v("Mã unit")]), _vm._v(" "), _c("small", {
    staticClass: "text-danger"
  }, [_vm._v("*")]), _vm._v(" "), _c("treeselect", {
    "class": _vm.hasError("unit_id") ? "is-invalid" : "",
    attrs: {
      placeholder: "Nhập unit..",
      required: "",
      "load-options": _vm.loadOptions,
      async: true
    },
    model: {
      value: _vm.sap_material.unit_id,
      callback: function callback($$v) {
        _vm.$set(_vm.sap_material, "unit_id", $$v);
      },
      expression: "sap_material.unit_id"
    }
  }), _vm._v(" "), _vm.hasError("unit_id") ? _c("span", {
    staticClass: "invalid-feedback",
    attrs: {
      role: "alert"
    }
  }, [_c("strong", [_vm._v(_vm._s(_vm.getError("unit_id")))])]) : _vm._e()], 1), _vm._v(" "), _c("div", {
    staticClass: "form-group"
  }, [_c("label", [_vm._v("Mã Barcode")]), _vm._v(" "), _c("small", {
    staticClass: "text-danger"
  }, [_vm._v("*")]), _vm._v(" "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.sap_material.bar_code,
      expression: "sap_material.bar_code"
    }],
    staticClass: "form-control",
    "class": _vm.hasError("bar_code") ? "is-invalid" : "",
    attrs: {
      id: "bar_code",
      name: "bar_code",
      placeholder: "Nhập mã Barcode(nếu có)...",
      type: "text"
    },
    domProps: {
      value: _vm.sap_material.bar_code
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.sap_material, "bar_code", $event.target.value);
      }
    }
  }), _vm._v(" "), _vm.hasError("bar_code") ? _c("span", {
    staticClass: "invalid-feedback",
    attrs: {
      role: "alert"
    }
  }, [_c("strong", [_vm._v(_vm._s(_vm.getError("bar_code")))])]) : _vm._e()]), _vm._v(" "), _c("div", {
    staticClass: "form-group"
  }, [_c("label", [_vm._v("Tên sản phẩm SAP")]), _vm._v(" "), _c("small", {
    staticClass: "text-danger"
  }, [_vm._v("*")]), _vm._v(" "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.sap_material.name,
      expression: "sap_material.name"
    }],
    staticClass: "form-control",
    "class": _vm.hasError("name") ? "is-invalid" : "",
    attrs: {
      id: "name",
      name: "name",
      placeholder: "Yêu cầu nhập tên sản phẩm SAP...",
      type: "text"
    },
    domProps: {
      value: _vm.sap_material.name
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.sap_material, "name", $event.target.value);
      }
    }
  }), _vm._v(" "), _vm.hasError("name") ? _c("span", {
    staticClass: "invalid-feedback",
    attrs: {
      role: "alert"
    }
  }, [_c("strong", [_vm._v(_vm._s(_vm.getError("name")))])]) : _vm._e()])]), _vm._v(" "), _c("div", {
    staticClass: "modal-footer justify-content-between"
  }, [_c("button", {
    staticClass: "btn btn-primary",
    attrs: {
      type: "submit",
      title: "Submit"
    }
  }, [_vm._v("\n\t\t\t\t\t\t\t" + _vm._s(_vm.is_editing ? "Cập nhật" : "Tạo mới") + "\n\t\t\t\t\t\t")]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-secondary",
    attrs: {
      type: "button"
    },
    on: {
      click: _vm.resetDialog
    }
  }, [_vm._v("\n\t\t\t\t\t\t\tReset\n\t\t\t\t\t\t")])])])])])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue":
/*!*********************************************************************************!*\
  !*** ./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _DialogAddUpdateSapMaterial_vue_vue_type_template_id_4097416d_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true& */ "./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true&");
/* harmony import */ var _DialogAddUpdateSapMaterial_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js& */ "./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _DialogAddUpdateSapMaterial_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _DialogAddUpdateSapMaterial_vue_vue_type_template_id_4097416d_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _DialogAddUpdateSapMaterial_vue_vue_type_template_id_4097416d_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "4097416d",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_DialogAddUpdateSapMaterial_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_DialogAddUpdateSapMaterial_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true&":
/*!****************************************************************************************************************************!*\
  !*** ./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true& ***!
  \****************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_DialogAddUpdateSapMaterial_vue_vue_type_template_id_4097416d_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/home/sap/dialogs/DialogAddUpdateSapMaterial.vue?vue&type=template&id=4097416d&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_DialogAddUpdateSapMaterial_vue_vue_type_template_id_4097416d_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_DialogAddUpdateSapMaterial_vue_vue_type_template_id_4097416d_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);