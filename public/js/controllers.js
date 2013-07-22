'use strict';

angular.module('areslands.controllers', []).

controller('CharacterStatsController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
	$scope.remainingPoints = 0;
	$scope.maxLife = 1;
	$scope.currentLife = 0;
	$scope.stats = {
		'stat_life': 0,
		'stat_dexterity': 0,
		'stat_magic': 0,
		'stat_strength': 0,
		'stat_luck': 0
	};

	var regenerationPerSecond = function() {
		if ( $scope.currentLife != $scope.maxLife )
		{
			$scope.currentLife = +$scope.currentLife + (0.05 + parseInt($scope.stats['stat_life']) * 0.01);
			$scope.currentLife = $scope.currentLife.toFixed(2);

			if ( $scope.currentLife > $scope.maxLife )
			{
				$scope.currentLife = $scope.maxLife;
			}
			else
			{
				$timeout(regenerationPerSecond, 1000);
			}
		}

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

.controller('MyCtrl2', [function() {

}]);