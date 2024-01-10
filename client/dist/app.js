/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./client/src/js/dialog.js":
/*!*********************************!*\
  !*** ./client/src/js/dialog.js ***!
  \*********************************/
/***/ (() => {

var dialogs = document.querySelectorAll('dialog');
dialogs.forEach(function (dialog) {
  var toggleButtons = document.querySelectorAll("[data-toggle-dialog=\"".concat(dialog.id, "\"]"));
  toggleButtons.forEach(function (toggleButton) {
    toggleButton.addEventListener('click', function (e) {
      e.preventDefault();
      if (dialog.open) {
        dialog.close();
      } else {
        dialog.showModal();
      }
    });
  });
});
window.addEventListener('click', function (e) {
  if (e.target.nodeName === 'DIALOG') {
    e.target.close();
  }
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!******************************!*\
  !*** ./client/src/js/app.js ***!
  \******************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _dialog__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dialog */ "./client/src/js/dialog.js");
/* harmony import */ var _dialog__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_dialog__WEBPACK_IMPORTED_MODULE_0__);

document.addEventListener('DOMContentLoaded', function () {
  var adminToolbar = document.getElementById('admin-toolbar');
  var adminToolbarToggle = document.querySelector('[data-toggle-admin-toolbar]');
  var initialAdminToolbarActive = localStorage.getItem('ss-at-admin-toolbar-active');
  var inactiveClass = 'ss-at-collapse';
  if (initialAdminToolbarActive !== 'false') {
    adminToolbar.querySelector('.admin-toolbar-inner').classList.remove(inactiveClass);
  } else {
    adminToolbarToggle.classList.toggle('ss-at-origin-center');
    adminToolbarToggle.classList.toggle('ss-at-rotate-180');
  }
  adminToolbarToggle.addEventListener('click', function () {
    var toolbar = adminToolbar.querySelector('.admin-toolbar-inner');
    toolbar.classList.toggle(inactiveClass);
    adminToolbarToggle.classList.toggle('ss-at-origin-center');
    adminToolbarToggle.classList.toggle('ss-at-rotate-180');
    localStorage.setItem('ss-at-admin-toolbar-active', !toolbar.classList.contains(inactiveClass));
  });
});
})();

/******/ })()
;
//# sourceMappingURL=app.js.map