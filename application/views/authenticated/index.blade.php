@if ( count($npcs) > 0 )
	@if ( ! $character->is_traveling )
	<div class="bar">
		<ul class="inline">
		@foreach ( $npcs as $npc )
			<li data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $npc->tooltip_dialog }}">
				<a href="{{ URL::to('authenticated/npc/' . $npc->name) }}">
					<img src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" alt="" width="72px" height="82px">
				</a>
			</li>
		@endforeach
		</ul>
	</div>
	@endif
@endif
<!--<hr class="line">-->

<div class="row">
	<!-- BUFFS -->
	<ul class="unstyled inline" style="margin-left: 20px;">
		@foreach ( $skills as $skill )
			<li>
				<img src="{{ URL::base() }}/img/icons/skills/{{ $skill->skill_id }}.jpg" alt="" width="32px" height="32px" data-toggle="tooltip" data-placement="right" data-original-title="
				<b>{{ $skill->skill->name }}</b> (Nivel: {{ $skill->level }})
				<p>{{ $skill->skill->description }}</p>">

				@if ( $skill->end_time != 0 )
				<small><div class='timer' data-endtime='{{ $skill->end_time - time() }}'></div></small>
				@endif
			</li>
		@endforeach
	</ul>
	<!-- END BUFFS -->

	<div class="span6" style="margin-left: 20px; margin-right: -20px;">
		<h2>Personaje</h2>
		<div style="min-height: 405px;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) && $lrhand = $items['lrhand'][0]->item )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="" width="80px" height="80px" data-toggle="popover" data-placement="top" data-original-title="
						{{ $lrhand->get_text_for_tooltip() }}
						<a href='{{ URL::to('authenticated/manipulateItem/' . $items['lrhand'][0]->id) }}'>
							Desequipar
						</a>">
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
						<a href='{{ URL::to('authenticated/manipulateItem/' . $items['rhand'][0]->id) }}'>
							Desequipar
						</a>">
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
						<a href='{{ URL::to('authenticated/manipulateItem/' . $items['lhand'][0]->id) }}'>
							Desequipar
						</a>">
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
			<!--
			<div style="position: absolute; margin-left: 260px; margin-top: 50px;">
				<img src="{{ URL::base() }}/img/characters/ayudante.png" alt="">
			</div>
			-->
			<!-- END AYUDANTE -->
			
			<!-- PERSONAJE -->
			<img src="{{ URL::base() }}/img/characters/{{ $character->race }}_{{ $character->gender }}_0.png" alt="">
			<!-- END PERSONAJE -->
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="span6" ng-controller="CharacterStatsController" ng-init="remainingPoints='{{ $character->points_to_change }}'">
		<h2>Estadísticas</h2>
		<ul class="unstyled" style="width: 340px;">
			<li data-toggle="tooltip" data-placement="top" data-original-title="<b>Barra de actividad:</b> Completa la barra de actividad realizando acciones (explorar, batallar, viajar, etc.) para obtener las <b>recompensas</b>.">
				<span style="font-size: 11px;">BARRA DE ACTIVIDAD</span>
				<div class="progress" style="height: 5px;">
					<div id="activityBar" class="bar bar-success" style="width: {{ 100 * $character->activity_bar->filled_amount / Config::get('game.activity_bar_max') }}%"></div>
				</div>
			</li>

			<li style="margin-bottom: 50px;">
				<span style="font-size: 11px;" ng-init="currentLife='{{ $character->current_life }}'; maxLife='{{ $character->max_life }}'">
					<b>SALUD:</b> <span data-toggle="tooltip" data-placement="top" data-original-title="Salud actual / Salud máxima">[[ currentLife ]]/[[ maxLife ]]</span>
				</span>
				<div class="progress" style="height: 5px;">
					<div class="bar bar-success" id="lifeBar"></div>
				</div>
			</li>
			
			<li style="margin-bottom: 10px;" ng-show="remainingPoints>0">
				<div class="dark-box" style="width: 300px;">
					<p><b>Puntos restantes para cambiar:</b> [[ remainingPoints ]]</p>
					<p>Puntos a cambiar: <select class="input select" ng-model="pointsToChange" ng-init="pointsToChange=1;" ng-options="n for n in [] | range:1:remainingPoints"></select></p>
				</div>
			</li>

			<li style="margin-bottom: 5px;" ng-init="stats['stat_life']='{{ $character->stat_life }}'">
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Vitalidad:</b> Aumenta los puntos de vida que posees y la regeneración de los mismos.">
					<span class="ui-button button" style="cursor: default;">
						<a ng-click="addStat('stat_life')" class="button-icon" ng-show="remainingPoints>0">+</a>
						<i class="button-icon hearth" ng-show="remainingPoints<=0"></i>
						<span class="button-content">
							<b>Vitalidad:</b> [[ stats['stat_life'] ]]

							@if ( isset($positiveBonifications['stat_life']) && $positiveBonifications['stat_life'] > 0 )
								<span class="positive">+{{ $positiveBonifications['stat_life'] }}</span>
							@endif

							@if ( isset($negativeBonifications['stat_life']) && $negativeBonifications['stat_life'] > 0 )
								<span class="negative">-{{ $negativeBonifications['stat_life'] }}</span>
							@endif
						</span>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 5px;" ng-init="stats['stat_dexterity']='{{ $character->stat_dexterity }}'">
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Destreza:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos.">
					<span class="ui-button button" style="cursor: default;">
						<a ng-click="addStat('stat_dexterity')" class="button-icon" ng-show="remainingPoints>0">+</a>
						<i class="button-icon boot" ng-show="remainingPoints<=0"></i>
						<span class="button-content">
							<b>Destreza:</b> [[ stats['stat_dexterity'] ]]

							@if ( isset($positiveBonifications['stat_dexterity']) && $positiveBonifications['stat_dexterity'] > 0 )
								<span class="positive">+{{ $positiveBonifications['stat_dexterity'] }}</span>
							@endif

							@if ( isset($negativeBonifications['stat_dexterity']) && $negativeBonifications['stat_dexterity'] > 0 )
								<span class="negative">-{{ $negativeBonifications['stat_dexterity'] }}</span>
							@endif
						</span>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 5px;" ng-init="stats['stat_magic']='{{ $character->stat_magic }}'">
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Magia:</b> Aumenta el poder de los ataques mágicos.">
					<span class="ui-button button" style="cursor: default;">
						<a ng-click="addStat('stat_magic')" class="button-icon" ng-show="remainingPoints>0">+</a>
						<i class="button-icon fire" ng-show="remainingPoints<=0"></i>
						<span class="button-content">
							<b>Magia:</b> [[ stats['stat_magic'] ]]

							@if ( isset($positiveBonifications['stat_magic']) && $positiveBonifications['stat_magic'] > 0 )
								<span class="positive">+{{ $positiveBonifications['stat_magic'] }}</span>
							@endif

							@if ( isset($negativeBonifications['stat_magic']) && $negativeBonifications['stat_magic'] > 0 )
								<span class="negative">-{{ $negativeBonifications['stat_magic'] }}</span>
							@endif
						</span>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 5px;" ng-init="stats['stat_strength']='{{ $character->stat_strength }}'">
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Fuerza:</b> Aumenta el poder de los ataques físicos.">
					<span class="ui-button button" style="cursor: default;">
						<a ng-click="addStat('stat_strength')" class="button-icon" ng-show="remainingPoints>0">+</a>
						<i class="button-icon axe" ng-show="remainingPoints<=0"></i>
						<span class="button-content">
							<b>Fuerza:</b> [[ stats['stat_strength'] ]]

							@if ( isset($positiveBonifications['stat_strength']) && $positiveBonifications['stat_strength'] > 0 )
								<span class="positive">+{{ $positiveBonifications['stat_strength'] }}</span>
							@endif

							@if ( isset($negativeBonifications['stat_strength']) && $negativeBonifications['stat_strength'] > 0 )
								<span class="negative">-{{ $negativeBonifications['stat_strength'] }}</span>
							@endif
						</span>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 5px;" ng-init="stats['stat_luck']='{{ $character->stat_luck }}'">
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Suerte:</b> Aumenta la probabilidad de asestar un golpe crítico, ya sea mágico o físico.">
					<span class="ui-button button" style="cursor: default;">
						<a ng-click="addStat('stat_luck')" class="button-icon" ng-show="remainingPoints>0">+</a>
						<i class="button-icon thunder" ng-show="remainingPoints<=0"></i>
						<span class="button-content">
							<b>Suerte:</b> [[ stats['stat_luck'] ]]

							@if ( isset($positiveBonifications['stat_luck']) && $positiveBonifications['stat_luck'] > 0 )
								<span class="positive">+{{ $positiveBonifications['stat_luck'] }}</span>
							@endif

							@if ( isset($negativeBonifications['stat_luck']) && $negativeBonifications['stat_luck'] > 0 )
								<span class="negative">-{{ $negativeBonifications['stat_luck'] }}</span>
							@endif
						</span>
					</span>
				</span>
			</li>
		</ul>
	</div>
	<!-- END ESTADÍSTICAS -->
