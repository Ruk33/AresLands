<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="areslands"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="areslands"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="areslands"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" ng-app="areslands"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>AresLands - {{ $title }}</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="{{ URL::base() }}/css/normalize.min.css">
		<link rel="stylesheet" href="{{ URL::base() }}/css/bootstrap.min.css">
		<!--<link rel="stylesheet" href="{{ URL::base() }}/css/main.min.css">-->
		<link rel="stylesheet" href="{{ URL::base() }}/css/main.css">

		
		<script src="{{ URL::base() }}/js/vendor/angular.min.js"></script>
		

		<!--<script src="{{ URL::base() }}/js/vendor/ui-bootstrap-custom-0.4.0.min.js"></script>-->
		
		
		<script src="{{ URL::base() }}/js/vendor/jquery-1.9.1.min.js"></script>
		<script src="{{ URL::base() }}/js/vendor/bootstrap.min.js"></script>
		
	</head>
	<body ng-init="basePath='{{ URL::base() }}/'">
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->

		<div id="wrap">
			<div class="container">
				@if ( Auth::check() && Auth::user()->name == 'Ruke' )
				<div class="dark-box pull-left"><b>Usuarios conectados:</b> {{ Character::where('last_activity_time', '>', time() - 300)->count() }}</div>
				@endif

				<a href="{{ URL::base() }}"><div class="logo"></div></a>
				<div class="row-fluid col-wrap">
					<div class="span2 menu col" style="width: 176px; ">
						@if ( Request::route()->controller == 'authenticated' )
							<div class="mini-player-display">
								<img src="{{ URL::base() }}/img/icons/race/{{ $character->race }}_{{ $character->gender }}.jpg" alt="" width="30px" height="30px" class="pull-left">
								<div class="pull-left" style="margin-left: 5px;">
									<a href="{{ URL::to('authenticated/character/' . $character->name) }}" style="color: rgb(231, 180, 47); font-size: 12px;"><b>{{ $character->name }}</b></a>
									<br>
									Nivel: {{ $character->level }}
								</div>

								<div class="pull-right">
									<img src="{{ URL::base() }}/img/xp.png" alt="" width="22px" height="18px" data-toggle="tooltip" data-placement="top" data-original-title="<b>Experiencia</b><br>{{ $character->xp }}/{{ $character->xp_next_level }}">

									<img src="{{ URL::base() }}/img/copper.gif" alt="" width="14px" height="15px" data-toggle="tooltip" data-placement="top" data-original-title="
									<b>Monedas</b>
									<br>
									{{ $coins['gold'] }} <img src='/img/gold.gif' style='vertical-align: text-bottom;'>
									{{ $coins['silver'] }} <img src='/img/silver.gif' style='vertical-align: text-bottom;'>
									{{ $coins['copper'] }} <img src='/img/copper.gif' style='vertical-align: text-bottom;'>">
	
									@if ( $character->clan_id != 0 )
										<a href="{{ URL::to('authenticated/clan/' . $character->clan_id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Accede a la página de tu grupo"><img src="{{ URL::base() }}/img/shield-icon.png" alt=""></a>
									@endif
									
								</div>
							</div>
						@endif
						<ul class="unstyled menu">
							@if ( Auth::check() && isset($character) )
								<li><a href="{{ URL::to('authenticated/index') }}"><img src="{{ URL::base() }}/img/menu/character.jpg" alt="" width="177px" height="36px"></a></li>
								<li style="position: relative;"><div style="position: absolute; top: 7px; right: 10px; color: white" data-toggle="tooltip" data-placement="top" data-original-title="Mensaje(s) sin leer"><span class="badge badge-warning">{{ $character->get_unread_messages_count() }}</span></div><a href="{{ URL::to('authenticated/messages') }}"><img src="{{ URL::base() }}/img/menu/messages.jpg" alt=""  width="177px" height="36px"></a></li>
								
								@if ( $character->can_travel() === true )
								<li><a href="{{ URL::to('authenticated/travel') }}"><img src="{{ URL::base() }}/img/menu/travel.jpg" alt="" width="177px" height="36px"></a></li>
								@endif
								
								@if ( $character->can_fight() )
								<li><a href="{{ URL::to('authenticated/battle') }}"><img src="{{ URL::base() }}/img/menu/battle.jpg" alt="" width="177px" height="36px"></a></li>
								@endif
	
								@if ( $character->can_explore() )
								<li><a href="{{ URL::to('authenticated/explore') }}"><img src="{{ URL::base() }}/img/menu/explore.jpg" alt="" width="177px" height="36px"></a></li>
								@endif
	
								<li><a href="{{ URL::to('authenticated/clan') }}"><img src="{{ URL::base() }}/img/menu/group.jpg" alt="" width="177px" height="36px"></a></li>
								<li><a href="{{ URL::to('authenticated/trade') }}"><img src="{{ URL::base() }}/img/menu/trade.jpg" alt="" width="177px" height="36px"></a></li>
								<li><a href="{{ URL::to('authenticated/characters') }}"><img src="{{ URL::base() }}/img/menu/characters.jpg" alt="" width="177px" height="36px"></a></li>
								<li><a href="{{ URL::to('authenticated/ranking') }}"><img src="{{ URL::base() }}/img/menu/ranking.jpg" alt="" width="177px" height="36px"></a></li>
								<li><a href="http://ironfist.com.ar/forums/index"><img src="{{ URL::base() }}/img/menu/forum.jpg" alt="Ir al foro" width="177px" height="36px"></a></li>
								<li><a href="{{ URL::to('authenticated/logout') }}"><img src="{{ URL::base() }}/img/menu/logout.jpg" alt="" width="177px" height="36px"></a></li>
							@else
								<li><a href="{{ URL::to('home/index') }}"><img src="{{ URL::base() }}/img/menu/inicio.jpg" alt="" width="177px" height="36px"></a></li>
								<li><a href="{{ URL::to('home/thanks') }}"><img src="{{ URL::base() }}/img/menu/thanks.jpg" alt="" width="177px" height="36px"></a></li>
							@endif
							<li><a href="{{ URL::to('game/index') }}" target="_blank"><img src="{{ URL::base() }}/img/menu/guide.jpg" width="177px" height="36px"></a></li>
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
					<img src="{{ URL::base() }}/img/ironfist-logo.png" width="212px" height="259px">
					<p style="color: white; font-size: 11px;">
						Todas las marcas aquí mencionadas son propiedad de sus respectivos dueños. 
						<br>
						©2013 IronFist. Todos los derechos reservados.
						<br>
						AresLands es una realidad gracias a <b>Vicente Buendia</b> y a su proyecto open <a href="http://sourceforge.net/projects/tierras/" target="_blank" rel="nofollow">Tierras de Leyenda</a> y a <a href="{{ URL::to('home/thanks') }}">todos los que contribuyeron con nosotros</a>.
					</p>
				</div>
			</div>
		</footer>

		<script src="{{ URL::base() }}/js/app.js"></script>
		
		<script src="{{ URL::base() }}/js/configuration.js"></script>
		<script src="{{ URL::base() }}/js/services.js"></script>
		<script src="{{ URL::base() }}/js/controllers.js"></script>
		<script src="{{ URL::base() }}/js/filters.js"></script>
		<script src="{{ URL::base() }}/js/directives.js"></script>
		

		<!--
		<script src="{{ URL::base() }}/js/vendor/angular.min.js"></script>

		<script src="{{ URL::base() }}/js/vendor/jquery-1.9.1.min.js"></script>
		<script src="{{ URL::base() }}/js/vendor/bootstrap.min.js"></script>

		<script src="{{ URL::base() }}/js/app.min.js"></script>

		-->

		<!--
		<script src="{{ URL::base() }}/js/vendor/modernizr-2.6.2.min.js"></script>
		-->

		<!--
		<script src="{{ URL::base() }}/js/main.min.js"></script>
		-->
		<script src="{{ URL::base() }}/js/libs/jquery.countdown.min.js"></script>

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
				var date = new Date();
				date.setSeconds(date.getSeconds() + time);

				$this.countdown({
					until: date,
					layout: '{hnn}:{mnn}:{snn}',
					expiryText: '<a href="" onclick="location.reload();">Actualizar</a>'
				});
			});
		</script>

		<!--
			<script>
				var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			</script>

			<script src="//google-analytics.com/ga.js" async></script>
		-->
	</body>
</html>
