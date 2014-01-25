'use strict';

angular.module('areslands.controllers', []).

controller('CharacterController', ['$scope', '$http', '$timeout', 'CharacterOfLoggedUser', 'DividedCoin', 'StatPrice', 'BASE_PATH', function($scope, $http, $timeout, CharacterOfLoggedUser, DividedCoin, StatPrice, BASE_PATH)
{
	$scope.character = {};
	$scope.statsPrices = {};
	$scope.pointsToChange = 1;

	CharacterOfLoggedUser.get(function(character)
	{
		$scope.character = character;
	});

	$scope.$watch('character.stat_strength', function(value)
	{
		StatPrice.get({stat: 'stat_strength'}, function(price)
		{
			console.log(price.price);
			DividedCoin.get({amount: price.price}, function(coins)
			{
				$scope.statsPrices.strength = 'Precio: ' + coins.text;
			});
		});
	});

	$scope.$watch('character.stat_dexterity', function(value)
	{
		StatPrice.get({stat: 'stat_dexterity'}, function(price)
		{
			DividedCoin.get({amount: price.price}, function(coins)
			{
				$scope.statsPrices.dexterity = 'Precio: ' + coins.text;
			});
		});
	});

	$scope.$watch('character.stat_resistance', function(value)
	{
		StatPrice.get({stat: 'stat_resistance'}, function(price)
		{
			DividedCoin.get({amount: price.price}, function(coins)
			{
				$scope.statsPrices.resistance = 'Precio: ' + coins.text;
			});
		});
	});

	$scope.$watch('character.stat_magic', function(value)
	{
		StatPrice.get({stat: 'stat_magic'}, function(price)
		{
			DividedCoin.get({amount: price.price}, function(coins)
			{
				$scope.statsPrices.magic = 'Precio: ' + coins.text;
			});
		});
	});

	$scope.$watch('character.stat_magic_skill', function(value)
	{
		StatPrice.get({stat: 'stat_magic_skill'}, function(price)
		{
			DividedCoin.get({amount: price.price}, function(coins)
			{
				$scope.statsPrices.magic_skill = 'Precio: ' + coins.text;
			});
		});
	});

	$scope.$watch('character.stat_magic_resistance', function(value)
	{
		StatPrice.get({stat: 'stat_magic_resistance'}, function(price)
		{
			DividedCoin.get({amount: price.price}, function(coins)
			{
				$scope.statsPrices.magic_resistance = 'Precio: ' + coins.text;
			});
		});
	});

	$scope.addStat = function(stat) {
		// Verificamos que el atributo exista
		if ( $scope.character[stat] == null )
		{
			return;
		}

		$scope.character[stat] = Number($scope.character[stat]);
		$scope.pointsToChange = Number($scope.pointsToChange);

		$scope.character[stat] += $scope.pointsToChange;
		$scope.character.points_to_change -= $scope.pointsToChange;

		$http({
			method: "POST",
			url: BASE_PATH + 'authenticated/addStat',
			data: {'stat_name': stat, 'stat_amount': $scope.pointsToChange},
	   }).success(function(data) {
			if ( ! data )
			{
				$scope.character.points_to_change += $scope.pointsToChange;
				$scope.character[stat] -= $scope.pointsToChange;
			}
		}).error(function() {
			$scope.character.points_to_change += $scope.pointsToChange;
			$scope.character[stat] -= $scope.pointsToChange;
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

.controller('Character', ['$scope', '$http', function($scope, $http) {
	$scope.character = [];
	
	$scope.getCharacter = function(name) {
		$http.get($scope.basePath + 'api/character/' + name).success(function(data) {
			$scope.characters[name] = data;
		});
	};
}])

.controller('Item', ['$scope', '$http', function($scope, $http) {
	$scope.item = [];
	$scope.price = [];

	$scope.getItemTooltip = function(id) {
		$http.get($scope.basePath + 'api/itemTooltip/' + id).success(function (data) {
			$scope.item[id] = data + '<p>Precio: ' + $scope.price[id] + '</p>';
		});
	};

	$scope.onMouseOver = function(id)
	{
		if ( ! $scope.item[id] )
		{
			$scope.item[id] = 'Cargando...';
			$scope.getItemTooltip(id);
		}
	};
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

	var showConnected = function() {
		$http.get($scope.basePath + 'chat/connected/' + $scope.chat.channel).success(function (data) {
			var connectedCharacters = '';

			for ( var i in data ) {
				connectedCharacters += data[i].name + ', ';
			}
			
			sendBotMessage('Personajes conectados: ' + connectedCharacters.slice(0, -2));
		});
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
	
	var sendBotMessage = function(message) {
		$scope.chat.messages[$scope.chat.channel].unshift({ 'name': 'AresLands BOT', 'message': message });
	};
	
	var clearMessages = function() {
		$scope.chat.messages[$scope.chat.channel] = [];
	};
	
	$scope.formatMessageTime = function(time) {
		if ( ! time ) {
			return;
		}
		
		var d = new Date(time * 1000);
		var hours = new String(d.getHours());
		var minutes = new String(d.getMinutes());
		var seconds = new String(d.getSeconds());
		
		if ( hours.length != 2 ) {
			hours = '0' + hours;
		}
		
		if ( minutes.length != 2 ) {
			minutes = '0' + minutes;
		}
		
		if ( seconds.length != 2 ) {
			seconds = '0' + seconds;
		}
		
		return hours + ':' + minutes + ':' + seconds;
	};

	$scope.sendMessage = function() {
		switch ( $scope.chat.input ) {
			case '/online':
				showConnected();
				break;
				
			case '/clear':
				clearMessages();
				break;
				
			default:
				$http.post($scope.basePath + 'chat/message', { 'message': $scope.chat.input, 'channel': $scope.chat.channel });
		}

		$scope.chat.input = '';
	};

	$scope.switchChannel = function(channel) {
		if ( channel != $scope.chat.channel ) {
			$scope.chat.channel = channel;

			if ( ! $scope.chat.messages[channel] ) {
				$scope.chat.messages[channel] = [];
			}

			if ( channel > 0 ) {
				sendBotMessage('Cambiaste de canal a: Clan');
			} else {
				sendBotMessage('Cambiaste de canal a: General');
			}
		}
	};

	getMessages();
}])

.controller('MyCtrl2', [function() {

}]);