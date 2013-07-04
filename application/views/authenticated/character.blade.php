@if ( isset($character) && isset($characterToSee) )
	<div class="span6" style="margin-left: 175px;">
		<h2>{{ $characterToSee->name }}</h2>

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
			<img src="{{ URL::base() }}/img/characters/{{ $characterToSee->race }}_{{ $characterToSee->gender }}_
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

		@if ( $character->id != $characterToSee->id && $character->zone_id == $characterToSee->zone_id )
			<h2>¿Te atreves a batallar?</h2>
			<a href="{{ URL::to('authenticated/toBattle/' . $characterToSee->name) }}">Luchar contra <b>{{ $characterToSee->name }}</b></a>
		@endif
	</div>
@else
	<h2>El personaje especificado no existe</h2>
	<p><em>En estas tierras hay muchos forasteros, pero ninguno con este nombre...</em></p>
@endif