@if ( isset($character) && isset($characterToSee) )
	<div class="span6" style="margin-left: 20px; margin-right: -20px;">
		<h2>{{ $characterToSee->name }} (Nivel: {{ $characterToSee->level }})</h2>

		<div style="min-height: 405px;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) && $lrhand = $items['lrhand'][0]->item )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						<img src="{{ URL::base() }}/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="" width="80px" height="80px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $lrhand->get_text_for_tooltip() }}">
					</div>
				</div>
			<!-- END DOS MANOS -->
			@else
				<!-- MANO DERECHA -->
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['rhand']) && $rhand = $items['rhand'][0]->item )
						<img src="{{ URL::base() }}/img/icons/items/{{ $rhand->id }}.png" alt="" width="80px" height="80px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $rhand->get_text_for_tooltip() }}">
					@endif
					</div>
				</div>
				<!-- END MANO DERECHA -->

				<!-- MANO IZQUIERDA -->
				<div style="position: absolute; margin-left: 250px; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['lhand']) && $lhand = $items['lhand'][0]->item )
						<img src="{{ URL::base() }}/img/icons/items/{{ $lhand->id }}.png" alt="" width="80px" height="80px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $lhand->get_text_for_tooltip() }}">
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
			<img src="{{ URL::base() }}/img/characters/{{ $characterToSee->race }}_{{ $characterToSee->gender }}_0.png" alt="">
			<!-- END PERSONAJE -->
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="span6">
		<h2>Estadísticas</h2>
		<ul class="unstyled">
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Vitalidad:</b> Aumenta los puntos de vida que posees y la regeneración de los mismos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon hearth"></i>
						<span class="button-content">
							<b>Vitalidad:</b> {{ mt_rand($characterToSee->stat_life, $characterToSee->stat_life * 2) }}
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Destreza:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon boot"></i>
						<span class="button-content">
							<b>Destreza:</b> {{ mt_rand($characterToSee->stat_dexterity, $characterToSee->stat_dexterity * 2) }}
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Magia:</b> Aumenta el poder de los ataques mágicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon fire"></i>
						<span class="button-content">
							<b>Magia:</b> {{ mt_rand($characterToSee->stat_magic, $characterToSee->stat_magic * 2) }}
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Fuerza:</b> Aumenta el poder de los ataques físicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon axe"></i>
						<span class="button-content">
							<b>Fuerza:</b> {{ mt_rand($characterToSee->stat_strength, $characterToSee->stat_strength * 2) }}
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="right" data-original-title="<b>Suerte:</b> Aumenta la probabilidad de asestar un golpe crítico, ya sea mágico o físico.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon thunder"></i>
						<span class="button-content">
							<b>Suerte:</b> {{ mt_rand($characterToSee->stat_luck, $characterToSee->stat_luck * 2) }}
						</span>
					</span>
				</span>
			</li>
		</ul>

		<?php
			$characterToSeeZone = $characterToSee->zone;
			if ( $characterToSeeZone->type == 'city' || $characterToSeeZone->type = 'land' )
			{
				$characterToSeeZone = $characterToSeeZone->id;
			}
			else
			{
				$characterToSeeZone = $characterToSeeZone->belongs_to;
			}

			$characterZone = $characterToSee->zone;
			if ( $characterZone->type == 'city' || $characterZone->type = 'land' )
			{
				$characterZone = $characterZone->id;
			}
			else
			{
				$characterZone = $characterZone->belongs_to;
			}
		?>
		@if ( $character->id != $characterToSee->id && $characterZone == $characterToSeeZone )
			<h2>¿Te atreves a batallar?</h2>
			@if ( $character->is_in_clan_of($characterToSee) )
				    <p class="text-warning">
				    	<strong>¡Cuidado!</strong>
				    	<br>
				    	{{ $characterToSee->name }} pertenece al mismo grupo en el que estás.
				    </p>
			@endif
			<a href="{{ URL::to('authenticated/toBattle/' . $characterToSee->name) }}">Luchar contra <b>{{ $characterToSee->name }}</b></a>
		@endif
	</div>
	<!-- END ESTADISTICAS -->
@else
	<h2>El personaje especificado no existe</h2>
	<p><em>En estas tierras hay muchos forasteros, pero ninguno con este nombre...</em></p>
@endif