'use strict';

var CharacterCreationController = function($scope, $http) {
	$scope.errorDiv = false;

	$scope.sendForm = function(data) {
		$scope.error = false;

		$http.post($scope.basePath + 'charactercreation/create', data)
			.success(function(data) {
				if (data.ok) {
					window.location.href = $scope.basePath + 'authenticated/index';
				} else if (data.errors) {
					$scope.errors = data.errors;
					$scope.errorDiv = true;
				}
			})
			.error(function() {
				$scope.errors = [
					'Hubo un error interno en el servidor, por favor inténtalo nuevamente.'
				];

				$scope.errorDiv = true;
			}
		);
	};
};

// Requerimientos
CharacterCreationController.$inject = ['$scope', '$http'];