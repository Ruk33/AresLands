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

	<?php flush(); ?>

	<body ng-init="basePath='{{ URL::base() }}/'">
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->
		
		@if ( isset($character) && ! $character->characteristics )
			<div id="characteristicsModal" class="modal hide fade">
				<div class="modal-body">
					<ul id="characteristicsTabs">
					</ul>
					
					{{ Form::open(URL::to_route("post_authenticated_character_characteristics")) }}
					
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
												{{ Form::radio("characteristics[{$characteristic->get_name()}]", $characteristic->get_name()) }}
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
        @else
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
                                    
                                    case 'levelUp':
                                        echo '¡Haz subido de nivel!';
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
                                            <img src="' . Item::find($itemId)->get_image_path() . '" width="80px" height="80px">
                                        </div>
                                        <div style="margin-left: 125px;">Has abierto el cofre y dentro del mismo, se encontraba esto. ¡Felicitaciones!</div>';
                                    break;
                                
                                case 'levelUp':
                                    echo 'Haz alcanzado un nuevo nivel. Tu vida se ha incrementado y se te ha dado una bonificacion de puntos para atributos.';
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
		@endif

		<div id="wrap">
			<div class="container">
                <div class="race-corner {{ $character->race }}-{{ $character->gender }}"></div>
                
				<div style="position: absolute; text-shadow: black 0 0 5px; right: 10px; top: -50px; padding: 10px; font-size: 12px; color: white; text-transform: uppercase;">
					Hora del servidor: <span id='serverTime'>00:00:00</span>
				</div>
				
				@if ( isset($character) && isset($npcs) && count($npcs) > 0 )
					@if ( ! $character->is_traveling )
					<div class="pull-right">
						<div id="npcs" class="carousel slide" style="margin: 0; margin-top: 20px; padding: 0;">
							<ul class="inline" style="margin: 0; padding: 0;">
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

											<li>
												@if ( $npc->is_blocked_to($character) )
                                                    <img class="grayEffect" data-toggle="tooltip" data-placement="bottom" data-original-title="Mercader bloqueado, necesitas mas nivel" src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" alt="" width="72px" height="82px">
                                                @else
                                                    <a href="{{ $npc->get_link() }}">
                                                        <img src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" data-toggle="tooltip" data-placement="bottom" data-original-title="<div style='color: #FFC200;'>Mercader {{ $npc->name }}</div>{{ $npc->tooltip_dialog }}" alt="" width="72px" height="82px">
                                                    </a>
                                                @endif
											</li>
										@endforeach
										</ul>
									</div>
								</li>
								@if ( $itemCount > 1 )
								<li style="width: 40px;">
									<a class="carousel-control right" href="#npcs" data-slide="next">&rsaquo;</a>
								</li>
								@endif
							</ul>
						</div>
					</div>
					@endif
				@endif

				<div class="pull-left">
					<a href="{{ URL::base() }}"><div class="logo"></div></a>
				</div>
				
				<div class="row-fluid col-wrap">
					<div class="pull-left col menu-column" style="width: 176px;">
						@if ( isset($character) )
							<div class="mini-player-display">
								<div class="pull-left" style="margin-left: 32px;">
                                    <div style="width: 45px; overflow: hidden;">
                                        <a href="{{ URL::to_route("get_authenticated_character_show", array($character->server_id, $character->name)) }}" style="font-size: 12px;">
                                            <b>{{ $character->name }}</b>
                                        </a>                                        
                                    </div>
                                    Nivel: {{ $character->level }}
								</div>
                                
                                <div>
                                    <div class="coins">
                                        <ul class="inline coin-list">
                                            <li>
                                                <i class='coin coin-gold pull-left'></i>
                                                @if ( $coins['gold'] > 100 )
                                                    <span data-toggle="tooltip" data-original-title="{{ $coins['gold'] }}">99+</span>
                                                @else
                                                    {{ $coins['gold'] }}
                                                @endif
                                            </li>
                                            <li><i class='coin coin-silver pull-left'></i> {{ $coins['silver'] }}</li>
                                            <li><i class='coin coin-copper pull-left'></i> {{ $coins['copper'] }}</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="quests">
                                        <div id="quests_popover" style="display: none;">
											<div style="width: 300px; margin-top: -15px;">
											@if ( count($startedQuests) > 0 )
												@foreach ( $startedQuests as $startedQuest )
													<?php $quest = $startedQuest->quest()->select(array('id', 'name'))->first(); ?>
													@if ( $startedQuest->progress == 'reward' )
													<div class="positive">
													@endif
													<span style="line-height: 25px; color: orange;">{{ $quest->name }}</span>

													@if ( $progress = $character->get_progress_for_view($quest) )
                                                        <small>{{ $progress }}</small>
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

										<img id="quests" src="{{ URL::base() }}/img/quest-icon.png" width="20px" height="21px" data-toggle="tooltip" data-original-title="<center><b>Misiones aceptadas</b></center>" data-placement="top" data-container="body">
										
										<script>
											$('#quests').popover({
												html: true,
												content: function() {
													return $('#quests_popover').html();
												},
												placement: 'bottom'
											});
										</script>
                                    </div>
                                    
                                    @if ( $character->clan_id != 0 )
                                    <div class="clan" data-toggle="tooltip" data-original-title="Ir a la pagina del grupo">
                                        <a href="{{ URL::to_route("get_authenticated_clan_show", array($character->clan_id)) }}">
                                            <img src="{{ URL::base() }}/img/shield-icon.png" alt="Grupo" width="20px" height="21px">
                                        </a>
                                    </div>
                                    @endif
                                </div>
							</div>
						@endif
						<ul class="unstyled menu" width="150px;">
							@if ( Auth::check() && isset($character) )
                                <li class="nav-header">Inicio</li>
								<li>
									<i class="img-circle menu-icon menu-index"></i>
									<a href="{{ URL::to_route("get_authenticated_index") }}">Tu personaje</a>
								</li>
								<li>
									<i class="img-circle menu-icon menu-messages"></i>
									<a href="{{ URL::to_route("get_authenticated_message_index") }}">
										Mensajes
										@if ( $character->get_unread_messages_count() > 0 )
										<div class="pull-right" data-toggle="tooltip" data-placement="top" data-original-title="Mensaje(s) sin leer">
											<span class="badge badge-important" style="font-family: arial;">
												{{ $character->get_unread_messages_count() }}
											</span>
										</div>
										@endif
									</a>
								</li>
                                @if ( $character->characteristics )
								<li>
									<i class="img-circle menu-icon menu-talents"></i>
									<a href="{{ URL::to_route("get_authenticated_talent_index") }}">Talentos</a>

									@if ( $character->talent_points > 0 )
										<div class="pull-right" data-toggle="tooltip" data-placement="top" data-original-title="Puntos de talentos disponibles">
												<span class="badge badge-important" style="font-family: arial;">
													{{ $character->talent_points }}
												</span>
										</div>
									@endif
								</li>
								@endif

								@if ( $character->clan_id == 0 )
								<li>
									<i class="img-circle menu-icon menu-ranking"></i>
									<a href="{{ URL::to_route("get_authenticated_clan_create") }}">Crear grupo</a>
								</li>
								@endif
                                
                                @if ( $character->can_travel() === true || $character->can_fight() || $character->can_explore() )
                                <li class="nav-header">Acciones</li>
                                @endif
                                
								@if ( $character->can_travel() === true )
								<li>
									<i class="img-circle menu-icon menu-travel"></i>
									<a href="{{ URL::to_route("get_authenticated_action_travel") }}">Viajar</a>
								</li>
								@endif
								
								@if ( $character->can_fight() === true )
								<li>
									<i class="img-circle menu-icon menu-battle"></i>
									<a href="{{ URL::to_route("get_authenticated_battle_index") }}">Batallar</a>
								</li>
                                @endif
                                
                                @if ( $character->can_explore() )
								<li>
									<i class="img-circle menu-icon menu-explore"></i>
									<a href="{{ URL::to_route("get_authenticated_action_explore") }}">Explorar</a>
								</li>
								@endif
								<li>
									<i class="img-circle menu-icon menu-trade"></i>
									<a href="{{ URL::to_route("get_authenticated_trade_index") }}">Comercios</a>
								</li>
                                <li class="nav-header">Eventos</li>
                                @if ( $character->can_fight() === true && ! IoC::resolve("Dungeon")->has_finished() )
								<li>
									<i class="img-circle menu-icon menu-dungeons"></i>
									<a href="{{ URL::to_route("get_authenticated_dungeon_index") }}">Portal Oscuro</a>
								</li>
								@endif
                                <li>
									<i class="img-circle menu-icon menu-ranking"></i>
									<a href="{{ URL::to_route("get_authenticated_ranking_index", array("pvp")) }}">Ranking</a>
								</li>
								<li>
									<i class="img-circle menu-icon menu-orbs"></i>
									<a href="{{ URL::to_route("get_authenticated_orb_index") }}">Orbes</a>
								</li>
								<li>
									<i class="img-circle menu-icon menu-tournaments"></i>
									<a href="{{ URL::to_route("get_authenticated_tournament_index") }}">Torneos</a>

									@if ( $tournament )
										@if ( Tournament::is_active() )
											<div class="pull-right" data-toggle="tooltip" data-placement="top" data-original-title="Torneo {{ $tournament->name }} finaliza en {{ date('z \d\i\a\(\s\) H:i:s', $tournament->ends_at - time()) }}">
										@else
											<div class="pull-right" data-toggle="tooltip" data-placement="top" data-original-title="El torneo {{ $tournament->name }} comienza en {{ date('z \d\i\a\(\s\) H:i:s', $tournament->starts_at - time()) }}">
										@endif
												<span class="badge badge-important" style="font-family: arial;">
													!
												</span>
											</div>
									@endif
								</li>
                                <li class="nav-header">VIP</li>
								<li>
									<i class="img-circle menu-icon menu-secret-shop"></i>
									<a href="{{ URL::to_route("get_authenticated_secret_shop_index") }}">Mercado secreto</a>
								</li>
                                <li class="nav-header">Ayuda</li>
								<li>
									<i class="img-circle menu-icon menu-tutorial"></i>
									<a href="//titangames.com.ar/forums/topic/175/guia-inicial" target="_blank">Guia</a>
								</li>
								<li>
									<i class="img-circle menu-icon menu-forum"></i>
									<a href="//titangames.com.ar/forums" target="_blank">Foro</a>
								</li>
                                <li class="nav-header">otro</li>
								<li>
									<i class="img-circle menu-icon menu-logout"></i>
									<a href="{{ URL::to_route("get_authenticated_logout") }}">Desconectarse</a>
								</li>
							@else
								<li><a href="{{ URL::to('/') }}">Inicio</a></li>
								<li><a href="{{ URL::to('game/index') }}" target="_blank">Guia</a></li>
							@endif
						</ul>
					</div>

					<div class="content col rock-background pull-right" style="width: 763px; border-left: 1px solid black;">
						<div id="content">
							{{ $content }}
						</div> <!-- /content -->
					</div>
				</div>
                    
                <div style="background-image: url({{ URL::base() }}/img/footer-content.png); height: 100px; margin-top: -20px; background-position: 1px 0; background-repeat: no-repeat; position: relative; z-index: 2; width: 101%;"></div>
				
				<div id="footer">
					<div class="pull-left" style="padding: 25px;">
						<a href="//titangames.com.ar" target="_blank">
							<img src="{{ URL::base() }}/img/logo-titangames.png">
						</a>
					</div>
					<div style="padding-top: 50px; margin-left: 130px;">
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
			</div> <!-- /container -->
		</div> <!-- /wrap -->

		@if ( Auth::check() && isset($character) )
			<div class="chat" ng-controller="Chat">
				<ul class="inline" style="margin-bottom: 5px;">
					<li ng-click="toggleChat();" style="width: 190px; cursor: pointer; color: white;">
                        <span data-toggle="tooltip" data-original-title="Nuevos mensajes" ng-show="chat.newMessages">*</span>
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
				
				<div class="chat-container" ng-show="chat.show">
					<div class="chat-list-container">
						<ul class="chat-list unstyled" name="messages">
							<li ng-repeat="message in chat.messages[chat.channel]">
								<div class="pull-right" style="color: rgb(77, 77, 77);">[[ formatMessageTime(message.time) ]]</div>
								<a href="{{ URL::base() }}/authenticated/character/show/[[ message.server_id ]]/[[ message.name ]]">[[ message.name ]]</a>: [[ message.message ]]
							</li>
						</ul>
					</div>

					<form ng-submit="sendMessage()">
						<input type="text" class="input-block-level" style="border-radius: 0;" ng-model="chat.input" />
					</form>
				</div>
			</div>        
		@endif

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
		<script src="{{ URL::base() }}/js/libs/jquery.countdown.min.js"></script>

		<script>
			$(document).ready(function() {
				/*
				 *	Iniciamos los tooltips
				 */
				$('[data-toggle="tooltip"]').tooltip({ html: true, container: '#wrap' });
				$('[data-toggle="popover"]').popover({ html: true });

				var lowerCountdown;
				var originalTitle = document.title;

				function onTick(countdown)
				{
					if ( ! lowerCountdown )
					{
						lowerCountdown = countdown;
					}
					else
					{
						// horas
						if ( countdown[4] < lowerCountdown[4] )
						{
							lowerCountdown = countdown;
						}
						else if ( countdown[4] == lowerCountdown[4] )
						{
							// minutos
							if ( countdown[5] < lowerCountdown[5] )
							{
								lowerCountdown = countdown;
							}
							else if ( countdown[5] == lowerCountdown[5] )
							{
								// segundos
								if ( countdown[6] < lowerCountdown[6] )
								{
									lowerCountdown = countdown;
								}
							}
						}
					}

					var time = [lowerCountdown[4], lowerCountdown[5], lowerCountdown[6]];

					for ( var i in time )
					{
						if ( String(time[i]).length == 1 )
						{
							time[i] = '0' + time[i];
						}
					}

					document.title = time[0] + ':' + time[1] + ':' + time[2] + ' ' + originalTitle;
				}

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
						expiryText: '<a href="" onclick="location.reload();">Actualizar</a>',
						onTick: onTick
					});
				});

	            $('#serverTime').countdown({
	                since: (new Date({{ time() }})),
	                layout: '{hnn}:{mnn}:{snn}'
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
