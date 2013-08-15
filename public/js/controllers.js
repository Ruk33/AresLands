'use strict';

angular.module('areslands.controllers', []).

controller('CharacterStatsController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
	$scope.remainingPoints = 0;
	$scope.stats = {
		'stat_life': 0,
		'stat_dexterity': 0,
		'stat_magic': 0,
		'stat_strength': 0,
		'stat_luck': 0
	};

	var lifeBar = $('#lifeBar');

	var updateLifeBar = function(currentLife) {
		var life = 100 * currentLife / $scope.maxLife;
		lifeBar.attr('style', 'width: ' + life + '%;');
	};

	var regenerationPerSecond = function() {
		var currentLife, maxLife, statLife;

		if ( $scope.currentLife && $scope.maxLife )
		{
			currentLife = Number($scope.currentLife);
			maxLife = Number($scope.maxLife);
			statLife = Number($scope.stats['stat_life']);
			
			if ( currentLife < maxLife )
			{
				currentLife += (0.05 + statLife * 0.01);
				$scope.currentLife = currentLife.toFixed(2);

				updateLifeBar($scope.currentLife);
			}
			else
			{
				$scope.currentLife = maxLife;

				updateLifeBar($scope.currentLife);

				return;
			}
		}
		
		$timeout(regenerationPerSecond, 1000);
	};

	regenerationPerSecond();

	$scope.addStat = function(stat) {
		$scope.pointsToChange = parseInt($scope.pointsToChange);
		$scope.stats[stat] = parseInt($scope.stats[stat]);

		$scope.remainingPoints -= $scope.pointsToChange;
		$scope.stats[stat] += $scope.pointsToChange;

		$http({
			method: "POST",
			url: $scope.basePath + 'authenticated/addStat',
			data: {stat_name: stat, stat_amount: $scope.pointsToChange},
	   }).success(function(data) {
			if ( ! data )
			{
				$scope.remainingPoints += $scope.pointsToChange;
				$scope.stats[stat] -= $scope.pointsToChange;
			}
		}).error(function() {
			$scope.remainingPoints += $scope.pointsToChange;
			$scope.stats[stat] -= $scope.pointsToChange;
		});
	};
}])

.controller('CharactersController', ['$scope', '$http', function($scope, $http) {
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
}])

.controller('Item', ['$scope', '$http', function($scope, $http) {
	$scope.item = [];
	$scope.price = [];

	$scope.getItemTooltip = function(id) {
		$http.get($scope.basePath + 'api/itemTooltip/' + id).success(function (data) {
			$scope.item[id] = data + '<p>Precio: ' + $scope.price[id] + '</p>';
		});
	}

	$scope.onMouseOver = function(id)
	{
		if ( ! $scope.item[id] )
		{
			$scope.item[id] = 'Cargando...';
			$scope.getItemTooltip(id);
		}
	}
}])

.controller('MyCtrl2', [function() {

}]);