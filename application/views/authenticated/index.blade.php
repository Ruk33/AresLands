<div ng-controller="CharacterController" style="margin-left: -15px;">		
	<!-- ACTIVIDADES -->
	@if ( count($activities) > 0 )
	<div class="activity-container" style="margin-left: 20px;">
		<div style="text-transform: uppercase; color: #D0D2D0; font-size: 10px; margin-bottom: 11px; margin-left: 30px; color: #fff3df;">actividades</div>
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

	<!-- TALENTOS -->
	@if ( count($talents) > 0 )
	<div style="margin-left: 20px;">
		<h2>Talentos</h2>

		<ul class="inline">
			@foreach ( $talents as $skillId )
			<li class="clan-member-link text-center" skill-tooltip skill-id="{{ $skillId }}" skill-level="1">
				{{ Form::open(URL::to_route("post_authenticated_talent_cast")) }}
                    {{ Form::token() }}
                    {{ Form::hidden('skill_id', $skillId) }}
                    {{ Form::hidden('id', $character->id) }}
                    <img src="{{ URL::base() }}/img/icons/skills/{{ $skillId }}.png" alt="" width="48px" height="48px">
                    <div>
                        {{ Form::submit('Lanzar', array('class' => 'ui-button ui-input-button')) }}
                    </div>
				{{ Form::close() }}
			</li>
			@endforeach
		</ul>
	</div>
	@endif
	<!-- END TALENTOS -->

	<div class="text-center" style="width: 700px; margin: 0 auto;">
		@if ( Session::has('error') )
			<div class="alert alert-error">
				{{ Session::get('error') }}
			</div>
		@endif
		
		@if ( Session::has('message') )
			<div class="alert alert-success">
				{{ Session::get('message') }}
			</div>
		@endif
	</div>
	
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

		<div style="min-height: 405px; position: relative;">
			<!-- ARMA -->
			<div style="position: absolute; left: 40px; top: 150px;">
				<div class="box box-box-64-gold">
					@if ( $weapon )
                        {{ Form::open(URL::to_route("post_authenticated_inventory_use")) }}
                            {{ Form::hidden("id", $weapon->character_item_id) }}
                            {{ Form::hidden("amount", 1) }}
                            <div data-toggle="tooltip" data-placement="top" data-original-title="{{ $weapon->get_text_for_tooltip() }}">
                                {{ Form::image($weapon->get_image_path(), $weapon->name, array("width" => "64px", "height" => "64px")) }}
                            </div>
                        {{ Form::close() }}
					@endif
				</div>
			</div>
			<!-- END ARMA -->

			@if ( ! $character->has_two_handed_weapon() )
				<!-- ESCUDO -->
				<div style="position: absolute; left: 230px; top: 150px;">
					<div class="box box-box-64-gold">
						@if ( $shield )
							{{ Form::open(URL::to_route("post_authenticated_inventory_use")) }}
                                {{ Form::hidden("id", $shield->character_item_id) }}
                                {{ Form::hidden("amount", 1) }}
                                <div data-toggle="tooltip" data-placement="top" data-original-title="{{ $shield->get_text_for_tooltip() }}">
                                    {{ Form::image($shield->get_image_path(), $shield->name, array("width" => "64px", "height" => "64px")) }}
                                </div>
                            {{ Form::close() }}
						@endif
					</div>
				</div>
				<!-- END ESCUDO -->
			@endif

			<!-- ORBES -->
			<div class="box box-box-64-violet" style="position: absolute; left: 230px; top: 230px;">
				@if ( isset($orb) )
					<img src="{{ $orb->get_image_path() }}" data-toggle="tooltip" data-title="<div style='width: 200px;'><strong>{{ $orb->name }}</strong><p>{{ $orb->description }}</p></div>">
				@endif
			</div>
			<!-- END ORBES -->

			<!-- AYUDANTE -->
			<div style="position: absolute; left: 230px; top: 65px;">
                <div class="box box-box-64-blue">
                    @if ( $mercenary )
                        {{ Form::open(URL::to_route("post_authenticated_inventory_use")) }}
                            {{ Form::hidden("id", $mercenary->character_item_id) }}
                            {{ Form::hidden("amount", 1) }}
                            <div data-toggle="tooltip" data-placement="top" data-original-title="{{ $mercenary->get_text_for_tooltip() }}">
                                {{ Form::image($mercenary->get_image_path(), $mercenary->name, array("width" => "64px", "height" => "64px")) }}
                            </div>
                        {{ Form::close() }}
                    @endif
                </div>
			</div>
			<!-- END AYUDANTE -->

			<!-- AYUDANTE SECUNDARIO -->
			@if ( $secondMercenary )
				<div style="position: absolute; left: 40px; top: 65px;">
					<div class="box box-box-64-green">
						<img src="{{ $secondMercenary->get_image_path() }}" alt="" width="64px" height="64px" data-toggle="tooltip" data-placement="top" data-original-title="{{ $secondMercenary->get_text_for_tooltip() }}">
					</div>
				</div>
			@endif
			<!-- END AYUDANTE SECUNDARIO -->
			
			<!-- PERSONAJE -->
            <div class="{{ KingOfTheHill::get_character_css_aura_class($character) }}">
                <img src="{{ $character->get_image_path() }}" alt="">
            </div>
			<!-- END PERSONAJE -->
		</div>
		
		<div class="span6" style="margin-top: -20px;">
			<!-- INVENTARIO -->
			<div class="alert-center">
				<div class="alert-top">
				</div>

				<div class="alert-content">
					<div class="inventario-label">
						inventario
						<hr>
					</div>

					<ul class="inline inventory-list">
						@for ( $i = 1, $max = 6; $i <= $max; $i++ )
							<li>
								<div class="box box-box-64-gray">
									@if ( isset($inventoryItems[$i]) && $inventoryItems[$i]->item )
										<img inventory-button item-id="{{ $inventoryItems[$i]->item->id }}" 
                                             character-item-id="{{ $inventoryItems[$i]->id }}" 
                                             token="{{ Session::token() }}" 
                                             type="{{ $inventoryItems[$i]->item->type }}" 
                                             amount="{{ $inventoryItems[$i]->count }}" 
                                             item-tooltip="{{ $inventoryItems[$i]->item->get_text_for_tooltip() }}" 
                                             style="cursor: pointer;" 
                                             src="{{ $inventoryItems[$i]->item->get_image_path() }}" 
                                             alt="" 
                                             width="80px" 
                                             height="80px"
                                        >
										<div class="inventory-item-amount" data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">{{ $inventoryItems[$i]->count }}</div>
									@endif
								</div>
							</li>
						@endfor

						<li style="vertical-align: top;" data-toggle="tooltip" data-original-title="Casillero bloqueado">
							<div class="box box-box-64-gold">
								<i class="icon-lock" style="vertical-align: -20px;"></i>
							</div>
						</li>

						<li style="vertical-align: top;" data-toggle="tooltip" data-original-title="Casillero bloqueado">
							<div class="box box-box-64-gold">
								<i class="icon-lock" style="vertical-align: -20px;"></i>
							</div>
						</li>
					</ul>
				</div>

				<div class="alert-bot"></div>
			</div>			
			<!-- END INVENTARIO -->
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="span6" ng-init="remainingPoints='{{ $character->points_to_change }}'">
		<h2>Estadísticas</h2>
		<ul class="unstyled text-center" style="width: 340px;">
			<li>
				<span style="font-size: 11px;">
					<b>SALUD:</b> 
					<span data-toggle="tooltip" data-placement="top" data-original-title="Salud actual / Salud máxima">
						<span ng-bind="character.current_life || '?'">?</span>/<span ng-bind="character.max_life || '?'">?</span>
					</span>
				</span>
				<div style="position: relative;">
					<div class="bar-empty-fill">
						<div id="lifeBar" life-bar="character" regeneration="{{ $character->regeneration_per_second + $character->regeneration_per_second_extra }}"></div>
					</div>
					<div class="bar-border">
					</div>
				</div>
			</li>
			@if ( $character->activity_bar )
			<li data-toggle="tooltip" data-placement="top" data-original-title="<b>Barra de actividad:</b> Completa la barra de actividad realizando acciones (explorar, batallar, viajar, etc.) para obtener las <b>recompensas</b>.">
				<span style="font-size: 11px;">BARRA DE ACTIVIDAD</span>
				<div style="position: relative;">
					<div class="bar-empty-fill">
						<div id="activityBar" style="width: {{ 100 * $character->activity_bar->filled_amount / Config::get('game.activity_bar_max') }}%"></div>
					</div>
					<div class="bar-border">
					</div>
				</div>
			</li>
			@endif
			<li style="margin-bottom: 30px;">
				<span style="font-size: 11px;">
					<b>EXPERIENCIA:</b> 
					<span data-toggle="tooltip" data-placement="top" data-original-title="Experiencia actual / Experiencia para subir de nivel">
						<span ng-bind="character.xp || '0'">?</span>/<span ng-bind="character.xp_next_level || '0'">0</span>
					</span>
				</span>
				<div style="position: relative;">
					<div class="bar-empty-fill">
						<div id="experienceBar" style="width: [[ 100 * character.xp / character.xp_next_level ]]%"></div>
					</div>
					<div class="bar-border">
					</div>
				</div>
			</li>
			
			<li style="margin-bottom: 10px;" ng-show="character.points_to_change > 0">
				<div class="clan-member-link text-center remaining-points-content">
					<p><b>Puntos restantes para cambiar:</b> <span ng-bind="character.points_to_change || '?'">?</span></p>
					<p style="margin: 0;">
						Puntos a cambiar: 
                        <input type="number" class="span3" ng-model="pointsToChange" />
					</p>
				</div>
			</li>
            
            <li class="secondary-attributes-li">
                <ul class="inline">
                    <li data-toggle="tooltip" data-original-title="<b>Chance de bloqueo:</b> Aumenta la chance de bloquear cantidades de daño (ya sea físico o mágico).<div class='positive'>Tu chance de bloqueo es: {{ $character->get_combat_behavior()->get_armor()->get_block_chance($character->get_combat_behavior()->get_damage()) }}%</div>"><div class="secondary-attribute block-chance"></div></li>
                    <li data-toggle="tooltip" data-original-title="<b>Bloqueo:</b> Aumenta la cantidad de daño a bloquear."><div class="secondary-attribute block"></div></li>
                    <li data-toggle="tooltip" data-original-title="<b>Chance de crítico:</b> Aumenta la chance de infligir un golpe crítico. Chance máxima, 50%.<div class='positive'>Tu chance de crítico es: {{ number_format($character->get_combat_behavior()->get_damage()->get_critical_chance($character), 2, ',', '.') }}%</div>"><div class="secondary-attribute critical-chance"></div></li>
                    <li data-toggle="tooltip" data-original-title="<b>Multiplicador de crítico:</b> Aumenta el daño que realizan tus ataques críticos."><div class="secondary-attribute critical-multiplier"></div></li>
                    <li data-toggle="tooltip" data-original-title="<b>Evasión:</b> Aumenta la chance de eludir completamente un ataque físico o mágico.<div class='positive'>Tu chance de eludir ataques es: {{ number_format($character->get_combat_behavior()->get_armor()->get_miss_chance($character->get_combat_behavior()->get_damage()), 2, ',', '.') }}%</div>"><div class="secondary-attribute evasion"></div></li>
                    <li data-toggle="tooltip" data-original-title="<b>Doble golpe:</b> Aumenta la chance de efectuar un doble golpe.<div class='positive'>Tu chance de efectuar doble golpe es: {{ number_format($character->get_combat_behavior()->get_damage()->get_double_hit_chance($character), 2, ',', '.') }}%</div>"><div class="secondary-attribute double-hit"></div></li>
                </ul>
            </li>
			
			<li style="margin-bottom: 10px;">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_strength')" class="button-icon" ng-show="character.points_to_change > 0" style="cursor: pointer;" dynamic-tooltip="statsPrices.strength">+</a>
					<i class="button-icon axe" ng-show="character.points_to_change <= 0"></i>
                    <span class="button-content" style="width: 200px;" data-toggle="tooltip" data-placement="top" data-original-title="<b>Fuerza:</b> Aumenta tu daño en ataques físicos y tu posibilidad de bloquear daño. Además, reduce la posibilidad del enemigo de bloquear tu daño.<div class='positive'>Tu poder físico es: {{ number_format($character->get_combat_behavior()->get_damage()->get_damage($character), 2, ',', '.') }}</div>">
						<b class="pull-left">Fuerza física:</b>

						<div class="pull-right">
							<span ng-bind="character.stat_strength">?</span>
							
							@if ( $character->getStatBag()->getExtraStrength() != 0 )
								@if ( $character->getStatBag()->getExtraStrength() > 0 )
									<span class="positive">+{{ $character->getStatBag()->getExtraStrength() }}</span>
								@else
									<span class="negative">{{ $character->getStatBag()->getExtraStrength() }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_dexterity')" class="button-icon" ng-show="character.points_to_change > 0" style="cursor: pointer;" dynamic-tooltip="statsPrices.dexterity">+</a>
					<i class="button-icon boot" ng-show="character.points_to_change <= 0"></i>
                    <span class="button-content" style="width: 200px;" data-toggle="tooltip" data-placement="top" data-original-title="<b>Destreza física:</b> Aumenta tu chance de ataques críticos (tanto mágicos como físicos) y tu posibilidad de esquivar ataques. Reduce además, la chance del enemigo de que esquive tus ataques.">
						<b class="pull-left">Destreza física:</b>

						<div class="pull-right">
							<span ng-bind="character.stat_dexterity">?</span>

							@if ( $character->getStatBag()->getExtraDexterity() != 0 )
								@if ( $character->getStatBag()->getExtraDexterity() > 0 )
									<span class="positive">+{{ $character->getStatBag()->getExtraDexterity() }}</span>
								@else
									<span class="negative">{{ $character->getStatBag()->getExtraDexterity() }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_resistance')" class="button-icon" ng-show="character.points_to_change > 0" style="cursor: pointer;" dynamic-tooltip="statsPrices.resistance">+</a>
					<i class="button-icon hearth" ng-show="character.points_to_change <= 0"></i>
                    <span class="button-content" style="width: 200px;" data-toggle="tooltip" data-placement="top" data-original-title="<b>Resistencia física:</b> Aumenta tu resistencia contra ataques físicos.">
						<b class="pull-left">Resistencia:</b>

						<div class="pull-right">
							<span ng-bind="character.stat_resistance">?</span>

							@if ( $character->getStatBag()->getExtraResistance() != 0 )
								@if ( $character->getStatBag()->getExtraResistance() > 0 )
									<span class="positive">+{{ $character->getStatBag()->getExtraResistance() }}</span>
								@else
									<span class="negative">{{ $character->getStatBag()->getExtraResistance() }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<?php $magicDamage = $character->stat_magic + $character->stat_magic_extra; ?>
			<li style="margin-bottom: 10px;">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_magic')" class="button-icon" ng-show="character.points_to_change > 0" style="cursor: pointer;" dynamic-tooltip="statsPrices.magic">+</a>
					<i class="button-icon fire" ng-show="character.points_to_change <= 0"></i>
                    <span class="button-content" style="width: 200px;" data-toggle="tooltip" data-placement="top" data-original-title="<b>Poder mágico:</b> Aumenta tu daño en ataques mágicos y reduce la chance de bloqueo del enemigo.<div class='positive'>Tu poder mágico es: {{ number_format($character->get_combat_behavior()->get_damage()->get_magical_damage($character), 2, ',', '.') }}</div>">
						<b class="pull-left">Poder mágico:</b>

						<div class="pull-right">
							<span ng-bind="character.stat_magic">?</span>

							@if ( $character->getStatBag()->getExtraMagic() != 0 )
								@if ( $character->getStatBag()->getExtraMagic() > 0 )
									<span class="positive">+{{ $character->getStatBag()->getExtraMagic() }}</span>
								@else
									<span class="negative">{{ $character->getStatBag()->getExtraMagic() }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_magic_skill')" class="button-icon" ng-show="character.points_to_change > 0" style="cursor: pointer;" dynamic-tooltip="statsPrices.magic_skill">+</a>
					<i class="button-icon arrow" ng-show="character.points_to_change <= 0"></i>
                    <span class="button-content" style="width: 200px;" data-toggle="tooltip" data-placement="top" data-original-title="<b>Habilidad mágica:</b> Aumenta tu chance de golpe crítico mágico.">
						<b class="pull-left">Habilidad mágica:</b>

						<div class="pull-right">
							<span ng-bind="character.stat_magic_skill">?</span>

							@if ( $character->getStatBag()->getExtraMagicSkill() != 0 )
								@if ( $character->getStatBag()->getExtraMagicSkill() > 0 )
									<span class="positive">+{{ $character->getStatBag()->getExtraMagicSkill() }}</span>
								@else
									<span class="negative">{{ $character->getStatBag()->getExtraMagicSkill() }}</span>
								@endif
							@endif
						</div>
					</span>
				</span>
			</li>
			<li style="margin-bottom: 10px;">
				<span class="ui-button button" style="cursor: default; width: 250px;">
					<a ng-click="addStat('stat_magic_resistance')" class="button-icon" ng-show="character.points_to_change > 0" style="cursor: pointer;" dynamic-tooltip="statsPrices.magic_resistance">+</a>
					<i class="button-icon thunder" ng-show="character.points_to_change <= 0"></i>
                    <span class="button-content" style="width: 200px;" data-toggle="tooltip" data-placement="top" data-original-title="<b>Contraconjuro:</b> Aumenta tu resistencia contra ataques mágicos.">
						<b class="pull-left">Contraconjuro:</b>

						<div class="pull-right">
							<span ng-bind="character.stat_magic_resistance">?</span>

							@if ( $character->getStatBag()->getExtraMagicResistance() != 0 )
								@if ( $character->getStatBag()->getExtraMagicResistance() > 0 )
									<span class="positive">+{{ $character->getStatBag()->getExtraMagicResistance() }}</span>
								@else
									<span class="negative">{{ $character->getStatBag()->getExtraMagicResistance() }}</span>
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
    
    <div class="row" style="margin-left: 20px;">
        <div ng-controller="Skill" class="span6">		
            <!-- BUFFS -->
            <h2>Efectos activos</h2>
            @if ( count($skills) > 0 )
                <ul class="unstyled inline buff-list">
                    @foreach ( $skills as $skill )
                        <li>
                            <div class="box box-box-32-gray">
                                <img src="{{ URL::base() }}/img/icons/skills/{{ $skill->skill_id }}.png" alt="" width="32px" height="32px" skill-tooltip skill-id="{{ $skill->skill_id }}" skill-level="{{ $skill->level }}">

                                <div data-toggle="tooltip" data-placement="top" data-original-title="Cantidad">({{ $skill->amount }})</div>

                                <div style="font-size: 9px; font-family: arial;">
                                    @if ( $skill->end_time != 0 )
                                        <span class='timer' data-endtime='{{ $skill->end_time - time() }}'></span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
            Ninguno
            @endif
            <!-- END BUFFS -->
        </div>

        <div class="span6">
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
</div>