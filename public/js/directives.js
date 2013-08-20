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

/*
directive('droppable', function() {
	return function(scope, element, attrs) {

	};
}).

directive('draggable', function() {
	return function(scope, element, attrs) {
		var el = {};

		el.dom = $(element);
		el.allowedDroppable = $('[droppable="'+ attrs.draggable +'"]');
		el.css = {
			zIndex: el.dom.css('z-index')
		};

		el.isMouseDown = false;

		el.func = {
			onMouseMove: function(e) {
				if ( el.isMouseDown ) {
					el.dom.offset({
						left: e.pageX + el.css.pos_x - el.css.drg_w,
						top: e.pageY + el.css.pos_y - el.css.drg_h,
					});
				}
			},

			onMouseUp: function() {
				el.dom.css({top: '', left: ''});
				
				if ( el.droppable ) {
					el.dom.detach().appendTo(el.droppable);
				}

				el.dom.css('z-index', el.css.zIndex);
				el.isMouseDown = false;
			}
		}

		el.func.onMouseOver = function() {
			if ( el.isMouseDown ) {
				el.droppable = $(this);
			}
		}

		el.func.onMouseDown = function(e) {
			e.preventDefault();
			el.isMouseDown = true;

			el.css.drg_h = el.dom.outerHeight();
			el.css.drg_w = el.dom.outerWidth();
			el.css.pos_y = el.dom.offset().top + el.css.drg_h - e.pageY;
			el.css.pos_x = el.dom.offset().left + el.css.drg_w - e.pageX;

			el.dom.css('z-index', 99999);
			el.dom.on('mousemove', el.func.onMouseMove);
		}

		el.func.init = function() {
			el.dom.css('cursor', 'move');

			el.dom.on('mousedown', el.func.onMouseDown);
			el.dom.on('mouseup', el.func.onMouseUp);

			$('[droppable="'+ attrs.draggable +'"]').on('mouseover', el.func.onMouseOver);
		};

		el.func.init();
	};
}).
*/

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