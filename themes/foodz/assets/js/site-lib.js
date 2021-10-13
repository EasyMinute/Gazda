(function ($, root, undefined) {
	"use strict";

	try {
		document.createEvent("TouchEvent");
		root.ideapark_is_mobile = true;
	} catch (e) {
		root.ideapark_is_mobile = false;
	}
	root.ideapark_is_responsinator = false;
	if (document.referrer) {
		root.ideapark_is_responsinator = (document.referrer.split('/')[2] == 'www.responsinator.com');
	}

	root.ideapark_debounce = function (func, wait, immediate) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			var later = function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};

	root.ideapark_isset = function (obj) {
		return typeof (obj) != 'undefined';
	};

	root.ideapark_empty = function (obj) {
		return typeof (obj) == 'undefined' || (typeof (obj) == 'object' && obj == null) || (typeof (obj) == 'array' && obj.length == 0) || (typeof (obj) == 'string' && ideapark_alltrim(obj) == '') || obj === 0;
	};

	root.ideapark_is_array = function (obj) {
		return typeof (obj) == 'array';
	};

	root.ideapark_is_function = function (obj) {
		return typeof (obj) == 'function';
	};

	root.ideapark_is_object = function (obj) {
		return typeof (obj) == 'object';
	};

	root.ideapark_alltrim = function (str) {
		var dir = arguments[1] !== undefined ? arguments[1] : 'a';
		var rez = '';
		var i, start = 0, end = str.length - 1;
		if (dir == 'a' || dir == 'l') {
			for (i = 0; i < str.length; i++) {
				if (str.substr(i, 1) != ' ') {
					start = i;
					break;
				}
			}
		}
		if (dir == 'a' || dir == 'r') {
			for (i = str.length - 1; i >= 0; i--) {
				if (str.substr(i, 1) != ' ') {
					end = i;
					break;
				}
			}
		}
		return str.substring(start, end + 1);
	};

	root.ideapark_ltrim = function (str) {
		return ideapark_alltrim(str, 'l');
	};

	root.ideapark_rtrim = function (str) {
		return ideapark_alltrim(str, 'r');
	};

	root.ideapark_dec2hex = function (n) {
		return Number(n).toString(16);
	};

	root.ideapark_hex2dec = function (hex) {
		return parseInt(hex, 16);
	};

	root.ideapark_in_array = function (val, thearray) {
		var rez = false;
		for (var i = 0; i < thearray.length; i++) {
			if (thearray[i] == val) {
				rez = true;
				break;
			}
		}
		return rez;
	};

	root.ideapark_detectIE = function () {
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}

		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}

		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
			return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}
		return false;
	};

	root.ideapark_loadScript = function (src, cb, async) {
		var script = document.createElement('script');
		script.async = !!(typeof async !== 'undefined' && async);
		script.src = src;

		script.onerror = function () {
			if (typeof cb !== 'undefined') {
				cb(new Error("Failed to load" + src));
			}
		};

		script.onload = function () {
			if (typeof cb !== 'undefined') {
				cb();
			}
		};

		document.getElementsByTagName("head")[0].appendChild(script);
	}

})(jQuery, this);

