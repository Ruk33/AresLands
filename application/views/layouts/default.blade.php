<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" xmlns:ng="http://angularjs.org" id="ng-app"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" xmlns:ng="http://angularjs.org" id="ng-app"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" xmlns:ng="http://angularjs.org" id="ng-app"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" ng-app> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>AresLands - {{ $title }}</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="css/normalize.min.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/main.css">
	</head>
	<body>
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->

		<div id="wrap">
			<div class="container">
				<div class="logo"></div>
				<div class="row-fluid col-wrap">
					<div class="span2 menu col" style="width: 176px; ">
						@if (Auth::check())
							<div class="mini-player-display">
								<img src="img/icons/race/enano_ico.jpg" alt="" class="pull-left">
								<div class="pull-left" style="margin-left: 5px;">
									<b style="color: rgb(231, 180, 47); font-size: 12px;">Ruke</b>
									<br>
									Nivel: 17
								</div>
							</div>
						@endif
						<ul class="unstyled">
							<li><img src="img/inicio.jpg" alt=""></li>
							<li><img src="img/inicio.jpg" alt=""></li>
							<li><img src="img/inicio.jpg" alt=""></li>
						</ul>
					</div>

					<div class="span10 content col" style="width: 764px; margin-left: 0;">
						@if (Auth::check())
							<div class="bar">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
								<img src="img/icons/actions/travel-disabled.png" alt="">
							</div>
							<hr class="line">
						@endif
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
					<img src="img/ironfist-logo.png">
					<p style="color: white; font-size: 11px;">
						Todas las marcas aquí mencionadas son propiedad de sus respectivos dueños. 
						<br>
						©2013 IronFist. Todos los derechos reservados.
					</p>
				</div>
			</div>
		</footer>

		<script src="/js/vendor/angular.min.js"></script>

		<script src="/js/vendor/modernizr-2.6.2.min.js"></script>
		<script src="/js/vendor/jquery-1.9.1.min.js"></script>
		<script src="/js/vendor/bootstrap.min.js"></script>

		<!--
			<script>
				var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			</script>

			<script src="//google-analytics.com/ga.js" async></script>
		-->
	</body>
</html>
