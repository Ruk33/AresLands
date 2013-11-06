
<div style="margin-left: -15px;">
@if ( count($npcs) > 0 )
	@if ( ! $character->is_traveling )
	<div class="bar">
		<ul class="inline">
		@foreach ( $npcs as $npc )
			<li data-toggle="tooltip" data-placement="bottom" data-original-title="<div style='color: #FFC200;'>Mercader {{ $npc->name }}</div>{{ $npc->tooltip_dialog }}">
				<a href="{{ URL::to('authenticated/npc/' . $npc->id . '/' . $npc->name) }}">
					<img src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" alt="" width="72px" height="82px">
				</a>
			</li>
		@endforeach

		@foreach ( $blockedNpcs as $blockedNpc )
			<li data-toggle="tooltip" data-placement="bottom" data-original-title="<strong>Bloqueado</strong><br>Debes explorar un poco mas para que este mercader se habilite.">
				<img class="grayEffect" src="{{ URL::base() }}/img/icons/npcs/{{ $blockedNpc->id }}.png" alt="" width="72px" height="82px">
			</li>
		@endforeach
		</ul>
	</div>
	@endif
@endif

<div ng-controller="Skill">
	@if ( Session::has('error') )
		<div class="alert alert-error">
			{{ var_dump(Session::get('error')) }}
			{{ Session::get('error') }}
		</div>
	@endif
	
	<!-- BUFFS -->
	@if ( count($skills) > 0 )
		<h2>Magias activas</h2>
		<ul class="unstyled inline">
			@foreach ( $skills as $skill )
				<li class="text-center clan-member-link" style="vertical-align: top;">
					<img src="{{ URL::base() }}/img/icons/skills/{{ $skill->skill_id }}.png" alt="" width="32px" height="32px" skill-tooltip skill-id="{{ $skill->skill_id }}" skill-level="{{ $skill->level }}">

					<div>
					@if ( $skill->end_time != 0 )
					<small><span class='timer' data-endtime='{{ $skill->end_time - time() }}'></span></small>
					@else
						∞
					@endif
					</div>

					<div><small>Cantidad: {{ $skill->amount }}</small></div>
				</li>
			@endforeach
		</ul>
	@endif
	<!-- END BUFFS -->
	
	<?php
	
		switch ( $zone->id )
		{
			case 1:
				echo '<div class="span6 montes-background" style="margin-left: 20px; margin-right: -20px;">';
				break;
			
			case 2:
				echo '<div class="span6 valle-background" style="margin-left: 20px; margin-right: -20px;">';
				break;
			
			case 3:
				echo '<div class="span6 lago-background" style="margin-left: 20px; margin-right: -20px;">';
				break;
			
			case 4:
				echo '<div class="span6 piramides-background" style="margin-left: 20px; margin-right: -20px;">';
				break;
			
			default:
				echo '<div class="span6" style="margin-left: 20px; margin-right: -20px;">';
				break;
		}
	
	?>
	
		<h2>Personaje</h2>

		<div style="min-height: 405px;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) && $lrhand = $items['lrhand'][0]->item )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="" width="80px" height="80px" data-toggle="popover" data-placement="top" data-original-title="
						{{ $lrhand->get_text_for_tooltip() }}
						
						<div class='text-center'>
							<a href='{{ URL::to('authenticated/manipulateItem/' . $items['lrhand'][0]->id) }}'>
								Desequipar
							</a>
						</div>">
					</div>
				</div>
			<!-- END DOS MANOS -->
			@else
				<!-- MANO DERECHA -->
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['rhand']) && $rhand = $items['rhand'][0]->item )
						<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $rhand->id }}.png" alt="" width="80px" height="80px" data-toggle="popover" data-placement="top" data-original-title="
						{{ $rhand->get_text_for_tooltip() }}
						
						<div class='text-center'>
							<a href='{{ URL::to('authenticated/manipulateItem/' . $items['rhand'][0]->id) }}'>
								Desequipar
							</a>
						</div>">
					@endif
					</div>
				</div>
				<!-- END MANO DERECHA -->

				<!-- MANO IZQUIERDA -->
				<div style="position: absolute; margin-left: 250px; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['lhand']) && $lhand = $items['lhand'][0]->item )
						<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $lhand->id }}.png" alt="" width="80px" height="80px" data-toggle="popover" data-placement="top" data-original-title="
						{{ $lhand->get_text_for_tooltip() }}
						
						<div class='text-center'>
							<a href='{{ URL::to('authenticated/manipulateItem/' . $items['lhand'][0]->id) }}'>
								Desequipar
							</a>
						</div>">
					@endif
					</div>
				</div>
				<!-- END MANO IZQUIERDA -->
			@endif

			<!-- ORBES -->
			<div class="quest-reward-item" style="position: absolute; margin-left: 250px; margin-top: 250px;">
				@if ( isset($orbs[0]) )
					<img src="{{ URL::base() }}/img/icons/orbs/{{ $orbs[0]->id }}.png" data-toggle="tooltip" data-title="<div style='width: 200px;'><strong>{{ $orbs[0]->name }}</strong><p>{{ $orbs[0]->description }}</p></div>">
				@endif
			</div>

			<div class="quest-reward-item" style="position: absolute; margin-left: 298px; margin-top: 250px;">
				@if ( isset($orbs[1]) )
					<img src="{{ URL::base() }}/img/icons/orbs/{{ $orbs[1]->id }}.png" data-toggle="tooltip" data-title="<div style='width: 200px;'><strong>{{ $orbs[1]->name }}</strong><p>{{ $orbs[1]->description }}</p></div>">
				@endif
			</div>
			<!-- END ORBES -->

			<!-- AYUDANTE -->
			<div style="position: absolute; margin-left: 255px; margin-top: 65px;">
				@if ( isset($items['mercenary']) )
					<?php $mercenary = $items['mercenary'][0]->item; ?>
					<img src="{{ URL::base() }}/img/icons/items/{{ $mercenary->id }}.png" alt="" width="64px" height="64px" data-toggle="tooltip" data-placement="top" data-original-title="
					{{ $mercenary->get_text_for_tooltip() }}">
				@endif
			</div>
			<!-- END AYUDANTE -->
			
			<!-- PERSONAJE -->
			<img src="{{ URL::base() }}/img/characters/{{ $character->race }}_{{ $character->gender }}_999.png" alt="">
			<!-- END PERSONAJE -->
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="span6" ng-controller="CharacterStatsController" ng-init="remainingPoints='{{ $character->points_to_change }}'">
		<h2>Estadísticas</h2>
		<ul class="unstyled text-center" style="width: 340px;">
			<li data-toggle="tooltip" data-placement="top" data-original-title="<b>Barra de actividad:</b> Completa la barra de actividad realizando acciones (explorar, batallar, viajar, etc.) para obtener las <b>recompensas</b>.">
				<span style="font-size: 11px;">BARRA DE ACTIVIDAD</span>
				<div class="progress" style="height: 5px;">
					@if ( $character->activity_bar )
					<div id="activityBar" class="bar bar-success" style="width: {{ 100 * $character->activity_bar->filled_amount / Config::get('game.activity_bar_max') }}%"></div>
					@endif
				</div>
			</li>

			<li>
				<span style="font-size: 11px;" ng-init="currentLife='{{ $character->current_life }}'; maxLife='{{ $character->max_life }}'">
					<b>SALUD:</b> 
					<span data-toggle="tooltip" data-placement="top" data-original-title="Salud actual / Salud máxima">
						<span ng-bind="currentLife || '?'">?</span>/<span ng-bind="maxLife || '?'">?</span>
					</span>
				</span>
				<div class="progress" style="height: 5px;">
					<div class="bar bar-success" id="lifeBar"></div>
				</div>
			</li>
			
			<li style="margin-bottom: 30px;">
				<span style="font-size: 11px;">
					<b>EXPERIENCIA:</b> 
					<span data-toggle="tooltip" data-placement="top" data-original-title="Experiencia actual / Experiencia para subir de nivel">
						{{ $character->xp }}/{{ $character->xp_next_level }}
					</span>
				</span>
				<div class="progress" style="height: 5px;">
					<div class="bar bar-success" id="experienceBar" style="width: {{ 100 * $character->xp / $character->xp_next_level }}%"></div>
				</div>
			</li>
			
			<li style="margin-bottom: 10px;" ng-show="remainingPoints>0">
				<div class="clan-member-link text-center" style="width: 300px; border: 1px solid #2E2E2E;">
					<p><b>Puntos restantes para cambiar:</b> <span ng-bind="remainingPoints || '?'">?</span></p>
					<p style="margin: 0;">Puntos a cambiar: <select class="input select" ng-model="pointsToChange" ng-init="pointsToChange=1;" ng-options="n for n in [] | range:1:remainingPoints"></select></p>
				</div>
			</li>
			
			<?php $physicalDamage = $character->stat_strength + $character->stat_strength_extra; ?>
			<li style="margin-bottom: 10px;" ng-init="stats['stat_strength']='{{ $character->stat_strength }}'" data-toggle="tooltip" data-placement="top" data-original-title="<p><b>Fuerza:</b> Aumenta el poder de los ataques físicos.</p><p>Si posees mas Fuerza que Magia, tu personaje golpeará únicamente con ataques físicos.</p><p class='positive'>Poder de ataque físico: {{ $physicalDamage * 0.25 }}-{{ $physicalDamage * 0.75 }}</p>">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_strength')" class="button-icon" ng-show="remainingPoints>0" style="cursor: pointer;">+</a>
					<i class="button-icon hearth" ng-show="remainingPoints<=0"></i>
					<span class="button-content" style="width: 200px;">
						<b class="pull-left">Fuerza física:</b>

						<div class="pull-right">
							<span ng-bind="stats['stat_strength'] || '?'">?</span>
							
							@if ( $character->stat_strength_extra != 0 )
								@if ( $character->stat_strength_extra > 0 )
									<span class="positive">+{{ $character->stat_strength_extra }}</span>
								@else
									<span class="negative">{{ $character->stat_strength_extra }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;" ng-init="stats['stat_dexterity']='{{ $character->stat_dexterity }}'" data-toggle="tooltip" data-placement="top" data-original-title="<p><b>Destreza:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos si tienes mucha mas velocidad que tu adversario.</p><p>Tu tiempo de golpeo se reduce por cada punto de destreza (cuanto menos tiempo de golpeo mejor).</p><p class='positive'>Tiempo de golpeo (menor es mejor): {{ number_format(1000 / ($character->stat_dexterity + $character->stat_dexterity_extra + 1), 2) }}</p>">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_dexterity')" class="button-icon" ng-show="remainingPoints>0" style="cursor: pointer;">+</a>
					<i class="button-icon boot" ng-show="remainingPoints<=0"></i>
					<span class="button-content" style="width: 200px;">
						<b class="pull-left">Destreza física:</b>

						<div class="pull-right">
							<span ng-bind="stats['stat_dexterity'] || '?'">?</span>

							@if ( $character->stat_dexterity_extra != 0 )
								@if ( $character->stat_dexterity_extra > 0 )
									<span class="positive">+{{ $character->stat_dexterity_extra }}</span>
								@else
									<span class="negative">{{ $character->stat_dexterity_extra }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;" ng-init="stats['stat_resistance']='{{ $character->stat_resistance }}'" data-toggle="tooltip" data-placement="top" data-original-title="<p><b>Resistencia física:</b> Aumenta tu defensa contra ataques físicos.</p>">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_resistance')" class="button-icon" ng-show="remainingPoints>0" style="cursor: pointer;">+</a>
					<i class="button-icon boot" ng-show="remainingPoints<=0"></i>
					<span class="button-content" style="width: 200px;">
						<b class="pull-left">Resistencia:</b>

						<div class="pull-right">
							<span ng-bind="stats['stat_resistance'] || '?'">?</span>

							@if ( $character->stat_resistance_extra != 0 )
								@if ( $character->stat_resistance_extra > 0 )
									<span class="positive">+{{ $character->stat_resistance_extra }}</span>
								@else
									<span class="negative">{{ $character->stat_resistance_extra }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<?php $magicDamage = $character->stat_magic + $character->stat_magic_extra; ?>
			<li style="margin-bottom: 10px;" ng-init="stats['stat_magic']='{{ $character->stat_magic }}'" data-toggle="tooltip" data-placement="top" data-original-title="<p><b>Magia:</b> Aumenta el poder de los ataques mágicos.</p><p>Si posees mas Magia que Fuerza, tu personaje golpeará únicamente con ataques mágicos.</p><p class='positive'>Poder de ataque mágico: {{ $magicDamage * 0.25 }}-{{ $magicDamage * 0.75 }}</p>">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_magic')" class="button-icon" ng-show="remainingPoints>0" style="cursor: pointer;">+</a>
					<i class="button-icon fire" ng-show="remainingPoints<=0"></i>
					<span class="button-content" style="width: 200px;">
						<b class="pull-left">Poder mágico:</b>

						<div class="pull-right">
							<span ng-bind="stats['stat_magic'] || '?'">?</span>

							@if ( $character->stat_magic_extra != 0 )
								@if ( $character->stat_magic_extra > 0 )
									<span class="positive">+{{ $character->stat_magic_extra }}</span>
								@else
									<span class="negative">{{ $character->stat_magic_extra }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;" ng-init="stats['stat_magic_skill']='{{ $character->stat_magic_skill }}'" data-toggle="tooltip" data-placement="top" data-original-title="<p><b>Habilidad mágica:</b> Aumenta tu velocidad al lanzar magias, pudiendo lograr así, múltiples ataques consecutivos.</p>">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_magic_skill')" class="button-icon" ng-show="remainingPoints>0" style="cursor: pointer;">+</a>
					<i class="button-icon axe" ng-show="remainingPoints<=0"></i>
					<span class="button-content" style="width: 200px;">
						<b class="pull-left">Habilidad mágica:</b>

						<div class="pull-right">
							<span ng-bind="stats['stat_magic_skill'] || '?'">?</span>

							@if ( $character->stat_magic_skill_extra != 0 )
								@if ( $character->stat_magic_skill_extra > 0 )
									<span class="positive">+{{ $character->stat_magic_skill_extra }}</span>
								@else
									<span class="negative">{{ $character->stat_magic_skill_extra }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;" ng-init="stats['stat_magic_resistance']='{{ $character->stat_magic_resistance }}'" data-toggle="tooltip" data-placement="top" data-original-title="<p><b>Contraconjuro:</b> Aumenta la resistencia contra ataques mágicos.</p>">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_magic_resistance')" class="button-icon" ng-show="remainingPoints>0" style="cursor: pointer;">+</a>
					<i class="button-icon thunder" ng-show="remainingPoints<=0"></i>
					<span class="button-content" style="width: 200px;">
						<b class="pull-left">Contraconjuro:</b>

						<div class="pull-right">
							<span ng-bind="stats['stat_magic_resistance'] || '?'">?</span>

							@if ( $character->stat_magic_resistance_extra != 0 )
								@if ( $character->stat_magic_resistance_extra > 0 )
									<span class="positive">+{{ $character->stat_magic_resistance_extra }}</span>
								@else
									<span class="negative">{{ $character->stat_magic_resistance_extra }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
		</ul>
	</div>
	<!-- END ESTADÍSTICAS -->

	<div class="clearfix" style="margin-bottom: 35px;"></div>

	<div class="span6" style="margin-right: -20px;">
		<!-- INVENTARIO -->
		<script>
			function confirmItemDestroy()
			{
				return confirm('¿Realmente deseas destruir el objeto?');
			}
		</script>
		<h2>Inventario</h3>
		<ul class="inline">
			@for ( $i = 1, $max = 6; $i <= $max; $i++ )
				@if ( $i == 5 )
					</ul>
					<ul class="inline">
				@endif
				<li style="vertical-align: top;">
				<div class="inventory-item">
				@if ( isset($items['inventory']) )
					@foreach ( $items['inventory'] as $characterItem )
						@if ( $characterItem->slot == $i && $item = $characterItem->item )
							@if ( $item->type == 'potion' )
							<div id="{{ $characterItem->id }}" class="modal hide fade" style="background-color: #0C0B0B; border: 1px solid #353535; box-shadow: #4282D5 0px 0px 15px; top: 35%;">
								<div class="modal-body">
								{{ Form::open('authenticated/manipulateItem') }}
									{{ Form::token() }}
									{{ Form::hidden('id', $characterItem->id) }}
									
									<h4>¿Qué cantidad de deseas usar?</h4>
									
									<?php

									for ( $n = 1, $amount = array(); $n <= $characterItem->count; $n++ )
									{
										if ( $n > 25 )
										{
											$n += 4;
										}

										if ( $n > 50 )
										{
											break;
										}

										$amount[$n] = $n;
									}

									?>

									<div>{{ Form::select('amount', $amount) }}</div>
									
									{{ Form::submit('Usar', array('class' => 'btn btn-primary')) }}
								{{ Form::close() }}
								</div>
							</div>
							@endif
							<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $characterItem->item_id }}.png" alt="" width="80px" height="80px" data-toggle="popover" data-placement="top" data-original-title="
							{{ $item->get_text_for_tooltip() }}

							<div class='text-center' style='margin-top: 20px;'>
							@if ( $item->type == 'arrow' && isset($items['lrhand']) && $items['lrhand'][0]->item->type != 'bow' )
								<span style='font-size: 11px;'>Debes tener equipado un arco para usar flechas</span>
							@else
								@if ( $item->type == 'potion' )
									<a href='#{{ $characterItem->id }}' data-toggle='modal' class='pull-left'>Usar</a>
								@else
									<a href='{{ URL::to('authenticated/manipulateItem/' . $characterItem->id) }}' class='pull-left'>Equipar</a>
								@endif

								<a href='{{ URL::to('authenticated/destroyItem/' . $characterItem->id) }}' onclick='return confirmItemDestroy();' class='pull-right' color: white;'>Tirar</a>
							@endif
							</div>">
							<div class="inventory-item-amount" data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">{{ $characterItem->count }}</div>
						@endif
					@endforeach
				@endif
				</div>
				</li>
			@endfor

			<li style="vertical-align: top;" data-toggle="tooltip" data-original-title="Casillero bloqueado">
				<div class="inventory-item">
					<i class="icon-lock" style="vertical-align: -25px;"></i>
				</div>
			</li>

			<li style="vertical-align: top;" data-toggle="tooltip" data-original-title="Casillero bloqueado">
				<div class="inventory-item">
					<i class="icon-lock" style="vertical-align: -25px;"></i>
				</div>
			</li>
		</ul>
		<!-- END INVENTARIO -->
	</div>

	<div class="span6">
		<!-- ACTIVIDADES -->
		@if ( count($activities) > 0 )
		<div>
			<h2>Actividad(es)</h2>
			<ul class="unstyled">
				@foreach ( $activities as $activity )
				<li style="padding: 5px;">
					<img src="{{ URL::base() }}/img/icons/actions/{{ $activity->name }}.jpg" alt="{{ $zone->name }}" width="32px" height="32px" style="margin-right: 5px;">
					<b>
					@if ( $activity->name == 'travel' )
						Viajando a {{ $activity->data['zone']->name }}: 
					@elseif ( $activity->name == 'battlerest' )
						Descanso de batalla: 
					@elseif ( $activity->name == 'explore' )
						Explorando: 
					@endif
					</b>
					<span class="timer" data-endtime="{{ $activity->end_time - time() }}"></span>
				</li>
				@endforeach
			</ul>
		</div>
		@endif
		<!-- END ACTIVIDADES -->

		<!-- ZONA -->
		<div>
			<h2>Ubicación</h2>
			@if ( count($activities) > 0 )
				@foreach ( $activities as $activity )
					@if ( $activity->name == 'travel' )
						Saliendo de 
					@endif
				@endforeach
			@endif

			@if ( $exploringTime && $exploringTime->time > 0 )
			<span data-toggle="tooltip" data-original-title="<p>{{ $zone->description }}</p><p><b>Tiempo explorado:</b> {{ date('z \d\í\a\(\s\) H:i:s', $exploringTime->time) }}</p>">
			@else
			<span data-toggle="tooltip" data-original-title="<p>{{ $zone->description }}</p><p><b>Tiempo explorado:</b> 0 días 00:00:00</p>">
			@endif
				<img src="{{ URL::base() }}/img/zones/32/{{ $zone->id }}.png" alt="{{ $zone->name }}" width="32px" height="32px">
				<b>{{ $zone->name }}</b>
			</span>
		</div>
		<!-- END ZONA -->
	</div>
</div>