<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="areslands"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="areslands"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="areslands"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" ng-app="areslands"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>AresLands - {{ $title }}</title>
		<meta name="description" content="Juego derivado del antiguo Tierra de Leyenda, restaurado y mejorado por IronFist. ¡Únete a este mundo épico y vive grandes aventuras!">
		<meta name="viewport" content="width=device-width">
        
        <link rel="stylesheet" type="text/css" href="{{ Minifier::make(array('//css/normalize.min.css', '//css/bootstrap.min.css', '//css/main.css')) }}">

		@if ( Request::env() == 'local' )
			<script src="{{ URL::base() }}/js/vendor/jquery-1.9.1.min.js"></script>
			<script src="{{ URL::base() }}/js/vendor/bootstrap.min.js"></script>
			<script src="{{ URL::base() }}/js/vendor/angular.min.js"></script>
			<script src="{{ URL::base() }}/js/vendor/angular-resource.min.js"></script>
		@else
            <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>
			<script type="text/javascript" src="{{ Minifier::make(array('//js/vendor/jquery-1.9.1.min.js', '//js/vendor/bootstrap.min.js', '//js/vendor/angular.min.js', '//js/vendor/angular-resource.min.js')) }}"></script>
		@endif
    </head>
    
    <body ng-init="basePath='{{ URL::base() }}/'">
        <div id="wrap">
            <div class="container">
                <a href="{{ URL::base() }}" class="block-center logo"></a>
                <div class="row" style="margin-top: 18px;">
                    {{ $content }}
                </div>
            </div>
        </div>
        
        @if ( Request::env() == 'local' )
			<script src="{{ URL::base() }}/js/app.js"></script>
			<script src="{{ URL::base() }}/js/configuration.js"></script>
			<script src="{{ URL::base() }}/js/services.js"></script>
			<script src="{{ URL::base() }}/js/controllers.js"></script>
			<script src="{{ URL::base() }}/js/filters.js"></script>
			<script src="{{ URL::base() }}/js/directives.js"></script>
		@else
			<script type="text/javascript" src="{{ Minifier::make(array('//js/app.js', '//js/configuration.js', '//js/services.js', '//js/controllers.js', '//js/filters.js', '//js/directives.js')) }}"></script>
		@endif
        
        <script>            
            $(document).ready(function() {
				/*
				 *	Iniciamos los tooltips
				 */
				$('[data-toggle="tooltip"]').tooltip({ html: true, container: '#wrap' });
				$('[data-toggle="popover"]').popover({ html: true });
            });
        </script>
    </body>
</html>