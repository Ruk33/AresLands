'use strict';

angular.module('areslands.services', ['configuration', 'ngResource']).

factory('Character', ['$resource', 'BASE_PATH', function($resource, BASE_PATH) {
	return $resource(BASE_PATH + 'api/character/:name/:tooltip', {}, {
		tooltip: {
			method: 'GET',
			params: {
				'tooltip': true
			}
		}
	});
}])

/*factory('Item', ['$resource', 'BASE_PATH', function($resource, BASE_PATH) {
	return $resource(BASE_PATH + '/item/index/:itemId');
}])*/;