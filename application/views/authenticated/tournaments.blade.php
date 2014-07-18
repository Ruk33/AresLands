<div class="row orb-content">
    <div class="dialog-box">        
		<h2>
            <img src="{{ URL::base() }}/img/icons/npcs/8.png" />
            Torneos
        </h2>
        
		<p style="margin-top: 30px; margin-bottom: 20px;">
			Ante nosotros una nueva prueba, un nuevo desafío, una nueva 
            oportunidad para demostrar el poder de los nuestros. 
            ¿Quieres ser recordado por siempre? Solo los mejores son recordados, 
            solo quienes pueden demostrar su valia seran los homenajeados.
		</p>
        
        <div class="row">
            <div class="span6">
                @if ( $canRegisterClan )
                <div class="button-golden-mark" style="margin-left: 10px;">
                    {{ Form::open(URL::to_route("post_authenticated_tournament_register_clan")) }}
                        {{ Form::hidden("id", $tournament->id) }}
                        {{ Form::submit("Inscribir grupo", array("class" => "ui-button ui-input-button")) }}
                    {{ Form::close() }}
                </div>
                @endif
                
                @if ( $canUnRegisterClan )
                <div class="button-golden-mark" style="margin-left: 10px;">
                    {{ Form::open(URL::to_route("post_authenticated_tournament_unregister_clan")) }}
                        {{ Form::hidden("id", $tournament->id) }}
                        {{ Form::submit("Desapuntar grupo", array("class" => "ui-button ui-input-button")) }}
                    {{ Form::close() }}
                </div>
                @endif
            </div>
            
            <div class="span6">
                <div class="text-right" style="margin-top: 25px;">
                    <a href="{{ URL::to_route("get_authenticated_tournament_index") }}">
                        Ver torneos anteriores
                    </a>
                </div>
            </div>
        </div>
	</div>

	@if ( $canReclaimMvpReward )
		<div class="text-center" style="margin-bottom: 25px;">
			<a href="{{ URL::to('authenticated/claimTournamentMvpReward/' . $tournament->id) }}" class="normal-button">Reclama tu recompensa MVP</a>
		</div>
	@endif
	@if ( $canReclaimClanLiderReward )
		<div class="text-center" style="margin-bottom: 25px;">
			<a href="{{ URL::to('authenticated/claimTournamentClanLeaderReward/' . $tournament->id) }}" class="normal-button">Reclama tu recompensa lider</a>
		</div>
	@endif
    
	<table class="table table-striped brown-table">
		<thead>
			<tr>
				<th colspan="2">Información del torneo</th>
			</tr>
		</thead>
		<tbody>
			<tr>
                <td class="span6"><b>Nombre del torneo</b></td>
				<td class="span6">{{ $tournament->name }}</td>
			</tr>
			
			<tr>
                <td><b>Ganador</b></td>
				<td>{{ ( $tournament->clan_winner_id ) ? $tournament->winner->get_link() : 'Nadie' }}</td>
			</tr>

			<tr>
                <td><b>Permite el uso de pociones</b></td>
				<td>{{ ( $tournament->allow_potions ) ? 'Si' : 'No' }}</td>
			</tr>

			<tr>
                <td><b>Fecha de inicio</b></td>
				<td>{{ date('d-m-Y H:i:s', $tournament->starts_at) }}</td>
			</tr>

			<tr>
				<td><b>Fecha final</b></td>
				<td>{{ date('d-m-Y H:i:s', $tournament->ends_at) }}</td>
			</tr>

			<tr>
				<td><b>Duracion</b></td>
				<td>{{ date('z \d\í\a\(\s\) H:i:s', $tournament->ends_at - $tournament->starts_at) }}</td>
			</tr>

			<tr>
				<td><b>Obligatorio para todos los grupos</b></td>
				<td>{{ ( $tournament->all_clans ) ? 'Si' : 'No' }}</td>
			</tr>

			<tr>
				<td><b>Numero minimo de miembros por grupo</b></td>
				<td>{{ $tournament->min_members }}</td>
			</tr>

			<tr>
				<td data-toggle="tooltip" data-original-title="Personaje mas valioso"><b>MVP</b></td>
				<td>
                    <span style="line-height: 35px;">
                        {{ ( $tournament->mvp_id ) ? $tournament->mvp->get_link() : 'Nadie' }}
                    </span>
                    
                    @if ( $tournament->can_reclaim_mvp_reward($character) )
                    <a href="{{ URL::to_route("get_authenticated_tournament_claim_mvp_reward", array($tournament->id)) }}" class="ui-button button pull-right">
                        <i class="button-icon arrow"></i>
                        <span class="button-content">
                            Reclamar recompensa MVP
                        </span>
                    </a>
                    @endif
                </td>
			</tr>

			<tr>
				<td><b>Luchas totales</b></td>
				<td>{{ $tournament->battle_counter }}</td>
			</tr>

			<tr>
				<td><b>Jugadores totales</b></td>
				<td>{{ $tournament->get_registered_characters_count() }}</td>
			</tr>

			<tr>
				<td><b>Pociones magicas usadas</b></td>
				<td>{{ $tournament->potion_counter }}</td>
			</tr>

			<tr>
				<td><b>Pociones de vida usadas</b></td>
				<td>{{ $tournament->life_potion_counter }}</td>
			</tr>

			<tr>
				<td data-toggle="tooltip" data-original-title="Varia segun actividad"><b>Recompensa de monedas para todos</b></td>
				<td>{{ Item::get_divided_coins($tournament->coin_reward)['text'] }}</td>
			</tr>

			<tr>
				<td data-toggle="tooltip" data-original-title="Varia segun actividad"><b>Recompensa monedas para MVP</b></td>
				<td>{{ Item::get_divided_coins($tournament->mvp_coin_reward)['text'] }}</td>
			</tr>

			<tr>
				<td><b>Recompensa cofres para MVP</b></td>
				<td>
					<div class="quest-reward-item" data-toggle="tooltip" data-original-title="{{ Item::find(Config::get('game.chest_item_id'))->get_text_for_tooltip() }}<p>Cantidad: 2</p>">
						<img src="{{ URL::base() }}/img/icons/items/{{ Config::get('game.chest_item_id') }}.png" />
					</div>
				</td>
			</tr>

			<tr>
				<td><b>Objeto recompensa para lider de clan</b></td>
				<td>
					<div class="pull-left quest-reward-item" data-toggle="tooltip" data-original-title="{{ $tournament->reward_item->get_text_for_tooltip() }}<p>Cantidad: {{ $tournament->item_reward_amount }}</p>">
						<img src="{{ URL::base() }}/img/icons/items/{{ $tournament->item_reward }}.png" />
					</div>
                    
                    @if ( $tournament->can_reclaim_leader_reward($character) )
                    <a href="{{ URL::to_route("get_authenticated_tournament_claim_leader_reward", array($tournament->id)) }}" class="ui-button button pull-right">
                        <i class="button-icon document"></i>
                        <span class="button-content">
                            Reclamar recompensa Lider
                        </span>
                    </a>
                    @endif
				</td>
			</tr>
		</tbody>
	</table>

	<table class="table table-striped brown-table">
		<thead>
			<tr>
                <th class="span1"><div class="text-center">#</div></th>
				<th class="span4">Grupo</th>
                <th class="span3"><div class="text-center">% de victorias</div></th>
				<th class="span2"><div class="text-center">Victorias</div></th>
				<th class="span2"><div class="text-center">Derrotas</div></th>
			</tr>
		</thead>

		<tbody>
			<?php $n = 1; ?>
			@foreach ( $registeredClans as $registeredClan )
			<tr>
                <td><div class="text-center">{{ $n++; }}</div></td>
				<td>{{ ( $registeredClan->disqualified ) ? '<s data-toggle="tooltip" data-original-title="Descalificado">' . $registeredClan->clan->get_link() . '</s>' : $registeredClan->clan->get_link() }}</td>
				<td>
                    <div class="text-center">
                        {{ TournamentClanScore::get_victory_percentage($tournament->id, $registeredClan->clan->id) }}%
                    </div>
                </td>
				<td>
                    <div class="text-center">
                        {{ TournamentClanScore::get_victories($tournament->id, $registeredClan->clan->id) }}
                    </div>
                </td>
				<td>
                    <div class="text-center">
                        {{ TournamentClanScore::get_defeats($tournament->id, $registeredClan->clan->id) }}
                    </div>
                </td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>