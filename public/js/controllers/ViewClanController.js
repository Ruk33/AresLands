var ViewClanController = function($scope, $http) {
	$scope.errorDiv = false;

	var editClanMessage = function(message) {
		$http.post($scope.basePath + '/authenticated/clan/editMessage', {'message':message});
	};

	CKEDITOR.on('instanceReady', function(instance)
	{
		instance.editor.document.on('keydown', function(ev)
		{
			if ( instance.editor.getData().length >= 1000 )
			{
				ev.data.preventDefault();
			}
		});

		instance.editor.on('blur', function()
		{
			editClanMessage(instance.editor.getData());
		});
	});
};

// Requerimientos
ViewClanController.$inject = ['$scope', '$http'];