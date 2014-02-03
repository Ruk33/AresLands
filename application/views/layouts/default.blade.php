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

		<link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>
		
		<link rel="stylesheet" href="{{ URL::base() }}/css/normalize.min.css">
		<link rel="stylesheet" href="{{ URL::base() }}/css/bootstrap.min.css">

		<link rel="stylesheet" type="text/css" href="{{ Minifier::make(array('//css/main.css')) }}">
		<script type="text/javascript" src="{{ Minifier::make(array('//js/vendor/jquery-1.9.1.min.js', '//js/vendor/bootstrap.min.js', '//js/vendor/angular.min.js', '//js/vendor/angular-resource.min.js')) }}"></script>
	</head>

	<?php flush(); ?>

	<body ng-init="basePath='{{ URL::base() }}/'">
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->

		@if ( Session::has('modalMessage') )
			<?php 
			$modalMessage = Session::get('modalMessage');
			Session::forget('modalMessage');
			?>
			<div id="modalMessage" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<span class="ui-button button" style="margin-left: 120px;">
						<i class="button-icon check"></i>
						<span class="button-content" style="color: orange;">
							<?php
							switch ( $modalMessage )
							{
								case 'activityBar':
									echo '¡Completaste la barra de actividad!';
									break;

								case 'loggedOfDay':
									echo '¡Recompensa por ingreso del día!';
									break;

								case 'chest':
									echo '¡Has recibido la recompensa del cofre!';
									break;
							}
							?>
						</span>
					</span>
					<p style="margin-top: 20px; margin-left: 120px;">
						<?php
						switch ( $modalMessage )
						{
							case 'activityBar':
								echo '¡Bien hecho!, haz completado la barra de actividades. Revisa tus mensajes para conocer tus <b>recompensas</b>.';
								break;

							case 'loggedOfDay':
								echo '<b>Logged of day</b> es la <b>recompensa</b> que se otorga todos los días a los jugadores que se atreven a ingresar, ¡bien hecho!.';
								break;

							case 'chest':
								$itemId = (int) Session::get('chest');
								echo '<div class="inventory-item pull-left" style="margin-left: 25px;">
										<img src="' . URL::base() . '/img/icons/items/' . $itemId . '.png" width="80px" height="80px">
									</div>
									<div style="margin-left: 125px;">Has abierto el cofre y dentro del mismo, se encontraba esto. ¡Felicitaciones!</div>';
								break;
						}
						?>
					</p>
				</div>
			</div>

			<script>
				$('#modalMessage').modal();
			</script>
		@endif
		
		@if ( isset($character) && ! $character->characteristics )
			<div id="characteristicsModal" class="modal hide fade">
				<div class="modal-body">
					<ul id="characteristicsTabs">
					</ul>
					
					{{ Form::open(URL::to('authenticated/setCharacteristics')) }}
					
					{{ Form::token() }}
					
					<div class="tab-content">
						<div class="tab-pane active" id="-1">
							<div class="introduction span6" style="padding: 10px; margin-left: 10px; margin-top: 50px;">
								<h3 class="text-center">¡Bienvenido/a a AresLands!</h3>
								<p>Bienvenido aventurero/a, veo en tus ojos que te esperan grandes aventuras, feroces enemigos y leales amigos. Pero cuéntame ¿tienes algún talento?, ¿estás listo para sobrevivir en AresLands?. Ya lo veremos... ya lo veremos...</p>
								<p>Hay muchas clases de personas y criaturas que pasan por aquí... diferentes razas... diferentes idiomas... pero dime ¿cómo eres tu?</p>
								<div class="alert alert-info">
									<strong>Nota</strong>
									<p>Dependiendo de tus siguientes elecciones, se te desbloquearán algunas opciones y otras se te bloquearán. Lee atentamente para saber qué toca a cada característica.</p>
								</div>
							</div>
							
							<div class="paginator">
								<div class="text-center">
									<a href="#0" data-toggle="tab">Cuéntame</a>
								</div>
							</div>
						</div>
						
						<?php $allCharacteristics = Characteristic::get_all(); ?>
						@foreach ( $allCharacteristics as $i => $characteristics )
						<div class="tab-pane" id="{{ $i }}">
							<h3 class="text-center">¿Cómo es tu personaje? ({{ $i+1 }}/{{ count($allCharacteristics) }})</h3>
							<ul class="thumbnails">
								@foreach ( $characteristics as $characteristic )
								<li>
									<div class="thumbnail span6">
										<div class="caption">
											<div class="pull-left" style="margin-top: 45px; margin-left: 15px;">
												{{ Form::radio($i, $characteristic->get_name()) }}
											</div>
											<div style="margin-left: 50px;">
												<h3>{{ $characteristic->get_name() }}</h3>
												<p>{{ $characteristic->get_description() }}</p>
												<ul class="unstyled">
													@foreach ( $characteristic->get_bonusses() as $bonus )
													<li>{{ $bonus }}</li>
													@endforeach
												</ul>
											</div>
										</div>
									</div>
								</li>
								@endforeach
							</ul>
							
							<div class="paginator">
								<div class="text-center">
									@if ( isset($allCharacteristics[$i-1]) )
									<a href="#{{ $i-1 }}" data-toggle="tab">Atrás</a>
									@endif
									
									@if ( isset($allCharacteristics[$i+1]) )
									<a href="#{{ $i+1 }}" data-toggle="tab">Siguiente</a>
									@else
									{{ Form::submit('Guardar', array('onclick' => 'return confirm("¿Seguro que quieres guardar estas caracteristicas para tu personaje?");')) }}
									@endif
								</div>
							</div>
						</div>
						@endforeach
					</div>
					
					{{ Form::close() }}
					
					<script>
						$('#characteristicsTabs').tab('show');
					</script>
				</div>
				<!--
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Guardar!</a>
				</div>
				-->
			</div>
		
			<script>
				$('#characteristicsModal').modal({
					keyboard: false,
					backdrop: 'static'
				});
			</script>
		@endif

		<div id="wrap">
			<div class="container">
				<!-- Torneo -->
				@if ( Tournament::is_active() )
					<div class="pull-left alert alert-info" style="width: 200px; font-size: 12px;">
						<?php $tournament = Tournament::get_active()->first(); ?>
						<span>Torneo "{{ $tournament->name }}" finaliza en </span>
						<span class='timer' data-layout="{hnn}:{mnn}:{snn} y {dn} día(s)" data-endtime='{{ $tournament->ends_at - time() }}'></span>
					</div>
				@else
					@if ( Tournament::is_upcoming() )
						<?php $tournament = Tournament::get_upcoming()->first(); ?>
						<div class="pull-left alert alert-info" style="width: 200px; font-size: 12px;">
							<span>El torneo "{{ $tournament->name }}" comienza en </span>
							<span class='timer' data-layout="{hnn}:{mnn}:{snn} y {dn} día(s)" data-endtime='{{ $tournament->starts_at - time() }}'></span>
						</div>
					@endif
				@endif
				<!-- End Torneo -->
				
				<div style="position: absolute; right: 10px; top: -70px; padding: 10px; font-size: 12px; color: white; text-transform: uppercase;">
					Hora del servidor: <span id='serverTime'>00:00:00</span>
				</div>
				
				@if ( isset($character) && isset($npcs) && count($npcs) > 0 )
					@if ( ! $character->is_traveling )
					<div class="pull-right">
						<div id="npcs" class="carousel slide" style="margin: 0; padding: 0;">
							<ul class="inline" style="margin: 0; padding: 0;">
								<li style="width: 64px;"><a class="carousel-control left" href="#npcs" data-slide="prev">&lsaquo;</a></li>
								<li style="width: 440px">
									<div class="carousel-inner">
										<ul class="active item inline">
										<?php $i = 0; $itemCount = 1; ?>
										@foreach ( $npcs as $npc )
											<?php $i++; ?>

											@if ( $i > 5 )
												<?php $i = 0; $itemCount++; ?>
												</ul>
												<ul class="item inline">
											@endif

											<li data-toggle="tooltip" data-placement="bottom" data-original-title="<div style='color: #FFC200;'>Mercader {{ $npc->name }}</div>{{ $npc->tooltip_dialog }}">
												<a href="{{ URL::to('authenticated/npc/' . $npc->id . '/' . Str::slug($npc->name)) }}">
													<img src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" alt="" width="72px" height="82px">
												</a>
											</li>
										@endforeach
										</ul>
									</div>
								</li>
								<li style="width: 64px;"><a class="carousel-control right" href="#npcs" data-slide="next">&rsaquo;</a></li>
							</ul>
						</div>
					</div>
					@endif
				@endif

				<div class="pull-left">
					<a href="{{ URL::base() }}"><div class="logo"></div></a>
				</div>
				
				<div class="row-fluid col-wrap">
					<div class="span2 menu col" style="width: 176px; ">
						@if ( Request::route()->controller == 'authenticated' )
							<div class="mini-player-display">
								<div class="icon-race-30 icon-race-30-{{ $character->race }}_{{ $character->gender }} pull-left"></div>
								<div class="pull-left" style="margin-left: 5px;">
									<a href="{{ URL::to('authenticated/character/' . $character->name) }}" style="color: rgb(231, 180, 47); font-size: 12px;">
										<b>{{ $character->name }}</b>
									</a>
									
									<div class="clear-fix"></div>
									
									<span>
									Nivel: {{ $character->level }}
									</span>
								</div>
								<ul class="inline pull-right">
									<li style="padding: 0; vertical-align: top;">
										<div id="quests_popover" style="display: none;">
											<div style="width: 250px; margin-top: -15px;">
											@if ( count($startedQuests) > 0 )
												@foreach ( $startedQuests as $startedQuest )
													<?php $quest = $startedQuest->quest()->select(array('id', 'name'))->first(); ?>
													@if ( $startedQuest->progress == 'reward' )
													<div class="positive">
													@endif
													<span style="line-height: 60px; color: orange;">{{ $quest->name }}</span>

													@if ( $progress = $character->get_progress_for_view($quest) )
														{{ $progress }}
													@endif
													@if ( $startedQuest->progress == 'reward' )
													</div>
													@endif
												@endforeach
											@else
												Sin misiones
											@endif
											</div>
										</div>

										<img id="quests" style="cursor: pointer;" src="{{ URL::base() }}/img/quest-icon.png" width="16px" height="19px" data-toggle="tooltip" data-original-title="<center>Misiones</center>" data-placement="top" data-container="body">
										
										<script>
											$('#quests').popover({
												html: true,
												content: function() {
													return $('#quests_popover').html();
												},
												placement: 'bottom'
											});
										</script>
									</li>

									<li style="padding: 0; vertical-align: top;" data-toggle="tooltip" data-placement="top" data-original-title="
										<b>Monedas</b>
										<ul class='inline' style='margin: 0;'>
											<li><i class='coin coin-gold pull-left'></i> {{ $coins['gold'] }}</li>
											<li><i class='coin coin-silver pull-left'></i> {{ $coins['silver'] }}</li>
											<li><i class='coin coin-copper pull-left'></i> {{ $coins['copper'] }}</li>
										</ul>">
										<img src="{{ URL::base() }}/img/coin-icon.png" width="16px" height="19px">
									</li>

									@if ( $character->clan_id != 0 )
										<li style="padding: 0; vertical-align: top;" data-toggle="tooltip" data-placement="top" data-original-title="Accede a la página de tu grupo">
											<a href="{{ URL::to('authenticated/clan/' . $character->clan_id) }}"><img src="{{ URL::base() }}/img/shield-icon.png" alt="Grupo" width="16px" height="19px"></a>
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
								
								@if ( $character->characteristics )
								<li><a href="{{ URL::to('authenticated/talents') }}" class="menu menu-talents"></a></li>
								@endif
								
								<li><a href="{{ URL::to('authenticated/clan') }}" class="menu menu-group"></a></li>
								<li><a href="{{ URL::to('authenticated/trade') }}" class="menu menu-trade"></a></li>
								<li><a href="{{ URL::to('authenticated/characters') }}" class="menu menu-characters"></a></li>
								<li><a href="{{ URL::to('authenticated/ranking') }}" class="menu menu-ranking"></a></li>
								<li><a href="{{ URL::to('authenticated/orbs') }}" class="menu menu-orbs"></a></li>
								<li><a href="{{ URL::to('authenticated/tournaments') }}" class="menu menu-tournaments"></a></li>
								
								<li><a href="http://ironfist.com.ar/forums" class="menu menu-forum" target="_blank"></a></li>
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
				
				<div id="footer" style="margin-top: 50px; padding-left: 50px;">
					<div class="pull-left">
						<a href="//ironfist.com.ar" target="_blank">
							<img src="{{ URL::base() }}/img/ironfist-logo.png" width="106px" height="129.5px">
						</a>
					</div>
					<div style="padding-top: 50px; margin-left: 130px;">
						<p>
							Todas las marcas aquí mencionadas son propiedad de sus respectivos dueños. 
							<br>
							©2014 IronFist. Todos los derechos reservados.
							<a href="//ironfist.com.ar/privacy" target="_blank">Política de privacidad</a>
							-
							<a href="//ironfist.com.ar/terms" target="_blank">Condiciones generales de uso</a>
							<br>
							AresLands es una realidad gracias a <b>Vicente Buendia</b> y a su proyecto open <a href="http://sourceforge.net/projects/tierras/" target="_blank" rel="nofollow">Tierras de Leyenda</a> y a <a href="{{ URL::to('home/thanks') }}">todos los que contribuyeron con nosotros</a>.
						</p>
					</div>
				</div>
			</div>
			</div> <!-- /container -->
		</div> <!-- /wrap -->

		@if ( Auth::check() && isset($character) )
			<div style="position: fixed; border: 2px solid #E99337; border-bottom: 0; border-top-left-radius: 5px; border-top-right-radius: 5px; z-index: 999; bottom: 0; right: 10px; padding: 5px; width: auto; height: auto; background-color: #181818;" ng-controller="Chat">
				<ul class="inline" style="margin-bottom: 5px;">
					<li ng-click="chat.show = ! chat.show;" style="width: 190px; cursor: pointer; color: white;">
						Chat <span class="label label-info" data-toggle="tooltip" data-original-title="<div class='text-left'><b>Comandos disponibles:</b><ul class='unstyled'><li><span class='positive'>/online</span> : Muestra los personajes conectados</li><li><span class='positive'>/clear</span> : Vacia el historial de mensajes</li></ul></div>">?</span>
					</li>
					
					@if ( $character->clan_id > 0 )
					<li ng-show="chat.show" style="margin-left: 45px;">
						<button class="btn btn-small btn-inverse" ng-click="switchChannel(0)">Cambiar a general</button>
					</li>
					<li ng-show="chat.show">
						<button class="btn btn-small btn-inverse" ng-click="switchChannel({{ $character->clan_id }})">Cambiar a clan</button>
					</li>
					@endif
				</ul>
				
				<div ng-show="chat.show" style="height: 300px; width: 500px;">
					<div style="width: 50; height: 270px; background-color: #0F0F0F; overflow: auto; border: 1px solid #4F4843;">
						<ul class="unstyled">
							<li ng-repeat="message in chat.messages[chat.channel]" style="padding: 5px; border-bottom: 1px dashed #292929; font-size: 13px;">
								<div class="pull-right" style="color: rgb(77, 77, 77);">[[ formatMessageTime(message.time) ]]</div>
								<strong style="color: orange;">[[ message.name ]]:</strong> [[ message.message ]]
							</li>
						</ul>
					</div>

					<form ng-submit="sendMessage()">
						<input type="text" class="input-block-level" style="border-radius: 0;" ng-model="chat.input" />
					</form>
				</div>
			</div>
		@endif

		<script type="text/javascript" src="{{ Minifier::make(array('//js/app.js', '//js/configuration.js', '//js/services.js', '//js/controllers.js', '//js/filters.js', '//js/directives.js')) }}"></script>
		<script src="{{ URL::base() }}/js/libs/jquery.countdown.min.js"></script>

		<script>
			/*
			 *	Iniciamos los tooltips
			 */
			$('[data-toggle="tooltip"]').tooltip({ html: true, container: '#wrap' });
			$('[data-toggle="popover"]').popover({ html: true });

			/*
			 *	Iniciamos los timers
			 */
			$('.timer').each(function() {
				var $this = $(this);
				var time = $this.data('endtime');
				var date = new Date();
				var layout = '{hnn}:{mnn}:{snn}';
				
				date.setSeconds(date.getSeconds() + time);
				
				if ( $this.data('layout') ) {
					layout = $this.data('layout');
				}

				$this.countdown({
					until: date,
					layout: layout,
					expiryText: '<a href="" onclick="location.reload();">Actualizar</a>'
				});
			});
            
            $('#serverTime').countdown({
                since: (new Date({{ time() }})),
                layout: '{hnn}:{mnn}:{snn}'
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
