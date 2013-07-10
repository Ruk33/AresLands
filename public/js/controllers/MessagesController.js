var MessagesController = function($scope, $http) {
	$scope.messages = [];

	$http.post($scope.basePath + '/authenticated/messages')
		.success(function(data) {
			console.log(data);
			$scope.messages = data;
		})
		.error(function(){
			$scope.error = 'No se pudieron traer los datos del servidor, por favor inténtalo en unos segundos.'
		});
};

// Requerimientos
MessagesController.$inject = ['$scope', '$http'];