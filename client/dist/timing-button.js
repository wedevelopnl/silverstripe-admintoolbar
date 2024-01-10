/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************!*\
  !*** ./client/src/js/timing-button.js ***!
  \****************************************/
var configKey = '[data-timing-toggle]';
var button = document.querySelector('[data-timing-button]');
var isToggled = localStorage.getItem(configKey) === 'true';
if (isToggled) {
  button.classList.remove('admin-toolbar-hidden');
}
var iframe = document.createElement('iframe');
var startTime = new Date().getTime();
var adminDisabledURL = new URL(window.location.href);
adminDisabledURL.searchParams.set('AdminToolbarDisabled', '1');
iframe.addEventListener('load', function () {
  button.querySelector('.ss-at-btn-content').innerHTML = "".concat(new Date().getTime() - startTime, "ms");
  iframe.remove();
});
iframe.src = adminDisabledURL.toString();
iframe.style.display = 'none';
document.body.appendChild(iframe);
/******/ })()
;
//# sourceMappingURL=timing-button.js.map