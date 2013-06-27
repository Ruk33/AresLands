<div class="bar">
	@foreach ( $npcs as $npc )
		<a href="{{ URL::to('authenticated/npc/' . $npc->name) }}"><img src="/img/icons/npcs/{{ $npc->id }}.png" alt="" data-toggle="tooltip" data-placement="left" data-original-title="{{ $npc->tooltip_dialog }}"></a>
	@endforeach
</div>
<!--<hr class="line">-->

<div class="row">
	<ul class="unstyled inline" style="margin-left: 20px;">
		@foreach ( $skills as $skill )
			<li>
				<img src="/img/icons/skills/{{ $skill->skill_id }}.jpg" alt="" width="32px" height="32px" data-toggle="tooltip" data-placement="right" data-original-title="
				<b>{{ $skill->skill->name }}</b> (Nivel: {{ $skill->level }})
				<p>{{ $skill->skill->description }}</p>">
			</li>
		@endforeach
	</ul>

	<div class="span6" style="margin-left: 20px; margin-right: -20px;">
		<h2>Personaje</h2>
		<div style="min-height: 405px;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) && $lrhand = $items['lrhand'][0]->item )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						<img src="/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
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
						<img style="cursor: pointer;" src="/img/icons/items/{{ $rhand->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
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
						<img style="cursor: pointer;" src="/img/icons/items/{{ $lhand->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
						{{ $lhand->get_text_for_tooltip() }}
						<a href='{{ URL::to('authenticated/manipulateItem/' . $items['lhand'][0]->id) }}'>
							Desequipar
						</a>">
					@endif
					</div>
				</div>
				<!-- END MANO IZQUIERDA -->
			@endif

			<!-- AYUDANTE -->
			<div style="position: absolute; margin-left: 260px; margin-top: 50px;">
				<img src="/img/characters/ayudante.png" alt="">
			</div>
			<!-- END AYUDANTE -->
			
			<!-- PERSONAJE -->
			<img src="/img/characters/{{ $character->race }}_{{ $character->gender }}_
			@if ( isset($rhand) )
				{{ $rhand->id }}
			@elseif ( isset($lhand) )
				{{ $lhand->id }}
			@elseif ( isset($lrhand) )
				{{ $lrhand->id }}
			@else
				0
			@endif
			.png" alt="">
			<!-- END PERSONAJE -->
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="span6">
		<h2>Estadísticas</h2>
		<ul class="unstyled">
			<li style="margin-bottom: 10px;"><span data-toggle="tooltip" data-placement="top" data-original-title="<b>Vida:</b> vida actual / vida máxima"><b>Vida:</b> {{ $character->current_life }}/{{ $character->max_life }}</span></li>
			
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Vitalidad:</b> Aumenta los puntos de vida que el personaje posee y la regeneración de los mismos.">
					<b>Vitalidad:</b> {{ $character->stat_life }}

					@if ( isset($positiveBonifications['stat_life']) && $positiveBonifications['stat_life'] > 0 )
						<span class="positive">+{{ $positiveBonifications['stat_life'] }}</span>
					@endif

					@if ( isset($negativeBonifications['stat_life']) && $negativeBonifications['stat_life'] > 0 )
						<span class="negative">-{{ $negativeBonifications['stat_life'] }}</span>
					@endif
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Destreza:</b> Aumenta la precisión de los ataques, aumentando así la probabilidad de asestar ya sea un golpe físico o mágico.">
					<b>Destreza:</b> {{ $character->stat_dexterity }}

					@if ( isset($positiveBonifications['stat_dexterity']) && $positiveBonifications['stat_dexterity'] > 0 )
						<span class="positive">+{{ $positiveBonifications['stat_dexterity'] }}</span>
					@endif

					@if ( isset($negativeBonifications['stat_dexterity']) && $negativeBonifications['stat_dexterity'] > 0 )
						<span class="negative">-{{ $negativeBonifications['stat_dexterity'] }}</span>
					@endif
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Magia:</b> Aumenta el poder de los ataques mágicos.">
					<b>Magia:</b> {{ $character->stat_magic }}

					@if ( isset($positiveBonifications['stat_magic']) && $positiveBonifications['stat_magic'] > 0 )
						<span class="positive">+{{ $positiveBonifications['stat_magic'] }}</span>
					@endif

					@if ( isset($negativeBonifications['stat_magic']) && $negativeBonifications['stat_magic'] > 0 )
						<span class="negative">-{{ $negativeBonifications['stat_magic'] }}</span>
					@endif
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Fuerza:</b> Aumenta el poder de los ataques físicos.">
					<b>Fuerza:</b> {{ $character->stat_strength }}

					@if ( isset($positiveBonifications['stat_strength']) && $positiveBonifications['stat_strength'] > 0 )
						<span class="positive">+{{ $positiveBonifications['stat_strength'] }}</span>
					@endif

					@if ( isset($negativeBonifications['stat_strength']) && $negativeBonifications['stat_strength'] > 0 )
						<span class="negative">-{{ $negativeBonifications['stat_strength'] }}</span>
					@endif
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Suerte:</b> Aumenta la probabilidad de asestar un golpe crítico, ya sea mágico o físico. Además, aumenta las recompensas y la probabilidad de obtener un objetos raros.">
					<b>Suerte:</b> {{ $character->stat_luck }}

					@if ( isset($positiveBonifications['stat_luck']) && $positiveBonifications['stat_luck'] > 0 )
						<span class="positive">+{{ $positiveBonifications['stat_luck'] }}</span>
					@endif

					@if ( isset($negativeBonifications['stat_luck']) && $negativeBonifications['stat_luck'] > 0 )
						<span class="negative">-{{ $negativeBonifications['stat_luck'] }}</span>
					@endif
				</span>
			</li>
		</ul>
	</div>
	<!-- END ESTADÍSTICAS -->
	
	<!-- ZONA -->
	<div class="span6">
		<h2>Ubicación</h2>
		@if ( count($activities) > 0 )
			@foreach ( $activities as $activity )
				@if ( $activity->name == 'travel' )
					Saliendo de 
				@endif
			@endforeach
		@endif
		{{ $character->zone->name }}
	</div>
	<!-- END ZONA -->
	
	<!-- ACTIVIDADES -->
	@if ( count($activities) > 0 )
	<div class="span6">
		<h2>Actividad(es)</h2>
		<ul>
			@foreach ( $activities as $activity )
			<li>
				@if ( $activity->name == 'travel' )
					Estás viajando a {{ $activity->data['zone']->name }}: 
				@elseif ( $activity->name == 'battlerest' )
					Descanzando de batalla: 
				@endif
				<span class="timer" data-endtime="{{ $activity->end_time }}"></span>
			</li>
			@endforeach
		</ul>
	</div>
	@endif
	<!-- END ACTIVIDADES -->
</div>

<!-- INVENTARIO -->
<h2>Inventario</h3>
<div class="text-center">
	@for ( $i = 1, $max = 6; $i <= $max; $i++ )
		<div class="inventory-item" style="float: left;">
		@if ( isset($items['inventory']) )
			@foreach ( $items['inventory'] as $characterItem )
				@if ( $characterItem->slot == $i && $item = $characterItem->item )
					<img style="cursor: pointer;" src="/img/icons/inventory/items/{{ $characterItem->item_id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
					{{ $item->get_text_for_tooltip() }}
					@if ( $item->type == 'arrow' && $items['lrhand'][0]->item->type != 'bow' )
						<span style='font-size: 11px;'>Debes tener equipado un arco para usar flechas</span>
					@else
						<a href='{{ URL::to('authenticated/manipulateItem/' . $characterItem->id) }}'>
							@if ( $item->type == 'potion' )
								Usar
							@else
								Equipar
							@endif
						</a>
					@endif">
					<div class="inventory-item-amount" data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">{{ $characterItem->count }}</div>
				@endif
			@endforeach
		@endif
		</div>
	@endfor
</div>
<!-- END INVENTARIO -->