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
}]).
	
factory('Skill', ['$resource', 'BASE_PATH', function($resource, BASE_PATH) {
	return $resource(BASE_PATH + 'api/skill/:id/:level/:tooltip', {}, {
		tooltip: {
			method: 'GET',
			params: {
				'tooltip': true
			}
		}
	});
}]).
	
factory('Item', ['$resource', 'BASE_PATH', function($resource, BASE_PATH) {
	return $resource(BASE_PATH + 'api/item/:id/:price/:tooltip', {}, {
		tooltip: {
			method: 'GET',
			params: {
				'tooltip': true
			}
		}
	});
}]);