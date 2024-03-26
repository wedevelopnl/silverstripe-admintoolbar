/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************!*\
  !*** ./client/src/js/timing-toggle.js ***!
  \****************************************/
var selector = '[data-timing-toggle]';
var checkbox = document.querySelector(selector);
function getState() {
  return localStorage.getItem(selector) === 'true';
}
function setState(state) {
  localStorage.setItem(selector, state);
}
document.addEventListener('DOMContentLoaded', function () {
  if (checkbox) {
    checkbox.checked = getState();
  }
});
checkbox === null || checkbox === void 0 ? void 0 : checkbox.addEventListener('change', function (e) {
  setState(e.currentTarget.checked);
  window.location.reload();
});
/******/ })()
;
//# sourceMappingURL=timing-toggle.js.map