</div>

<!-- ACTIVIDADES -->
@if ( count($activities) > 0 )
<div>
	<h2>Actividad(es)</h2>
	<ul>
		@foreach ( $activities as $activity )
		<li>
			@if ( $activity->name == 'travel' )
				Estás viajando a {{ $activity->data['zone']->name }}: 
			@elseif ( $activity->name == 'battlerest' )
				Descanzando de batalla: 
			@elseif ( $activity->name == 'explore' )
				Explorando: 
			@endif
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
	<div class="pull-left" style="margin-right: 10px;">
		<img src="{{ URL::base() }}/img/zones/32/{{ $zone->id }}.png" alt="{{ $zone->name }}" width="32px" height="32px">
	</div>
	<b>{{ $zone->name }}</b>
	<p><i>{{ $zone->description }}</i></p>
</div>
<!-- END ZONA -->

<!-- INVENTARIO -->
<h2>Inventario</h3>
<ul class="inline">
	@for ( $i = 1, $max = 6; $i <= $max; $i++ )
		<li style="vertical-align: top;">
		<div class="inventory-item">
		@if ( isset($items['inventory']) )
			@foreach ( $items['inventory'] as $characterItem )
				@if ( $characterItem->slot == $i && $item = $characterItem->item )
					<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $characterItem->item_id }}.png" alt="" width="80px" height="80px" data-toggle="popover" data-placement="top" data-original-title="
					{{ $item->get_text_for_tooltip() }}

					<div style='padding: 20px;'>
					@if ( $item->type == 'arrow' && isset($items['lrhand']) && $items['lrhand'][0]->item->type != 'bow' )
						<span style='font-size: 11px;'>Debes tener equipado un arco para usar flechas</span>
					@else
						<a href='{{ URL::to('authenticated/manipulateItem/' . $characterItem->id) }}' class='btn btn-primary pull-left'>
							@if ( $item->type == 'potion' )
								Usar
							@else
								Equipar
							@endif
						</a>
					@endif

					<a href='{{ URL::to('authenticated/destroyItem/' . $characterItem->id) }}' class='btn btn-danger pull-right'>Destruir</a>
					</div>">
					<div class="inventory-item-amount" data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">{{ $characterItem->count }}</div>
				@endif
			@endforeach
		@endif
		</div>
		</li>
	@endfor
</ul>
<!-- END INVENTARIO -->

<!--<script src="{{ URL::base() }}/js/controllers/CharacterStatsController.js"></script>-->