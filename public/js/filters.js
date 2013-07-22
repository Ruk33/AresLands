'use strict';

angular.module('areslands.filters', []).
filter('interpolate', ['version', function(version) {
	return function(text) {
		return String(text).replace(/\%VERSION\%/mg, version);
	}
}]).

filter('range', function() {
	return function(input, min, max) {
		min = parseInt(min);
		max = parseInt(max);

		for (var i = min; i <= max; i++) {
			input.push(i);
		}

		return input;
	};
});