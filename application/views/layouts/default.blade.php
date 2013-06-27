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

		<link rel="stylesheet" href="/css/normalize.min.css">
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<link rel="stylesheet" href="/css/main.css">

		<script src="/js/vendor/jquery-1.9.1.min.js"></script>
		<script src="/js/vendor/bootstrap.min.js"></script>
		<script src="/js/vendor/angular.min.js"></script>
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
						@if ( Request::route()->controller == 'authenticated' )
							<div class="mini-player-display">
								<img src="/img/icons/race/{{ Session::get('character')->race }}_{{ Session::get('character')->gender }}.jpg" alt="" class="pull-left">
								<div class="pull-left" style="margin-left: 5px;">
									<a href="{{ URL::to('authenticated/character/' . Session::get('character')->name) }}" style="color: rgb(231, 180, 47); font-size: 12px;"><b>{{ Session::get('character')->name }}</b></a>
									<div class="pull-right">
										<img src="/img/copper.gif" alt="" data-toggle="tooltip" data-placement="top" data-original-title="
										{{ $coins['gold'] }} <img src='/img/gold.gif' style='vertical-align: text-bottom;'>
										{{ $coins['silver'] }} <img src='/img/silver.gif' style='vertical-align: text-bottom;'>
										{{ $coins['copper'] }} <img src='/img/copper.gif' style='vertical-align: text-bottom;'>">
									</div>
									<br>
									Nivel: {{ Session::get('character')->level }}
								</div>
							</div>
						@endif
						<ul class="unstyled menu">
							@if ( Auth::check() )
								<li><a href="{{ URL::to('authenticated/index') }}"><img src="/img/menu/character.jpg" alt=""></a></li>
								<li style="position: relative;"><div style="position: absolute; top: 7px; right: 10px; color: white" data-toggle="tooltip" data-placement="top" data-original-title="Mensaje(s) sin leer"><span class="badge badge-warning">{{ Session::get('character')->get_unread_messages_count() }}</span></div><a href="{{ URL::to('authenticated/messages') }}"><img src="/img/menu/messages.jpg" alt=""></a></li>
								<li><a href="{{ URL::to('authenticated/travel') }}"><img src="/img/menu/travel.jpg" alt=""></a></li>
								<li><a href="{{ URL::to('authenticated/battle') }}"><img src="/img/menu/battle.jpg" alt=""></a></li>
								<li><a href="{{ URL::to('authenticated/clan') }}"><img src="/img/menu/group.jpg" alt=""></a></li>
								<li><a href="{{ URL::to('authenticated/trade') }}"><img src="/img/menu/trade.jpg" alt=""></a></li>
								<li><a href="{{ URL::to('authenticated/characters') }}"><img src="/img/menu/characters.jpg" alt=""></a></li>
							@else
								<li><img src="/img/menu/inicio.jpg" alt=""></li>
							@endif
						</ul>
					</div>

					<div class="span10 content col" style="width: 764px; margin-left: 0;">
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
					<img src="/img/ironfist-logo.png">
					<p style="color: white; font-size: 11px;">
						Todas las marcas aquí mencionadas son propiedad de sus respectivos dueños. 
						<br>
						©2013 IronFist. Todos los derechos reservados.
					</p>
				</div>
			</div>
		</footer>

		<script src="/js/libs/jquery.countdown.js"></script>

		<script>
			/*
			 *	Iniciamos los tooltips
			 */
			$('[data-toggle="tooltip"]').tooltip({ html: true });
			$('[data-toggle="popover"]').popover({ html: true });

			/*
			 *	Iniciamos los timers
			 */
			$('.timer').each(function() {
				var $this = $(this);
				var time = $this.data('endtime');
				var date = new Date(time * 1000);

				$this.countdown({
					until: date,
					layout: '{hnn}:{mnn}:{snn}',
					expiryText: '<a href="" onclick="location.reload();">Actualizar</a>'
				});
			});
		</script>

		<script src="/js/vendor/modernizr-2.6.2.min.js"></script>

		<!--
			<script>
				var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			</script>

			<script src="//google-analytics.com/ga.js" async></script>
		-->
	</body>
</html>
