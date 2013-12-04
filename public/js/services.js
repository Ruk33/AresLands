'use strict';

angular.module('areslands.services', ['configuration', 'ngResource']).

factory('DividedCoin', ['$resource', 'BASE_PATH', function($resource, BASE_PATH)
{
	return $resource(BASE_PATH + 'api/dividedCoin/:amount');
}]).

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

factory('CharacterOfLoggedUser', ['$resource', 'BASE_PATH', function($resource, BASE_PATH) {
	return $resource(BASE_PATH + 'api/characterOfLoggedUser');
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