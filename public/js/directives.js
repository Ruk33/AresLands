'use strict';

angular.module('areslands.directives', []).

directive('dynamicTooltip', function() {
	return function(scope, element, attrs) {
		var content;

		function updateToolTip(value)
		{
			$(element).attr('data-original-title', value).tooltip('fixTitle').tooltip('show');
		}

		/*
		 *	Handle para cuando el valor cambia
		 */
		scope.$watch(attrs.dynamicTooltip, function(value) {
			updateToolTip(value);
		});

		$(element).tooltip({
			html: true, 
			/*placement: 'top', 
			title: attrs.dinamicTooltip*/
		});
	};
}).

directive('tabs', function() {
	return {
		restrict: 'E',
		transclude: true,
		scope: {},
		controller: function($scope, $element) {
			var panes = $scope.panes = [];

			$scope.select = function(pane) {
				angular.forEach(panes, function(pane) {
					pane.selected = false;
				});
				pane.selected = true;
			}

			this.addPane = function(pane) {
				if (panes.length == 0) $scope.select(pane);
				panes.push(pane);
			}
		},
		template:
			'<div class="tabbable">' +
				'<ul class="nav nav-tabs">' +
					'<li ng-repeat="pane in panes" ng-class="{active:pane.selected}">'+
						'<a href="" ng-click="select(pane)">{{pane.title}}</a>' +
					'</li>' +
				'</ul>' +
				'<div class="tab-content" ng-transclude></div>' +
			'</div>',
		replace: true
	};
}).

directive('pane', function() {
	return {
		require: '^tabs',
		restrict: 'E',
		transclude: true,
		scope: { title: '@' },
		link: function(scope, element, attrs, tabsCtrl) {
			tabsCtrl.addPane(scope);
		},
		template:
			'<div class="tab-pane" ng-class="{active: selected}" ng-transclude>' +
			'</div>',
		replace: true
	};
}).

directive('appVersion', ['version', function(version) {
	return function(scope, elm, attrs) {
		elm.text(version);
	};
}]);