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
		<!--<link rel="stylesheet" href="{{ URL::base() }}/css/main.min.css">-->
		<link rel="stylesheet" href="{{ URL::base() }}/css/main.css">

		<!--<script src="{{ URL::base() }}/js/vendor/ui-bootstrap-custom-0.4.0.min.js"></script>-->
	
		<script src="{{ URL::base() }}/js/vendor/jquery-1.9.1.min.js"></script>
		<script src="{{ URL::base() }}/js/vendor/bootstrap.min.js"></script>
		
	</head>
	<body ng-init="basePath='{{ URL::base() }}/'">
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->

		@if ( Session::has('activityBarReward') )
			<?php Session::forget('activityBarReward'); ?>
			<div id="activityBarRewardMessage" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<span class="ui-button button" style="margin-left: 120px;">
						<i class="button-icon check"></i>
						<span class="button-content" style="color: orange;">
							¡Completaste la barra de actividad!
						</span>
					</span>
					<p style="margin-top: 20px; margin-left: 120px;">¡Bien hecho!, haz completado la barra de actividades. Revisa tus mensajes para conocer tus <b>recompensas</b>.</p>
				</div>
			</div>

			<script>
				$('#activityBarRewardMessage').modal();
			</script>
		@endif

		<div id="wrap">
			<div class="container">
				@if ( Auth::check() && Auth::user()->name == 'Ruke' )
					<div class="dark-box pull-left">
						<b>Usuarios conectados:</b> 
						{{ Character::where('last_activity_time', '>', time() - 300)->count() }}
					</div>
				@endif

				<a href="{{ URL::base() }}"><div class="logo"></div></a>
				<div class="row-fluid col-wrap">
					<div class="span2 menu col" style="width: 176px; ">
						@if ( Request::route()->controller == 'authenticated' )
							<div class="mini-player-display">
								<div class="icon-race-30 icon-race-30-{{ $character->race }}_{{ $character->gender }} pull-left"></div>
								<div class="pull-left" style="margin-left: 5px;">
									<a href="{{ URL::to('authenticated/character/' . $character->name) }}" style="color: rgb(231, 180, 47); font-size: 12px;">
										<b>{{ $character->name }}</b>
									</a>
									<br>
									Nivel: {{ $character->level }}
								</div>

								<ul class="inline pull-right">
									<li style="padding: 0; vertical-align: middle;">
										<img src="{{ URL::base() }}/img/xp.png" alt="Experiencia" width="22px" height="18px" data-toggle="tooltip" data-placement="top" data-original-title="<b>Experiencia</b><br>{{ $character->xp }}/{{ $character->xp_next_level }}">
									</li>

									<li style="padding: 0; vertical-align: middle;">
										<i class="coin coin-copper" data-toggle="tooltip" data-placement="top" data-original-title="
										<b>Monedas</b>
										<ul class='inline' style='margin: 0;'>
											<li><i class='coin coin-gold pull-left'></i> {{ $coins['gold'] }}</li>
											<li><i class='coin coin-silver pull-left'></i> {{ $coins['silver'] }}</li>
											<li><i class='coin coin-copper pull-left'></i> {{ $coins['copper'] }}</li>
										</ul>" alt="Monedas"></i>
									</li>
	
									@if ( $character->clan_id != 0 )
										<li style="padding: 0; vertical-align: middle;">
											<a href="{{ URL::to('authenticated/clan/' . $character->clan_id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Accede a la página de tu grupo"><img src="{{ URL::base() }}/img/shield-icon.png" alt="Grupo" width="16px" height="19px"></a>
										</li>
									@endif
								</ul>
							</div>
						@endif
						<ul class="unstyled menu">
							@if ( Auth::check() && isset($character) )
								<li><a href="{{ URL::to('authenticated/index') }}" class="menu menu-character"></a></li>
								<li style="position: relative;">
									<a href="{{ URL::to('authenticated/messages') }}" class="menu menu-messages">
										@if ( $character->get_unread_messages_count() > 0 )
										<div style="position: absolute; top: 7px; right: 10px; color: white" data-toggle="tooltip" data-placement="top" data-original-title="Mensaje(s) sin leer">
											<span class="badge badge-warning">
												{{ $character->get_unread_messages_count() }}
											</span>
										</div>
										@endif
									</a>
								</li>
								
								@if ( $character->can_travel() === true )
								<li><a href="{{ URL::to('authenticated/travel') }}" class="menu menu-travel"></a></li>
								@endif
								
								@if ( $character->can_fight() )
								<li><a href="{{ URL::to('authenticated/battle') }}" class="menu menu-battle"></a></li>
								@endif
	
								@if ( $character->can_explore() )
								<li><a href="{{ URL::to('authenticated/explore') }}" class="menu menu-explore"></a></li>
								@endif
	
								<li><a href="{{ URL::to('authenticated/clan') }}" class="menu menu-group"></a></li>
								<li><a href="{{ URL::to('authenticated/trade') }}" class="menu menu-trade"></a></li>
								<li><a href="{{ URL::to('authenticated/characters') }}" class="menu menu-characters"></a></li>
								<li><a href="{{ URL::to('authenticated/ranking') }}" class="menu menu-ranking"></a></li>
								<li><a href="{{ URL::to('authenticated/orbs') }}" class="menu menu-orbs"></a></li>
								<li><a href="http://ironfist.com.ar/forums/index" class="menu menu-forum" target="_blank"></a></li>
								<li><a href="{{ URL::to('authenticated/logout') }}" class="menu menu-logout"></a></li>
							@else
								<li><a href="{{ URL::to('home/index') }}" class="menu menu-start"></a></li>
								<li><a href="{{ URL::to('home/thanks') }}" class="menu menu-thanks"></a></li>
							@endif
							<li><a href="{{ URL::to('game/index') }}" class="menu menu-guide" target="_blank"></a></li>
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
		</div>

		<script src="{{ URL::base() }}/js/vendor/angular.min.js"></script>

		@if ( Request::env() == 'local' )
			<script src="{{ URL::base() }}/js/app.js"></script>
			
			<script src="{{ URL::base() }}/js/configuration.js"></script>
			<script src="{{ URL::base() }}/js/services.js"></script>
			<script src="{{ URL::base() }}/js/controllers.js"></script>
			<script src="{{ URL::base() }}/js/filters.js"></script>
			<script src="{{ URL::base() }}/js/directives.js"></script>
		@else
			<script src="{{ URL::base() }}/js/app.min.js"></script>
		@endif

		<!--
		<script src="{{ URL::base() }}/js/vendor/modernizr-2.6.2.min.js"></script>
		-->

		<script src="{{ URL::base() }}/js/libs/jquery.countdown.min.js"></script>

		<script>
			/*
			 *	Iniciamos los tooltips
			 */
			$('[data-toggle="tooltip"]').tooltip({ html: true/*, container: '#tooltip'*/ });
			$('[data-toggle="popover"]').popover({ html: true/*, container: '#tooltip'*/ });

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
