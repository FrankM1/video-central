(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
var _ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null);

function Application() {
	var settings = {};

	_.extend( this, {
		controller: {},
		l10n: {},
		model: {},
		view: {}
	});

	this.settings = function( options ) {
		if ( options ) {
			_.extend( settings, options );
		}

		if ( settings.l10n ) {
			this.l10n = _.extend( this.l10n, settings.l10n );
			delete settings.l10n;
		}

		return settings || {};
	};
}

global.video_central = global.video_central || new Application();
module.exports = global.video_central;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L2VkaXRvci5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsInZhciBfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpO1xuXG5mdW5jdGlvbiBBcHBsaWNhdGlvbigpIHtcblx0dmFyIHNldHRpbmdzID0ge307XG5cblx0Xy5leHRlbmQoIHRoaXMsIHtcblx0XHRjb250cm9sbGVyOiB7fSxcblx0XHRsMTBuOiB7fSxcblx0XHRtb2RlbDoge30sXG5cdFx0dmlldzoge31cblx0fSk7XG5cblx0dGhpcy5zZXR0aW5ncyA9IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdGlmICggb3B0aW9ucyApIHtcblx0XHRcdF8uZXh0ZW5kKCBzZXR0aW5ncywgb3B0aW9ucyApO1xuXHRcdH1cblxuXHRcdGlmICggc2V0dGluZ3MubDEwbiApIHtcblx0XHRcdHRoaXMubDEwbiA9IF8uZXh0ZW5kKCB0aGlzLmwxMG4sIHNldHRpbmdzLmwxMG4gKTtcblx0XHRcdGRlbGV0ZSBzZXR0aW5ncy5sMTBuO1xuXHRcdH1cblxuXHRcdHJldHVybiBzZXR0aW5ncyB8fCB7fTtcblx0fTtcbn1cblxuZ2xvYmFsLnZpZGVvX2NlbnRyYWwgPSBnbG9iYWwudmlkZW9fY2VudHJhbCB8fCBuZXcgQXBwbGljYXRpb24oKTtcbm1vZHVsZS5leHBvcnRzID0gZ2xvYmFsLnZpZGVvX2NlbnRyYWw7XG4iXX0=
