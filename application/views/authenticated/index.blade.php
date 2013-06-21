<div class="row">
	<ul class="unstyled inline" style="margin-left: 20px;">
		@foreach ( $skills as $skill )
			<li>
				<img src="/img/icons/skills/{{ $skill->skill_id }}.jpg" alt="" width="32px" height="32px" data-toggle="tooltip" data-placement="right" data-original-title="
				<b>{{ $skill->skill->name }}</b><p>{{ $skill->skill->description }}</p>">
			</li>
		@endforeach
	</ul>

	<div class="span6" style="margin-left: 20px; margin-right: -20px;">
		<h2>Personaje</h2>
		<div style="min-height: 405px;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						<img src="/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="">
					</div>
				</div>
			<!-- END DOS MANOS -->
			@else
				<!-- MANO DERECHA -->
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['rhand']) && $rhand = $items['rhand'][0]->item )
						<img style="cursor: pointer;" src="/img/icons/items/{{ $rhand->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
						<div style='width: 600px; text-align: left;'>
							<strong>{{ $rhand->name }}</strong> (<small>{{ $rhand->type }}</small>)
							<p>{{ $rhand->description }}</p>
							<ul>
								<li>Vitalidad: {{ $rhand->stat_life }}</li>
								<li>Destreza: {{ $rhand->stat_dexterity }}</li>
								<li>Magia: {{ $rhand->stat_magic }}</li>
								<li>Fuerza: {{ $rhand->stat_strength }}</li>
								<li>Suerte: {{ $rhand->stat_luck }}</li>
							</ul>
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
						<img style="cursor: pointer;" src="/img/icons/items/{{ $lhand->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
						<div style='width: 600px; text-align: left;'>
							<strong>{{ $lhand->name }}</strong> (<small>{{ $lhand->type }}</small>)
							<p>{{ $lhand->description }}</p>
							<ul>
								<li>Vitalidad: {{ $lhand->stat_life }}</li>
								<li>Destreza: {{ $lhand->stat_dexterity }}</li>
								<li>Magia: {{ $lhand->stat_magic }}</li>
								<li>Fuerza: {{ $lhand->stat_strength }}</li>
								<li>Suerte: {{ $lhand->stat_luck }}</li>
							</ul>
							<a href='{{ URL::to('authenticated/manipulateItem/' . $items['lhand'][0]->id) }}'>
								Desequipar
							</a>
						</div>">
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
		<h2>Estás en...</h2>
		Los montes bárbaros
	</div>
	<!-- END ZONA -->
	
	<!-- ACTIVIDADES -->
	<div class="span6">
		<h2>Actividad</h2>
		<ul>
			<li>Viajando a El Valle De La Sangre: 2:53</li>
		</ul>
	</div>
	<!-- END ACTIVIDADES -->
</div>

<!-- INVENTARIO -->
<h2>Inventario</h3>
<div class="text-center">
	@for ( $i = 1, $max = 6; $i <= $max; $i++ )
		<div class="inventory-item" style="float: left;">
		@foreach ( $items['inventory'] as $characterItem )
			@if ( $characterItem->slot == $i && $item = $characterItem->item )
				<img style="cursor: pointer;" src="/img/icons/inventory/items/{{ $characterItem->item_id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
				<div style='width: 600px; text-align: left;'>
					<strong>{{ $item->name }}</strong> (<small>{{ $item->type }}</small>)
					<p>{{ $item->description }}</p>
					<ul>
						<li>Vitalidad: {{ $item->stat_life }}</li>
						<li>Destreza: {{ $item->stat_dexterity }}</li>
						<li>Magia: {{ $item->stat_magic }}</li>
						<li>Fuerza: {{ $item->stat_strength }}</li>
						<li>Suerte: {{ $item->stat_luck }}</li>
					</ul>
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
					@endif					
				</div>">
				<div class="inventory-item-amount" data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">{{ $characterItem->count }}</div>
			@endif
		@endforeach
		</div>
	@endfor
</div>
<!-- END INVENTARIO -->

<script>
	/*
	 *	Iniciamos los tooltips
	 */
	$('[data-toggle="tooltip"]').tooltip({ html: true });
	$('[data-toggle="popover"]').popover({ html: true });
</script>