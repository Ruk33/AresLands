'use strict';

angular.module('areslands', [
	'areslands.filters',
	'areslands.services',
	'areslands.directives',
	'areslands.controllers',
]).config([
	'$interpolateProvider',
	function($interpolateProvider)
	{
		$interpolateProvider.startSymbol('[[');
		$interpolateProvider.endSymbol(']]');
	}
]);