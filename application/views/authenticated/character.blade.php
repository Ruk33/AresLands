@if ( isset($character) && isset($characterToSee) )
	<div class="span6" style="margin-left: 20px; margin-right: -20px;">
		<h2>{{ $characterToSee->name }} (Nivel: {{ $characterToSee->level }})</h2>

		<div style="min-height: 405px;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) && $lrhand = $items['lrhand'][0]->item )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						<img src="{{ URL::base() }}/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="{{ $lrhand->get_text_for_tooltip() }}">
					</div>
				</div>
			<!-- END DOS MANOS -->
			@else
				<!-- MANO DERECHA -->
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['rhand']) && $rhand = $items['rhand'][0]->item )
						<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $rhand->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="{{ $rhand->get_text_for_tooltip() }}">
					@endif
					</div>
				</div>
				<!-- END MANO DERECHA -->

				<!-- MANO IZQUIERDA -->
				<div style="position: absolute; margin-left: 250px; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['lhand']) && $lhand = $items['lhand'][0]->item )
						<img style="cursor: pointer;" src="{{ URL::base() }}/img/icons/items/{{ $lhand->id }}.png" alt="" data-toggle="popover" data-placement="top" data-original-title="{{ $lhand->get_text_for_tooltip() }}">
					@endif
					</div>
				</div>
				<!-- END MANO IZQUIERDA -->
			@endif

			<!-- AYUDANTE -->
			<!--
			<div style="position: absolute; margin-left: 260px; margin-top: 50px;">
				<img src="{{ URL::base() }}/img/characters/ayudante.png" alt="">
			</div>
			-->
			<!-- END AYUDANTE -->
			
			<!-- PERSONAJE -->
			<img src="{{ URL::base() }}/img/characters/{{ $characterToSee->race }}_{{ $characterToSee->gender }}_0.png" alt="">
			<!-- END PERSONAJE -->
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="span6">
		<h2>Estadísticas</h2>
		<ul class="unstyled">
			<li>
				<b>Vitalidad:</b> {{ mt_rand($characterToSee->stat_life, $characterToSee->stat_life * 2) }}
			</li>
			<li>
				<b>Destreza:</b> {{ mt_rand($characterToSee->stat_dexterity, $characterToSee->stat_dexterity * 2) }}
			</li>
			<li>
				<b>Magia:</b> {{ mt_rand($characterToSee->stat_magic, $characterToSee->stat_magic * 2) }}
			</li>
			<li>
				<b>Fuerza:</b> {{ mt_rand($characterToSee->stat_strength, $characterToSee->stat_strength * 2) }}
			</li>
			<li>
				<b>Suerte:</b> {{ mt_rand($characterToSee->stat_luck, $characterToSee->stat_luck * 2) }}
			</li>
		</ul>

		@if ( $character->id != $characterToSee->id && $character->zone_id == $characterToSee->zone_id )
			<h2>¿Te atreves a batallar?</h2>
			<a href="{{ URL::to('authenticated/toBattle/' . $characterToSee->name) }}">Luchar contra <b>{{ $characterToSee->name }}</b></a>
		@endif
	</div>
	<!-- END ESTADISTICAS -->
@else
	<h2>El personaje especificado no existe</h2>
	<p><em>En estas tierras hay muchos forasteros, pero ninguno con este nombre...</em></p>
@endif