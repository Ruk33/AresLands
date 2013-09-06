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

.controller('Skill', ['$scope', '$http', function($scope, $http) {
	$scope.skill = [];

	$scope.getSkillNextLevelToolTip = function(skillId, level) {
		$http.get($scope.basePath + 'api/skill/' + skillId + '/' + level).success(function (data) {
			if ( data.description ) {
				$scope.skill[skillId] += '<hr><strong>Siguiente nivel...</strong><p>' + data.description + '</p>';

				if ( data.requirements_text ) {
					$scope.skill[skillId] += '<strong>Requiere</strong><p>' + data.requirements_text + '</p>';
				}
			}
		});
	};

	$scope.getSkillTooltip = function(skillId, level, showRequirements, next) {
		$http.get($scope.basePath + 'api/skill/' + skillId + '/' + level).success(function (data) {
			$scope.skill[skillId] = '<strong>' + data.name + '</strong> - Nivel ' + level + '<p>' + data.description + '</p>';
			if ( showRequirements && data.requirements_text ) {
				$scope.skill[skillId] += '<strong>Requiere</strong><p>' + data.requirements_text + '</p>';
			}
			if ( next ) {
				$scope.getSkillNextLevelToolTip(skillId, level + 1);
			}
		});
	};

	$scope.onMouseOver = function(skillId, level, showRequirements, next) {
		if ( ! $scope.skill[skillId] ) {
			$scope.skill[skillId] = 'Cargando...';
			$scope.getSkillTooltip(skillId, level, showRequirements, next);
		}
	};
}])

.controller('Chat', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
	$scope.chat = {};
	$scope.chat.messages = [];
	$scope.chat.connected = [];
	$scope.chat.last = [];
	$scope.chat.show = false;
	$scope.chat.channel = 0;

	var getConnected = function() {
		$http.get($scope.basePath + 'chat/connected/' + $scope.chat.channel).success(function (data) {
			if ( ! $scope.chat.connected[$scope.chat.channel] ) {
				$scope.chat.connected[$scope.chat.channel] = [];
			}
			
			$scope.chat.connected[$scope.chat.channel] = data;
		});

		$timeout(getConnected, 300000);
	};

	var getMessages = function() {
		$http.get($scope.basePath + 'chat/messages/' + $scope.chat.last[$scope.chat.channel] + '/' + $scope.chat.channel).success(function (data) {
			if ( data.length > 0 ) {
				if ( ! $scope.chat.last[$scope.chat.channel] ) {
					$scope.chat.last[$scope.chat.channel] = [];
				}

				if ( ! $scope.chat.messages[$scope.chat.channel] ) {
					$scope.chat.messages[$scope.chat.channel] = [];
				}

				$scope.chat.last[$scope.chat.channel] = data[data.length-1].time;

				for (var i in data) {
					$scope.chat.messages[$scope.chat.channel].unshift(data[i]);
				}
			}
		});

		$timeout(getMessages, 2000);
	};

	$scope.sendMessage = function() {
		$http.post($scope.basePath + 'chat/message', { 'message': $scope.chat.input, 'channel': $scope.chat.channel });
		$scope.chat.input = '';
	};

	$scope.switchChannel = function(channel) {
		if ( channel != $scope.chat.channel ) {
			$scope.chat.channel = channel;

			if ( ! $scope.chat.messages[channel] ) {
				$scope.chat.messages[channel] = [];
			}

			if ( channel > 0 ) {
				$scope.chat.messages[channel].unshift({ 'name': 'AresLands BOT', 'message': 'Cambiaste de canal a: Clan' });
			} else {
				$scope.chat.messages[channel].unshift({ 'name': 'AresLands BOT', 'message': 'Cambiaste de canal a: General' });
			}

			getConnected();
		}
	};

	getConnected();
	getMessages();
}])

.controller('MyCtrl2', [function() {

}]);