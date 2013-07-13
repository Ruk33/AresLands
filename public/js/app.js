'use strict';

angular.module('areslands', ['areslands.filters', 'areslands.services', 'areslands.controllers'])
.config(['$interpolateProvider', function($interpolateProvider) {
	$interpolateProvider.startSymbol('[[');
	$interpolateProvider.endSymbol(']]');
}]);