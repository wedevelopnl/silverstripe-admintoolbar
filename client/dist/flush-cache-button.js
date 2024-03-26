/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************!*\
  !*** ./client/src/js/flush-cache-button.js ***!
  \*********************************************/
var selector = '[data-flush-cache-button]';
var button = document.querySelector(selector);
button === null || button === void 0 ? void 0 : button.addEventListener('click', function () {
  var flushURL = new URL(window.location.href);
  flushURL.searchParams.set('AdminToolbarDisabled', '1');
  flushURL.searchParams.set('flush', '1');
  button.classList.remove('font-icon-back-in-time');
  button.classList.add('font-icon-spinner');
  fetch(flushURL.toString()).then(function () {
    window.location.reload();
  });
});
/******/ })()
;
//# sourceMappingURL=flush-cache-button.js.map