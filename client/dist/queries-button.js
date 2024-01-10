/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************!*\
  !*** ./client/src/js/queries-button.js ***!
  \*****************************************/
var configKey = '[data-queries-toggle]';
var button = document.querySelector('[data-queries-button]');
var isToggled = localStorage.getItem(configKey) === 'true';
if (isToggled) {
  button.classList.remove('admin-toolbar-hidden');
}
var queriesURL = new URL(window.location.href);
var dialog = document.createElement('dialog');
dialog.classList.add('admin-toolbar-queries-button');
queriesURL.searchParams.set('AdminToolbarDisabled', '1');
queriesURL.searchParams.set('showqueries', 'inline');
function parseResponse(text) {
  var parser = new DOMParser();
  var doc = parser.parseFromString(text, 'text/html');
  var timeRegex = /(\\d\\.\\d*)s/;
  var timings = [];
  var queries = [];
  doc.querySelectorAll('p.alert.alert-warning').forEach(function (el) {
    var matches = timeRegex.exec(el.innerHTML);
    queries.push(el.innerHTML);
    timings.push(parseFloat(matches[1], 10));
  });
  var queryTimeMS = timings.length ? (timings.reduce(function (prev, cur) {
    return prev + cur;
  }, 0) * 1000).toFixed(0) : 0;
  button.innerHTML = "".concat(queryTimeMS, "ms (").concat(timings.length, " queries)");
  dialog.innerHTML = "<ul>".concat(queries.reduce(function (acc, item) {
    return "".concat(acc, "<li><code>").concat(item, "</code></li>");
  }, ''), "</ul>");
  button.addEventListener('click', function () {
    dialog.showModal();
  });
  dialog.addEventListener('click', function (e) {
    var rect = dialog.getBoundingClientRect();
    if (e.clientY < rect.top || e.clientY > rect.bottom || e.clientX < rect.left || e.clientX > rect.right) {
      dialog.close();
    }
  });
  document.body.appendChild(dialog);
}
fetch(queriesURL.toString()).then(function (res) {
  return res.text();
}).then(parseResponse);
/******/ })()
;
//# sourceMappingURL=queries-button.js.map