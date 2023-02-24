/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/admin.js":
/*!*************************!*\
  !*** ./src/js/admin.js ***!
  \*************************/
/***/ (() => {

(function ($) {
  // Elementor template builder 
  $('body.post-type-rtcl_builder').find('.page-title-action').attr('href', '#');
  $('body.post-type-rtcl_builder #wpcontent').on('click', '.page-title-action, .row-title, .row-actions [class*="edit"] a', function (e) {
    e.preventDefault();

    var _self = $(e.target);

    var href = _self.attr("href");

    var post_id = 0;
    var saved_template = '';

    if (href) {
      var url = href.slice(href.indexOf('?') + 1).split('&');

      if (url && url[0].split('=')[1]) {
        post_id = parseInt(url[0].split('=')[1]);
      }
    }

    if (post_id) {
      saved_template = 'saved-template';
    }

    var modal = new RtclModal({
      footer: false,
      wrapClass: 'heading templeate-builder-popups ' + saved_template
    });
    var data = {
      action: 'rtcl_el_templeate_builder',
      post_id: post_id ? post_id : null,
      __rtcl_wpnonce: rtcl_el_tb.__rtcl_wpnonce
    };
    $.ajax({
      url: rtcl_el_tb.ajaxurl,
      data: data,
      type: "POST",
      beforeSend: function beforeSend() {
        modal.addModal().addLoading();
      },
      success: function success(response) {
        modal.removeLoading(); // console.log( response )

        modal.addTitle(response.title);

        if (response.success) {
          modal.content(response.content);
        }
      },
      error: function error(e) {}
    });
  });
  $('body.post-type-rtcl_builder').on('click', '#rtcl_tb_button', function (e) {
    e.preventDefault();

    var _self = $(e.target);

    var page_name_field = _self.parents('.templeate-builder-popups').find('#rtcl_tb_template_name');

    var page_name = page_name_field.val();

    var page_type = _self.parents('.templeate-builder-popups').find('#rtcl_tb_template_type').val();

    var default_template = _self.parents('.templeate-builder-popups').find('#default_template:checked').val();

    var page_id = _self.parents('.templeate-builder-popups').find('#page_id').val();

    var data = {
      action: 'rtcl_el_create_templeate',
      page_id: page_id ? page_id : null,
      page_name: page_name ? page_name : null,
      page_type: page_type ? page_type : null,
      default_template: default_template ? default_template : null,
      __rtcl_wpnonce: rtcl_el_tb.__rtcl_wpnonce
    }; // console.log( data );

    if (!page_name) {
      page_name_field.next('.message').show();
    } else {
      page_name_field.next('.message').hide();
      $.ajax({
        url: rtcl_el_tb.ajaxurl,
        data: data,
        type: "POST",
        beforeSend: function beforeSend() {
          var loader_html = '<div class="rtcl-tb-loader"> <svg class="rtcl-tb-spinner" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></div>';

          _self.parents('.rtcl-tb-button-wrapper').append(loader_html);
        },
        success: function success(response) {
          _self.parents('.rtcl-tb-button-wrapper').find('.rtcl-tb-loader').remove();

          _self.parents('.templeate-builder-popups').find('#page_id').attr("value", response.post_id);

          _self.parents('.templeate-builder-popups').addClass('saved-template');

          _self.parents('.templeate-builder-popups').find('.rtcl-tb-edit-button-wrapper a').attr("href", response.post_edit_url);

          _self.parents('.templeate-builder-popups').find('#rtcl_tb_button').attr("disabled", 'disabled');

          _self.parents('.templeate-builder-popups').find('.rtcl-modal-close').attr("data-save", 'saved');
        },
        error: function error(e) {}
      });
    }
  }); // Switch 

  $('body').on('click', 'td.column-set_default .rtcl-switch-wrapper', function (e) {
    e.preventDefault();

    var _self = $(this);

    var is_checked = _self.find('.set_default:checked').val();

    var page_id = _self.find('.set_default').val();

    var type = _self.find('.template_type').val();

    var selector_name = '.page-type-' + type;
    $('body').find(selector_name).each(function () {
      $(this).find('.set_default').prop("checked", false);
    });

    _self.find('.loader').addClass('slider-loading');

    var data = {
      action: 'rtcl_el_default_template',
      page_id: !is_checked ? page_id : 0,
      template_type: type ? type : null,
      __rtcl_wpnonce: rtcl_el_tb.__rtcl_wpnonce
    };
    $.ajax({
      url: rtcl_el_tb.ajaxurl,
      data: data,
      type: "POST",
      success: function success(response) {
        if (response.success && parseInt(response.post_id)) {
          _self.find('.set_default').prop("checked", true);
        }

        _self.find('.loader').removeClass('slider-loading');
      },
      error: function error(e) {
        console.log(e);
      }
    });
  }); // Disabled Edit Button.

  $('body').on('change input', '.templeate-builder-popups .rtcl-field', function () {
    $('body').find('#rtcl_tb_button').removeAttr('disabled');
    $('body').find('.templeate-builder-popups').removeClass('saved-template');
  }); // Pupups Close Event.

  $(document).on('rtcl.RtclModal.close', function (event, wrapper) {
    var close_button = $(wrapper).find('.templeate-builder-popups .rtcl-modal-close');
    var page_data = close_button.attr("data-save");

    if ('saved' == page_data) {
      location.reload();
    }
  }); // End elementor template builder 
})(jQuery);

/***/ }),

/***/ "./src/sass/store.scss":
/*!*****************************!*\
  !*** ./src/sass/store.scss ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/assets/js/admin": 0,
/******/ 			"assets/css/rtcl-builder-store": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkrtcl_elementor_builder"] = self["webpackChunkrtcl_elementor_builder"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["assets/css/rtcl-builder-store"], () => (__webpack_require__("./src/js/admin.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["assets/css/rtcl-builder-store"], () => (__webpack_require__("./src/sass/store.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;