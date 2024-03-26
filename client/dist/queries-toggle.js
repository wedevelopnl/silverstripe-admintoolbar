/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************!*\
  !*** ./client/src/js/queries-toggle.js ***!
  \*****************************************/
var selector = '[data-queries-toggle]';
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
//# sourceMappingURL=queries-toggle.js.map