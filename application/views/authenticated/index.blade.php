<div class="row">
	<div class="span6" style="margin-left: 20px; margin-right: -20px;">
		<h2>Personaje</h2>
		<div style="min-height: 405px;">
			<!-- TWO HAND WEAPON -->
			@if ( isset($items['lrhand']) )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item"><img src="/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt=""></div>
				</div>
			<!-- TWO HAND END -->
			@else
				<!-- WEAPON -->
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['rhand']) )
						<img src="/img/icons/items/{{ $items['rhand'][0]->item->id }}.png" alt="">
					@endif
					</div>
				</div>
				<!-- END WEAPON -->

				<!-- SHIELD -->
				<div style="position: absolute; margin-left: 250px; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['lhand']) )
						<img src="/img/icons/items/{{ $items['lhand'][0]->item->id }}.png" alt="">
					@endif
					</div>
				</div>
				<!-- SHIELD END -->
			@endif

			<!-- HELPER -->
			<div style="position: absolute; margin-left: 260px; margin-top: 50px;">
				<img src="/img/characters/ayudante.png" alt="">
			</div>
			<!-- END HELPER -->
			
			<!-- CHARACTER -->
			<img src="/img/characters/{{ Session::get('character')->race }}_{{ Session::get('character')->gender }}_{{ 1 }}.png" alt="">
			<!-- END CHARACTER -->
		</div>
	</div>

	<!-- STATS -->
	<div class="span6">
		<h2>Estadísticas</h2>
		<ul class="unstyled">
			<li style="margin-bottom: 10px;"><span data-toggle="tooltip" data-placement="top" data-original-title="<b>Vida:</b> vida actual / vida máxima"><b>Vida:</b> {{ Session::get('character')->current_life }}/{{ Session::get('character')->max_life }}</span></li>
			
			<li><span data-toggle="tooltip" data-placement="right" data-original-title="<b>Vitalidad:</b> Aumenta los puntos de vida que el personaje posee y la regeneración de los mismos."><b>Vitalidad:</b> {{ Session::get('character')->stat_life }}</span></li>
			<li><span data-toggle="tooltip" data-placement="right" data-original-title="<b>Destreza:</b> Aumenta la precisión de los ataques, aumentando así la probabilidad de asestar ya sea un golpe físico o mágico."><b>Destreza:</b> {{ Session::get('character')->stat_dexterity }}</span></li>
			<li><span data-toggle="tooltip" data-placement="right" data-original-title="<b>Magia:</b> Aumenta el poder de los ataques mágicos."><b>Magia:</b> {{ Session::get('character')->stat_magic }}</span></li>
			<li><span data-toggle="tooltip" data-placement="right" data-original-title="<b>Fuerza:</b> Aumenta el poder de los ataques físicos."><b>Fuerza:</b> {{ Session::get('character')->stat_strength }}</span></li>
			<li><span data-toggle="tooltip" data-placement="right" data-original-title="<b>Suerte:</b> Aumenta la probabilidad de asestar un golpe crítico, ya sea mágico o físico. Además, aumenta las recompensas y la probabilidad de obtener un objetos raros."><b>Suerte:</b> {{ Session::get('character')->stat_luck }}</span></li>
		</ul>
	</div>
	<!-- STATS END -->
	
	<!-- ZONE -->
	<div class="span6">
		<h2>Estás en...</h2>
		Los montes bárbaros
	</div>
	<!-- ZONE END -->
	
	<!-- ACTIVITIES -->
	<div class="span6">
		<h2>Actividad</h2>
		<ul>
			<li>Viajando a El Valle De La Sangre: 2:53</li>
		</ul>
	</div>
	<!-- ACTIVITIES END -->
</div>

<!-- INVENTORY -->
<h2>Inventario</h3>
<div class="text-center">
	@for ( $i = 1, $max = 6; $i <= $max; $i++ )
		<div class="inventory-item" style="float: left;">
		@for ( $n = 0, $count = count($items['inventory']); $n < $count; $n++ )
			@if ( isset($items['inventory'][$n]) && $items['inventory'][$n]->slot == $i )
				@if ( $item = $items['inventory'][$n]->item ) @endif
				<img style="cursor: pointer;" src="/img/icons/inventory/items/{{ $item->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="
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
					<a href='{{ URL::to('authenticated/manipulateItem/' . $item->id) }}'>
						@if ( $item->type == 'potion' )
							Usar
						@else
							@if ( $item->type == 'arrow' && $items['lrhand'][0]->item->type != 'bow' )
								Necesitas tener equipado un arco para usar flechas
							@else
								Equipar
							@endif
						@endif
					</a>
				</div>">
				<div class="inventory-item-amount" data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">{{ $items['inventory'][$n]->count }}</div>
			@endif
		@endfor
		</div>
	@endfor
</div>
<!-- INVENTORY END -->

<script>
	/*
	 *	Iniciamos los tooltips
	 */
	$('[data-toggle="tooltip"]').tooltip({ html: true });
	$('[data-toggle="popover"]').popover({ html: true });
</script>