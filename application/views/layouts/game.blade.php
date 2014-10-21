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

		<link rel="stylesheet" href="{{ URL::base() }}/css/normalize.min.css">
		<link rel="stylesheet" href="{{ URL::base() }}/css/bootstrap.min.css">

		<link rel="stylesheet" type="text/css" href="{{ Minifier::make(array('//css/main.css')) }}">
		<script type="text/javascript" src="{{ Minifier::make(array('//js/vendor/jquery-1.9.1.min.js', '//js/vendor/bootstrap.min.js')) }}"></script>
	</head>
	<body>
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->

		<div id="wrap">
			<div class="container">
				<!--<div class="dark-box pull-left"><b>Usuarios conectados:</b> {{ Character::where('last_activity_time', '>', time() - 300)->count() }}</div>-->
				<a href="{{ URL::base() }}"><div class="logo"></div></a>
				<div class="row-fluid col-wrap">
					<div class="span12 content col">
						<div id="content">
							{{ $content }}
						</div> <!-- /content -->
					</div>
				</div>
			</div> <!-- /container -->
			<div id="push"></div>
		</div> <!-- /wrap -->
		<div id="footer">
			<div class="text-center">
				<div>
                    <a href="//titangames.com.ar">
					    <img src="{{ URL::base() }}/img/logo-titangames.png">
                    </a>
                    <p>
                        Todas las marcas aquí mencionadas son propiedad de sus respectivos dueños.
                        <br>
                        ©2014 TitanGames. Todos los derechos reservados.
                        <a href="//titangames.com.ar/privacy" target="_blank">Política de privacidad</a>
                        -
                        <a href="//titangames.com.ar/terms" target="_blank">Condiciones generales de uso</a>
                        <br>
                        AresLands es una realidad gracias a <b>Vicente Buendia</b> y a su proyecto open <a href="http://sourceforge.net/projects/tierras/" target="_blank" rel="nofollow">Tierras de Leyenda</a> y a <a href="{{ URL::to('home/thanks') }}">todos los que contribuyeron con nosotros</a>.
                    </p>
				</div>
			</div>
		</div>

		<script>
			/*
			 *	Iniciamos los tooltips
			 */
			$('[data-toggle="tooltip"]').tooltip({ html: true });
			$('[data-toggle="popover"]').popover({ html: true });
		</script>
	</body>
</html>
