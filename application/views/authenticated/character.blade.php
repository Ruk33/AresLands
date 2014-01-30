@if ( isset($character) && isset($characterToSee) )
	<div class="text-center" style="margin-left: -15px;">
		<h1 style="margin-bottom: -15px !important;">{{ $characterToSee->name }}</h1>
		
		@if ( $characterToSee->clan_id > 0 )
			<i>Miembro de {{ $characterToSee->clan()->select(array('id', 'name'))->first()->get_link() }}</i>
		@endif
		
		<div style="font-size: 12px;">Nivel: {{ $characterToSee->level }}</div>
		
		@if ( $character->is_admin() )
			<!-- BUFFS -->
			@if ( count($skills) > 0 )
				<ul class="unstyled inline">
					@foreach ( $skills as $skill )
						<li class="text-center clan-member-link" style="vertical-align: top; position: relative;">
							<a class="close" style="position: absolute; top: 1px; right: 5px;" href="{{ URL::to('admin/removeCharacterSkill/' . $skill->id) }}">&times;</a>
							
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
		@endif
		
		
		@if ( $character->is_admin() )
			{{ Form::open('admin/modifyCharacterStats') }}
			{{ Form::hidden('character', $characterToSee->id) }}
		@endif
		
		<ul class="inline" style="margin-top: 40px; margin-bottom: 25px;">
			<li>
				<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Fuerza física:</b> Aumenta la potencia de tus ataques físicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon hearth"></i>
						<span class="button-content">
							@if ( $character->is_admin() )
								{{ Form::number('stat_strength', $characterToSee->stat_strength, array('style' => 'width: 50px')) }}
							@else
								@if ( $hideStats )
									{{ mt_rand($characterToSee->stat_strength, $characterToSee->stat_strength * 1.3) }}
								@else
									{{ $characterToSee->stat_strength }}
								@endif
							@endif
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Destreza física:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon boot"></i>
						<span class="button-content">
							@if ( $character->is_admin() )
								{{ Form::number('stat_dexterity', $characterToSee->stat_dexterity, array('style' => 'width: 50px')) }}
							@else
								@if ( $hideStats )
									{{ mt_rand($characterToSee->stat_dexterity, $characterToSee->stat_dexterity * 1.3) }}
								@else
									{{ $characterToSee->stat_dexterity }}
								@endif
							@endif
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Resistencia:</b> Aumenta tu defensa contra ataques físicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon boot"></i>
						<span class="button-content">
							@if ( $character->is_admin() )
								{{ Form::number('stat_resistance', $characterToSee->stat_resistance, array('style' => 'width: 50px')) }}
							@else
								@if ( $hideStats )
									{{ mt_rand($characterToSee->stat_resistance, $characterToSee->stat_resistance * 1.3) }}
								@else
									{{ $characterToSee->stat_resistance }}
								@endif
							@endif
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Poder mágico:</b> Aumenta el poder de los ataques mágicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon fire"></i>
						<span class="button-content">
							@if ( $character->is_admin() )
								{{ Form::number('stat_magic', $characterToSee->stat_magic, array('style' => 'width: 50px')) }}
							@else
								@if ( $hideStats )
									{{ mt_rand($characterToSee->stat_magic, $characterToSee->stat_magic * 1.3) }}
								@else
									{{ $characterToSee->stat_magic }}
								@endif
							@endif
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Habilidad mágica:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon axe"></i>
						<span class="button-content">
							@if ( $character->is_admin() )
								{{ Form::number('stat_magic_skill', $characterToSee->stat_magic_skill, array('style' => 'width: 50px')) }}
							@else
								@if ( $hideStats )
									{{ mt_rand($characterToSee->stat_magic_skill, $characterToSee->stat_magic_skill * 1.3) }}
								@else
									{{ $characterToSee->stat_magic_skill }}
								@endif
							@endif
						</span>
					</span>
				</span>
			</li>
			<li>
				<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Contraconjuro:</b> Aumenta tu defensa contra ataques mágicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon thunder"></i>
						<span class="button-content">
							@if ( $character->is_admin() )
								{{ Form::number('stat_magic_resistance', $characterToSee->stat_magic_resistance, array('style' => 'width: 50px')) }}
							@else
								@if ( $hideStats )
									{{ mt_rand($characterToSee->stat_magic_resistance, $characterToSee->stat_magic_resistance * 1.3) }}
								@else
									{{ $characterToSee->stat_magic_resistance }}
								@endif
							@endif
						</span>
					</span>
				</span>
			</li>
		</ul>
		
		@if ( $character->is_admin() )
			{{ Form::submit('Editar atributos', array('class' => 'btn btn-primary')) }}
			{{ Form::close() }}
		@endif
		
		@if ( $character->is_admin() )
			<hr>
			<h3>Atributos extra</h3>
		
			{{ Form::open('admin/modifyCharacterExtraStats') }}
			{{ Form::hidden('character', $characterToSee->id) }}
			
			<ul class="inline" style="margin-top: 40px; margin-bottom: 25px;">
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Fuerza física:</b> Aumenta la potencia de tus ataques físicos.">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon hearth"></i>
							<span class="button-content">
								{{ Form::number('stat_strength_extra', $characterToSee->stat_strength_extra, array('style' => 'width: 50px')) }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Destreza física:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos.">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon boot"></i>
							<span class="button-content">
								{{ Form::number('stat_dexterity_extra', $characterToSee->stat_dexterity_extra, array('style' => 'width: 50px')) }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Resistencia:</b> Aumenta tu defensa contra ataques físicos.">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon boot"></i>
							<span class="button-content">
								{{ Form::number('stat_resistance_extra', $characterToSee->stat_resistance_extra, array('style' => 'width: 50px')) }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Poder mágico:</b> Aumenta el poder de los ataques mágicos.">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon fire"></i>
							<span class="button-content">
								{{ Form::number('stat_magic_extra', $characterToSee->stat_magic_extra, array('style' => 'width: 50px')) }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Habilidad mágica:</b> Aumenta tu velocidad de golpeo en las batallas, pudiendo lograr así múltiples ataques consecutivos.">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon axe"></i>
							<span class="button-content">
								{{ Form::number('stat_magic_skill_extra', $characterToSee->stat_magic_skill_extra, array('style' => 'width: 50px')) }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="<b>Contraconjuro:</b> Aumenta tu defensa contra ataques mágicos.">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon thunder"></i>
							<span class="button-content">
								{{ Form::number('stat_magic_resistance_extra', $characterToSee->stat_magic_resistance_extra, array('style' => 'width: 50px')) }}
							</span>
						</span>
					</span>
				</li>
			</ul>
			
			{{ Form::submit('Editar atributos extra', array('class' => 'btn btn-primary')) }}
			{{ Form::close() }}
		@endif
		
		<div style="position: relative; width: 340px; margin: 0 auto;">
			<!-- DOS MANOS -->
			@if ( isset($items['lrhand']) && $lrhand = $items['lrhand'][0]->item )
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
						@if ( $character->is_admin() )
							<a style="position: absolute; top: -2px; right: 5px;" href="{{ URL::to('admin/removeEquippedCharacterItem/' . $items['lrhand'][0]->id) }}">&times;</a>
						@endif
						
						<img src="{{ URL::base() }}/img/icons/items/{{ $items['lrhand'][0]->item->id }}.png" alt="" width="80px" height="80px" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="{{ $lrhand->get_text_for_tooltip() }}">
					</div>
				</div>
			<!-- END DOS MANOS -->
			@else
				<!-- MANO DERECHA -->
				<div style="position: absolute; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['rhand']) && $rhand = $items['rhand'][0]->item )
						@if ( $character->is_admin() )
							<a style="position: absolute; top: -2px; right: 5px;" href="{{ URL::to('admin/removeEquippedCharacterItem/' . $items['rhand'][0]->id) }}">&times;</a>
						@endif
						
						<img src="{{ URL::base() }}/img/icons/items/{{ $rhand->id }}.png" alt="" width="80px" height="80px" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="{{ $rhand->get_text_for_tooltip() }}">
					@endif
					</div>
				</div>
				<!-- END MANO DERECHA -->

				<!-- MANO IZQUIERDA -->
				<div style="position: absolute; margin-left: 250px; margin-top: 150px;">
					<div class="equipped-item">
					@if ( isset($items['lhand']) && $lhand = $items['lhand'][0]->item )
						@if ( $character->is_admin() )
							<a style="position: absolute; top: -2px; right: 5px;" href="{{ URL::to('admin/removeEquippedCharacterItem/' . $items['lhand'][0]->id) }}">&times;</a>
						@endif
						
						<img src="{{ URL::base() }}/img/icons/items/{{ $lhand->id }}.png" alt="" width="80px" height="80px" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="{{ $lhand->get_text_for_tooltip() }}">
					@endif
					</div>
				</div>
				<!-- END MANO IZQUIERDA -->
			@endif

			<!-- ORBES -->
			<div class="quest-reward-item" style="position: absolute; margin-left: 250px; margin-top: 250px;">
				@if ( isset($orb) )
					<img src="{{ URL::base() }}/img/icons/orbs/{{ $orb->id }}.png" data-toggle="tooltip" data-title="<div style='width: 200px;'><strong>{{ $orb->name }}</strong><p>{{ $orb->description }}</p></div>">
				@endif
			</div>
			<!-- END ORBES -->

			<!-- AYUDANTE -->
			<div style="position: absolute; margin-left: 255px; margin-top: 65px;">
				@if ( isset($items['mercenary']) )
					<?php $mercenary = $items['mercenary'][0]->item; ?>
				
					@if ( $character->is_admin() )
						<a style="position: absolute; top: -10px; right: 0px;" href="{{ URL::to('admin/removeEquippedCharacterItem/' . $items['mercenary'][0]->id) }}">&times;</a>
					@endif
					
					<img src="{{ URL::base() }}/img/icons/items/{{ $mercenary->id }}.png" alt="" width="64px" height="64px" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="
					{{ $mercenary->get_text_for_tooltip() }}">
				@endif
			</div>
			<!-- END AYUDANTE -->
			
			<!-- PERSONAJE -->
			<img src="{{ URL::base() }}/img/characters/{{ $characterToSee->race }}_{{ $characterToSee->gender }}_999.png" alt="">
			<!-- END PERSONAJE -->
		</div>
		
		<div class="clear-fix"></div>
		
		<div>
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
		
		@if ( $characterToSee->can_be_attacked($character) )
			<h2 style="background-image: none;">¿Te atreves a batallar?</h2>
			@if ( $character->is_in_clan_of($characterToSee) )
				    <p class="text-warning">
				    	<strong>¡Cuidado!</strong>
				    	<br>
				    	{{ $characterToSee->name }} pertenece al mismo grupo en el que estás.
				    </p>
			@endif

			@if ( Tournament::is_active() )
				<p class="positive">Victoria: {{ Tournament::get_victory_score($character, $characterToSee) }}</p>
				<p class="negative">Derrota: {{ Tournament::get_defeat_score($character, $characterToSee) }}</p>
			@endif
			
			@foreach ( $pairs as $pair )
				@if ( $pair->id != $characterToSee->id )
					{{ Form::open(URL::to('authenticated/toBattle')) }}
						{{ Form::token() }}
						{{ Form::hidden('pair', $pair->id) }}
						{{ Form::hidden('name', $characterToSee->name) }}
						{{ Form::submit('Luchar con la ayuda de ' . $pair->name, array('class' => 'btn btn-link', 'style' => 'color: white; text-shadow: none;')) }}
					{{ Form::close() }}
				@endif
			@endforeach
			
			{{ Form::open(URL::to('authenticated/toBattle')) }}
				{{ Form::token() }}
				{{ Form::hidden('name', $characterToSee->name) }}
				{{ Form::submit('Luchar contra ' . $characterToSee->name, array('class' => 'btn btn-link', 'style' => 'color: white; text-shadow: none;')) }}
			{{ Form::close() }}
		@else
			@if ( $character->zone_id != $characterToSee->zone_id )
				<div class="negative" style="margin-top: 25px; font-size: 16px;">¡{{ $characterToSee->name }} está en otra zona!</div>
			@elseif ( $characterToSee->has_protection($character) )
				<div class="negative" style="margin-top: 25px; font-size: 16px;">{{ $characterToSee->name }} está momentáneamente protegido de tus ataques. Debes esperar un poco mas para poder atacarlo.</div>
			@endif
		@endif
		</div>
	</div>
@else
	<h2>El personaje especificado no existe</h2>
	<p><em>En estas tierras hay muchos forasteros, pero ninguno con este nombre...</em></p>
@endif