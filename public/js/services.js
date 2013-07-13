'use strict';

angular.module('areslands.services', ['configuration', 'ngResource']).

factory('Item', ['$resource', 'BASE_PATH', function($resource, BASE_PATH) {
	return $resource(BASE_PATH + '/item/index/:itemId');
}]);