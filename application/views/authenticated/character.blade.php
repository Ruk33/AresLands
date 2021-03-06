@if ( isset($character) && isset($characterToSee) )
	<div class="text-center" style="margin-left: -15px;">
		<h1 style="margin-bottom: -15px !important;">{{ $characterToSee->name }}</h1>
        		
		@if ( $characterToSee->clan_id > 0 )
            <div><i>Miembro de {{ $characterToSee->clan->get_link() }}</i></div>
		@endif
		
		<div style="font-size: 12px;">
            <div>Nivel: {{ $characterToSee->level }}</div>
            <div><i>Servidor {{ $characterToSee->server->name }} (Nº {{ $characterToSee->server_id }})</i></div>
        </div>
        
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
		
		<ul class="inline" style="margin-top: 40px; margin-bottom: 25px;">
			<li>
                <span data-toggle="tooltip" data-placement="top" data-original-title="<b>Fuerza:</b> Aumenta tu daño en ataques físicos y tu posibilidad de bloquear daño. Además, reduce la posibilidad del enemigo de bloquear tu daño.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon axe"></i>
						<span class="button-content">
                            @if ( $hideStats )
                                {{ mt_rand($characterToSee->stat_strength, $characterToSee->stat_strength * 1.3) }}
                            @else
                                {{ $characterToSee->stat_strength }}
                            @endif
						</span>
					</span>
				</span>
			</li>
			<li>
                <span data-toggle="tooltip" data-placement="top" data-original-title="<b>Destreza física:</b> Aumenta tu chance de ataques críticos (tanto mágicos como físicos) y tu posibilidad de esquivar ataques. Reduce además, la chance del enemigo de que esquive tus ataques.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon boot"></i>
						<span class="button-content">
                            @if ( $hideStats )
                                {{ mt_rand($characterToSee->stat_dexterity, $characterToSee->stat_dexterity * 1.3) }}
                            @else
                                {{ $characterToSee->stat_dexterity }}
                            @endif
						</span>
					</span>
				</span>
			</li>
			<li>
                <span data-toggle="tooltip" data-placement="top" data-original-title="<b>Resistencia física:</b> Aumenta tu resistencia contra ataques físicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon hearth"></i>
						<span class="button-content">
                            @if ( $hideStats )
                                {{ mt_rand($characterToSee->stat_resistance, $characterToSee->stat_resistance * 1.3) }}
                            @else
                                {{ $characterToSee->stat_resistance }}
                            @endif
						</span>
					</span>
				</span>
			</li>
			<li>
                <span data-toggle="tooltip" data-placement="top" data-original-title="<b>Poder mágico:</b> Aumenta tu daño en ataques mágicos y reduce la chance de bloqueo del enemigo.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon fire"></i>
						<span class="button-content">
                            @if ( $hideStats )
                                {{ mt_rand($characterToSee->stat_magic, $characterToSee->stat_magic * 1.3) }}
                            @else
                                {{ $characterToSee->stat_magic }}
                            @endif
						</span>
					</span>
				</span>
			</li>
			<li>
                <span data-toggle="tooltip" data-placement="top" data-original-title="<b>Habilidad mágica:</b> Aumenta tu chance de golpe crítico mágico.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon arrow"></i>
						<span class="button-content">
                            @if ( $hideStats )
                                {{ mt_rand($characterToSee->stat_magic_skill, $characterToSee->stat_magic_skill * 1.3) }}
                            @else
                                {{ $characterToSee->stat_magic_skill }}
                            @endif
						</span>
					</span>
				</span>
			</li>
			<li>
                <span data-toggle="tooltip" data-placement="top" data-original-title="<b>Contraconjuro:</b> Aumenta tu resistencia contra ataques mágicos.">
					<span class="ui-button button" style="cursor: default;">
						<i class="button-icon thunder"></i>
						<span class="button-content">
                            @if ( $hideStats )
                                {{ mt_rand($characterToSee->stat_magic_resistance, $characterToSee->stat_magic_resistance * 1.3) }}
                            @else
                                {{ $characterToSee->stat_magic_resistance }}
                            @endif
						</span>
					</span>
				</span>
			</li>
		</ul>
		
		@if ( $character->is_admin() )
			<h3>Atributos extra</h3>
			
			<ul class="inline" style="margin-top: 40px; margin-bottom: 25px;">
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="Fuerza física">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon axe"></i>
							<span class="button-content">
								{{ $characterToSee->getStatBag()->getExtraStrength() }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="Destreza física">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon boot"></i>
							<span class="button-content">
								{{ $characterToSee->getStatBag()->getExtraDexterity() }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="Resistencia física">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon hearth"></i>
							<span class="button-content">
								{{ $characterToSee->getStatBag()->getExtraResistance() }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="Poder mágico">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon fire"></i>
							<span class="button-content">
								{{ $characterToSee->getStatBag()->getExtraMagic() }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="Habilidad mágica">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon arrow"></i>
							<span class="button-content">
								{{ $characterToSee->getStatBag()->getExtraMagicSkill() }}
							</span>
						</span>
					</span>
				</li>
				<li>
					<span data-toggle="tooltip" data-placement="top" data-original-title="Contraconjuro">
						<span class="ui-button button" style="cursor: default;">
							<i class="button-icon thunder"></i>
							<span class="button-content">
								{{ $characterToSee->getStatBag()->getExtraMagicResistance() }}
							</span>
						</span>
					</span>
				</li>
			</ul>
		@endif
		
		<div style="position: relative; width: 340px; margin: 0 auto;">
			<!-- ARMA -->
			<div style="position: absolute; left: 40px; top: 150px;">
				<div class="box box-box-64-gold">
					@if ( $weapon )
						<img style="cursor: pointer;" src="{{ $weapon->get_image_path() }}" alt="" width="80px" height="80px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $weapon->get_text_for_tooltip() }}">
					@endif
				</div>
			</div>
			<!-- END ARMA -->

			@if ( ! $character->has_two_handed_weapon() )
				<!-- ESCUDO -->
				<div style="position: absolute; left: 230px; top: 150px;">
					<div class="box box-box-64-gold">
						@if ( $shield )
							<img style="cursor: pointer;" src="{{ $shield->get_image_path() }}" alt="" width="80px" height="80px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $shield->get_text_for_tooltip() }}">
						@endif
					</div>
				</div>
				<!-- END ESCUDO -->
			@endif

			<!-- ORBES -->
			<div class="box box-box-64-violet" style="position: absolute; left: 230px; top: 235px;">
				@if ( isset($orb) )
					<img src="{{ $orb->get_image_path() }}" data-toggle="tooltip" data-title="<div style='width: 200px;'><strong>{{ $orb->name }}</strong><p>{{ $orb->description }}</p></div>">
				@endif
			</div>
			<!-- END ORBES -->

			<!-- AYUDANTE -->
			<div style="position: absolute; left: 230px; top: 65px;">
                <div class="box box-box-64-blue">
                    @if ( $mercenary )
                        <img src="{{ $mercenary->get_image_path() }}" alt="" width="64px" height="64px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $mercenary->get_text_for_tooltip() }}">
                    @endif
                </div>
			</div>
			<!-- END AYUDANTE -->
			
			<!-- PERSONAJE -->
			<img src="{{ $characterToSee->get_image_path() }}" alt="">
			<!-- END PERSONAJE -->
		</div>
		
		<div class="clear-fix"></div>
		
		@if ( count($castableSkills) > 0 )
		<h2 style="margin-top: 50px; background-image: none;">Habilidades para lanzar</h2>
		<ul class="inline">
			@foreach ( $castableSkills as $castableSkill )
			<li class="clan-member-link">
				{{ Form::open(URL::to_route("post_authenticated_talent_cast")) }}
					{{ Form::token() }}
					{{ Form::hidden("skill_id", $castableSkill) }}
					{{ Form::hidden("id", $characterToSee->id) }}
					<img src="{{ URL::base() }}/img/icons/skills/{{ $castableSkill }}.png" alt="" skill-tooltip skill-id="{{ $castableSkill }}" skill-level="1">
					<div>
							{{ Form::submit('Lanzar', array('class' => 'ui-button ui-input-button')) }}
					</div>
				{{ Form::close() }}		
			</li>
			@endforeach
		</ul>
		@endif
		
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
		
		@if ( $character->can_attack($characterToSee) === true )
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
					{{ Form::open(URL::to_route("post_authenticated_battle_character")) }}
						{{ Form::token() }}
						{{ Form::hidden('pair', $pair->id) }}
						{{ Form::hidden('name', $characterToSee->name) }}
						{{ Form::submit('Luchar con la ayuda de ' . $pair->name, array('class' => 'btn btn-link', 'style' => 'color: white; text-shadow: none;')) }}
					{{ Form::close() }}
				@endif
			@endforeach
			
			{{ Form::open(URL::to_route("post_authenticated_battle_character")) }}
				{{ Form::token() }}
				{{ Form::hidden('name', $characterToSee->name) }}
				{{ Form::submit('Luchar contra ' . $characterToSee->name, array('class' => 'btn btn-link', 'style' => 'color: white; text-shadow: none;')) }}
			{{ Form::close() }}
		@else
			@if ( $character->zone_id != $characterToSee->zone_id )
				<div class="negative" style="margin-top: 25px; font-size: 16px;">¡{{ $characterToSee->name }} está en otra zona!</div>
			@endif
			@if ( $characterToSee->is_traveling && $character->can_follow($characterToSee) )
				{{ Form::open(URL::to_route("post_authenticated_character_follow")) }}
                    {{ Form::token() }}
                    {{ Form::hidden('id', $characterToSee->id) }}
                    <div>¡{{ $characterToSee->name }} se escapa!, ¿lo {{ Form::submit('persigues', array('class' => 'ui-input-button ui-button')) }}?</div>
				{{ Form::close() }}
			@endif
			@if ( $characterToSee->has_protection($character) )
				<div class="negative" style="margin-top: 25px; font-size: 16px;">{{ $characterToSee->name }} está momentáneamente protegido de tus ataques. Debes esperar un poco mas para poder atacarlo.</div>
			@endif
		@endif
		</div>
	</div>
@else
	<h2>El personaje especificado no existe</h2>
	<p><em>En estas tierras hay muchos forasteros, pero ninguno con este nombre...</em></p>
@endif