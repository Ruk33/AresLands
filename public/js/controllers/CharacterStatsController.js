var CharacterStatsController = function($scope, $http, $timeout) {
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
		$scope.remainingPoints--;
		$scope.stats[stat]++;

		$http({
            method: "POST",
            url: $scope.basePath + 'authenticated/addStat',
            data: {stat_name: stat},
       }).success(function(data) {
			if ( ! data )
			{
				$scope.remainingPoints++;
				$scope.stats[stat]--;
			}
		}).error(function() {
			$scope.remainingPoints++;
			$scope.stats[stat]--;
		});
	};
};

// Requerimientos
CharacterStatsController.$inject = ['$scope', '$http', '$timeout'];