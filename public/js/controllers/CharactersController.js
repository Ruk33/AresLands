'use strict';

var CharactersController = function($scope, $http) {
	$scope.predicate = 'pvp_points';
	$scope.reverse = true;

	$http.post($scope.basePath + 'authenticated/characters')
		.success(function(data) {
			$scope.characters = data;
		})
		.error(function() {
			$scope.error = 'No se pudieron traer los datos del servidor. Por favor, inténtalo nuevamente en unos segundos.';
		}
	);
};

// Requerimientos
CharactersController.$inject = ['$scope', '$http'];